<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * @author tiansh
 *
 */
class UserinfoController extends User_BaseController {

	public $actions = array(
		'indexUrl'         => '/user/userinfo/index',
		'editstep1Url'     => '/user/userinfo/editstep1',
		'editstep1PostUrl' => '/user/userinfo/editstep1_post',
		'editstep2Url'     => '/user/userinfo/editstep2',
		'editstep2PostUrl' => '/user/userinfo/editstep2_post',
	);

	public $sex = array(
		0 => '',
		1 => '女',
		2 => '男'
	);

	/**
	 * 基本资料
	 */
	public function indexAction() {
		$user_info = $this->userInfo;
		$this->assign('star', 0);
		$this->assign('sex', $this->sex);
		$this->assign('user_info', $user_info);
		$this->assign('pageTitle', '个人中心');
	}

	/**
	 * 编辑昵称
	 */
	public function editstep1Action() {
		$user_info = $this->userInfo;

		//机型
		list(, $models) = Gionee_Service_Models::getAllModels();

		//检测用户机型
		$model = $this->getModel();

		$this->assign('modles', $models);
		$this->assign('model', $model);
		$this->assign('user_info', $user_info);
		$this->assign('pageTitle', '基本资料修改');
	}

	/**
	 * 编辑详细资料
	 */
	public function editstep2Action() {
		//生日年份
		$year_list  = array();
		$month_list = array();
		$day_list   = array();
		for ($i = 1960; $i <= date('Y', Common::getTime()); $i++) {
			$year_list[] = $i;
		}
		for ($i = 1; $i <= 12; $i++) {
			if ($i < 10) $i = '0' . $i;
			$month_list[] = $i;
		}
		for ($i = 1; $i <= 31; $i++) {
			if ($i < 10) $i = '0' . $i;
			$day_list[] = $i;
		}

		$user_info = $this->userInfo;
		list($year, $month, $day) = explode('-', $user_info['birthday']);

		$this->assign('year_list', $year_list);
		$this->assign('month_list', $month_list);
		$this->assign('day_list', $day_list);
		$this->assign('year', $year);
		$this->assign('month', $month);
		$this->assign('day', $day);
		$this->assign('user_info', $this->userInfo);
		$this->assign('pageTitle', '详细资料修改');
	}

	/**
	 * 编辑昵称 post
	 */
	public function editstep1_postAction() {
		$user_info = $this->userInfo;
		$info      = $this->getPost(array('nickname', 'model'));
		if (!$info['nickname']) $this->output(-1, '请填写您的昵称.');
		if (!$info['model']) $this->output(-1, '请选择您的手机型号.');
		$result = Gionee_Service_User::updateUser($info, $user_info['id']);
		if (!$result) $this->output(-1, '修改失败,请重试.');

		$webroot = Common::getCurHost();
		$this->output(0, '资料修改成功.', array('type' => 'redirect', 'url' => $webroot . '/user/userinfo/index'));
	}


	/**
	 * 编辑基本资料 post
	 */
	public function editstep2_postAction() {
		$user_info        = $this->userInfo;
		$info             = $this->getPost(array('email', 'sex', 'qq'));
		$birthday         = $this->getPost(array('year', 'month', 'day'));
		$info['birthday'] = $birthday['year'] . '-' . $birthday['month'] . '-' . $birthday['day'];

		if (!$birthday['year'] || !$birthday['month'] || !$birthday['day']) $this->output(-1, '请选择您的出生年月日.');
		$result = Gionee_Service_User::updateUser($info, $user_info['id']);
		if (!$result) $this->output(-1, '修改失败,请重试.');

		$webroot = Common::getCurHost();
		$this->output(0, '资料修改成功.', array('type' => 'redirect', 'url' => $webroot . '/user/userinfo/index'));
	}

	/**
	 *
	 * 取机型
	 */
	private function getModel() {
		$ua = Util_Http::getServer('HTTP_USER_AGENT');
		preg_match('/GiONEE-(.*)\//iU', $ua, $matches);
		if ($matches) return $matches[1];
		preg_match('/GiONEE (.*)\ /iU', $ua, $matches);
		if ($matches) return $matches[1];
		preg_match('/zh-cn; (.*)\ /iU', $ua, $matches);
		if ($matches) return $matches[1];
		return null;
	}
}