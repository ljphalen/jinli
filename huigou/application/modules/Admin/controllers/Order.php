<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class OrderController extends Admin_BaseController {
	
	public $actions = array(
		'indexUrl' =>'/admin/order/index',
		'editUrl'=>'/admin/order/edit',
		'editPostUrl'=>'/admin/order/edit_post',
		'addUrl'=>'/admin/order/add',
		'addPostUrl'=>'/admin/order/add_post',
	);
	
	public $perpage = 20;
	
	/**
	 * 
	 * Get order list
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$params  = $this->getInput(array('username', 'status', 'out_trade_no', 'trade_no'));
		if ($page < 1) $page = 1;
		
		$search = array();
		if ($params['username']) $search['username'] = $params['username'];
		if ($params['status']) $search['status'] = $params['status'];
		if ($params['out_trade_no']) $search['out_trade_no'] = $params['out_trade_no'];
		if ($params['trade_no']) $search['trade_no'] = $params['trade_no'];
		
		list($total, $order) = Gc_Service_Order::getList($page, $this->perpage, $search);
		
		$this->assign('result', $order);
		$this->assign('total', $total);
		
		foreach($order as $key=>$value) {
			$order_ids[] = $value['id'];
		}
		$address = Gc_Service_Order::getAddressByOrderIds($order_ids);
		$address = Common::resetKey($address, 'order_id');
		$this->assign('address', $address);		
		
		$this->assign('params', $params);
		//get pager
		$url = $this->actions['indexUrl'] .'/?'. http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	public function editAction() {
		$id = $this->getInput('id');
		$order = Gc_Service_Order::getOrder($id);
		$this->assign('order', $order);
		
		$address = Gc_Service_Order::getOrderAddress($order['id']);
		$this->assign('address', $address);
		
		$goods = Gc_Service_LocalGoods::getLocalGoods($order['goods_id']);
		$this->assign('goods', $goods);
		
		$supplier = Gc_Service_Supplier::getSupplier($order['supplier']);
		$this->assign('supplier', $supplier);
	}
	
	/**
	 * 
	 */
	public function edit_postAction() {
		$id = intval($this->getPost('id'));
		$status = intval($this->getPost('status'));
		$order = Gc_Service_Order::getOrder($id);
		if (!$order) $this->output(-1, '参数错误.');
		if ($status < $order['status']) $this->output(-1, '订单状态更新错误.');
		//订单成功
		if ($status == 5) {
			$ret = Api_Gionee_Pay::codOrder(array('order_no'=>$order['out_trade_no']));
			if (!$ret) $this->output(-1, '操作失败.');
		}
		if ($status == 6) {
			$ret = Api_Gionee_Pay::cancelOrder(array('order_no'=>$order['out_trade_no']));
			if (!$ret) $this->output(-1, '操作失败.');
		}		
		$ret = Gc_Service_Order::updateOrder(array('status'=>$status), $id);
		$this->output(0,'操作成功.');
	}
}
