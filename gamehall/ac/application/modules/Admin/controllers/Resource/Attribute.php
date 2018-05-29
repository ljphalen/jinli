<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Resource_AttributeController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Resource_Attribute/index',
		'addUrl' => '/Admin/Resource_Attribute/add',
		'addPostUrl' => '/Admin/Resource_Attribute/add_post',
		'editUrl' => '/Admin/Resource_Attribute/edit',
		'editPostUrl' => '/Admin/Resource_Attribute/edit_post',
		'deleteUrl' => '/Admin/Resource_Attribute/delete',
	);
	
	public $perpage = 20;
	public $at_ptypes = array (
			1 => '应用分类',
			2 => '运营商'
	);

	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$sid = intval($this->getInput('sid'));
		if(!$sid || $sid == 1){
			$type = 1;
		} else {
			$type = $sid;
		}
		$perpage = $this->perpage;
		list($total, $result) = Resource_Service_Attribute::getList($page, $perpage,array('at_type'=>$type));
		$this->assign('result', $result);
		$this->assign('sid', $type);
		$this->assign('at_ptypes', $this->at_ptypes);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'].'?'));
	}
	
	/**
	 * 
	 * edit an subject
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Resource_Service_Attribute::getAttribute(intval($id));
		$this->assign('sid', $info['at_type']);
		$this->assign('info', $info);
		$this->assign('at_ptypes', $this->at_ptypes);
	}
	
	/**
	 * get an subjct by subject_id
	 */
	public function getAction() {
		$id = $this->getInput('id');
		$info = Resource_Service_Attribute::getAttribute(intval($id));
		if(!$info) $this->output(-1, '操作失败.');
		$this->output(0, '', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		$this->assign('at_ptypes', $this->at_ptypes);
		$sid = intval($this->getInput('sid'));
		if(!$sid || $sid == 1){
			$sid = 1;
		}
		$this->assign('sid', $sid);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('title', 'at_type','status'));
		$info = $this->_cookData($info);
		$result = Resource_Service_Attribute::addAttribute($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$sid = intval($this->getInput('sid'));
		if(!$sid || $sid == 1){
			$sid = 1;
		}
		$this->assign('sid', $sid);
		$info = $this->getPost(array('id', 'title', 'at_type','status'));
		$info = $this->_cookData($info);
		$ret = Resource_Service_Attribute::updateReleation($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.');
	}

	/**
	 *
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$sid = $this->getInput('sid');
		$info = Resource_Service_Attribute::getAttribute($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Resource_Service_Attribute::deleteAttribute($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '名称不能为空.'); 
		if(!$info['at_type']) $this->output(-1, '分类不能为空.');
		return $info;
	}
}
