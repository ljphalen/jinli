<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class KeywordsController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Keywords/index',
		'addUrl' => '/Admin/Keywords/add',
		'addPostUrl' => '/Admin/Keywords/add_post',
		'editUrl' => '/Admin/Keywords/edit',
		'editPostUrl' => '/Admin/Keywords/edit_post',
		'deleteUrl' => '/Admin/Keywords/delete',
	);
	public $versionName = 'Keywords_Version';
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		
		$perpage = $this->perpage;
		list($total, $keywords) = Gou_Service_Keywords::getList($page, $perpage);
		$this->assign('keywords', $keywords);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'] . '/?'));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_Keywords::getKeywords(intval($id));
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
		$info = $this->getPost(array('sort', 'keyword', 'status'));
		$info = $this->_cookData($info);
		$result = Gou_Service_Keywords::addKeywords($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'keyword', 'status'));
		$info = $this->_cookData($info);
		$ret = Gou_Service_Keywords::updateKeywords($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['keyword']) $this->output(-1, '关键字不能为空.'); 
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_Keywords::getKeywords($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Gou_Service_Keywords::deleteKeywords($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

}
