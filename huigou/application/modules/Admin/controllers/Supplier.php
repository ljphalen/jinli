<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class SupplierController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Supplier/index',
		'addUrl' => '/Admin/Supplier/add',
		'addPostUrl' => '/Admin/Supplier/add_post',
		'editUrl' => '/Admin/Supplier/edit',
		'editPostUrl' => '/Admin/Supplier/edit_post',
		'deleteUrl' => '/Admin/Supplier/delete',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
	    list(, $suppliers) = Gc_Service_Supplier::getList($page, $perpage);
		$this->assign('suppliers', $suppliers);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl']));
	}
	
	/**
	 * 
	 * edit an Supplier
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Gc_Service_Supplier::getSupplier(intval($id));
		$this->assign('info', $info);
	}
	
	/**
	 * get an subjct by Supplier_id
	 */
	public function getAction() {
		$id = $this->getInput('id');
		$info = Gc_Service_Supplier::getSupplier(intval($id));
		if(!$info) $this->output(-1, '操作失败.');
		$this->output(0, '', $info);
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
		$info = $this->_cookData($info);
		$result = Gc_Service_Supplier::addSupplier($info);
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
		$ret = Gc_Service_Supplier::updateSupplier($info, intval($info['id']));
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
		$info = Gc_Service_Supplier::getSupplier($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Gc_Service_Supplier::deleteSupplier($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

}
