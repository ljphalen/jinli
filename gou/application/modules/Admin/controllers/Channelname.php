<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class ChannelnameController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Channelname/index',
		'addUrl' => '/Admin/Channelname/add',
		'addPostUrl' => '/Admin/Channelname/add_post',
		'editUrl' => '/Admin/Channelname/edit',
		'editPostUrl' => '/Admin/Channelname/edit_post',
		'deleteUrl' => '/Admin/Channelname/delete',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		list($total, $names) = Gou_Service_ChannelName::getList($page, $perpage);
		$this->assign('names', $names);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'] . '?'));
	}
	
	/**
	 * 
	 * edit an Channelname
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_ChannelName::get(intval($id));
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
		$ret = Gou_Service_ChannelName::getBy(array('name'=>$info['name']));
		if($ret) $this->output(-1, '该记录已存在');
		$result = Gou_Service_ChannelName::add($info);
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
		$ret = Gou_Service_ChannelName::getBy(array('name'=>$info['name']));
		if($ret && $ret['id'] != $info['id']) $this->output(-1, '该记录已存在');
		$ret = Gou_Service_ChannelName::update($info, intval($info['id']));
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
		$info = Gou_Service_ChannelName::get($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Gou_Service_ChannelName::delete($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

}
