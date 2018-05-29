<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh
 *
 */
class Market_WithdrawController extends Api_BaseController {
	
	public $errors = array(
			'ERROR_TRUENAME_CAN_NOT_NULL'=> array(806, '真实姓名不能为空!'),
			'ERROR_ALIPAY_CAN_NOT_NULL'=> array(807, '支付宝账号不能为空!'),
			'ERROR_PASSWORD'=> array(805, '密码不正确'),
			'ERROR_SETTING_FAIL'=> array(804, '设置失败'),
			'ERROR_ILLEGAL_REQUEST'=> array(502, '非法的请求'),
			'ERROR_MONEY_CAN_NOT_NULL'=> array(822, '提现金额不能为空!'),
			'ERROR_MONEY_MUST_BE_INT'=> array(824, '提现金额只能为整数!'),
			'ERROR_MONEY_LESS'=> array(828, '您的余额少于提现金额!'),
			'ERROR_PASSWORD_CAN_NOT_NULL'=> array(106, '密码不能为空'),
			'ERROR_PASSWORD'=> array(826, '密码不正确!'),
			'ERROR_WITHDRAW_FAIL'=> array(827, '提现失败'),
			'ERROR_LOGIN_TIMEOUT'=> array(501, '会话超时，请重新登录'),			
	);
	
	/**
	 * 提现设置
	 */
	public function settingAction() {
		$imei = $this->getPost('imei');
		$token = $this->getPost('token');
		$truename = $this->getPost('true_name');
		$alipay = $this->getPost('alipay');
		$password = $this->getPost('password');
		$verify = $this->getPost('verify');
		$sms_token = $this->getPost('sms_token');
		
		if(!$imei) $imei = Common::getConfig('siteConfig', 'default_imei');
		
		if(!$token) $this->showError('ERROR_ILLEGAL_REQUEST');
		if(!$truename) $this->showError('ERROR_TRUENAME_CAN_NOT_NULL');
		if(!$alipay) $this->showError('ERROR_ALIPAY_CAN_NOT_NULL');
		
		$user = Fanli_Service_User::getUserBy(array('token'=>$token));
		if(!$user || $user['token_expire_time'] < Common::getTime()) $this->showError('ERROR_LOGIN_TIMEOUT');
		if($user['last_login_imei'] != $imei) $this->showError('ERROR_ILLEGAL_REQUEST');
		
		//chk password
		$password = Fanli_Service_User::_password($password, $user['hash']);
		if($password != $user['password']) $this->showError('ERROR_PASSWORD');
		
		//检测验证码
		list($code, $msg, $sms_id) = array_values(Fanli_Service_Sms::verify($sms_token, $verify,'withdraw_setting'));
		if ($code != 0) $this->clientOutput($code, $msg);
		
		$user_data = array(
			'alipay'=>$alipay,
			'truename'=>$truename,
		);
		
		$result = Fanli_Service_User::updateUser($user_data, $user['id']);
		if (!$result) $this->output('ERROR_SETTING_FAIL');
		$this->output(0, 'success');
	}
	
	/**
	 * withdraw
	 */
	public function withdrawAction() {
		$imei = $this->getPost('imei');
		$token = $this->getPost('token');
		$withdraw_money = $this->getPost('withdraw_money');
		$password = $this->getPost('password');
		
		if(!$imei) $imei = Common::getConfig('siteConfig', 'default_imei');
		
		if(!$token) $this->showError('ERROR_ILLEGAL_REQUEST');
		if(!$withdraw_money) $this->showError('ERROR_MONEY_CAN_NOT_NULL');
		if(!is_numeric($withdraw_money) || strpos($withdraw_money,".")!==false || $withdraw_money < 0)  $this->showError('ERROR_MONEY_MUST_BE_INT');
		if(!$password) $this->showError('ERROR_PASSWORD_CAN_NOT_NULL');
		
		$user = Fanli_Service_User::getUserBy(array('token'=>$token));
		if(!$user || $user['token_expire_time'] < Common::getTime()) $this->showError('ERROR_MONEY_MUST_BE_INT');
		if($user['last_login_imei'] != $imei) $this->showError('ERROR_ILLEGAL_REQUEST');
		if($user['money'] < $withdraw_money.'.00') $this->showError('ERROR_MONEY_LESS');
		
		//chk password
		$password = Fanli_Service_User::_password($password, $user['hash']);
		if($password != $user['password']) $this->showError('ERROR_PASSWORD');
		
		$data = array(
				'alipay'=>$user['alipay'],
				'user_id'=>$user['id'],
				'money'=>$withdraw_money,
				'point'=>$withdraw_money*10,
				'create_time'=>Common::getTime()
		);
		
		$result = Fanli_Service_withdrawLog::addLog($data);
		if (!$result) $this->showError('ERROR_WITHDRAW_FAIL');
		$user = Fanli_Service_User::getUserBy(array('id'=>$user['id']));
		$this->output(0, 'success', array('money'=>$user['money']));
	}
	
	/**
	 * user_info
	 */
	public function logAction() {
		$imei = $this->getPost('imei');
		$token = $this->getPost('token');
		$page = $this->getPost('page');
		$perpage = $this->getPost('perpage');
		
		if(!$imei) $imei = Common::getConfig('siteConfig', 'default_imei');
		
		if ($page < 1) $page = 1;
		if(!$perpage) $perpage = 10;
		if(!$token) $this->showError('ERROR_ILLEGAL_REQUEST');
		$user = Fanli_Service_User::getUserBy(array('token'=>$token));
		if(!$user || $user['token_expire_time'] < Common::getTime()) $this->showError('ERROR_LOGIN_TIMEOUT');
		if($user['last_login_imei'] != $imei) $this->showError('ERROR_ILLEGAL_REQUEST');
		
		list($total, $list) = Fanli_Service_withdrawLog::getList($page, $perpage, array('user_id'=>$user['id']));
		
		$data = array();
		foreach ($list as $key=>$val){
			$data[$key]['alipay'] = $val['alipay'];
			$data[$key]['money'] = $val['money'];
			$data[$key]['point'] = $val['point'];
			$data[$key]['status'] = $val['status'] == 1 ? '成功' : '失败';
			$data[$key]['create_time'] = date('Y-m-d H:i:s', $val['create_time']);
		}
		
		$hasnext = (ceil((int) $total / $perpage) - ($page)) > 0 ? true : false;
		$this->clientOutput(0, 'success', array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page));
	}
	
	
	/**
	 * 提现 -- 发放集分宝
	 */
	public function testAction() {
		Fanli_Service_Alipay::getOuthToken();
	}
}
