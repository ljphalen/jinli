<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class PushlogController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Pushlog/index',
		'logsUrl' => '/Admin/Pushlog/logs',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		
		list($total, $logs) = Theme_Service_Pushlog::getList($page, $perpage);
		$this->assign('logs', $logs);
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $this->actions['listUrl'] . '/?'));
	}
	
	public function logsAction() {
		$page = intval($this->getInput('page'));
	
		$param = $this->getInput(array('create_date', 'perpage'));
		$perpage = $param['perpage'] ? $param['perpage'] : $this->perpage;
		$search = array();
		if ($param['create_date']) $search['create_date'] = $param['create_date'];
	
		list($total, $logs) = Theme_Service_Pushlogs::getList($page, $perpage, $search);
		$this->assign('logs', $logs);
		$url = $this->actions['logsUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
	}
}
