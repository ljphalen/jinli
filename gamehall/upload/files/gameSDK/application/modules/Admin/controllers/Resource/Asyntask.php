<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 异步任务
 * Resource_AsyntaskController
 * @author wupeng
 */
class Resource_AsyntaskController extends Admin_BaseController {
	
	public $actions = array(
		'taskListUrl' => '/Admin/Resource_Asyntask/taskList',
		'splListUrl' => '/Admin/Resource_Asyntask/splList',
	);
	
	public $perpage = 20;
	
	public function taskListAction() {
		$perpage = $this->perpage;
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$requestData = $this->getInput(array('id', 'task_id', 'task', 'method', 'args', 'start_time', 'end_time', 'use_time', 'result'));
		$searchParams = $this->getTaskSearchParams($requestData);
		$sortParams = array('id' => 'DESC');
		list($total, $list) = Resource_Service_AsynTask::getPageList($page, $this->perpage, $searchParams, $sortParams);

		$this->assign('search', $requestData);
		$this->assign('list', $list);
		$this->assign('total', $total);
		$url = $this->actions['taskListUrl'].'/?' . http_build_query($requestData) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	public function splListAction() {
	    $perpage = $this->perpage;
	    $page = intval($this->getInput('page'));
	    if ($page < 1) $page = 1;
	    $requestData = $this->getInput(array('id', 'task_id', 'subject', 'observer', 'args', 'result', 'use_time'));
	    $searchParams = $this->getSplSearchParams($requestData);
	    $sortParams = array('id' => 'DESC');
	
	    list($total, $list) = Resource_Service_AsynTaskSpl::getPageList($page, $this->perpage, $searchParams, $sortParams);
	
	    $this->assign('search', $requestData);
	    $this->assign('list', $list);
	    $this->assign('total', $total);
	    $url = $this->actions['splListUrl'].'/?' . http_build_query($requestData) . '&';
	    $this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}

	private function getTaskSearchParams($search) {
	    $searchParams = array();		
		if ($search['task_id']) $searchParams['task_id'] = array('like', $search['task_id']);
		if ($search['task']) $searchParams['task'] = array('like', $search['task']);
		if ($search['method']) $searchParams['method'] = array('like', $search['method']);
		if ($search['args']) $searchParams['args'] = array('like', $search['args']);

		if ($search['start_time']) $searchParams['start_time'][] = array('>=', strtotime($search['start_time']));
		if ($search['end_time']) $searchParams['start_time'][] = array('<=', strtotime($search['end_time']));
		
		if ($search['use_time']) $searchParams['use_time'] =  $search['use_time'];
		if ($search['result']) $searchParams['result'] =  $search['result'];
	    return $searchParams;
	}

	private function getSplSearchParams($search) {
	    $searchParams = array();
	    if ($search['id']) $searchParams['id'] =  $search['id'];
	    if ($search['task_id']) $searchParams['task_id'] = array('like', $search['task_id']);
	    if ($search['subject']) $searchParams['subject'] = array('like', $search['subject']);
	    if ($search['observer']) $searchParams['observer'] = array('like', $search['observer']);
	    if ($search['args']) $searchParams['args'] = array('like', $search['args']);
	    if ($search['result']) $searchParams['result'] =  $search['result'];
	    if ($search['use_time']) $searchParams['use_time'] =  $search['use_time'];
	    return $searchParams;
	}

}
