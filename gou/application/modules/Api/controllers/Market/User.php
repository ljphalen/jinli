<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh
 *
 */


class Market_UserController extends Api_BaseController {
	
	public $errors = array(
			'ERROR_PHONE_NUMBER_CAN_NOT_NULL'=> array(101, '手机号不能为空'),
			'ERROR_PHONE_NUMBER_FORMAT'=> array(110, '手机号码格式不正确'),
			'ERROR_PASSWORD_CAN_NOT_NULL'=> array(102, '密码不能为空'),
			'ERROR_PASSWORD_LENGTH'=> array(106, '密码长度要6-16位之间'),
			'ERROR_SIGN_CAN_NOT_NULL'=> array(103, '签名不能为空'),
			'ERROR_SIGN'=> array(106, '签名错误'),
			'ERROR_VERIFY_CAN_NOT_NULL'=> array(104, '验证码不能为空'),
			'ERROR_VERIFY_TOKEN_CAN_NOT_NULL'=> array(105, '验证码token不能为空'),
			'ERROR_LOGIN_TIMEOUT'=> array(501, '会话超时，请重新登录'),
			'ERROR_ILLEGAL_REQUEST'=> array(502, '非法的请求'),
			'ERROR_PHONE_NUMBER_HAS_BIND'=> array(123, '该用户已绑定手机号'),
			'ERROR_PHONE_NUMBER_HAS_REGISTERED'=> array(108, '该手机号码已注册'),
			'ERROR_BIND_FAIL'=> array(109, '绑定失败'),
			'ERROR_REGISTER_FAIL'=> array(111, '注册失败'),
			'ERROR_LOGIN_FAIL'=> array(303, '登录失败'),
			'ERROR_USER_UNREGISTER'=> array(305, '用户不存在'),
			'ERROR_PHONE_OR_PASSWORD'=> array(306, '用户名或密码错误'),			
			'ERROR_OLD_PASSWORD_NOT_NULL'=> array(602, '请输入原始密码!'),
			'ERROR_NEW_PASSWORD_NOT_NULL'=> array(603, '请输入新密码!'),
			'ERROR_CNF_NEW_PASSWORD_NOT_NULL'=> array(604, '请输入确认新密码!'),
			'ERROR_OLDPASS_NEWPASS_NOT_EQUAL'=> array(605, '两次输入的密码不一致!'),
			'ERROR_BIND_PHONE'=> array(600, '该用户还未绑定手机,请先绑定手机!'),
			'ERROR_MOD_PASSWORD_FAIL'=> array(608, '密码修改失败！'),
			
			'ERROR_NO_SMS_VERIFY'=> array(706, '请先获取验证码验证！'),
			'ERROR_RESET_PASSWORD_FAIL'=> array(608, '密码修改失败！'),
		); 
	
