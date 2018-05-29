<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class KeywordslogController extends Admin_BaseController {
	
	public $actions = array(
		'indexUrl' =>'/admin/Keywordslog/index',
	);
	
	public $perpage = 20;
	
	/**
	 * 
	 * Get order list
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$params  = $this->getInput(array('keyword', 'start_time', 'end_time'));
		if ($page < 1) $page = 1;
		
		$search = array();
		if ($params['keyword']) $search['keyword'] = $params['keyword'];
		if ($params['start_time']) $search['start_time'] = strtotime($params['start_time']);
		if ($params['end_time']) $search['end_time'] = strtotime($params['end_time']);
		
		list($total, $logs) = Gou_Service_KeywordsLog::search($page, $this->perpage, $search);
		$this->assign('logs', $logs);
		$this->assign('total', $total);
		$this->assign('params', $params);
		//get pager
		$url = $this->actions['indexUrl'] .'/?'. http_build_query($params) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		$this->cookieParams();
	}
}
