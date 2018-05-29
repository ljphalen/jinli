<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class RedirectController extends Admin_BaseController{
	
	public $actions = array(
		'listUrl' => '/Admin/Redirect/index',
		'addUrl' => '/Admin/Redirect/add',
		'addPostUrl' => '/Admin/Redirect/add_post',
		'editUrl' => '/Admin/Redirect/edit',
		'editPostUrl' => '/Admin/Redirect/edit_post',
		'deleteUrl' => '/Admin/Redirect/delete',
	);
	
	public $perpage = 20;
	public $sorts = array( 
                        '开放市场', 
                        '联通订制机',
					);	

	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		
		list($total, $redirects) = Browser_Service_Redirect::getList($page, $perpage);
		
		$this->assign('redirects', $redirects);
		$this->assign('sorts', $this->sorts);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'].'/?'));
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction(){
		$this->assign('sorts', $this->sorts);
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction(){
		$info = $this->getPost(array('sort','name','url','redirect_name','redirect_url','md5_url'));
		if (!$info['name']) $this->output(-1, '预置名称不能为空.');
		if (!$info['url']) $this->output(-1, '预置地址不能为空.');
		if (!$info['redirect_name']) $this->output(-1, '跳转名称不能为空.');
		if (!$info['redirect_url']) $this->output(-1, '跳转地址不能为空.');
		$ret = Browser_Service_Redirect::addRedirect($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */

	public function editAction(){
		$id = $this->getInput('id');
		$info = Browser_Service_Redirect::getRedirect(intval($id)); 
		$this->assign('sorts', $this->sorts);
		$this->assign('info', $info);
	}

	/**
	 * 
	 * Enter description here ...
	 */

	public function edit_postAction(){
		$info = $this->getPost(array('sort','name','url','redirect_name', 'redirect_url','id','md5_url'));
		if (!$info['name']) $this->output(-1, '预置名称不能为空.');
		if (!$info['url']) $this->output(-1, '预置地址不能为空.');
		if (!$info['redirect_name']) $this->output(-1, '跳转名称不能为空.');
		if (!$info['redirect_url']) $this->output(-1, '跳转地址不能为空.');	
		$ret = Browser_Service_Redirect::updateRedirect($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Browser_Service_Redirect::getRedirect($id);
		if ($info && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		$ret = Browser_Service_Redirect::deleteRedirect($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
}
