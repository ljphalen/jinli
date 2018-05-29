<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Fanli_OrderController extends Admin_BaseController {
	
	public $actions = array(
		'indexUrl' => '/Admin/Fanli_Fxlog/index',
		'editUrl' => '/Admin/Fanli_Fxlog/edit',
		'editPostUrl' => '/Admin/Fanli_Fxlog/edit_post',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));		
		$param = $this->getInput(array('user_id', 'trade_id', 'product_id', 'create_start_time','create_end_time', 'order_start_time', 'order_end_time'));
		
		if ($param['user_id'] != '') $search['user_id'] = $param['user_id'];
		if ($param['trade_id'] != '') $search['trade_id'] = $param['trade_id'];
		if ($param['product_id'] != '') $search['product_id'] = $param['product_id'];
		if ($param['create_start_time']) $search['create_start_time'] = strtotime($param['create_start_time']);
		if ($param['create_end_time']) $search['create_end_time'] = strtotime($param['create_end_time']);
		if ($param['order_start_time']) $search['order_start_time'] = strtotime($param['order_start_time']);
		if ($param['order_end_time']) $search['order_end_time'] = strtotime($param['order_end_time']);
		
		if($search['create_start_time'] >= $search['create_end_time']){
			unset($params['create_start_time']);
			unset($params['create_end_time']);
			unset($search['create_start_time']);
			unset($search['create_end_time']);
		}
		if($search['order_start_time'] >= $search['order_end_time']){
			unset($params['order_start_time']);
			unset($params['order_end_time']);
			unset($search['order_start_time']);
			unset($search['order_end_time']);
		}
		
		list($total, $orders) = Fanli_Service_Order::search($page, $this->perpage, $search);
		
		$this->assign('orders', $orders);
		$this->assign('status', $this->status);
		$this->assign('param', $param);
		
		$this->cookieParams();
		$url = $this->actions['indexUrl'] .'/?'. http_build_query($param) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		$this->assign('total', $total);
	}
	
	
}
