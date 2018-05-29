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
		'loginUrl' => '/user/login/login',
		'logoutUrl' => '/user/login/logout',
		'indexUrl' => '/user/login/index' 
	);
	
	/**
	 * 
	 * 登录
	 */
	public function indexAction() {
		//已经登录
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		if (Gionee_Service_User::isLogin()){
			$this->redirect($webroot.'/user/index/signin');
		}
		$refer = $this->getInput('refer');
		$this->assign('refer',$refer);
		$this->assign('pageTitle','登录');
	}
	
	/**
	 * 
	 * 表单验证
	 */
	public function loginAction() {
		//已经登录
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		if (Gionee_Service_User::isLogin()){
			$this->redirect($webroot.'/index/index');
		}
		$login = $this->getRequest()->getPost();
		if (!isset($login['username']) || !isset($login['password'])) {
			$this->output(-1, '用户名或密码不能为空.');
		}
		$ret = Gionee_Service_User::login($login['username'], $login['password']);
		if (Common::isError($ret)) $this->output($ret['code'], $ret['msg']);
		if (!$ret) $this->output(-1, '用户名或密码错误.');
		
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		$url = $login['refer'] ? $login['refer'] : $webroot.'/user/index/signin';
		$this->output(0, '登录成功.', array('type'=>'redirect', 'url'=>$url));
	}
	
	/**
	 * 
	 * 退出登录
	 */
	public function logoutAction() {
		Gionee_Service_User::logout();
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		$this->redirect($webroot);
	}
}
