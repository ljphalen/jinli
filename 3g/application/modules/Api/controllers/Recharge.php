<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class RechargeController extends Api_BaseController {

    //获得手机归属地及充值实际所需金额
    public function infoAction() {
        $postData = $this->getInput(array('mobile', 'goods_id'));
        self::_checkData($postData['mobile'], $postData['goods_id']);
        $goods = self::_getGoodsInfo($postData['goods_id']);
        if (empty($goods)) {
            $this->output('-1', '该商品不存在或已下架！');
        }
        Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_U_G_PHONE, $goods['id']);
        Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_U_G_PHONE_UV, $goods['id'], $this->getSource());
        if (empty($goods['card_info_id'])) {
            $this->output('-1', '商品类型不对');
        }
        $cardMsg = User_Service_CardInfo::get($goods['card_info_id']);
        if($cardMsg['group_type'] == 1){
        	$this->_getRechargeRealData($postData['mobile'], intval($goods['price']), $cardMsg);
        }elseif($cardMsg['group_type'] == 3){
        	$this->_checkMobilePlatform($postData['mobile'], intval($goods['price']),$cardMsg['type_id']);
        }
    }

    
    private function _checkData($mobile, $goodsId) {
        if (!intval($goodsId)) {
            $this->output('-1', '参数错误！');
        }
        if (!intval($mobile) || !preg_match('/^1[3|5|6|7|8|9]\d{9}$/', $mobile)) {
            $this->output('-1', '请检查手机号是否正确！');
        }
    }

    private function _getGoodsInfo($goodsId) {
        $goods = User_Service_Commodities::getBy(array(
            'id'         => $goodsId,
            'status'     => 1,
            'start_time' => array('<=', time()),
            'end_time'   => array('>=', time()),
        ));
        return $goods;
    }

    /**
     * 根据类型得到真实充值／充流量包的数据
     *
     * @param number $type
     */
    private function _getRechargeRealData($mobile = 0, $outMoney = 0, $type=1) {
    	$data = array();
        $subPhone = Util_String::substr($mobile, 0, 7);
        $ofpayReq = new Vendor_Ofpay();
        $content  = $ofpayReq->req_telquery($mobile, $outMoney);
         if ($content['cardinfo']['retcode'] != 1) {
                  $this->assign('-1', '手机信息查询失败！',array('inprice'=>0,'outprice'=>0,'area'=>''));
           }
          $data = array(
               'inprice'  => Common::money($content['cardinfo']['inprice']),
               'outprice' => $outMoney,
                'area'     => $content['cardinfo']['game_area']
            );
          $this->output('0','查询成功',$data);
  	  }

    private function _checkMobilePlatform($mobile,$price,$type_id=0){
    	$ofpayReq = new Vendor_Ofpay();
    	$mobileInfo = $ofpayReq->req_mobinfo($mobile);
    	if(empty($mobileInfo) || empty($mobileInfo['area'])){
    		$this->output('-1','手机信息查询失败!');
    	}
    	if($mobileInfo['plat_id'] != $type_id){
    		$this->output('-1','对不起，该商品不支持您的手机运营商！');
    	}
    	$this->output('0','',array('outprice'=>$price,'area'=>$mobileInfo['area'],'inprice'=>$price));
    }
    /**
     * 充值返回的数据接口
     * 更新本地订单状态
     * order_status  订单的状态    0,未处理，1：处理成功，2:处理中，－1：取消订单
     * ret_status 主要针对 欧飞充值平台返回的状态    0：未处理，1：充值成功 2：充值进行中 -1: 充值失败
     */
    public function responseAction() {
        $postData  = $this->getInput(array('ret_code', 'sporder_id', 'ordersuccesstime', 'err_msg'));
        $orderInfo = User_Service_Order::getBy(array('order_sn' => $postData['sporder_id']));
        if(empty($orderInfo)){
        	error_log("订单不存在:".json_encode($postData)."\n",3,'/tmp/user_order_response_'.date('Ymd',time()).'log');
        	exit('Failed');
        }
      /*   if(in_array($orderInfo['order_status'],array('-1','1'))){
        	exit('OK!');
        } */
         $ret = self::_changeOrderStatausAndWriteLogs($postData, $orderInfo);
         if (in_array($postData['ret_code'], array('1', '9'))) { //充值成功或失败时发站内信
            	$status = ($postData['ret_code'] == '1') ? 1 : -1;//订单状态用于前台显示
            	User_Service_Order::changeScoresAndSendMsg($orderInfo, $status);
           }
       	 exit("OK");
    	}

    	//第三方返回status 状态 0:取消,1 待支付,2,支付矢败,3,处理中,4,支付成功 5 退款中 6 退款成功 7 订单过期 8 暂存订单 9 交易shibai 10 ,退款矢败
    	public function flowResponseAction(){
    		$postData = $this->getInput(array('body','req_channel','req_time','req_date','trans_code','err_msg','status','order_id'));
    		$order    = User_Service_Order::getBy(array('order_sn'=>$postData['order_id']));
    		if(empty($order))  {
    			exit(json_encode( array('rsp_code'=>'0001','rsp_desc'=>'订单不存在')));
    		  }
    		if(in_array($order['rec_status'],array('1','-1'))){
    			exit(json_encode(array('rsp_code'=>'0000','rsp_desc'=>'交易已完成!')));
    		}

    		 $postData['ret_code']= $status = 0;
    		if($postData['status'] == '4'){
    			$status = 1;
    			$postData['ret_code'] = 1;
    		}elseif (in_array($postData['status'],array('0','2','6','7','9','10'))){
    			$status = -1;
    			$postData['ret_code'] = 9;
    		}
    		$data = array(
    				'rsp_code'	=>'0001',
    				'rsp_desc'	=>'交易矢败',
    				'rcv_trans_id'=>$order['order_sn'],
    				'rcv_sys'			=>'002',
    				'rcv_date'		=>$postData['req_date'],
    				'rcv_date_time'=>$postData['req_time'],
    				'body' => array(),
    		);
    		if(intval($status)){
    			 $postData['ordersuccesstime'] = date('YmdHis',time());
    			 $postData['err_msg'] = $postData['err_msg'];
    			$ret =  self::_changeOrderStatausAndWriteLogs($postData,$order);
    			$log  = User_Service_Order::changeScoresAndSendMsg($order, $status);
    			$data['rsp_code'] = '0000';
    			$data['rsp_desc']='交易成功';
    		}
    		exit(json_encode($data));
    	}
    	
    /**
     * 更新订单状态及写日志
     *
     * @param unknown $postData
     * @param unknown $order
     *
     * @return boolean
     */
    private static function _changeOrderStatausAndWriteLogs($postData = array(), $order) {
        $data = array();
        switch ($postData['ret_code']) {
            case 0: {//进行中
                $data['pay_status']   = 1;
                $data['order_status'] = $data['shipping_status'] = $data['rec_status'] = 2;
                break;
            }
            case 1: {//充值成功
                $data['order_status'] = $data['shipping_status'] = $data['pay_status'] = $data['rec_status'] = 1;
                break;
            }
            case 9: {//充值失败
                $data['order_status'] = $data['pay_status'] = $data['shipping_status'] = $data['rec_status'] = -1;
                break;
            }
            default:
                break;
        }
        $data['desc']           = $postData['err_msg'];
        $data['rec_order_time'] = strtotime($postData['ordersuccesstime']);
        $result                 = User_Service_Order::update($data, $order['id']);//更新订单状态
        //写订单状态更新日志
        $logData = array(
            'order_id'        => $order['id'],
            'action_user'     => 'system',
            'order_status'    => $data['order_status'],
            'pay_status'      => $data['pay_status'],
            'shipping_status' => $data['shipping_status'],
            'add_time'        => time(),
            'action_note'     => $data['desc']
        );
        $writeLog     = User_Service_Actions::add($logData);
        //写Api调用返回结果的日志
        $orderType = $order['order_type'] == 1 ? 'recharge' : 'flow';
        $apiData   = array(
            'status'   => $postData['ret_code'],
            'add_time' => strtotime($postData['ordersuccesstime']),
            'order_sn' => $order['order_sn'],
            'desc'     => json_encode(array('orderinfo' => $postData)),
            'api_type' => $orderType
        );
        $api       = User_Service_Recharge::add($apiData);
        return $result && $writeLog;
    }


    public function reissueAction() {
        $order_sn = $this->getInput('order_sn');
        $ofpayReq = new Vendor_Ofpay();
        $res      = $ofpayReq->req_reissue($order_sn);
        //$res      = Api_Ofpay_Recharge::reissue($order_sn);
        $this->output($res['msginfo']['retcode'] == 1 ? '0' : '-1', $res['msginfo']['err_msg']);
    }
}