<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class NotifyController extends Apk_BaseController {
	
	public function orderAction() {
		$order_no = $this->getPost('out_order_no');
		$close_time = strtotime($this->getPost('close_time'));
		if (!$order_no || !$close_time) $this->output(-1, '参数错误.');
		
		$ret = Gou_Service_Order::getOrderPayStatus($order_no);
		if(!$ret) $this->output(-1, 'notify failed.');
		$this->output(0, $order_no);
	}
}