	/**
	 * reg
	 */
	public function registerAction() {
		$phone = $this->getPost('phone');
		$password = $this->getPost('password');
		$sign = $this->getPost('sign');
		$imei = $this->getPost('imei');
		$verify = $this->getPost('verify');
		$sms_token = $this->getPost('sms_token');
		$token = $this->getPost('token');
		
		if(!$imei) $imei = Common::getConfig('siteConfig', 'default_imei');
		
		if(!$phone) $this->showError('ERROR_PHONE_NUMBER_CAN_NOT_NULL');
		if(!Common::checkMobile($phone)) $this->showError('ERROR_PHONE_NUMBER_FORMAT');
		if(!$password) $this->showError('ERROR_PASSWORD_CAN_NOT_NULL');
		if (strlen($password) < 6 || strlen($password) > 16) $this->showError('ERROR_PASSWORD_LENGTH');
		if(!$sign) $this->showError('ERROR_SIGN_CAN_NOT_NULL');
		if(!$this->chkSign($sign, $phone)) $this->showError('ERROR_SIGN');
		if(!$verify) $this->showError('ERROR_VERIFY_CAN_NOT_NULL');
		if(!$sms_token) $this->showError('ERROR_VERIFY_TOKEN_CAN_NOT_NULL');
		
		//检测验证码
		list($code, $msg, $sms_id) = array_values(Fanli_Service_Sms::verify($sms_token, $verify,'register'));
		if ($code != 0) $this->clientOutput($code, $msg);
		
		if($token) {
			$user = Fanli_Service_User::getUserBy(array('token'=>$token));
			if(!$user || $user['token_expire_time'] < Common::getTime()) $this->showError('ERROR_LOGIN_TIMEOUT');
			if($user['last_login_imei'] != $imei) $this->showError('ERROR_ILLEGAL_REQUEST');
			if($user['phone'] && $user['password'])  $this->showError('ERROR_PHONE_NUMBER_HAS_BIND');
			
			$user_p = Fanli_Service_User::getUserBy(array('phone'=>$phone));
			if($user_p) $this->showError('ERROR_PHONE_NUMBER_HAS_REGISTERED');
			
			$data = array(
				'phone'=>$phone,
				'password'=>$password	
			);
			$result = Fanli_Service_User::updateUser($data, $user['id']);
			if (!$result) $this->showError('ERROR_BIND_FAIL');
			
			//修改验证状态
			Fanli_Service_Sms::updateSms(array('status'=>1), $sms_id);
			
			$this->output(0, 'success');
			
		} else {
			$user = Fanli_Service_User::getUserBy(array('phone'=>$phone));
			if($user) $this->showError('ERROR_PHONE_NUMBER_HAS_REGISTERED');
			
			$data = array(
					'phone'=>$phone,
					'password'=>$password,
					'register_imei'=>$imei,
			);
			$result = Fanli_Service_User::register($data);
			if (!$result) $this->showError('ERROR_REGISTER_FAIL');
			
			//修改验证状态
			Fanli_Service_Sms::updateSms(array('status'=>1), $sms_id);
			$this->output(0, 'success', array('token'=>$result));
		}
	}
	
	/**
	 * login
	 */
	public function loginAction() {
		$imei = $this->getPost('imei');
		$sign = $this->getPost('sign');
		$channel = $this->getPost('channel');
		if(!$imei) $imei = Common::getConfig('siteConfig', 'default_imei');
		if($channel && in_array($channel, array('qq', 'weibo', 'taobao'))) {
			$auth_username = $this->getPost('auth_username');
			$passport = Common::getConfig('passportConfig', $channel);
			
			$login_data = array(
				'auth_username'=>$auth_username,
				'imei'=>$imei,
				'passport_name'=>$channel
			);
			list($code, $msg, ) = array_values(Fanli_Service_User::login($login_data, $passport['passport_id']));
		} else {
			$phone = $this->getPost('phone');
			$password = $this->getPost('password');
			
			if(!$phone) $this->showError('ERROR_PHONE_NUMBER_CAN_NOT_NULL');
			if(!$password) $this->showError('ERROR_PASSWORD_CAN_NOT_NULL');
			
			$login_data = array(
				'phone'=>$phone,
				'password'=>$password,
				'imei'=>$imei	
			);
			list($code, $msg, ) = array_values(Fanli_Service_User::login($login_data));
		}
		if ($code != 0) $this->showError($msg);
		$this->clientOutput(0, 'success', array('token'=>$msg));		
	}
	
	/**
	 * user_info
	 */
	public function getUserAction() {
		$imei = $this->getPost('imei');
		$token = $this->getPost('token');
		if(!$imei) $imei = Common::getConfig('siteConfig', 'default_imei');
		if(!$token) $this->showError('ERROR_ILLEGAL_REQUEST');
		$user = Fanli_Service_User::getUserBy(array('token'=>$token));
		if(!$user || $user['token_expire_time'] < Common::getTime()) $this->showError('ERROR_LOGIN_TIMEOUT');
		if($user['last_login_imei'] != $imei) $this->showError('ERROR_ILLEGAL_REQUEST');
		
		$user_data = array(
			'username'=>$user['phone'] ? $user['phone'] : $user['username'],
			'money'=>$user['money'],
			'alipay'=>$user['alipay'],
			'is_bind_alipay'=>$user['alipay'] ? "true" : "false",
			'is_bind_phone'=>$user['phone'] ? "true" : "false",
		);
		
		$this->clientOutput(0, 'success', $user_data);
	}
	
