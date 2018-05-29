<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class RomController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Rom/index',
		'addUrl' => '/Admin/Rom/add',
		'addPostUrl' => '/Admin/Rom/add_post',
		'editUrl' => '/Admin/Rom/edit',
		'editPostUrl' => '/Admin/Rom/edit_post',
		'deleteUrl' => '/Admin/Rom/delete',
	);
	
	public $perpage = 20;
	public $appCacheName = 'APPC_Front_Index_index';
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		
		list($total, $roms) = Theme_Service_Rom::getList($page, $perpage);
		$this->assign('roms', $roms);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'].'/?'));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Theme_Service_Rom::getRom(intval($id));		
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
		$series = Theme_Service_Rom::getRomByName($info['name']);
		if($series) $this->output(-1, $info['name'].'已存在');
		$result = Theme_Service_Rom::addRom($info);
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
		$series = Theme_Service_Rom::getRomByName($info['name']);
		if($series && $series['id'] !=  $info['id']) $this->output(-1, $info['name'].'已存在');
		$ret = Theme_Service_Rom::updateRom($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['name']) $this->output(-1, '版本号名称不能为空.');
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Theme_Service_Rom::getRom($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Theme_Service_Rom::deleteRom($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
}
