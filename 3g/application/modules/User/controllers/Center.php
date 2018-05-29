<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

//个人设置
class CenterController extends User_BaseController {

	public $pageSize = 10;

	public function indexAction() {
		$login = Common_Service_User::checkLogin('/user/center/index', true);        //检测登陆状态

		Gionee_Service_Log::pvLog('user_center_index');
		Gionee_Service_Log::uvLog('user_center_index', $this->getSource());
		$type          = $this->getInput('type');
		$userInfo      = $login['keyMain'];
		$userScoreInfo = User_Service_Gather::getInfoByUid($userInfo['id']);
		$params        = $cities = array();
		$params['id']  = array('IN', array($userInfo['province_id'], $userInfo['city_id']));
		$areaInfo      = Gionee_Service_Area::getCityNameByIds($params);
		$levelMsg      = Common_Service_User::getUserLevelInfo($userInfo['id'], $userInfo['user_level'], $userInfo['level_group']);//用户等级信息
		if ($userInfo['province_id'] > 0) {
			$cities = Common_Service_User::getAreaDataByParentId($userInfo['province_id']);
		}
		$hasUnread = Common_Service_User::unReadMsgNumber($userInfo['id']);//是否有未读站内信
		$this->assign('upScore', explode('-', $levelMsg['nextLevelMsg']['range'])[0]);
		$this->assign('userMsg', $userInfo);
		$this->assign('scoreMsg', $userScoreInfo);
		$this->assign('levelMsg', $levelMsg);
		$this->assign('provinces', Common_Service_User::getAreaDataByParentId());
		$this->assign('cities', $cities);
		$this->assign('type', $type);
		$this->assign('areaInfo', $areaInfo);
		$this->assign('unread', $hasUnread);
	}

	public function editPostAction() {
		$login = Common_Service_User::checkLogin('/user/center/index', true);

		$userInfo = $login['keyMain'];
		$postData = $this->getInput(array('nickname', 'qq', 'email', 'province_id', 'city_id', 'address', 'sex'));
		if (mb_strlen($postData['nickname']) > 30) {
			$this->output('-1', '用户昵称太长');
		}
		if (trim($postData['email'])) {
			if (!filter_var($postData['email'], FILTER_VALIDATE_EMAIL)) {
				$this->output('-1', '您填写的email格式不正确!');
			}
		}
		if (mb_strlen(trim($postData['address'])) > 80) {
			$this->output('-1', '地址长度不能超过80个字符!');
		}
		$ret = Gionee_Service_User::updateUser($postData, $userInfo['id']);
		if ($ret) {
			$key = "USER:INFO:KEY" . $userInfo['id'];
			Common::getCache()->delete($key);
			$this->output('0', '修改成功!', array('redirect' => Common::getCurHost() . '/user/center/index'));
		} else {
			$this->output('-1', '修改失败!');
		}
	}

	//站内信列表
	public function msgAction() {
		$login = Common_Service_User::checkLogin('/user/center/msg', true);

		Gionee_Service_Log::pvLog('user_innermsg');
		Gionee_Service_Log::uvLog('user_innermsg', $this->getSource());
		$page     = $this->getInput('page');
		$page     = max($page, 1);
		$userInfo = $login['keyMain'];
		$uid      = $userInfo['id'];
		$key      = "USER:INNERMSG:" . $uid . ":PAGE:" . $page;
		$data     = Common::getCache()->get($key);
		$data     = array();
		if (empty($data)) {
			list($total, $data) = User_Service_InnerMsg::getList($page, $this->pageSize, array('uid' => $uid), array('id' => 'DESC'));
			foreach ($data as $k => $v) {
				$data[$k]['add_time'] = date('m月d日 H:i:s', $v['add_time']);
			}
			Common::getCache()->set($key, $data, 60);
		}
		//把所有未读信件标为已读
		User_Service_InnerMsg::updateBy(array('is_read' => 1), array('uid' => $uid));
		Common_Service_User::unReadMsgNumber($uid,true);
		Common::getCache()->delete('USER:INMSG:' . $uid);
		$more = $total > ($page * $this->pageSize) ? true : false;
		if ($page > 1) { //ajax请求
			$this->output('0', '', array('list' => $data, 'hasNext' => $more));
		} else { //直接渲染视图
			$this->assign('list', $data);
			$this->assign('more', $more);
		}
	}

