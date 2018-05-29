<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Activity_SharelogController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Activity_Sharelog/index',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$param = $this->getInput(array('uid'));
		if ($param['uid']) $search['uid'] = $param['uid'];
		
		list($total, $result) = Activity_Service_ShareLog::getList($page, $this->perpage, $param);
		
		$this->assign('result', $result);
		$this->assign('param', $param);
		$this->assign('total', $total);
		$this->cookieParams();
		$url = $this->actions['listUrl'] .'/?'. http_build_query($param) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
}
