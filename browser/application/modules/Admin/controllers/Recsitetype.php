<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class RecsitetypeController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Recsitetype/index',
		'addUrl' => '/Admin/Recsitetype/add',
		'addPostUrl' => '/Admin/Recsitetype/add_post',
		'editUrl' => '/Admin/Recsitetype/edit',
		'editPostUrl' => '/Admin/Recsitetype/edit_post',
		'deleteUrl' => '/Admin/Recsitetype/delete',
		'uploadUrl' => '/Admin/Recsitetype/upload',
		'uploadPostUrl' => '/Admin/Recsitetype/upload_post',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		
		list($total, $types) = Browser_Service_RecsiteType::getList($page, $perpage);
		$this->assign('types', $types);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'].'/?'));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Browser_Service_RecsiteType::getRecsitetype(intval($id));		
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
		$info = $this->getPost(array('name', 'sort'));
		$info = $this->_cookData($info);
		$result = Browser_Service_RecsiteType::addRecsitetype($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'name', 'sort'));
		$info = $this->_cookData($info);
		$ret = Browser_Service_RecsiteType::updateRecsitetype($info, intval($info['id']));
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
		$info = Browser_Service_RecsiteType::getRecsitetype($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Browser_Service_RecsiteType::deleteRecsitetype($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
}
