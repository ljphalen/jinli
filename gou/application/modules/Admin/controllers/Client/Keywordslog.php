<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Client_KeywordslogController extends Admin_BaseController {
	
	public $actions = array(
		'indexUrl' =>'/admin/Client_Keywordslog/index',
	);
	
	public $perpage = 40;
	
	/**
	 * 
	 * Get order list
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$params  = $this->getInput(array('keyword', 'start_time', 'end_time'));
		if ($page < 1) $page = 1;
		
		if(!$params['start_time']) $params['start_time'] = date('Y-m-d', strtotime("-30 day"));
		if(!$params['end_time']) $params['end_time'] = date('Y-m-d', strtotime("today"));
		
		$search = array();
		if ($params['keyword']) $search['keyword'] = $params['keyword'];
		if ($params['start_time']) $search['start_time'] = $params['start_time'];
		if ($params['end_time']) $search['end_time'] = $params['end_time'];
		
		$logs = Client_Service_KeywordsLog::search($page, $this->perpage, $search);
		$this->assign('logs', $logs);
		$this->assign('params', $params);
		//get pager
		//$url = $this->actions['indexUrl'] .'/?'. http_build_query($params) . '&';
		//$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		$this->cookieParams();
	}
}
