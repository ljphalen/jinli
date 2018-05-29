<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 开服列表
 * Game_Service_GameOpen
 * @author wupeng
 */
class Game_Service_GameOpen {
    
    const STATUS_OPEN = 1;
    const STATUS_CLOSE = 0;
    
    public static $open_type = array(
        1 => "新服",
        2 => "合服",
    );
    public static $status = array(
        self::STATUS_CLOSE => "关闭",
        self::STATUS_OPEN => "开启",
    );
    
	public static function getGameOpenListBy($searchParams, $sortParams = array()) {
	    return self::getDao()->getsBy($searchParams, $sortParams);
	}
	
	public static function getGameOpenBy($searchParams, $sortParams = array()) {
	    return self::getDao()->getBy($searchParams, $sortParams);
	}
	
	public static function getPageList($page = 1, $limit = 10, $searchParams = array(), $sortParams = array()) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList($start, $limit, $searchParams, $sortParams);
		$total = self::getDao()->count($searchParams);
		return array($total, $ret);
	}
	
	public static function getGameOpen($id) {
		if (!$id) return null;		
		$keyParams = array('id' => $id);
		return self::getDao()->getBy($keyParams);
	}
	
	public static function updateGameOpen($data, $id) {
		if (!$id) return false;
		$dbData = self::checkField($data);
		if (!is_array($dbData)) return false;
		$keyParams = array('id' => $id);
		return self::getDao()->updateBy($dbData, $keyParams);
	}
	
	public static function updateGameOpenBy($data, $searchParams) {
		if (!$searchParams) return false;
		$dbData = self::checkField($data);
		if (!is_array($dbData)) return false;
		return self::getDao()->updateBy($dbData, $searchParams);
	}
	
	public static function deleteGameOpen($id) {
		if (!$id) return false;
		$keyParams = array('id' => $id);
		return self::getDao()->deleteBy($keyParams);
	}

	public static function deleteGameOpenList($keyList) {
		if (!is_array($keyList)) return false;
		return self::getDao()->deletes('id', $keyList);
	}

	public static function addGameOpen($data) {
		if (!is_array($data)) return false;
		$dbData = self::checkField($data);
		return self::getDao()->insert($dbData);
	}

	private static function checkField($data) {
		$dbData = array();
		if(isset($data['id'])) $dbData['id'] = $data['id'];
		if(isset($data['game_id'])) $dbData['game_id'] = $data['game_id'];
		if(isset($data['server_id'])) $dbData['server_id'] = $data['server_id'];
		if(isset($data['server_name'])) $dbData['server_name'] = $data['server_name'];
		if(isset($data['open_type'])) $dbData['open_type'] = $data['open_type'];
		if(isset($data['open_time'])) $dbData['open_time'] = $data['open_time'];
		if(isset($data['update_time'])) $dbData['update_time'] = $data['update_time'];
		if(isset($data['status'])) $dbData['status'] = $data['status'];
		if(isset($data['game_status'])) $dbData['game_status'] = $data['game_status'];
		return $dbData;
	}

	private static function getDao() {
		return Common::getDao("Game_Dao_GameOpen");
	}

}
