<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * 话费充值渠道管理功能
 * @author huangsg
 *
 */
class Recharge_ChannelController extends Admin_BaseController {
	public $actions = array(
		'indexUrl' => '/Admin/Recharge_Channel/index',
		'addUrl' => '/Admin/Recharge_Channel/add',
		'addPostUrl' => '/Admin/Recharge_Channel/add_post',
		'editUrl' => '/Admin/Recharge_Channel/edit',
		'editPostUrl' => '/Admin/Recharge_Channel/edit_post',
		'deleteUrl' => '/Admin/Recharge_Channel/delete',
	);
	public $perpage = 20;
	
	public function indexAction(){
		$page = intval($this->getInput('page'));
		$page < 1 && $page = 1;
		$perpage = $this->perpage;
		list($total, $list) = Recharge_Service_Channel::getList($page, $this->perpage);
		$this->assign('list', $list);
		$url = $this->actions['indexUrl'] .'/?';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->cookieParams();
	}
	
	public function addAction(){}
	
	public function add_postAction(){
		$info = $this->getPost(array('title', 'status'));
		$info = $this->_checkData($info);
		$ret = Recharge_Service_Channel::addChannel($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function editAction(){
		$id = $this->getInput('id');
		$info = Recharge_Service_Channel::getChannel($id);
		$this->assign('info', $info);
	}
	
	public function edit_postAction(){
		$info = $this->getPost(array('id', 'title', 'status'));
		$info = $this->_checkData($info);
		$ret = Recharge_Service_Channel::updateChannel($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function deleteAction(){
		$id = $this->getInput('id');
		$info = Recharge_Service_Channel::getChannel($id);
		if (empty($info) && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		$ret = Recharge_Service_Channel::deleteChannel($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	private function _checkData($info){
		if (!$info['title']) $this->output(-1, '名称不能为空.');
		return $info;
	}
}