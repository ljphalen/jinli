<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class AccountController extends  Front_BaseController
{
	//获取服务器时间
	public function getTimeAction(){
		$result = Api_Gionee_Account::getServiceTime();
		$this->output('0','success',$result);
	}
	
	/**
	 *获取APPTOKEN
	 */
	public  function getAppTokenAction(){
		$token = Api_Gionee_Account::getAppToken();
	}
	/**
	 * 获取图形验证码
	 */
	public function getImageCodeAction(){
		$result =  Api_Gionee_Account::getImageCode();
		$result['vda'] = 'data:image/png;base64,'.$result['vda'];
		$this->output('0','',$result);
	}
	
	/**
	 * 图形注册验证
	 * @param  string  tn 手机号
	 * @param  string  vid 获取图形验证码时的返回值
	 *@param  string  vtx  传入的图形验证码的值
	 */
	public  function gvcRegisterAction(){
		$request = $this->getInput(array('tel','vid','vtx'));
		$tn = $request['tel'];
		$vid = $request['vid'];
		$vtx = $request['vtx'];
		if(!$tn || !$vid ||!$vtx){
			$this->output('-1','参数有错!');
		}
		$res = Api_Gionee_Account::registerByGvc($tn, $vid, $vtx);
		if($res['s']){
			$this->output('0','图形验证成功!',$res);
		}else{
			$this->output('-1','图形验证失败!',$res);
		}
		//return $res;
	}
	
	/**
	 * 获取短信验证码
	 * @param string  mobile
	 * @param string  vts 图形验证后生成的Session
	 */
	public function getSmsVierifyCodeAction(){
		$params = $this->getInput(array('tel','vts'));
		if(!$params['tel'] || !preg_match('/^1[3|5|6|7|8|9]\d{9}$/',$params['tel'])) {
			$this->output('-1','手机号格式不正确!');
		}
		$sn = $params['vts']?$params['vts']:'';
		$res = Api_Gionee_Account::getSmsVerifyCode($params['tel'], $sn);
		if(!empty($res)){ //要求提供图形验证码
			$this->output('-1','其它错误',$res);
		}else{
			$this->output('0','获取成功',$res);
		}
	}
	
	/**
	 * 短信验证并注册
	 * @param string mobile 手机号码
	 * @param string vierfyCode 验证码
	 * @param string vts 图形验证码后生成的session (如果需要时)
	 */
	public function registerBySmsAction(){
		$params = $this->getInput(array('tel','code','vts'));
	 	if(!$params['tel'] || !$params['code']) $this->output('-1','参数有错');
		$params['vts'] = $params['vts']?$params['vts']:'';
		$res = Api_Gionee_Account::registerBySms($params['tel'], $params['code'],$params['vts']);
		if($res['s']){
			$this->output('0','注册成功',$res);
		}else{
			$this->output('-1','注册失败',$res);
		}
	}
	
	/**
	 *设置用户登陆密码
	 *@param string session 通过短信验证码后的会话
	 *@param string pwd 用户设置的密码
	 */
	public function setPasswordAction(){
		$params = $this->getInput(array('s','pwd'));
		$password= Api_Gionee_Account::encode_password($params['pwd']);
		$res = Api_Gionee_Account::setLoginPassword($password, $params['s']);
		if($res&& $res['u'] && $res['tn']){
			$exists = Gionee_Service_User::getUserByName($res['tn']);
			if(!$exists){
				$params = array(
						'username'=>$res['tn'],
						'out_uid'=>$res['u'],
						'mobile'=>$res['tn'],
						'come_from'=>3
				);
				Gionee_Service_User::addUser($params);
			}
			$this->output('0','设置成功!');
		}else{
			$this->output('-1','设置失败!');
		}
	}
}