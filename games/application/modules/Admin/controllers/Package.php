<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class PackageController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Package/index',
		'addUrl' => '/Admin/Package/add',
		'addPostUrl' => '/Admin/Package/add_post',
		'editUrl' => '/Admin/Package/edit',
		'editPostUrl' => '/Admin/Package/edit_post',
		'deleteUrl' => '/Admin/Package/delete',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		
		list($total, $result) = Games_Service_Package::getList($page, $perpage);
		
		$this->assign('result', $result);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'].'/?'));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Games_Service_Package::getPackage(intval($id));
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
		$package = html_entity_decode($this->getPost("package"));
		$packages = explode(',', $package);
		if (!count($packages)) $this->output(-1, '添加数据不能为空'); 
		$result = Games_Service_Package::mutiAddPackage($packages);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'package', 'status'));
		$info = $this->_cookData($info);
		$ret = Games_Service_Package::updatePackage($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['package']) $this->output(-1, '包名不能为空.'); 
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Games_Service_Package::getPackage($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Games_Service_Package::deletePackage($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
}
