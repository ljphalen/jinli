<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class NotifyController extends Front_BaseController {
	
	public function orderAction() {
		$order_no = $this->getPost('out_order_no');
		$close_time = strtotime($this->getPost('close_time'));
		if (!$order_no || !$close_time) $ret = 'failed';
		
		$order = Gou_Service_Order::getByTradeNo($order_no);
		if (!$order) $ret = 'failed';
		
		if($order['order_type'] == 5) {
		    $ret = Cut_Service_Order::getOrderPayStatus($order_no);
		} else {
		    $ret = Gou_Service_Order::getOrderPayStatus($order_no);
		}
		
		if(!$ret) {
			$ret = 'failed';
		} else {
			$ret = 'success';
		}
		
		//logs
		$webroot = COmmon::getWebRoot();
		$log_data = array(
				'mark'=>$order_no,
				'api_type'=>'pay',
				'url'=>$webroot.'/notify/order',
				'request'=>json_encode(array('out_order_no'=>$order_no, 'close_time'=>$close_time)),
				'response'=>$ret,
				'create_time'=>Common::getTime()
		);
		Gou_Service_ApiLog::add($log_data);
		
		exit($ret);
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
		$info = $this->getPost(array('out_order_no', 'order_no', 'out_refund_no', 'refund_no', 'status', 'description', 'refund_channel', 'refund_status', 'refund_fee', 'refund_time'));
		$order_refund = Gou_Service_OrderRefund::getBy(array('trade_no'=>$info['out_order_no'], 'out_trade_no'=>$info['order_no'], 
		        'refund_no'=>$info['out_refund_no'], 'out_refund_no'=>$info['refund_no']));
		
		if (!$order_refund) $ret = 'failed';
		
		$ret = Gou_Service_OrderRefund::getRefundStatus($order_refund['refund_no'], array('description'=>$info['description']));
		if(!$ret) {
			$ret = 'failed';
		} else {
			$ret = 'success';
		}
		
		//logs
		$webroot = COmmon::getWebRoot();
		$log_data = array(
				'mark'=>$order_refund['trade_no'],
				'api_type'=>'refund',
				'url'=>$webroot.'/notify/refund',
				'request'=>json_encode($info),
				'response'=>$ret,
				'create_time'=>Common::getTime()
		);
		Gou_Service_ApiLog::add($log_data);
		
		exit($ret);
	}
}
