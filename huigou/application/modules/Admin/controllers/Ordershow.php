<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class OrdershowController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Ordershow/index',
		'editUrl' => '/Admin/Ordershow/edit',
		'editPostUrl' => '/Admin/Ordershow/edit_post',
	);
	
	public $perpage = 20;
	
	public $ordershow_status = array (
			0=>'所有',
			1=>'未审核',
			2=>'已审核',
			3=>'已发奖',
	);
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$sDate = $this->getInput('sdate');
		$eDate = $this->getInput('edate');
		$mobile = $this->getInput('mobile');
		$receive_name = $this->getInput('receive_name');
		$order_id = $this->getInput('order_id');
		$channel_id = $this->getInput('channel_id');
		$status = $this->getInput('status');
		
		$page = intval($this->getInput('page'));
		$param = $this->getInput(array('mobile', 'receive_name', 'order_id','channel_id', 'status', 'sdate','edate'));
		$perpage = $this->perpage;
		
		if ($param['mobile'] != '') $search['mobile'] = $param['mobile'];
		if ($param['receive_name'] != '') $search['receive_name'] = $param['receive_name'];
		if ($param['order_id'] != '') $search['order_id'] = $param['order_id'];
		if ($param['channel_id'] != '') $search['channel_id'] = $param['channel_id'];
		if ($param['status']) $search['status'] = $param['status'];
		if ($param['sdate'] != '') $search['sdate'] = strtotime($param['sdate']);
		if ($param['edate'] != '') $search['edate'] = strtotime($param['edate']);
		list($total, $listordershow) = Gc_Service_OrderShow::getList($page, $perpage, $search);
		
		//状态
		$this->assign('ordershow_status', $this->ordershow_status);
		
		//渠道
		list(,$ordershow_channel) = Gc_Service_OrderChannel::getAllOrderChannel();
		$ordershow_channel = Common::resetKey($ordershow_channel, 'id');
		
		$this->assign('ordershow_channel', $ordershow_channel);

		$this->assign('param', $param);
		$url = $this->actions['listUrl'] .'/?'. http_build_query($param) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));		
		$this->assign('sdate', $sDate);
		$this->assign('edate', $eDate);
		$this->assign('listordershow', $listordershow);
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Gc_Service_OrderShow::getOrderShow(intval($id));
	
		//状态
		$this->assign('ordershow_status', $this->ordershow_status);
		
		//渠道
		list(,$ordershow_channel) = Gc_Service_OrderChannel::getAllOrderChannel();
		$ordershow_channel = Common::resetKey($ordershow_channel, 'id');
		$this->assign('ordershow_channel', $ordershow_channel);
				
		$this->assign('info', $info);
	}
	
	
	/**
	 *
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('status','id','award'));
		$ret = Gc_Service_OrderShow::updateOrderShow($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.');
	}
}
