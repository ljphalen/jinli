<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class FatelogController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Fatelog/index',
		'editUrl' => '/Admin/Fatelog/edit',
		'editPostUrl' => '/Admin/Fatelog/edit_post',
	);
	
	public $fate_status = array(
		'-1'=>'所有',
		0=>'未中奖',
		1=>'中奖未确认',
		2=>'中奖已确认',	
		3=>'已发奖'
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$param = $this->getInput(array('mobile', 'status'));
		$perpage = $this->perpage;
		
		$search['mobile'] = $param['mobile'];
		$search['status'] = $param['status'];
		
		if ($search['status'] == -1) unset($search['status']);
		
		list($total, $users) = Gou_Service_FateLog::getList($page, $perpage, $search);
		
		$this->assign('fate_status', $this->fate_status);
		$this->assign('param', $param);		
		$this->assign('rules', $users);
		$url = $this->actions['listUrl'] .'/?'. http_build_query($param) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
	}
	
	/**
	 * 
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_FateLog::getFateLog(intval($id));
		$this->assign('info', $info);
	}
	
	/**
	 * 
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('order_id', 'id'));
		if (!$info['order_id']) $this->output(-1, '订单号不能为空.'); 
		$ret = Gou_Service_FateLog::updateFateLog(array('order_id'=>$info['order_id'], 'status'=>3), intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.'); 
	}
}
