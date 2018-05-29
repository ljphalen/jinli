<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Activity_ShareController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Activity_Share/index',
		'editUrl' => '/Admin/Activity_Share/edit',
		'editPostUrl' => '/Admin/Activity_Share/edit_post',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$params = $this->getInput(array('phone', 'uid', 'start_time','end_time'));
		
		if ($params['phone']) $search['phone'] = $params['phone'];
		if ($params['uid']) $search['uid'] = $params['uid'];
		if ($params['start_time']) $search['start_time'] = strtotime($params['start_time'].' 00:00:00');
		if ($params['end_time']) $search['end_time'] = strtotime($params['end_time'].' 23:59:59');
		
		list($total, $result) = Activity_Service_Share::search($page, $this->perpage, $search);
		
		
		$this->assign('result', $result);
		$this->assign('search', $params);
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
		$info = Activity_Service_Share::get(intval($id));
		$this->assign('info', $info);
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'status'));
		$ret = Activity_Service_Share::update($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.');
	}
}
