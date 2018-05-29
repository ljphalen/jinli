<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 后台登陆
 * @author rainkid
 */
class LoginController extends Common_BaseController {

	public $actions = array(
		'loginUrl'  => '/Admin/Login/login',
		'logoutUrl' => '/Admin/Login/logout',
		'indexUrl'  => '/Admin/Index/index'
	);

	/**
	 *
	 * Enter description here ...
	 */
	public function indexAction() {
		$this->assign('loginUrl', $this->actions['loginUrl']);
		$this->assign('logoutUrl', $this->actions['logoutUrl']);
		$this->assign('indexUrl', $this->actions['indexUrl']);
	}

	public function loginAction() {
		$login = $this->getRequest()->getPost();
		if (!isset($login['username']) || !isset($login['password'])) {
			return $this->showMsg(-1, '用户名或者密码不能为空.');
		}
		$ret = Admin_Service_User::login($login['username'], $login['password']);
		if (Common::isError($ret)) return $this->showMsg($ret['code'], $ret['msg']);
		if (!$ret) $this->showMsg(-1, '登录失败.');
		Common::redirect('/Admin/Index/index');
	}

	public function logoutAction() {
		Admin_Service_User::logout();
		Common::redirect("/Admin/Login/index");
	}
}
