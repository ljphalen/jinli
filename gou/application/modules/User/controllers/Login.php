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
		'indexUrl' => '/user/login/index',
	);
	
	
	public function indexAction() {
		//已经登录
		$this->setLoginFromUrl();
		$webroot = Common::getWebRoot();
		if (Gou_Service_User::isLogin()){
			$this->redirect($webroot.'/user/account/index?t_bi='.$this->t_bi);
		}		
	}
	
	public function login_stepAction() {
		//已经登录
		$refer = $this->getInput('refer');
		$webroot = Common::getWebRoot();
		if (Gou_Service_User::isLogin()){
			$this->redirect($webroot.'/user/account/index?t_bi='.$this->t_bi);
		}
		$callback = $webroot.$this->actions['loginUrl'];
		$url = Api_Gionee_Oauth::requestToken($callback);
		if (!$refer) $refer = Util_Http::getServer('HTTP_REFERER');
		Util_Cookie::set('login_refer', $refer, true, Common::getTime()+7200);
		$loginFrom = $this->getInput('loginFrom');
		$this->redirect($url);
	}
	
	public function loginAction() {
		//已经登录
		$webroot = Common::getWebRoot();
		if (Gou_Service_User::isLogin()){
			$this->redirect($webroot.'/user/account/index?t_bi='.$this->t_bi);
		}
		
		$code = $this->getInput('code');
		if (!$code) $this->redirect($webroot.'/user/login/logout');
		
		$callback = $webroot.$this->actions['loginUrl'];
		$ret = Api_Gionee_Oauth::accessToken($code, $callback);
		
		if (!$ret) $this->output(-1, '登录失败.');
		$info = Api_Gionee_Oauth::verify($ret);
		if (!$info) $this->output(-1, '登录失败.');
		$mobile = str_replace('+86', '', $info['tn']);
		if (!$mobile || !$info['u']) $this->output(-1, '登录失败'); 
		
		$user = Gou_Service_User::getByOutUid($info['u']);
		
		if (!$user) {
			$ret = Gou_Service_User::addUser(array('username'=>$mobile, 'mobile'=>$mobile, 'out_uid'=>$info['u']));
			if (!$ret) $this->output(-1, '登录失败.');

			$user = Gou_Service_User::getByOutUid($info['u']);
		}
		if (!$user['out_uid']) {
			$ret = Gou_Service_User::updateUser(array('out_uid'=>$info['u']), $user['id']);
		}
		if (!$ret) $this->output(-1, '登录失败.');
		$ret = Gou_Service_User::login($user['out_uid'], $user['password']);
		if (!$ret) $this->output(-1, '登录失败.');
		
		if ($user['username'] != $mobile) {
			Gou_Service_User::updateUser(array('username'=>$mobile, 'mobile'=>$mobile), $user['id']);
		} 
		
		$url = $webroot.'/user/account/index?t_bi='.$this->t_bi;
		$refer = Util_Cookie::get('login_refer', true);
		if ($refer) $url = $refer;
		//if (!$user['isgain']) $url = $webroot.'/user/account/gain?t_bi='.$this->t_bi;
		$this->redirect($url);
		exit;
	}
	
	/**
	 * 
	 * 退出登录
	 */
	public function logoutAction() {
		Gou_Service_User::logout();
		$webroot = Common::getWebRoot();
		$url = $webroot.'/user/account/index?t_bi='.$this->t_bi;
		$gglf = Util_Cookie::get('gglf', true);
		if ($gglf) $url = $gglf;
		$this->redirect($url);
		exit;
	}
}
