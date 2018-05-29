<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class ChannelmoduleController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Channelmodule/index',
		'addUrl' => '/Admin/Channelmodule/add',
		'addPostUrl' => '/Admin/Channelmodule/add_post',
		'editUrl' => '/Admin/Channelmodule/edit',
		'editPostUrl' => '/Admin/Channelmodule/edit_post',
		'deleteUrl' => '/Admin/Channelmodule/delete',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		list($total, $modules) = Gou_Service_ChannelModule::getList($page, $perpage);
		$this->assign('modules', $modules);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'] . '?'));
	}
	
	/**
	 * 
	 * edit an Channelmodule
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_ChannelModule::get(intval($id));
		$this->assign('info', $info);
	}	
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('name'));
		$info = $this->_cookData($info);
		$ret = Gou_Service_ChannelModule::getBy(array('name'=>$info['name']));
		if($ret) $this->output(-1, '该记录已存在');
		$result = Gou_Service_ChannelModule::add($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'name'));
		$info = $this->_cookData($info);
		$ret = Gou_Service_ChannelModule::getBy(array('name'=>$info['name']));
		if($ret && $ret['id'] != $info['id']) $this->output(-1, '该记录已存在');
		$ret = Gou_Service_ChannelModule::update($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['name']) $this->output(-1, '名称不能为空.');
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_ChannelModule::get($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Gou_Service_ChannelModule::delete($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

}
