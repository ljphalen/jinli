<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 用户玩游戏记录
 * User_Service_GameLog
 * @author wupeng
 */
class User_Service_GameLog {
	
	const F_UUID = 'uuid';
	const F_GAME_ID = 'game_id';
	const F_CONSUME_TIME = 'consume_time';
	const F_LOGIN_TIME = 'login_time';

	public static function getGameLogListBy($searchParams, $sortParams = array()) {
	    return self::getDao()->getsBy($searchParams, $sortParams);
	}

	public static function getTopListBy($searchParams, $limit = 10, $sortParams = array('consume_time' => 'desc', 'login_time' => 'desc')) {
		return self::getDao()->getList(0, $limit, $searchParams, $sortParams);
	}
	
	public static function getGameLogBy($searchParams, $sortParams = array()) {
	    return self::getDao()->getBy($searchParams, $sortParams);
	}

	public static function getPageList($page = 1, $limit = 10, $searchParams = array(), $sortParams = array()) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList($start, $limit, $searchParams, $sortParams);
		$total = self::getDao()->count($searchParams);
		return array($total, $ret);
	}

	public static function getGameLog($uuid, $game_id) {
		if (!$uuid || !$game_id) return null;		
		$keyParams = array();
		$keyParams[self::F_UUID] = $uuid;
		$keyParams[self::F_GAME_ID] = $game_id;
		return self::getDao()->getBy($keyParams);
	}

	public static function updateGameLog($data, $uuid, $game_id) {
		if (!$uuid || !$game_id) return false;
		$data = self::checkField($data);
		if (!is_array($data)) return false;
		$keyParams = array();
		$keyParams[self::F_UUID] = $uuid;
		$keyParams[self::F_GAME_ID] = $game_id;
		return self::getDao()->updateBy($data, $keyParams);
	}

	public static function updateGameLogBy($data, $searchParams) {
	    $data = self::checkField($data);
	    if (!is_array($data)) return false;
	    return self::getDao()->updateBy($data, $searchParams);
	}

	public static function deleteGameLog($uuid, $game_id) {
		if (!$uuid || !$game_id) return false;
		$keyParams = array();
		$keyParams[self::F_UUID] = $uuid;
		$keyParams[self::F_GAME_ID] = $game_id;
		return self::getDao()->deleteBy($keyParams);
	}

	public static function deleteGameLogList($keyList) {
		if (!is_array($keyList)) return false;
		foreach($keyList as $keys) {
			self::deleteGameLog($keys[self::F_UUID], $keys[self::F_GAME_ID]);
		}
		return true;
	}

	public static function addGameLog($data) {
		if (!is_array($data)) return false;
		$data = self::checkNewField($data);
		return self::getDao()->insert($data);
	}

	public static function addMutiGameLog($list) {
	    if (! $list) return false;
	    foreach ($list as $key => $data) {
	        $list[$key] = self::checkNewField($data);
	    }
	    return self::getDao()->mutiFieldInsert($list);
	}

	private static function checkNewField($data) {
	    $record = array();
		$record[self::F_UUID] = isset($data[self::F_UUID]) ? $data[self::F_UUID] : "";
		$record[self::F_GAME_ID] = isset($data[self::F_GAME_ID]) ? $data[self::F_GAME_ID] : "";
		$record[self::F_CONSUME_TIME] = isset($data[self::F_CONSUME_TIME]) ? $data[self::F_CONSUME_TIME] : 0;
		$record[self::F_LOGIN_TIME] = isset($data[self::F_LOGIN_TIME]) ? $data[self::F_LOGIN_TIME] : 0;
		return $record;
	}

	private static function checkField($data) {
	    $record = array();
		if(isset($data[self::F_UUID])) {
			$record[self::F_UUID] = $data[self::F_UUID];
		}
		if(isset($data[self::F_GAME_ID])) {
			$record[self::F_GAME_ID] = $data[self::F_GAME_ID];
		}
		if(isset($data[self::F_CONSUME_TIME])) {
			$record[self::F_CONSUME_TIME] = $data[self::F_CONSUME_TIME];
		}
		if(isset($data[self::F_LOGIN_TIME])) {
			$record[self::F_LOGIN_TIME] = $data[self::F_LOGIN_TIME];
		}
		return $record;
	}

	private static function getDao() {
		return Common::getDao("User_Dao_GameLog");
	}

}
