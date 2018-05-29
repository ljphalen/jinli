<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Fanli_WdlogController extends Admin_BaseController {
	
	public $actions = array(
		'indexUrl' => '/Admin/Fanli_Wdlog/index',
		'editUrl' => '/Admin/Fanli_Wdlog/edit',
		'editPostUrl' => '/Admin/Fanli_Wdlog/edit_post',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));		
		$param = $this->getInput(array('user_id', 'alipay', 'status', 'start_time','end_time'));
		
		if ($param['user_id']) $search['user_id'] = $param['user_id'];
		if ($param['alipay']) $search['alipay'] = $param['alipay'];
		if ($param['status']) $search['status'] = $param['status'] - 1;
		if ($param['start_time']) $search['start_time'] = strtotime($param['start_time'].' 00:00:00');
		if ($param['end_time']) $search['end_time'] = strtotime($param['end_time'].' 23:59:59');
		
		list($total, $logs) = Fanli_Service_withdrawLog::search($page, $this->perpage, $search);
		
		$this->assign('logs', $logs);
		$this->assign('param', $param);
		
		$this->cookieParams();
		$url = $this->actions['indexUrl'] .'/?'. http_build_query($param) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		$this->assign('total', $total);
	}
	
	
}
