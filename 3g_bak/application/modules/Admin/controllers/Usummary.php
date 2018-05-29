<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class UsummaryController extends Admin_BaseController {

	public $pageSize = 20;
	public $actions  = array(
		'indexUrl'       => '/Admin/Usummary/index',
		'scoreUrl'       => '/Admin/Usummary/score',
		'scoreDetailUrl' => '/Admin/Usummary/scoreDetail',
		'tasksUrl'       => '/Admin/Usummary/tasks',
	);

	public function indexAction() {
		$postData = $this->getInput(array(
			'mobile',
			'min_scores',
			'max_scores',
			'export',
			'min_experience_points',
			'max_experience_points'
		));
		$page     = $this->getInput('page');
		$page     = max($page, 1);
		$p        = array();
		if (preg_match('/^1[3|5|7|8|9]\d{9}$/', $postData['mobile'])) {
			$user     = Gionee_Service_User::getUserBy(array('username' => $postData['mobile']));
			$p['uid'] = $user['id'];
		}

		if (intval($postData['min_scores']) || intval($postData['max_scores'])) {
			$temp = array();
			if ($postData['min_scores']) {
				array_push($temp, array('>=', $postData['min_scores']));
			}
			if ($postData['max_scores']) {
				array_push($temp, array('<=', $postData['max_scores']));
			}
			$p['remained_score'] = $temp;
		}

		if (intval($postData['min_experience_points']) || intval($postData['max_experience_points'])) {
			$arr = array();
			if ($postData['min_experience_points']) {
				array_push($arr, array('>=', $postData['min_experience_points']));
			}
			if ($postData['max_experience_points']) {
				array_push($arr, array('<=', $postData['max_experience_points']));
			}
			$p['experience_points'] = $arr;
		}

		$pageSize = $this->pageSize;
		if ($postData['export']) {
			$pageSize = User_Service_Gather::count($p);
		}
		list($total, $data) = User_Service_Gather::getList($page, $pageSize, $p, array('remained_score' => 'DESC'));
		foreach ($data as $k => $v) {
			$user                      = Gionee_Service_User::getUser($v['uid']);
			$data[$k]['username']      = $user['username'];
			$prizeInfo                 = User_Service_Rewards::getBy(array('uid' => $v['uid']));
			$data[$k]['continus_days'] = $prizeInfo['continus_days'];
		}

		if ($postData['export']) {
			$this->_export($data, '用户表');
			exit();
		}

		$totalScores = User_Service_Gather::getSumData($p);
		$this->assign('totalScore', $totalScores);
		$this->assign('data', $data);
		$this->assign('params', $postData);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['indexUrl'] . "?" . http_build_query($postData) . "&"));

	}

	private function _export($data, $name = '') {
	}

	/**
	 * 积分统计
	 */
	public function scoreAction() {
		$page                 = $this->getInput('page');
		$page                 = max(1, $page);
		$params               = array();
		$params['score_type'] = array('IN', array('101', '102', '103', '201', '202', '203', '204')); //赚取积分的动作类型
		$params['group_id']   = array('IN', array('1', '2'));
		list($total, $dataList) = User_Service_ScoreLog::getDayEarnScoresInfo($page, $this->pageSize, $params, array('date'), array('date' => 'DESC'));
		$scoresMsg = User_Service_Gather::getSumScoresInfo(); //总积分统计
		$this->assign('scoreMsg', $scoresMsg);
		$this->assign('data', $dataList);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['scoreUrl'] . '?'));
	}

	public function scoreDetailAction() {
		$postData = $this->getInput(array('page', 'date'));
		$page     = max(1, $postData['page']);
		if (!$postData['date']) exit('参数有错！');
		$params               = array();
		$params['score_type'] = array('IN', array('101', '102', '103', '201', '202', '203', '204')); //赚取积分的动作类型
		$params['group_id']   = array('IN', array('1', '2'));
		$params['date']       = $postData['date'];
		list($total, $list) = User_Service_ScoreLog::getList($page, $this->pageSize, $params, array('id' => 'DESC'));
		foreach ($list as $k => $v) {
			$user                 = Gionee_Service_User::getUser($v['uid']);
			$list[$k]['username'] = $user['username'];
		}
		$this->assign('list', $list);
		$this->assign('groupType', Common::getConfig('userConfig', 'action_type'));
		$this->assign('scoreTypes', Common::getConfig('userConfig', 'actions'));
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['scoreDetailUrl'] . "?date={$postData['date']}&"));
	}


	/**
	 * 任务统计
	 */

	public function tasksAction() {
		$page                 = $this->getInput('page');
		$page                 = max(1, $page);
		$params               = array();
		$params['score_type'] = array('IN', array('101', '102', '103', '201', '202'));
		$params['group_id']   = array("IN", array('1', '2'));
		list($total, $dataList) = User_Service_ScoreLog::getDoneTasksList($page, $this->pageSize, $params, array('date'), array('date' => 'DESC'));
		$allDoneMsg = User_Service_ScoreLog::getTotalDoneTasksInfo($params);
		$this->assign('all', $allDoneMsg);
		$this->assign('dataList', $dataList);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['tasksUrl'] . "?"));
	}

}