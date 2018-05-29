<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Amigo_SupplierController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Amigo_Supplier/index',
		'addUrl' => '/Admin/Amigo_Supplier/add',
		'addPostUrl' => '/Admin/Amigo_Supplier/add_post',
		'editUrl' => '/Admin/Amigo_Supplier/edit',
		'editPostUrl' => '/Admin/Amigo_Supplier/edit_post',
		'deleteUrl' => '/Admin/Amigo_Supplier/delete',
	);
	
	// 0.Amigo商城使用，1.积分换购使用
	public $show_type = 0;
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		$show_type = intval($this->getInput('show_type'));
		$show_type = !empty($show_type) ? $show_type : $this->show_type;
	    list($total, $suppliers) = Gou_Service_Supplier::getList($page, $perpage, array('show_type'=>$show_type));
		$this->assign('suppliers', $suppliers);
		$url = $this->actions['listUrl'] .'/?show_type=' . $show_type . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign('show_type', $show_type);
	}
	
	/**
	 * 
	 * edit an Supplier
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_Supplier::getSupplier(intval($id));
		$this->assign('info', $info);
		$this->assign('show_type', $info['show_type']);
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		$show_type = intval($this->getInput('show_type'));
		$show_type = !empty($show_type) ? $show_type : $this->show_type;
		$this->assign('show_type', $show_type);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort','name', 'show_type'));
		$supplier = Gou_Service_Supplier::getBy(array('name'=>$info['name']));
		if($supplier) $this->output(-1, '操作失败,该供应商已存在');
		$info = $this->_cookData($info);
		$result = Gou_Service_Supplier::addSupplier($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort','name', 'show_type'));
		$info = $this->_cookData($info);
		$supplier = Gou_Service_Supplier::getBy(array('name'=>$info['name']));
		if($supplier && $supplier['id'] != $info['id']) $this->output(-1, '操作失败,该供应商已存在');
		$ret = Gou_Service_Supplier::updateSupplier($info, intval($info['id']));
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
		$info = Gou_Service_Supplier::getSupplier($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Gou_Service_Supplier::deleteSupplier($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

}
