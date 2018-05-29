<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Activity_ShareqqController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Activity_ShareQq/index',
		'editUrl' => '/Admin/Activity_ShareQq/edit',
		'editPostUrl' => '/Admin/Activity_ShareQq/edit_post',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$params = $this->getInput(array('status'));
		if ($params['status']) $search['status'] = $params['status'];
		
		list($total, $result) = Activity_Service_ShareQq::getList($page, $this->perpage, $search);
		
		$this->assign('result', $result);
		$this->assign('total', $total);
		$this->assign('search', $search);
		$this->cookieParams();
		$url = $this->actions['listUrl'] .'/?'. http_build_query($params) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Activity_Service_ShareQq::get(intval($id));
		$this->assign('info', $info);
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'status'));
		$ret = Activity_Service_ShareQq::update($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.');
	}
}
