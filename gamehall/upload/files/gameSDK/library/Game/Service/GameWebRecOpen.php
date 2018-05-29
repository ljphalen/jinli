<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 开服信息
 * Game_Service_GameWebRecOpen
 * @author wupeng
 */
class Game_Service_GameWebRecOpen {
    
    const SHOW_NUMS = 5;
    
	public static function getGameWebRecOpenListBy($searchParams, $sortParams = array('sort' => 'desc', 'open_id' => 'desc')) {
	    return self::getDao()->getsBy($searchParams, $sortParams);
	}
	
	public static function getGameWebRecOpenBy($searchParams, $sortParams = array()) {
	    return self::getDao()->getBy($searchParams, $sortParams);
	}
	
	public static function getPageList($page = 1, $limit = 10, $searchParams = array(), $sortParams = array('sort' => 'desc', 'open_id' => 'desc')) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList($start, $limit, $searchParams, $sortParams);
		$total = self::getDao()->count($searchParams);
		return array($total, $ret);
	}
	
	public static function getGameWebRecOpen($id) {
		if (!$id) return null;		
		$keyParams = array('id' => $id);
		return self::getDao()->getBy($keyParams);
	}
	
	public static function updateGameWebRecOpen($data, $id) {
		if (!$id) return false;
		$dbData = self::checkField($data);
		if (!is_array($dbData)) return false;
		$keyParams = array('id' => $id);
		return self::getDao()->updateBy($dbData, $keyParams);
	}
	
	public static function updateGameWebRecOpenBy($data, $params) {
		$dbData = self::checkField($data);
		if (!is_array($dbData)) return false;
		return self::getDao()->updateBy($dbData, $params);
	}
	
	public static function deleteGameWebRecOpen($id) {
		if (!$id) return false;
		$keyParams = array('id' => $id);
		return self::getDao()->deleteBy($keyParams);
	}

	public static function deleteGameWebRecOpenList($keyList) {
		if (!is_array($keyList)) return false;
		return self::getDao()->deletes('id', $keyList);
	}

	public static function deleteGameWebRecOpenBy($params) {
	    if (! $params) return false;
	    return self::getDao()->deleteBy($params);
	}

	public static function addGameWebRecOpen($data) {
		if (!is_array($data)) return false;
		$dbData = self::checkField($data);
		return self::getDao()->insert($dbData);
	}

	public static function addMutiGameWebRecOpen($list) {
	    if (! $list) return false;
	    foreach ($list as $key => $data) {
	        $list[$key] = self::checkNewField($data);
	    }
	    return self::getDao()->mutiFieldInsert($list);
	}

	private static function checkField($data) {
		$dbData = array();
		if(isset($data['id'])) $dbData['id'] = $data['id'];
		if(isset($data['recommend_id'])) $dbData['recommend_id'] = $data['recommend_id'];
		if(isset($data['open_id'])) $dbData['open_id'] = $data['open_id'];
		if(isset($data['open_status'])) $dbData['open_status'] = $data['open_status'];
		if(isset($data['sort'])) $dbData['sort'] = $data['sort'];
		return $dbData;
	}

	public static function checkNewField($data) {
	    $record = array();
	    $record['recommend_id'] = isset($data['recommend_id']) ? $data['recommend_id'] : 0;
	    $record['open_id'] = isset($data['open_id']) ? $data['open_id'] : 0;
	    $record['open_status'] = isset($data['open_status']) ? $data['open_status'] : 0;
	    $record['sort'] = isset($data['sort']) ? $data['sort'] : 0;
		return $record;
	}

	private static function getDao() {
		return Common::getDao("Game_Dao_GameWebRecOpen");
	}

}
