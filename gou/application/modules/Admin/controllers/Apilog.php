<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class ApilogController extends Admin_BaseController {
	
	public $actions = array(
		'indexUrl' => '/Admin/Apilog/index',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));		
		$param = $this->getInput(array('mark', 'api_type'));
		
		if ($param['mark']) $search['mark'] = $param['mark'];
		if ($param['api_type']) $search['api_type'] = $param['api_type'];
		
		$perpage = $this->perpage;
		list($total, $logs) = Gou_Service_ApiLog::getList($page, $perpage, $search);
		
		$this->assign('logs', $logs);
		$this->assign('param', $search);
		
		$url = $this->actions['indexUrl'] .'/?'. http_build_query($param) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign('total', $total);
	}
	
}