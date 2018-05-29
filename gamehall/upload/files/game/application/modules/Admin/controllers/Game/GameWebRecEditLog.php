<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 编辑记录
 * Game_GameWebRecEditLogController
 * @author wupeng
 */
class Game_GameWebRecEditLogController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Game_GameWebRecEditLog/index',
		'addUrl' => '/Admin/Game_GameWebRecEditLog/add',
		'editUrl' => '/Admin/Game_GameWebRecEditLog/edit',
		'addPostUrl' => '/Admin/Game_GameWebRecEditLog/addPost',
		'editPostUrl' => '/Admin/Game_GameWebRecEditLog/editPost',
		'deleteUrl' => '/Admin/Game_GameWebRecEditLog/delete',
		'batchUpdateUrl'=>'/Admin/Game_GameWebRecEditLog/batchUpdate'
	);

	public $perpage = 20;

	public function indexAction() {
		$perpage = $this->perpage;
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$requestData = $this->getInput(array('id', 'day_id', 'uid', 'create_time'));
		$searchParams = $this->getSearchParams($requestData);
		$sortParams = array('id' => 'DESC');

		list($total, $list) = Game_Service_GameWebRecEditLog::getPageList($page, $this->perpage, $searchParams, $sortParams);

		$this->assign('search', $requestData);
		$this->assign('list', $list);
		$this->assign('total', $total);
		$url = $this->actions['listUrl'].'/?' . http_build_query($requestData) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}

	public function addAction() {
	    
	}

	public function addPostAction() {
		$requestData = $this->getInput(array('id', 'day_id', 'uid', 'create_time'));
		$postData = $this->checkRequestData($requestData);
		$result = Game_Service_GameWebRecEditLog::addGameWebRecEditLog($postData);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	public function editAction() {
		$keys = $this->getInput(array('id'));
		$info = Game_Service_GameWebRecEditLog::getGameWebRecEditLog($keys['id']);
		$this->assign('info', $info);
	}

	public function editPostAction() {
		$requestData = $this->getInput(array('id', 'day_id', 'uid', 'create_time'));
		
		$postData = $this->checkRequestData($requestData);
		$editInfo = Game_Service_GameWebRecEditLog::getGameWebRecEditLog($requestData['id']);
		if (!$editInfo) $this->output(-1, '编辑的内容不存在');
		$updateParams = array_diff_assoc($postData, $editInfo);
		if (count($updateParams) >0) {
			$ret = Game_Service_GameWebRecEditLog::updateGameWebRecEditLog($updateParams, $requestData['id']);
			if (!$ret) $this->output(-1, '操作失败');
		}
		$this->output(0, '操作成功.');
	}

	public function deleteAction() {
		$keys = $this->getInput(array('id'));
		$info = Game_Service_GameWebRecEditLog::getGameWebRecEditLog($keys['id']);
		if (!$info) $this->output(-1, '无法删除');
		$result = Game_Service_GameWebRecEditLog::deleteGameWebRecEditLog($keys['id']);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	public function batchUpdateAction() {
		$requestData = $this->getPost(array('action', 'keys'));
		if (!count($requestData['keys'])) $this->output(-1, '没有可操作的项.');
		$keys = $requestData['keys'];

		if($requestData['action'] =='delete'){
			$ret = Game_Service_GameWebRecEditLog::deleteGameWebRecEditLogList($keys);
		}
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}

	private function getSearchParams($search) {
	    $searchParams = array();		
		if ($search['id']) $searchParams['id'] =  $search['id'];
		if ($search['day_id']) $searchParams['day_id'] =  $search['day_id'];
		if ($search['uid']) $searchParams['uid'] =  $search['uid'];
		if ($search['create_time']) $searchParams['create_time'] =  $search['create_time'];
	    return $searchParams;
	}

	private function checkRequestData($requestData) {
		if(!isset($requestData['id'])) $this->output(-1, 'ID不能为空.');
		if(!isset($requestData['day_id'])) $this->output(-1, '天ID不能为空.');
		if(!isset($requestData['uid'])) $this->output(-1, '编辑人不能为空.');
		if(!isset($requestData['create_time'])) $this->output(-1, '编辑时间不能为空.');
		return $requestData;
	}

}
