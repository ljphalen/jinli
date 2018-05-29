<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class OrderController extends Api_BaseController {

    public function init() {
        Yaf_Dispatcher::getInstance()->enableView();
    }

    public function notifyAction() {
        $from_xml= file_get_contents("php://input");
      
        //验证签名
        $notify = new WeiXin_Server_Notify();
        $notify->saveData($from_xml);
        $right = $notify->checkSign();
         
        $weixin = new WeiXin_Base();
        if(!$right) {
           $return_arr = array('return_code'=>'FAIL', 'return_msg'=>'签名错误');
           $xml = $weixin->arrayToXml($return_arr);
           //Common::log($return_arr, "notify.log");
           exit($xml);
        }
        
        $data = $notify->data;
        //Common::log($data, "notify.log");
        //订单业务处理        
        $order = Fj_Service_Order::getByTradeNo($data['out_trade_no']);
        
        if($order && ($order['status'] == 1) && $data['result_code'] == 'SUCCESS' && $data['return_code'] == 'SUCCESS') {
            $order_data = array(
                'out_trade_no'=>$data['transaction_id'],
                'pay_time'=>$data['time_end'],
                'status'=>2
            );
            $ret = Fj_Service_Order::updateOrder($order_data, $order['id']);
            if(!$ret) {
                $return_arr = array('return_code'=>'FAIL', 'return_msg'=>'订单处理失败');
                $xml = $weixin->arrayToXml($return_arr);
                //Common::log($return_arr, "notify.log");
                exit($xml);
            }
        }

        $return_arr = array('return_code'=>'SUCCESS', 'return_msg'=>'');
        $xml = $weixin->arrayToXml($return_arr);
        //Common::log($return_arr, "notify.log");
        exit($xml);
        
    }

    public function payAction() {
        $code = $this->getInput('code');
        $user = new WeiXin_Server_User();

        //=========步骤1：网页授权获取用户openid============
        if (!$code) {
            $url = $user->createOauthUrlForCode(WeiXin_Config::PAY_CALL_URL, false);
            $this->redirect($url);
            exit;
        }
        //=========步骤2：使用统一支付接口，获取prepay_id============
        $user->setCode($code);
        $user->getToken();

        //使用统一支付接口
        $unifiedOrder = new WeiXin_Client_Pay();

        //设置统一支付接口参数
        //设置必填参数
        //appid已填,商户无需重复填写
        //mch_id已填,商户无需重复填写
        //noncestr已填,商户无需重复填写
        //spbill_create_ip已填,商户无需重复填写
        //sign已填,商户无需重复填写
        $unifiedOrder->setParameter("openid","$user->openid");//商品描述
        $unifiedOrder->setParameter("body","test----");//商品描述
        //自定义订单号，此处仅作举例
        $timeStamp = time();
        $out_trade_no = WeiXin_Config::APPID."$timeStamp";
        $unifiedOrder->setParameter("out_trade_no","$out_trade_no");//商户订单号
        $unifiedOrder->setParameter("total_fee","1");//总金额
        $unifiedOrder->setParameter("notify_url",WeiXin_Config::NOTIFY_URL);//通知地址
        $unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
        //非必填参数，商户可根据实际情况选填
        //$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号
        //$unifiedOrder->setParameter("device_info","XXXX");//设备号
        //$unifiedOrder->setParameter("attach","XXXX");//附加数据
        //$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
        //$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间
        //$unifiedOrder->setParameter("goods_tag","XXXX");//商品标记
        //$unifiedOrder->setParameter("openid","XXXX");//用户标识
        $unifiedOrder->setParameter("product_id","1122");//商品ID

        $prepay_id = $unifiedOrder->getPrepayId();
        //=========步骤3：使用jsapi调起支付============
        $user->setPrepayId($prepay_id);

        $jsApiParams = $user->getParameters();
        $this->assign('jsApiParameters', $jsApiParams);
    }
    
    /**
     * 砍价订单列表
     */
    public function orderListAction(){
        $page = $this->getInput('page');
        $id = Common::encrypt($this->getInput('id'),'DECODE');
        
        if(!$page) $page = 1;
        $perpage = 10;
         
        $data = array();
        $webroot = Common::getWebRoot();
        list($total,$orders) = Fj_Service_Order::getList($page, $perpage, array('uid'=>$id), array('id'=>'DESC'));
    
        if($orders) {
            foreach ($orders as $key=>$value) {
                $data[$key]['price'] = $value['real_price'];
                $data[$key]['trade_no'] = $value['trade_no'];
                $data[$key]['price'] = $value['real_price'];
                $data[$key]['link'] = $webroot.'/order/detail?trade_no='.$value['trade_no'];
                $data[$key]['create_time'] = date('Y-m-d', $value['create_time']);
                $data[$key]['status'] = $value['status'];
                $data[$key]['status_text'] = Fj_Service_Order::orderStatus($value['status']);
            }
    
        }
        $hasnext = (ceil((int) $total / $perpage) - ($page)) > 0 ? true : false;
        $this->output(0, '', array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page));
    }


}
