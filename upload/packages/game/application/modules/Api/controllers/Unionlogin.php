<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class UnionloginController extends Api_BaseController {
	
	public function unionLoginAction(){
		$info = $this->unionLoginInputed();
		$this->checkUnionLoginParam($info);
		$this->sendAcoupon($info);
	}
	
	private function unionLoginInputed() {
		$info['uuid'] = $this->getInput('puuid');
		$info['apiKey'] = $this->getInput('apiKey');
		$info['loginTime'] = Common::getTime();
		$info['uname'] = $this->getInput('uname');
		$info['clientId'] = $this->getInput('clientId');
		/*$info['uuid'] = '5B2AA2C3FAF34AA297B922D902C991DB';
		$info['apiKey'] = 'C182B2152A414E8E8BA0CC8434AA2D33';
		$info['loginTime'] = Common::getTime();
		$info['uname'] = '18607178430';
		$info['clientId'] = '50955a3c6d16ccec5d97f3541012867c';*/
		return $info;
	}
	
	private function checkUnionLoginParam($info) {
		if (!$info['uuid']) {
			$this->localOutput(-1, 'not found puuid');
		}
		
		if (!$info['uname']) {
			$this->localOutput(-1, 'not found uname');
		}
		
		if (!$info['clientId']) {
			$this->localOutput(-1, 'not found clientId');
		}
		
		if (!$info['apiKey']) {
			$this->localOutput(-1, 'not found apiKey');
		}
		
		$gameInfo = Resource_Service_Games::getGameAllInfo(array('api_key'=>$info['apiKey']));
		//非联运游戏
		if (!$gameInfo) {
			$this->localOutput(-1, 'illegal game');
		}
		
		//验证客户上报的数据
		$rs = Common::verifyClientEncryptData($info['uuid'], $info['uname'], $info['clientId']);
		if(!$rs){
			$this->localOutput(-1, 'illegal request');
		}
		
		
	}

	private function sendAcoupon($unionLoginParam){
		//活动赠送
		$unionLoginParam['type'] = Util_Activity_Context::TASK_TYPE_ACTIVITY_TASK;
		$login_class = new Util_Activity_Context(new Util_Activity_UnionLogin($unionLoginParam));
		$sendResult = $login_class ->sendTictket();
		
		//福利任务赠送
		$unionLoginParam['type'] = Util_Activity_Context::TASK_TYPE_WEAK_TASK;
		$unionLoginParam['task_id'] = Util_Activity_Context::WEAL_TASK_UNIONLOGIN_TASK_ID;
		$login_class = new Util_Activity_Context(new Util_Activity_UnionLogin($unionLoginParam));
		$sendResult = $login_class ->sendTictket();
		$this->localOutput(0, '', $sendResult);
	
	}
	
   
}