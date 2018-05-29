<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class ExperienceController extends User_BaseController {

	public $pageSize = 10;

	public function  indexAction() {
		$login = Common_Service_User::checkLogin('/user/experience/index', true);
		Gionee_Service_Log::pvLog('user_experience_index');
		Gionee_Service_Log::uvLog('user_experience_index', $this->getSource());
		$userInfo = $login['keyMain'];
		Gionee_Service_User::getCurUserInfo(true);
		$list = User_Service_ExperienceInfo::getLevelPrivilegeDetailData($userInfo['experience_level']);
		$this->assign('list', $list);
		$this->assign('rules', $this->_configData());
		$this->assign('nextLevelPoints', $this->_getNextLevelPoints($userInfo['experience_level']));
		$this->assign('userInfo', $userInfo);
		$this->assign('gatherData', User_Service_Gather::getInfoByUid($userInfo['id']));
	}


	public function detailAction() {
		$login = Common_Service_User::checkLogin('/user/center/privilege', true);

		Gionee_Service_Log::pvLog('user_exprience_detail');
		Gionee_Service_Log::uvLog('user_exprience_detail', $this->getSource());
		$page     = $this->getInput('page');
		$page     = max($page, 1);
		$userInfo = $login['keyMain'];

		list($total, $dataList) = User_Service_ExperienceLog::getList($page, $this->pageSize, array('uid' => $userInfo['id']), array('id' => "DESC"));
		$more = $total > ($page * $this->pageSize) ? true : false;
		if ($page > 1) { //ajax请求
			$this->output('0', '', array('list' => $dataList, 'hasNext' => $more));
		} else { //直接渲染视图
			$this->assign('list', $dataList);
			$this->assign('more', $more);
		}
		$this->assign('user', $userInfo);
		$this->assign('scoreData', User_Service_Gather::getInfoByUid($userInfo['id']));
		$this->assign('types', Common::getConfig('userConfig', 'experience_activity_type'));
	}

	private function _configData() {
		$key    = "USER_EXPERIENCE_RULE:CONFIG";
		$config = Common::getCache()->get($key);
		if (empty($config)) {
			$keys = $keys = array(
				'user_experience_points_rule',
				'user_experience_privilege_info',
			);
			$ret  = Gionee_Service_Config::getsBy(array('3g_key' => array('IN', $keys)));
			foreach ($ret as $k => $v) {
				$config[$v['3g_key']] = $v['3g_value'];
			}
			Common::getCache()->set($key, $config, 10);
		}
		return $config;
	}

	private function _getNextLevelPoints($level) {
		$config    = Common::getConfig('userConfig', 'experience_level_data');
		$nextRange = explode('-', $config[$level + 1]['range']);
		return $nextRange[0];
	}
}