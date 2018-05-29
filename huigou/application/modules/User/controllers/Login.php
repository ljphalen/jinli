<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * 登录
 * @author tiansh
 *
 */
class LoginController extends Front_BaseController {
	
	public $actions = array(
		'loginUrl' => '/user/login/login',
		'logoutUrl' => '/user/login/logout',
		'indexUrl' => '/user/login/index' 
	);
	
	public function tokenAction() {
		$params = $this->getPost(array('tel_no', 'token', 'user_id'));
		Common::log($params, 'user.log');
		$info = array(
				'username'=>$params['tel_no'],
				'out_uid'=>$params['user_id'],
				'token'=>$params['token']
		);
		/* $info = array(
				'out_uid' => '8BFA8F255A4A4B12ADF67CB7EF898BCC',
				'username' => '13809886150',
				'token'=>'a'
		); */
		if (!$info['out_uid'] || !$info['token'] || !$info['username']) $this->output(-1, '参数错误.');
		
		$user = Gc_Service_User::getUserByOutUid($info['out_uid']);
		if (!$user) {
			$ret = Gc_Service_User::addUser(array('username'=>$info['username'], 'password'=>'', 'gionee_token'=>$info['token'], 'out_uid'=>$info['out_uid']));
			if (!$ret) $this->output(-1, '注册用户信息失败.');
			$user = Gc_Service_User::getUserByOutUid($info['out_uid']);
		}
		$token = Common::encrypt($user['id'].'|'.$user['out_uid'].'|'.(Common::getTime()+300));
		$token = urlencode(urlencode($token));
		Common::log('GET A TOKEN : ' .$token, 'user.log');
		$this->output(0, '登录成功.', $token);
	}
	
	/**
	 * 
	 * 表单验证
	 */
	public function loginAction() {
		$post = $this->getPost(array('tel_no', 'token', 'user_id', 'secretKey'));
		
		$info = array(
			'username'=>$post['tel_no'],
			'out_uid'=>$post['user_id'],
			'token'=>$post['token']
		);
		
		$info = array(
				'out_uid' => '8BFA8F255A4A4B12ADF67CB7EF898BCC',
				'username' => '13809886150',
				'token'=>''
		); 
		if (!$info['out_uid'] || !$info['username']) $this->output(-1, '参数错误.');
		
		$user = Gc_Service_User::getUserByOutUid($info['out_uid']);
		if (!$user) {
			$ret = Gc_Service_User::addUser(array('username'=>$info['username'], 'password'=>'', 'silver_coin'=>1000,'gionee_token'=>$info['token'], 'out_uid'=>$info['out_uid']));
			if (!$ret) $this->output(-1, '注册用户信息失败.');
			$user = Gc_Service_User::getUserByOutUid($info['out_uid']);
		}
		
		$ret = Gc_Service_User::login($user['username'], $user['password']);
		if (Common::isError($ret)) $this->output($ret['code'], $ret['msg']);
		if (!$ret) $this->output(-1, '登录失败.');
		
		$this->output(0, '登录成功.', '');
	}
	
	/**
	 * 
	 */
	public function isLoginAction() {
		$ret = Gc_Service_User::isLogin();
		if(!$ret) $this->output(-1, '');
		$this->output(0, 'success', '');
	}
	
	/**
	 * 
	 * 退出登录
	 */
	public function logoutAction() {
		$ret = Gc_Service_User::logout();
		if(!$ret) $this->output(-1, '');
		$this->output(0, '退出成功.');
	}
}
