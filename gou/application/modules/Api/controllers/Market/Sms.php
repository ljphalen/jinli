<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh
 *
 */
class Market_SmsController extends Api_BaseController {
	
	public $errors = array(
			'ERROR_PHONE_NUMBER_CAN_NOT_NULL'=> array(101, '手机号不能为空'),
			'ERROR_SIGN_CAN_NOT_NULL'=> array(103, '签名不能为空'),
			'ERROR_SIGN'=> array(106, '签名错误'),
			'ERROR_GET_SMS_FOTEN'=> array(405, '获取验证码太频繁'),
			'ERROR_SEND_SMS_FAIL'=> array(409, '验证码发送失败'),
			'ERROR_SMS'=> array(411, '验证码错误'),
			'ERROR_USER_UNREGISTER'=> array(305, '用户不存在'),
			'ERROR_SMS_HAS_CHECKED'=> array(413, '该验证码已验证,请重新获取验证码！'),
			'ERROR_SMS_EXPIRED'=> array(412, '验证码已过期！'),
			
	);
	
	/**
	 * reg
	 */
	public function getSmsAction() {
		$phone = $this->getPost('phone');
		$sign = $this->getPost('sign');
		$imei = $this->getPost('imei');
		$operate = $this->getPost('operate');
		if(!$imei) $imei = Common::getConfig('siteConfig', 'default_imei');
		
		if(!$phone) $this->showError('ERROR_PHONE_NUMBER_CAN_NOT_NULL');
		if(!$sign) $this->showError('ERROR_SIGN_CAN_NOT_NULL');
		if(!$this->chkSign($sign, $phone)) $this->showError('ERROR_SIGN');
		
		$sms = Fanli_Service_Sms::getBy(array('phone'=>$phone), array('id'=>'DESC'));
		if($sms && $sms['expire_time'] > Common::getTime()) {
			$this->showError('ERROR_GET_SMS_FOTEN');
		}
		
		$rand = mt_rand('100000', '999999');
		$count = Fanli_Service_Sms::getCount();
		$token = md5(uniqid($count + 1 + $rand));
		$data = array(
			'phone'=>$phone,
			'verify'=>$rand,
			'imei'=>$imei,
			'token'=>$token,
			'operate'=>$operate,
		);
		
		$result = Fanli_Service_Sms::addSms($data);
		if (!$result) $this->showError('ERROR_SEND_SMS_FAIL');
		
		//发送
		Common::sms($phone, '你在购物大厅操作的验证码为：'.$rand.'请在10分钟内验证.');
		
		$cur_sms = Fanli_Service_Sms::getSms($result);
		$this->output(0, 'success', array('sms_token'=>$cur_sms['token']));
	}
	
	/**
	 * check sms
	 */
	public function chkSmsAction() {
		$phone = $this->getPost('phone');
		$sms_token = $this->getPost('sms_token');
		$verify = $this->getPost('verify');
	
		$sms = Fanli_Service_Sms::getBy(array('token'=>$sms_token), array('id'=>'DESC'));
		if(!$sms || $sms['verify'] != $verify || $sms['operate'] != 'get_password') {
			$this->showError('ERROR_SMS');
		}
		
		$user = Fanli_Service_User::getUserBy(array('phone'=>$phone));
		if(!$user) $this->showError('ERROR_USER_UNREGISTER');
		
		if($sms['status'] == 1) $this->showError('ERROR_SMS_HAS_CHECKED');
		if(Common::getTime() > $sms['expire_time']) $this->showError('ERROR_SMS_EXPIRED');
		
		//修改验证状态
		Fanli_Service_Sms::updateSms(array('status'=>1), $sms['id']);
		$this->output(0, 'success');
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
