<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class NavtypeController extends Admin_BaseController{
	
	public $actions = array(
		'listUrl' => '/Admin/Navtype/index',
		'addUrl' => '/Admin/Navtype/add',
		'addPostUrl' => '/Admin/Navtype/add_post',
		'editUrl' => '/Admin/Navtype/edit',
		'editPostUrl' => '/Admin/Navtype/edit_post',
		'deleteUrl' => '/Admin/Navtype/delete',
	);
	
	public $perpage = 20;
	public $types = array(
			'分类导航',
			'精品导航',
	);
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		
		list($total, $navs) = Browser_Service_NavType::getList($page, $perpage);
		
		$this->assign('navs', $navs);
		$this->assign('types', $this->types);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'].'/?'));
	}

	/**
	 * 
	 * Enter description here ...
	 */

	public function addAction(){
		$this->assign('types', $this->types);
	}

	/**
	 * 
	 * Enter description here ...
	 */

	public function add_postAction(){
		$info = $this->getPost(array('name', 'type', 'sort', 'descrip'));
		if (!$info['name']) $this->output(-1, '名称不能为空.');
		$ret = Browser_Service_NavType::addNavType($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */

	public function editAction(){
		$id = $this->getInput('id');
		$info = Browser_Service_NavType::getNavType(intval($id)); 
	        $this->assign('types', $this->types);	
	        $this->assign('info', $info);	
	}

	/**
	 * 
	 * Enter description here ...
	 */

	public function edit_postAction(){
		$info = $this->getPost(array('id', 'name', 'sort', 'type', 'descrip'));
		if (!$info['name']) $this->output(-1, '机型不能为空.');
		$ret = Browser_Service_NavType::updateNavType($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Browser_Service_NavType::getNavType($id);
		if ($info && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		$ret = Browser_Service_NavType::deleteNavType($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
}
