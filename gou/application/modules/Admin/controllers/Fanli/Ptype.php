<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Fanli_PtypeController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Fanli_Ptype/index',
		'addUrl' => '/Admin/Fanli_Ptype/add',
		'addPostUrl' => '/Admin/Fanli_Ptype/add_post',
		'editUrl' => '/Admin/Fanli_Ptype/edit',
		'editPostUrl' => '/Admin/Fanli_Ptype/edit_post',
		'deleteUrl' => '/Admin/Fanli_Ptype/delete',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
	    list($total, $suppliers) = Fanli_Service_Ptype::getList($page, $perpage);
		$this->assign('suppliers', $suppliers);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'] . '?'));
	}
	
	/**
	 * 
	 * edit an Ptype
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Fanli_Service_Ptype::getType(intval($id));
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
		$info = $this->getPost(array('sort','name'));
		$supplier = Fanli_Service_Ptype::getBy(array('name'=>$info['name']));
		if($supplier) $this->output(-1, '操作失败,该分类已存在');
		$info = $this->_cookData($info);
		$result = Fanli_Service_Ptype::addType($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort','name'));
		$info = $this->_cookData($info);
		$supplier = Fanli_Service_Ptype::getBy(array('name'=>$info['name']));
		if($supplier && $supplier['id'] != $info['id']) $this->output(-1, '操作失败,该供分类已存在');
		$ret = Fanli_Service_Ptype::updateType($info, intval($info['id']));
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
		$info = Fanli_Service_Ptype::getType($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Fanli_Service_Ptype::deleteType($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

}