	/**
	 * 获取用户所有积分变化日志
	 */
	public function scoreAction() {
		$login    = Common_Service_User::checkLogin('/user/center/score', true);
		$userInfo = $login['keyMain'];
		$refurl   = $this->getInput('refurl');
		Gionee_Service_Log::pvLog('user_scorelog');
		Gionee_Service_Log::uvLog('user_scorelog', $this->getSource());
		list($count1, $mixData) = User_Service_ScoreLog::getList(1, $this->pageSize, array('uid' => $userInfo['id']), array('id' => 'DESC')); //所有记录
		$params2                   = $params3 = array();
		$params2['uid']            = $params3['uid'] = $userInfo['id'];
		$params2['affected_score'] = array('>=', 0);
		$params3['affected_score'] = array('<', 0);
		list($count2, $increData) = User_Service_ScoreLog::getList(1, $this->pageSize, $params2, array('id' => 'DESC')); //赚取积分的日志
		list($count3, $descData) = User_Service_ScoreLog::getList(1, $this->pageSize, $params3, array('id' => 'DESC')); //消费积分日志
		$data = array('mix'   => array('count' => $count1, 'data' => $mixData),
		              'incre' => array('count' => $count2, 'data' => $increData),
		              'desc'  => array('count' => $count3, 'data' => $descData)
		);
		//用户当前积分数
		$scoreInfo = User_Service_Gather::getInfoByUid($userInfo['id']);
		$this->assign('scoreInfo', $scoreInfo);
		$this->assign('data', $data);
		$this->assign('actions', Common::getConfig('userConfig', 'actions'));
		$this->assign('pageSize', $this->pageSize);
		if (empty($refurl)) {
			$refurl = Util_Cookie::get('USER_REQUEST_URI', true);
		}
		$this->assign('refurl', $refurl);
	}

	/**
	 * ajax 获取积分数据
	 */
	public function ajaxScoreAction() {
		$postData = $this->getInput(array('page', 'type'));
		$params   = array();
		if (in_array($postData['type'], array('1', '2'))) {
			if ($postData['type'] == '1') {
				$params['affected_score'] = array('>=', 0);
			} else {
				$params['affected_score'] = array('<', 0);
			}
		}
		$userInfo      = Gionee_Service_User::getCurUserInfo();
		$params['uid'] = $userInfo['id'];
		list($total, $dataList) = User_Service_ScoreLog::getList($postData['page'], $this->pageSize, $params, array('id' => 'DESC'));
		$actions = Common::getConfig('userConfig', 'actions');
		foreach ($dataList as $k => $v) {
			$dataList[$k]['score_type'] = $actions[$v['score_type']];
			$dataList[$k]['add_time']   = date('m月d日', $v['add_time']);
		}
		$hasNext = $total > $postData['page'] * $this->pageSize ? true : false; //是否还有分页数据
		$this->output('0', '', array('list' => $dataList, 'hasNext' => $hasNext));
	}

	/**
	 * 获取城市信息
	 */
	public function ajaxGetAreaDataListAction() {
		$province_id = $this->getInput('province_id');
		if (!$province_id) $this->output('-1', '请选择省份信息');
		$dataList = Common_Service_User::getAreaDataByParentId($province_id);
		$this->output(0, '', $dataList);
	}


	/**
	 * 用户经验等级信息
	 */
	public function privilegeAction() {
		$login    = Common_Service_User::checkLogin('/user/center/privilege', true);
		$userInfo = $login['keyMain'];
		$list     = User_Service_ExperienceInfo::getLevelPrivilegeDetailData($userInfo['experience_level']);
		$this->assign('list', $list);
		$this->assign('rules', $this->_configData());
		$this->assign('nextLevelPoints', $this->_getNextLevelPoints($userInfo['experience_level']));
		$this->assign('userInfo', $userInfo);
		$this->assign('gatherData', User_Service_Gather::getInfoByUid($userInfo['id']));
	}
}
