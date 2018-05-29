<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class UnionloginController extends Api_BaseController {
	const LOG_FILE = "unionlogin";
	
	public function unionLoginAction(){
		$input = $this->getUnionLoginInput();
		$this->checkUnionLoginParam($input);
		$this->sendAticket($input);
		$this->sendGiftActivationCode($input);
		$this->doSummerHolidayActivity($input);
		$this->saveUserLogin($input);
	}
	
	private function saveUserLogin($input) {
	    $params['uuid'] = $input['uuid'];
	    $params['apiKey'] = $input['apiKey'];
	    $params['loginTime'] = $input['loginTime'];
	    Async_Task::execute('Async_Task_Adapter_UserGame', 'login', $params);
	}
	
	/**
	 * 暑假活动
	 */
	private  function doSummerHolidayActivity($input){
		$msg = array('$input' => $input);
		Util_Log::info(__CLASS__, 'unionlogin', $msg);
		$uuid = $input['uuid'];
		$apiKey = $input['apiKey'];
		$gameId = $this->getGameByApikey ( $apiKey );
		
		$msg = array('gameId' => $gameId);
		Util_Log::info(__CLASS__, 'testsummer.log', $msg);
		if(!$gameId){
			return false;
		}
		Activity_Service_SummerHoliday::doActivity($uuid, $gameId, Activity_Service_SummerHoliday::LOGIN_TASK_TYPE);
		
		
	}

	
	private function getGameByApikey($apiKey) {
		$params['api_key'] = $apiKey;
		$params['status']  = Resource_Service_Games::STATE_ONLINE;
		$result = Resource_Service_Games::getBy($params);
		if($result){
			return $result['id'];
		}
		return false;
	}

	
	private function getUnionLoginInput() {
		$input['uuid'] = $this->getInput('puuid');
		$input['apiKey'] = $this->getInput('apiKey');
		$input['loginTime'] = Common::getTime();
		$input['uname'] = $this->getInput('uname');
		$input['clientId'] = $this->getInput('clientId');
		$input['imei'] = $this->getInput('imei');
		$input['serverId'] = $this->getInput('serverId');
		$input['jarVersion'] = $this->getInput('jarVersion');
		$debugMsg = array('msg' => '联运游戏登陆请求参数', 'event'=> $input);
		Util_Log::info(__CLASS__, self::LOG_FILE, $debugMsg);
		return $input;
	}
	
	private function checkUnionLoginParam($input) {
		if (!$input['uuid']) {
			$this->setOutPutMsg('not found puuid');
		}
		
		if (!$input['uname']) {
			$this->setOutPutMsg('not found uname');
		}
		
		if (!$input['clientId']) {
			$this->setOutPutMsg('not found clientId');
		}
		
		if (!$input['apiKey']) {
			$this->setOutPutMsg('not found apiKey');
		}
		
		$gameInfo = $this->getGameInfo($input);
		if (!$gameInfo) {
			$this->setOutPutMsg('illegal game');
		}
		
		if(!$input['imei']){
		    $this->setOutPutMsg('illegal request');
		}
		
		if($input['imei'] != Util_Imei::EMPTY_IMEI){
		     $ret = Util_Imei::isEncryptImeiValid($input['imei']);
		     if(!$ret){
		         $this->setOutPutMsg('illegal imei');
		     }
		}
		 
	    $rs = Common::verifyClientEncryptData($input['uuid'], $input['uname'], $input['clientId']);
		if(!$rs){
			$this->setOutPutMsg('illegal request');
		}
		
		$this->inspectorIsValidRequest ( $input );
	}
	
	private function inspectorIsValidRequest($input) {
		if(strnatcmp($input['jarVersion'], '2.2.2') >= 0 ){
			if(!$input['serverId']){
				$this->setOutPutMsg('not found serverId');
			}
				
			$imeiDecrypt = Util_Imei::decryptImei($input['imei']);
			$ret = $this->verifyEncryServerId($input, $imeiDecrypt);
			if(!$ret){
			    $debugMsg = array('msg' => 'illegal request', 'event'=> '');
			    Util_Log::info(__CLASS__, self::LOG_FILE, $debugMsg);
				$this->setOutPutMsg('illegal request');
			}
		}
	}
	
	private function setOutPutMsg($msg) {
	    if(Util_Environment::isOnline()){
	        $this->localOutput(-1, 'illegal request');
	    } else {
	        $debugMsg = array('msg' => $msg, 'event'=> '');
	        Util_Log::info(__CLASS__, self::LOG_FILE, $debugMsg);
	        $this->localOutput(-1, $msg);
	    }
	}
	
	private function getGameInfo($input) {
	    return  Resource_Service_Games::getBy(
	            array('api_key'=>$input['apiKey'],
	                  'status'  => Resource_Service_Games::STATE_ONLINE,
                      'cooperate' => Resource_Service_Games::COMBINE_GAME));
	}
	 /**
	 * @param info
	 * @param imeiDecrypt
	 * @param ret
	 */
	 private function verifyEncryServerId($input, $imeiDecrypt) {
		   $keyParam = array(
					'apiName' => strtoupper('unionLogin'),
					'imei' => $imeiDecrypt,
					'uname' => $input['uname'],
			);
			$ivParam = $input['uuid'];
			$serverId = $input['serverId'];
			$serverIdParam = array(
					'clientVersion' => $input['jarVersion'],
					'imei' => $imeiDecrypt,
					'uname' => $input['uname'],
			);
		 return Util_Inspector::verifyServerId($keyParam, $ivParam, $serverId, $serverIdParam);
	}


	private function sendAticket($input){
	    // 活动赠送
	    $eventObj = new Task_Event('login_game', $input);
	    Task_EventHandle::postEvent($eventObj);
		
		//福利任务赠送
		$input['type'] = Util_Activity_Context::TASK_TYPE_WEAK_TASK;
		$input['task_id'] = Util_Activity_Context::WEAL_TASK_UNIONLOGIN_TASK_ID;
		$login_class = new Util_Activity_Context(new Util_Activity_UnionLogin($input));
		$login_class ->sendTictket();
	}
	
	private function sendGiftActivationCode($input){
	    if(strnatcmp($input['jarVersion'], '2.2.2') < 0 ){
	        return;
	    }
	    $gameInfo = $this->getGameInfo($input);
	    $gameId = $gameInfo['id'];
	    $input['version'] = $input['jarVersion'];
	    $input['activity_type'] = Client_Service_GiftActivity::LOGIN_GAME_SEND_GIFT;
	    $debugMsg = array('msg' => "联运游戏登陆开始赠送 ", 'event'=> $input);
		Util_Log::info(__CLASS__, Client_Service_GiftActivity::LOG_FILE, $debugMsg);
	    Client_Service_GiftActivity::sendGiftActivationCode($input, $gameId);
	}
}