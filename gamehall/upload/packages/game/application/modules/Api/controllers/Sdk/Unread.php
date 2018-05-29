<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Sdk_UnreadController extends Api_BaseController {
	
	const SIGN = 'GameSDK';
	const TIME_STAMP = 'timeStamp';
	const GAME_ID = 'gameId';
	const SP = 'sp';
	const GAME_PACKAGE = 'gamePackage';
	const UUID = 'uuid';
	const ID = 'id';
	
	const USE_WANKA_DATA = "1";
	const USE_GIONEE_DATA = "2";
	
	public function ticketAction() {
		$input = $this->getInput(array(self::ID, self::TIME_STAMP, self::UUID, self::SP));
		$this->checkInputParams($input);
		
		$unreadInfo = $this->getTicketUnreadInfo($input);
		$this->outputData($unreadInfo);
	}
	
	public function strategyAction(){
		$input = $this->getInput(array(self::ID, self::TIME_STAMP, self::GAME_PACKAGE, self::SP));
		$this->checkInputParams($input);
		
		$unreadInfo = $this->getStrategyUnreadInfo($input);
		$this->outputData($unreadInfo);
	}
	
	public function giftAction() {
		$input = $this->getInput(array(self::ID, self::TIME_STAMP, self::GAME_PACKAGE, self::SP));
		$this->checkInputParams($input);
		
		$unreadInfo = $this->getGiftUnreadInfo($input);
		$this->outputData($unreadInfo);
	}
	
	private function checkInputParams($params) {
		foreach ( $params as $key => $value ) {
			if (!isset($value)) {
				$this->localOutput(1, '参数错误', array(), self::SIGN);
			}
		}
	}
	
	private function getTicketUnreadInfo($input) {
		$oldId = $input[self::ID];
		$params = array();
		$params['id'] = array('>', $oldId);
		$params['uuid'] = $input[self::UUID];
		$params['status'] = 1;
		
		$latestId = $oldId;
		$latestTick = Client_Service_TicketTrade::getBy($params, array('id'=>'DESC'));
		if ($latestTick) {
			$latestId = $latestTick['id'];
		}
		
		$unreadInfo = array();
		$unreadInfo[Util_JsonKey::ID] = $latestId;
		$unreadInfo[Util_JsonKey::UNREAD_COUNT] = Client_Service_TicketTrade::getRowCount($params);
		return $unreadInfo;
	}
	
	private function getStrategyUnreadInfo($input) {
		$gameId = $this->getGameIdByPackageName($input[self::GAME_PACKAGE]);
		if (!$gameId) {
			$this->localOutput(1, '游戏不存在', array(), self::SIGN);
		}
		
		$unreadInfo = array();
		$strategySource = Game_Service_Config::getValue('strategy_source');
		if ($strategySource == self::USE_WANKA_DATA) {
			$unreadInfo[Util_JsonKey::ID] = 0;
			$unreadInfo[Util_JsonKey::UNREAD_COUNT] = 0;
			return $unreadInfo;
		}
		
		$oldId = $input[self::ID];
		$params = array();
		$params['id'] = array('>', $oldId);
		$params['game_id'] = $gameId;
		$params['status'] = 1;
		$params['ntype'] = 4;
		
		$latestId = $oldId;
		$latestStrategy = Client_Service_News::getBy($params, array('id'=>'DESC'));
		if ($latestStrategy) {
			$latestId = $latestStrategy['id'];
		}
		
		$unreadInfo[Util_JsonKey::ID] = $latestId;
		$unreadInfo[Util_JsonKey::UNREAD_COUNT] = Client_Service_News::getCount($params);
		return $unreadInfo;
	}
	
	private function getGiftUnreadInfo($input) {
		$gameId = $this->getGameIdByPackageName($input[self::GAME_PACKAGE]);
		if (!$gameId) {
			$this->localOutput(1, '游戏不存在', array(), self::SIGN);
		}
		
		$oldId = $input[self::ID];
		$params = array();
		$params['id'] = array('>', $oldId);
		$params['status'] = 1 ;
		$params['game_status'] = 1;
		$params['game_id'] = $gameId;
		
		$latestId = $oldId;
		$latestGift = Client_Service_Gift::getBy($params, array('id'=>'DESC'));
		if ($latestGift) {
			$latestId = $latestGift['id'];
		}
		
		$unreadInfo = array();
		$unreadInfo[Util_JsonKey::ID] = $latestId;
		$unreadInfo[Util_JsonKey::UNREAD_COUNT] = Client_Service_Gift::getCount($params);
		return $unreadInfo;
	}
	
	private function getGameIdByPackageName($packageName) {
		if(!$packageName) {
			return;
		}
		
		$params = array();
		$params['status'] = 1;
		$params['packagecrc'] = crc32($packageName);
		$gameInfoList = Resource_Service_Games::getsBy($params);
		if (!$gameInfoList) {
			return;
		}
		
		foreach ($gameInfoList as $key => $value) {
			if ($value['package'] == $packageName) {
				return $value['id'];
			}
		}
		return;
	}
	
	private function outputData($unreadInfo) {
		$data = $unreadInfo;
		$data[Util_JsonKey::TIME_STAMP] = Common::getTime();
		$this->localOutput(0, '', $data, self::SIGN);
	}
}