	/**
	 * edit password
	 */
	public function editPasswordAction() {
		$imei = $this->getPost('imei');
		$token = $this->getPost('token');
		$password = $this->getPost('password');
		$new_password = $this->getPost('new_password');
		$new_password_cnf = $this->getPost('new_password_cnf');
		
		if(!$imei) $imei = Common::getConfig('siteConfig', 'default_imei');
		
		if(!$token) $this->showError('ERROR_ILLEGAL_REQUEST');
		if(!$password) $this->showError('ERROR_OLD_PASSWORD_NOT_NULL');
		if(!$new_password) $this->showError('ERROR_NEW_PASSWORD_NOT_NULL');
		if(!$new_password_cnf) $this->showError('ERROR_CNF_NEW_PASSWORD_NOT_NULL');
		if($new_password != $new_password_cnf)  $this->showError('ERROR_OLDPASS_NEWPASS_NOT_EQUAL');
		
		$user = Fanli_Service_User::getUserBy(array('token'=>$token));
		if(!$user['phone']) $this->showError('ERROR_BIND_PHONE');
		if($user['last_login_imei'] != $imei) $this->showError('ERROR_ILLEGAL_REQUEST');
		if(!$user || $user['token_expire_time'] < Common::getTime()) $this->showError('ERROR_LOGIN_TIMEOUT');
		
		$user_data = array(
				'password'=>$new_password,
		);
		$ret = Fanli_Service_User::updateUser($user_data, $user['id']);
		if(!$ret) $this->showError('ERROR_MOD_PASSWORD_FAIL');
	}
	
	
	public function resetPasswordAction() {
		$sms_token = $this->getPost('sms_token');
		$phone = $this->getPost('phone');
		$new_password = $this->getPost('new_password');
		$new_password_cnf = $this->getPost('new_password_cnf');
		
		if(!$sms_token) $this->showError('ERROR_ILLEGAL_REQUEST');
		if(!$new_password) $this->showError('ERROR_NEW_PASSWORD_NOT_NULL');
		if(!$new_password_cnf) $this->showError('ERROR_CNF_NEW_PASSWORD_NOT_NULL');
		if($new_password != $new_password_cnf)  $this->showError('ERROR_OLDPASS_NEWPASS_NOT_EQUAL');
		
		$sms = Fanli_Service_Sms::getBy(array('token'=>$sms_token), array('id'=>'DESC'));
		if(!$sms || $sms['status'] != 1 || $sms['operate'] != 'get_password') {
			$this->showError('ERROR_NO_SMS_VERIFY');
		}
		
		$user = Fanli_Service_User::getUserBy(array('phone'=>$phone));
		if(!$user) $this->showError('ERROR_USER_UNREGISTER');
		if($user['phone'] != $sms['phone']) $this->showError('ERROR_NO_SMS_VERIFY');
		$user_data = array(
				'password'=>$new_password,
		);
		$ret = Fanli_Service_User::updateUser($user_data, $user['id']);
		if(!$ret) $this->showError('ERROR_RESET_PASSWORD_FAIL');
		
		//修改验证状态
		Fanli_Service_Sms::updateSms(array('status'=>1), $sms['id']);
		$this->clientOutput(0, 'success');
	}
	
	/**
	 * 加解密 与 java通用
	 * @param string $string
	 * @param string $action
	 * @return string
	 */
	private function encrypt($string, $action = 'ENCODE') {
		$iv = '';
		$passport_config = Common::getConfig('passportConfig');
		if (!in_array($action, array('ENCODE', 'DECODE'))) $action = 'ENCODE';
		if ($action == 'ENCODE') { //加密
			$encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $passport_config['gionee']['secret'], $string, MCRYPT_MODE_CBC, $iv);
			return base64_encode($encrypted);
		} else { //解密
			$encryptedData = base64_decode($string);
			return mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $passport_config['gionee']['secret'], $encryptedData, MCRYPT_MODE_CBC, $iv);
		}
	}
	
	
	/**
	 * 验证sign
	 * @param unknown_type $string
	 */
	private function chkSign($sign, $string) {
		$passport_config = Common::getConfig('passportConfig');
		return $sign === md5($string.$passport_config['gionee']['secret']);
	}
}
