<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class ScoreController extends Admin_BaseController {

	public $actions  = array(
		'indexUrl' => '/Admin/Score/index',
	);
	public $pageSize = 20;

	public function indexAction() {
		$page = $this->getInput('page');
		list($total, $dataList) = User_Service_ScoreLog::getList(max($page, 1), $this->pageSize, array(), array('id' => 'DESC'));
		foreach ($dataList as $k => $v) {
			$user                       = Gionee_Service_User::getUser($v['uid']);
			$dataList[$k]['user_phone'] = $user['username'];
		}
		$logTypes   = Common::getConfig('userConfig', 'actions');
		$actionType = Common::getConfig('userConfig', 'action_type');
		$this->assign('actionTypes', $actionType);
		$this->assign('logTypes', $logTypes);
		$this->assign('data', $dataList);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['indexUrl'] . "?"));
	}


}