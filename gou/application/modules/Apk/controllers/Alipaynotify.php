<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class AlipaynotifyController extends Apk_BaseController {
	
    public function indexAction() {
		$params = $this->getPost(array('service', 'sec_id', 'v', 'sign', 'notify_data'));
		
	    if(!$params['sign'] || !$params['notify_data']) {
	        $status = 'fail';
	    }
	    
	    $api = new Api_Alipay_Notify();
	    $notify = $api->verifyNotify($params);
	    if($notify) {
	        $params['notify_data'] = $notify;
	        $status = 'success';
	        //处理订单业务
	        $order = Gou_Service_Order::getByTradeNo($notify['out_trade_no']);
	        if($order && $order['status'] != 2 && $notify['trade_status'] == 'TRADE_SUCCESS') {
	            //支付订单
	            $ret = Cut_Service_Order::payOrder($order, $notify);
	            if(!$ret) $status = 'fail';
	            
	            //logs
	            $webroot = COmmon::getWebRoot();
	            $log_data = array(
    	            'mark'=>$notify['out_trade_no'],
    	            'api_type'=>'alipay',
    	            'url'=>"notify---".$webroot.'/alipaynotify/index',
    	            'request'=>json_encode($params),
    	            'response'=>$status,
    	            'create_time'=>Common::getTime()
	            );
	            //Gou_Service_ApiLog::add($log_data);
	            Common::getQueue()->push("api_log", $log_data);
	            
	            //如果支付时间大于15分钟,需退款给用户
	            if(strtotime($notify['gmt_payment']) - $order['create_time'] > 20*60) {
	                //请求退款
	              Gou_Service_Order::updateOrder(array('status'=>10), $order['id']);
	              Cut_Service_Goods::updateGoods(array('quantity'=>1,'status'=>1), $order['goods_id']);
	              //log
	              $log = array(
    	              'order_id'=>$order['id'],
    	              'order_type'=>1,
    	              'uid'=>0,
    	              'create_time'=>time(),
    	              'update_data'=>json_encode(array('status' => 10))
	              );
	              $log_ret = Gou_Service_Order::addOrderLog($log);	              
	              Common::sms($order['phone'], 'Hi 亲爱的伙伴，您的订单支付时间超过了15分钟，我们将给您全额退款！');
	            $phone_arr = array('18575536449', '13530743763', '18676656198');
	              foreach($phone_arr as $key=>$val){
	                  Common::sms($val, '后台砍价订单'.$order['trade_no'].'需退款，请及时处理！');
	              }
	            }
	        }
	        
	    } else {
	        $status = 'fail';
	    }
	    
		exit($status);
	}
	
	
	/*
	 * 
	 * refund
	 * out_order_no  商户支付订单号
	 * order_no  支付订单号
	 * out_refund_no  商户退款订单号
	 * refund_no  退款订单号
	 */
    public function refundAction() {
        $params = $this->getPost(array('notify_time', 'notify_id', 'batch_no', 'success_num', 'sign', 'result_details',
        'notify_type', 'sign_type'));
        
        $api = new Api_Alipay_Notify();
        $verify = $api->verifyRefundNotify($params);
        if($verify) {
            //解析参数和处理业务
            $status = 'success';
            //处理订单业务
            $order_refund = Gou_Service_OrderRefund::getBy(array('refund_no'=>$params['batch_no']));
            if(!$order_refund) $status = 'fail';
            $order = Gou_Service_Order::getByTradeNo($order_refund['trade_no']);
            
            //更新支付状态
            $up_ret = Gou_Service_Order::updateOrder(array('status'=>12), $order['id']);
            if(!$up_ret) $status = 'fail';
            
            Gou_Service_OrderRefund::update(array('status'=>30), $order_refund['id']);
            
            //log
            $log = array(
                'order_id'=>$order['id'],
                'order_type'=>1,
                'uid'=>0,
                'create_time'=>time(),
                'update_data'=>json_encode(array('status' => 12))
            );
            $log_ret = Gou_Service_Order::addOrderLog($log);
        } else {
            $status = 'fail';
        }
		
		//logs
		$webroot = Common::getWebRoot();
		$log_data = array(
				'mark'=>$params['batch_no'],
				'api_type'=>'alipayrefund',
				'url'=>"refund-notify---".$webroot.'/alipaynotify/refund',
				'request'=>json_encode($params),
				'response'=>$status,
				'create_time'=>Common::getTime()
		);
		Common::getQueue()->push("api_log", $log_data);
		Common::sms($order['phone'], 'Hi 亲爱的伙伴，您在砍价专区的订单由于支付时间超过15分钟，已将您的订单金额退回您的支付宝账户，请登录支付宝查看！');
		
		exit($status);
	}
}
