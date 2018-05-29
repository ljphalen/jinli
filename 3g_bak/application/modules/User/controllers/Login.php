<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 登录
 * @author tiansh
 *
 */
class LoginController extends User_BaseController {

	public $actions = array(
		'loginUrl'  => '/user/login/login',
		'logoutUrl' => '/user/login/logout',
		'indexUrl'  => '/user/login/index'
	);


	public function indexAction() {

	}

	public function loginAction() {
		//已经登录
		$webroot = Common::getCurHost();
		$code    = $this->getInput('code');
		if (empty($code)) {
			Common::redirect($webroot . '/user/login/logout');
		}

		$callback = $webroot . $this->actions['loginUrl'];
		$from     = $this->getInput('f');
		//4从amigo系统登陆
		//5浏览器领取升级积分入口
		if (in_array($from, array('1', '2', '3', '4', '5'))) {
			$callback .= "?f=$from";
		}

		$ret = Api_Gionee_Oauth::accessToken($code, $callback);
		if (!empty($ret['error'])) {
			$this->output(-1, '登录失败0.');
		}

		$info = Api_Gionee_Oauth::verify($ret);
		if (empty($info['u'])) {
			$this->output(-1, '登录失败1.');
		}

		$user = Gionee_Service_User::checkUser($info, $from);
		if (empty($user['id'])) {
			$this->output(-1, '登录失败2.');
		}

		$this->_fixUserScores($user['id']);
		Common::redirect($this->_referUrl());
	}

	//返回跳转地址
	private function _referUrl() {
		$refer = Util_Cookie::get('GIONEE_LOGIN_REFER', true);
		$url   = !empty($refer) ? $refer : Common::getCurHost() . '/user/index/index';
		return $url;
	}

	//用户统计表中是否有信息，没有就添加
	private function _fixUserScores($uid) {
		$userScoreInfo = User_Service_Gather::getInfoByUid($uid);
		//$userScoreInfo = User_Service_Gather::getBy(array('uid' => $id));
		if (empty($userScoreInfo)) {
			User_Service_Gather::add(array('uid' => $uid, 'created_time' => date('Y-m-d', time())));
		}
	}


	/**
	 *
	 * 退出登录
	 */
	public function logoutAction() {
		Gionee_Service_User::logout();
		$webroot = Common::getCurHost();
		Common::redirect($webroot);
	}


	//用户信息
	public function browserUserInfoAction() {
		$info = Gionee_Service_User::ckLogin();
		if (!empty($info['id'])) {
			$this->output(0, '成功', $info);
		}
		$this->output(-1, '用户信息不存在');
	}

	//登录
	public function browserSignIn() {
		$pData = $this->getPost(array('username', 'password'));
		if (!empty($pData['username'])) {
			$this->output(0, '登入成功');
		}
		$this->output(-1, '用户信息错误');
	}

	//注册
	public function browserSignUp() {
		$this->output(0, '注册成功');
	}

	//登出
	public function browserSignOut() {
		Gionee_Service_User::logout();
		$this->output(0, '登出成功');
	}

}
