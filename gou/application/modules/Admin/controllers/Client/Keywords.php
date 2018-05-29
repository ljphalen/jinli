<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Client_KeywordsController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Client_Keywords/index',
		'addUrl' => '/Admin/Client_Keywords/add',
		'addPostUrl' => '/Admin/Client_Keywords/add_post',
		'editUrl' => '/Admin/Client_Keywords/edit',
		'editPostUrl' => '/Admin/Client_Keywords/edit_post',
		'deleteUrl' => '/Admin/Client_Keywords/delete',
	);
	
	public $perpage = 20;
	public $versionName = 'Keywords_Version';
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		
		$perpage = $this->perpage;
		list($total, $keywords) = Client_Service_Keywords::getList($page, $perpage);
		$this->assign('keywords', $keywords);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'] . '/?'));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Client_Service_Keywords::getKeywords(intval($id));
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
		$result = Client_Service_Keywords::addKeywords($info);
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
		$ret = Client_Service_Keywords::updateKeywords($info, intval($info['id']));
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
		$info = Client_Service_Keywords::getKeywords($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Client_Service_Keywords::deleteKeywords($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

}
