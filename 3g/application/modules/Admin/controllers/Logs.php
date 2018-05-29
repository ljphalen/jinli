<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class LogsController extends Admin_BaseController {

	public $pageSize = 20;
	public $actions  = array(
		'scoreUrl'    => '/Admin/Logs/score',
		'innerMsgUrl' => '/Admin/Logs/innerMsg',
		'apiLogUrl'   => '/Admin/Logs/rechargeLog',
		'sendMsgUrl'  => '/Admin/Logs/sendMsg',
		'addUrl'      => '/Admin/Logs/add',
	);

	/**
	 * 积分日志
	 */
	public function  scoreAction() {
		$page     = $this->getInput('page');
		$page     = max($page, 1);
		$postData = $this->getInput(array('group_id', 'score_type', 'start_time', 'end_time', 'tel', 'export'));
		$sDate    = $postData['start_time'];
		$eDate    = $postData['end_time'];
		!$sDate && $sDate = date('Y-m-d', strtotime("-30 day"));
		!$eDate && $eDate = date('Y-m-d');
		$params = array();
		if ($postData['group_id']) {
			$params['group_id'] = $postData['group_id'];
		}
		if ($postData['score_type']) {
			$params['score_type'] = $postData['score_type'];
		}
		$sDate = date("Y-m-d",strtotime($sDate));
		$params['add_time'] = array(
			array('>=', strtotime($sDate . ' 00:00:00')),
			array('<=', strtotime($eDate . ' 23:59:59'))
		);
		if ($postData['tel']) {
			$userMsg = Gionee_Service_User::getUserByName($postData['tel']);
			if ($userMsg) {
				$params['uid'] = $userMsg['id'];
			}
		}
		$urlParams = array(
			'group_id'   => $postData['group_id'],
			'score_type' => $postData['score_type'],
			'start_time'   => $postData['start_time'],
			'end_time'   => $postData['end_time'],
			'tel'        => $postData['tel']
		);
		
		$pageSize = $this->pageSize;
		if($postData['export']){
			$num = User_Service_ScoreLog::count($params);
			$pageSize = $num;
		}
		
		list($total, $dataList) = User_Service_ScoreLog::getList($page, $pageSize, $params, array('id' => 'DESC'));
		foreach ($dataList as $k => $v) {
			$user                       = Gionee_Service_User::getUser($v['uid']);
			$dataList[$k]['user_phone'] = $user['username'];
		}

		if ($postData['export']) {
			$this->_export('order', $sDate, $eDate, $dataList);
			exit();
		}
		$this->assign('groups', Common::getConfig('userConfig', 'action_type'));
		$this->assign('scoreTypes', $this->_getActionTypes($postData['group_id']));
		$this->assign('data', $dataList);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['indexUrl'] . "?" . http_build_query($urlParams) . "&"));
		$this->assign('postData', $postData);
		$this->assign('sdate', $sDate);
		$this->assign('edate', $eDate);
	}

	/**
	 * 用户账户添加积分
	 *
	 */
	public function addAction() {
	}

	public function ajaxAddScoreAction() {
		$postData = $this->getInput(array('username', 'score','reason'));
		if (!intval($postData['username']) || !intval($postData['score'])) $this->output('-1', '参数有错！');
		$userInfo = Gionee_Service_User::getUserByName($postData['username']);
		if (empty($userInfo)) $this->output('-1', '该用户不存在！');

		$userScoreInfo = User_Service_Gather::getInfoByUid($userInfo['id']);
		//$userScoreInfo = User_Service_Gather::getBy(array('uid'=>$userInfo['id']));
		if (empty($userScoreInfo)) $this->output('-1', '该用户还没有激活用户中心！');
		$scoreType = '206';
		if($postData['score']< 0){
			$scoreType = '312';
		}
		$userScoreInfo['total_score'] += $postData['score'];
		$userScoreInfo['remained_score'] += $postData['score'];
		$params = array(
			'uid'         => $userScoreInfo['uid'],
			'score'       => $postData['score'],
			'level_group' => $userInfo['level_group'],
			'user_level'  => $userInfo['user_level'],
			'group_id'    => 2,
			'score_type'  => $scoreType,
		);
		$ret    = Common_Service_User::scoreVarify($params);
		Admin_Service_Log::op($params);
		if ($ret) {
			Common_Service_User::sendInnerMsg(array('uid'=>$userInfo['id'], 'status'=>1,'classify'=>14,'score'=>$postData['score'],'reason'=>$postData['reason']), 'give_score_tpl');
			$this->output('0', '操作成功！');
		} else {
			$this->output('-1', '添加失败！');
		}
	}

	/**
	 * 站内信
	 */
	public function innerMsgAction() {
		$postData = $this->getInput(array('page', 'status', 'username', 'type'));
		$page     = max($postData['page'], 1);
		$where    = array();
		if (!empty($postData['status'])) {
			$where['status'] = $postData['status'];
		}

		if (!empty($postData['type'])) {
			$where['type'] = $postData['type'];
		}
		if (!empty($postData['username'])) {
			$userInfo     = Gionee_Service_User::getUserByName($postData['username']);
			$where['uid'] = $userInfo['id'];
		}
		list($total, $dataList) = User_Service_InnerMsg::getList($page, $this->pageSize, $where, array('id' => 'DESC'));
		foreach ($dataList as $k => $v) {
			$userMsg                  = Gionee_Service_User::getUser($v['uid']);
			$dataList[$k]['username'] = $userMsg['username'];
		}
		$this->assign('data', $dataList);
		$this->assign('params', $postData);
		$this->assign('statusList', array('-1' => '失败', '1' => '成功'));
		$this->assign('params', $postData);
		$this->assign('types', Common::getConfig('userConfig', 'innermsg_type_list'));
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['innerMsgUrl'] . "?status={$postData['status']}&phone={$postData['phone']}&"));
	}

	/**
	 * 手动添加发送站内信
	 */
	public function  sendMsgAction() {
		$postData = $this->getInput(array('username', 'content', 'message_type'));
		$params   = array();
		$p        = array(
			'type'     => $postData['message_type'],
			'content'  => $postData['content'],
			'status'   => 1,
			'add_time' => time(),
			'cat_id'   => 1,
			'is_read'  => 0
		);
		if ($postData['message_type']) {
			if ($postData['username']) { //针对单个用户
				$userInfo = Gionee_Service_User::getUserByName($postData['username']);
				$params   = array_merge($p, array('uid' => $userInfo[id]));
				$ret      = User_Service_InnerMsg::add($params);
			} else {//针对所有激活用户
				$activeUsers = User_Service_Earn::getAllActivateUserIds(array('uid'));
				foreach ($activeUsers as $v) {
					$params = array_merge($p, array('uid' => $v['uid']));
					$ret    = User_Service_InnerMsg::add($params);
				}

			}
			if ($ret) {
				$this->output('0', '信息发送成功！');
			} else {
				$this->output('-1', '信处发送失败！');
			}
		}
		$this->assign('msgType', Common::getConfig('userConfig', 'innermsg_type_list'));
	}

	/**
	 * ajax 动态得到积分变动的类型
	 * @return boolean
	 */
	public function ajaxGetScoreActionByGroupIdAction() {
		$group_id = $this->getInput('group_id');
		if (!$group_id) return false;
		$this->output('0', '', $this->_getActionTypes($group_id));
	}

	public function rechargeLogAction() {
		$postData = $this->getInput(array('page', 'order_sn', 'export', 'sdate', 'edate'));
		$page     = max($postData['page'], 1);
		$params   = array();
		if (intval($postData['order_sn'])) {
			$params['order_sn'] = $postData['order_sn'];
		}

		list($total, $dataList) = User_Service_Recharge::getList($page, $this->pageSize, $params, array('id' => 'DESC'));
		$config = Common::getConfig('userConfig');
		foreach ($dataList as $k => $v) {
			$dataList[$k]['recharge_type']   = $config['pay_status'][$v['status']];
			$dataList[$k]['add_time']        = date('Y-m-d H:i:s', $v['add_time']);
			$description                     = json_decode($v['desc'], true);
			$dataList[$k]['msg']             = $description['orderinfo'] ? $description['orderinfo']['err_msg'] : $description['err_msg'];
			$dataList[$k]['api_type']        = $config['ofpay_api_log'][$v['api_type']];
			$dataList[$k]['recharge_status'] = $config['recharge_status'][$v['status']];
			$dataList[$k]['content']         = $v['desc'];
		}
		if ($postData['export']) {
			$this->_export('rechargeLog', $postData['sdate'], $postData['edate'], $dataList);
			exit();
		}
		$this->assign('searchParams', $postData);
		$this->assign('data', $dataList);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['apiLogUrl'] . "?"));
	}

	private function _getActionTypes($group_id) {
		$actions = Common::getConfig('userConfig', 'actions');
		if (!$group_id) return $actions;
		$data = array();
		foreach ($actions as $k => $v) {
			if ($group_id == '2') {//产生积分
				if (in_array(substr($k, 0, 1), array('1', '2'))) {
					$data[$k] = $v;
				}
			} elseif ($group_id == '3') {
				if (in_array(substr($k, 0, 1), array('3', '4'))) {
					$data[$k] = $v;
				}
			}
		}
		return $data;
	}


	private function _export($type, $sdate, $edate, $dataList) {
		ini_set('memory_limit', '1024M');
		header('Content-Type: application/vnd.ms-excel;charset=GB2312');
		$filename = $sdate . '至' . $edate . '.csv';
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=' . iconv('utf8', 'gb2312', $filename));
		$out = fopen('php://output', 'w');
		fputcsv($out, array(chr(0xEF) . chr(0xBB) . chr(0xBF)));
		switch ($type) {
			case 'rechargeLog': {
				fputcsv($out, array('序号', '内部订单号', '外部订单号', 'API类型', '状态', '时间', '错误描述'));
				foreach ($dataList as $k => $v) {
					fputcsv($out, array(
						$v['id'],
						$v['order_sn'],
						$v['order_id'],
						$v['api_type'],
						$v['recharge_status'],
						$v['add_time'],
						$v['msg']
					));
				}
			}
				break;
			case 'order': {
				$groupId    = $dataList[0]['group_id'];
				$group      = Common::getConfig('userConfig', 'action_type');
				$scoreTypes = $this->_getActionTypes($groupId);
				fputcsv($out, array('序号', '用户手机号', '活动类别', '动作', '变动前积分数', '变动后积分数', '受影响积分数', '时间'));
				foreach ($dataList as $k => $v) {
					fputcsv($out, array(
						$v['id'],
						$v['user_phone'],
						$group[$v['group_id']]['val'],
						$scoreTypes[$v['score_type']],
						$v['before_score'],
						$v['after_score'],
						$v['affected_score'],
						date('Y-m-d H:i:s', $v['add_time'])
					));
				}
			}
				break;
		}
	}
}