<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class UserController extends Game_BaseController{
	
	public $actions = array(
			'loginPostUrl' => '/api/user/login',
			'logoutUrl' => '/api/user/logout',
			'registerUrl' => '/game/user/register',
			'refreshgvcUrl' => '/api/user/refreshgvc',
			'registerbygvcUrl' => '/api/user/registerbygvc',
			'getsmsUrl' => '/api/user/getsms',
			'regsmsUrl' => '/api/user/regsms',
			'regpassUrl' => '/api/user/regpass',
			
	);

	/**
	 * 账号登陆页面
	 */
	public function loginAction() {
		$request = $this->getInput(array('redirect_uri'));
		$redirectUrl = $request['redirect_uri'];
		$redirectUrl = $redirectUrl ? $redirectUrl : Common::getWebRoot();
		$online = Account_Service_User::checkOnlineByWeb();
		if($online['uuid']) $this->redirect($redirectUrl);
		$this->assign('redirectUrl', $redirectUrl);
		$forgetUrl = Game_Service_Config::getValue("game_account_forgetpwd");
		$this->assign('forgetUrl', $forgetUrl ? $forgetUrl : 'http://id.gionee.com/gsp/reset/start#/');
	}
	
	/**
	 * 注册页面
	 */
	public function registerAction(){
		$request = $this->getInput(array('redirect_uri'));
		$redirectUrl = $request['redirect_uri'];
		$redirectUrl = $redirectUrl ? $redirectUrl : Common::getWebRoot();
		$online = Account_Service_User::checkOnlineByWeb();
		if($online['uuid']) $this->redirect($redirectUrl);
		$this->assign('redirectUrl', $redirectUrl ? $redirectUrl : Common::getWebRoot());
	}
}
