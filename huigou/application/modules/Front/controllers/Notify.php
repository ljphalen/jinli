<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class NotifyController extends Front_BaseController {
	
	public function orderAction() {
		$order_no = $this->getPost('order_no');
		if (!$order_no) $this->output(-1, '参数错误.');
		$ret = Gc_Service_Order::getByOutTradeNo($order_no);
		if (!$ret) {
			Common::log('E : ' . $order_no, 'notify.log');
			$this->output(-1, 'order is not exist.');
		}
		$result = Api_Gionee_Pay::getOrder(array('order_no'=>$order_no));
		if ($result['process_status'] == 3) {
			$ret = Gc_Service_Order::updateByOutTradeNo(array('status'=>2, 'pay_time'=>Common::getTime()), $order_no);
			if (!$ret) {
				Common::log('update order status : ' . $order_no, 'notify.log');
			}
		}
		$this->output(0, $order_no);
	}
}