<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class OrderfreelogController extends Admin_BaseController {
	
	public $actions = array(
		'indexUrl' => '/Admin/Orderfreelog/index',
		'editUrl' => '/Admin/Orderfreelog/edit',
		'editPostUrl' => '/Admin/Orderfreelog/edit_post',
	);
	
	public $perpage = 20;
	public $status = array(
			1 => '未发奖',
			2 => '已发奖'
			);
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));		
		$param = $this->getInput(array('username', 'goods_id','status'));
		if ($param['username'] != '') $search['username'] = $param['username'];
		if ($param['goods_id'] != '') $search['goods_id'] = $param['goods_id'];		
		if ($param['status'] != '') $search['status'] = $param['status'];
		$perpage = $this->perpage;
		
		list($total, $logs) = Gc_Service_OrderFreeLog::getList($page, $perpage, $search);
		
		$this->assign('logs', $logs);
		$this->assign('status', $this->status);
		$this->assign('param', $param);
		$url = $this->actions['indexUrl'] .'/?'. http_build_query($param) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$Info = Gc_Service_OrderFreeLog::getOrderFreeLog(intval($id));
		
		$this->assign('info', $Info);
		$this->assign('status', $this->status);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'status', 'remark'));
		$ret = Gc_Service_OrderFreeLog::updateOrderFreeLog($info, intval($info['id']));
		if (!$ret) $this->output(-1, '更新日志失败');
		$this->output(0, '更新日志成功.'); 		
	}
}