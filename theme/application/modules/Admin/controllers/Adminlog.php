<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class AdminlogController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Adminlog/index',
	);
	
	public $perpage = 20;
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		
		$page = intval($this->getInput('page'));
		$param = $this->getInput(array('username', 'message'));
		$perpage = $this->perpage;
		
		if ($param['username'] != '') $search['username'] = $param['username'];
		if ($param['message'] != '') $search['message'] = $param['message'];
		list($total, $adminlog) = Admin_Service_AdminLog::getList($page, $perpage, $search);
		
		$this->assign('param', $param);
		$url = $this->actions['listUrl'] .'/?'. http_build_query($param) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));		
		$this->assign('adminlog', $adminlog);
	}
}
