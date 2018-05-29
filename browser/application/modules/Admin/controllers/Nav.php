<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class NavController extends Admin_BaseController{
	
	public $actions = array(
		'listUrl' => '/Admin/Nav/index',
		'addUrl' => '/Admin/Nav/add',
		'addPostUrl' => '/Admin/Nav/add_post',
		'editUrl' => '/Admin/Nav/edit',
		'editPostUrl' => '/Admin/Nav/edit_post',
		'deleteUrl' => '/Admin/Nav/delete',
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
		$param = $this->getInput(array('navtype', 'type'));
		if (isset($param['navtype'])) $search['navtype'] = $param['navtype'];
		if (isset($param['type'])) $search['type'] = $param['type'];
		$perpage = $this->perpage;
		
		list($total, $navs) = Browser_Service_Nav::getList($page, $perpage, $search);
		list(,$navtypes) = Browser_Service_NavType::getAllType();

		$this->assign('types', $this->types);
		
		$this->assign('navtypes', Common::resetKey($navtypes, 'id'));
		$this->assign('navs', $navs);
		$this->assign('page', $page);
		$this->assign('param', $param);
		$url = $this->actions['listUrl'] .'/?'. http_build_query($param) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction(){
		list(,$navtypes) = Browser_Service_NavType::getAllType();
		$this->assign('types', $this->types);
		$this->assign('navtypes', $navtypes);
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction(){
		$info = $this->getPost(array('name', 'type', 'sort', 'link', 'navtype'));
		if (!$info['name']) $this->output(-1, '名称不能为空.');
		$ret = Browser_Service_Nav::addNav($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */

	public function editAction(){
		$id = $this->getInput('id');
		$page = $this->getInput('page');
		
		list(,$navtypes) = Browser_Service_NavType::getAllType();
		$info = Browser_Service_Nav::getNav(intval($id)); 
		$this->assign('types', $this->types);
		$this->assign('navtypes', $navtypes);
		$this->assign('page', $page);
	    $this->assign('info', $info);	
	}

	/**
	 * 
	 * Enter description here ...
	 */

	public function edit_postAction(){
		$info = $this->getPost(array('id', 'name', 'sort', 'link', 'navtype', 'type'));
		if (!$info['name']) $this->output(-1, '机型不能为空.');
		$ret = Browser_Service_Nav::updateNav($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Browser_Service_Nav::getNav($id);
		if ($info && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		$ret = Browser_Service_Nav::deleteNav($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
}
