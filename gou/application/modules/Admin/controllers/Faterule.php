<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class FateruleController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/FateRule/index',
		'addUrl' => '/Admin/FateRule/add',
		'addPostUrl' => '/Admin/FateRule/add_post',
		'editUrl' => '/Admin/FateRule/edit',
		'editPostUrl' => '/Admin/FateRule/edit_post',
		'deleteUrl' => '/Admin/FateRule/delete',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		
		list($total, $rules) = Gou_Service_FateRule::getList($page, $perpage);
		
		$this->assign('rules', $rules);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'].'/?'));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_FateRule::getFateRule(intval($id));
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
		$info = $this->getPost(array('price', 'rate', 'times', 'scope'));
		$info = $this->_cookData($info);
		$result = Gou_Service_FateRule::addFateRule($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('price', 'rate', 'times', 'scope', 'id'));
		$info = $this->_cookData($info);
		$ret = Gou_Service_FateRule::updateFateRule($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['price']) $this->output(-1, '价格不能为空.'); 
		if(!$info['rate']) $this->output(-1, '概率不能为空.'); 
		if(!$info['times']) $this->output(-1, '次数不能为空.'); 
		if(!$info['scope']) $this->output(-1, '范围不能为空.'); 
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_FateRule::getFateRule($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Gou_Service_FateRule::deleteFateRule(intval($id));
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
}
