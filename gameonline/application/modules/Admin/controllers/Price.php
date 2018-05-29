<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class PriceController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Price/index',
		'addUrl' => '/Admin/Price/add',
		'addPostUrl' => '/Admin/Price/add_post',
		'editUrl' => '/Admin/Price/edit',
		'editPostUrl' => '/Admin/Price/edit_post',
		'deleteUrl' => '/Admin/Price/delete'
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		
		$perpage = $this->perpage;
		
		list($total, $result) = Game_Service_GamePrice::getList($page, $perpage);
		$this->assign('result', $result);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'].'?'));
	}
	
	/**
	 * 
	 * edit an subject
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Game_Service_GamePrice::getGamePrice(intval($id));
		$this->assign('info', $info);
	}
	
	/**
	 * get an subjct by subject_id
	 */
	public function getAction() {
		$id = $this->getInput('id');
		$info = Game_Service_GamePrice::getGamePrice(intval($id));
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
		$info = $this->getPost(array('sort', 'name', 'status', 'hits'));
		$info = $this->_cookData($info);
		$result = Game_Service_GamePrice::addGamePrice($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'name', 'status', 'hits'));
		$info = $this->_cookData($info);
		$ret = Game_Service_GamePrice::updateGamePrice($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['name']) $this->output(-1, '游戏资费名称不能为空.'); 
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Game_Service_GamePrice::getGamePrice($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Game_Service_GamePrice::deleteGamePrice($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	
	
}
