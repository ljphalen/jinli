<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Sdk_GiftController extends Api_BaseController {
	
	public $perpage = 10;
	
	
	/**
	 * 取得游戏信息
	 */
	public function getGameInfoByAppIdAction(){
		$appId = trim($this->getInput('appId'));
		if(!$appId){
			$this->clientOutput(array());
		} 
		
		$this->saveGameInfoBehaviour();
		
		$data['success'] = true;
		$data['sign'] = 'GameSDK';
		$data['data'] = array();
		
		
		$params['api_key'] = $appId;
		$gameInfo = Resource_Service_Games::getBy($params);
		if(!$gameInfo){
			$this->clientOutput($data);
		}
		$data['data']['gameId'] = $gameInfo['id'];
		$data['data']['package'] = $gameInfo['package'];
		
		$this->clientOutput($data);
	}
	/**
	 * 取得游戏信息
	 */
	public function getGameInfoByPackageAction(){
		$package= trim($this->getInput('package'));
		if(!$package){
			$this->clientOutput(array());
		}
	
		$this->saveGameInfoBehaviour();
		
		$data['success'] = true;
		$data['sign'] = 'GameSDK';
		$data['data'] = array();
	
	
		$params['package'] = $package;
		$gameInfo = Resource_Service_Games::getBy($params);
		if(!$gameInfo){
			$this->clientOutput($data);
		}
		$data['data']['gameId'] = $gameInfo['id'];
		$data['data']['appId'] = $gameInfo['api_key'];
	
		$this->clientOutput($data);
	}
	
	private function saveGameInfoBehaviour() {
	    $imei = trim($this->getInput('imei'));
	    if (!$imei) {
	        $sp = $this->getInput('sp');
	        $imei = Common::parseSdkSp($sp, 'imei');
	    }
	    if (!$imei) {
	        return;
	    }
	    $behaviour = new Client_Service_ClientBehaviour($imei, Client_Service_ClientBehaviour::CLIENT_SDK);
	    $behaviour->saveBehaviours(Client_Service_ClientBehaviour::ACTION_GET_GAME_INFO_IN_GIFT);
	}
	
	/**
	 * 单个游戏我的礼包列表
	 */
	public function myGiftListByGameIdAction(){
		$imei = $this->getInput('imei');
		$uuid = $this->getInput('puuid');
		$page = $this->getInput('page');
		$gameId = intval($this->getInput('gameId'));
		 
		if(!$imei || !$uuid) {
			$this->clientOutput(array());
		}
		if ($page < 1) $page = 1;
		 
		$online = Account_Service_User::checkOnline($uuid, $imei, 'uuid');
		if(!$online) $this->clientOutput(array('sign'=>'GameSDK', 'code'=>'0'));
		 
		$this->organizeMyGiftListByGameId ( $uuid, $page, $gameId, $online);

	}
	/**
	 * @param uuid
	 * @param page
	 * @param gameId
	 */
	 private function organizeMyGiftListByGameId($uuid, $page, $gameId, $online) {
	 	
	 	$userInfo = Account_Service_User::getUserInfo(array('uuid'=>$uuid));
		$params['uname'] = $userInfo['uname'];
		$params['game_id'] = $gameId;
		$params['log_type'] = Client_Service_Giftlog::GRAB_GIFT_LOG;
		list($total,$giftList)  = Client_Service_Giftlog::getList($page, 10, $params, array('create_time'=>'DESC'));
		$hasNext = (ceil((int) $total / $this->perpage) - $page) > 0 ? true : false;
		$temp = array();
		$data['success'] = true;
		$data['sign'] = 'GameSDK';
		foreach ($giftList as $value){
			$log = array();
    		$giftInfo = Client_Service_Gift::getGiftBaseInfo($value['gift_id']);
    		$gameInfo = Resource_Service_GameData::getGameAllInfo($value['game_id']);
			$temp['list'][]=array(
					'iconUrl'=>$gameInfo['img'],
					'giftName'=>html_entity_decode( $giftInfo['name'], ENT_QUOTES),
					'giftStartTime'  => intval($giftInfo['use_start_time']),
					'giftEndTime'=> intval($giftInfo['use_end_time']),
					'giftKey'=>$value['activation_code'],
					'giftId'=>$value['gift_id'],
					'gameId'=>$value['game_id'],			
			);
		}
		$data['data'] = $temp;
		$data['hasNext'] = $hasNext ;
		$data['curPage'] = intval($page) ;
		$data['totalCount'] = intval($total) ;
		$this->clientOutput($data);
	}

	

}
