<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class LoginController extends Common_BaseController {
	
	public $actions = array(
		'loginUrl' => '/Admin/Login/login',
		'logoutUrl' => '/Admin/Login/logout',
		'indexUrl' => '/Admin/Index/index' 
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
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function loginAction() {
		$login = $this->getRequest()->getPost();
		if (!isset($login['username']) || !isset($login['password'])) {
			return $this->showMsg(-1, '用户名或者密码不能为空.');
		}
		$ret = Admin_Service_User::login($login['username'], $login['password']);
		if (Common::isError($ret)) return $this->showMsg($ret['code'], $ret['msg']);
		if (!$ret) $this->showMsg(-1, '登录失败.');

        $userInfo = Admin_Service_User::isLogin();
        $this->behavioralStat('login', $userInfo);

		$this->redirect('/Admin/Index/index');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function logoutAction() {
        $userInfo = Admin_Service_User::isLogin();
        $this->behavioralStat('logout', $userInfo);

		Admin_Service_User::logout();
		$this->redirect("/Admin/Login/index");
	}

    private function behavioralStat($action, $userInfo){
        $log = array(
            'uid' => $userInfo['uid'],
            'username' => $userInfo['username'],
            'groupid' => $userInfo['groupid'],
            'url' => Common::getCurPageURL(),
            'type' => $action,
            'time' => Common::getTime(),
            'data' => ''
        );

        Common::getMongo()->insert('behavioral', $log);
    }
}
