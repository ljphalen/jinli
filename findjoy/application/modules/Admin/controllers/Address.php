<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class AddressController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Address/index',
		'addUrl' => '/Admin/Address/add',
		'addPostUrl' => '/Admin/Address/add_post',
		'editUrl' => '/Admin/Address/edit',
		'editPostUrl' => '/Admin/Address/edit_post',
		'deleteUrl' => '/Admin/Address/delete',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		list($total, $address) = Fj_Service_Address::getList($page, $perpage);
		$this->assign('address', $address);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'] . '?'));
	}
	
	/**
	 * 
	 * edit an Address
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Fj_Service_Address::get(intval($id));
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
		$info = $this->getPost(array('detail_address'));
		$info = $this->_cookData($info);
		$ret = Fj_Service_Address::getBy(array('detail_address'=>$info['detail_address']));
		if($ret) $this->output(-1, '该记录已存在');
		$result = Fj_Service_Address::add($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'detail_address'));
		$info = $this->_cookData($info);
		$ret = Fj_Service_Address::getBy(array('detail_address'=>$info['detail_address']));
		if($ret && $ret['id'] != $info['id']) $this->output(-1, '该记录已存在');
		$ret = Fj_Service_Address::update($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['detail_address']) $this->output(-1, '名称不能为空.');
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Fj_Service_Address::get($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Fj_Service_Address::delete($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

}
