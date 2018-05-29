<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class AppController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/App/index',
		'addUrl' => '/Admin/App/add',
		'addPostUrl' => '/Admin/App/add_post',
		'editUrl' => '/Admin/App/edit',
		'editPostUrl' => '/Admin/App/edit_post',
		'deleteUrl' => '/Admin/App/delete',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		list($total, $apps) = User_Service_App::getList($page, $perpage);
		$this->assign('apps', $apps);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'].'/?'));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = User_Service_App::getApp(intval($id));
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
		$info = $this->getPost(array('name', 'appid'));
		$info = $this->_cookData($info);
		$app = User_Service_App::getAppByName($info['name']);
		if($app) $this->output(-1, '该名称已存在');
		$result = User_Service_App::addApp($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('name', 'id', 'appid'));
		$info = $this->_cookData($info);
		$app = User_Service_App::getAppByName($info['name']);
		if($app && $app['id']!= $info['id']) $this->output(-1, '该名称已存在');
		$ret = User_Service_App::updateApp($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['name']) $this->output(-1, '名称不能为空.');
		if(!$info['appid']) $this->output(-1, 'AppId不能为空.');
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = User_Service_App::getApp($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = User_Service_App::deleteApp(intval($id));
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
}
