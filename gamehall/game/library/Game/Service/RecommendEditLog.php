<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 首页编辑记录
 * Game_Service_RecommendEditLog
 * @author wupeng
 */
class Game_Service_RecommendEditLog {
    
	public static function getRecommendEditLogByDayId($day_id) {
	    $searchParams = array('day_id' => $day_id);
	    $sortParams = array('create_time' => 'desc');
	    return self::getDao()->getBy($searchParams, $sortParams);
	}
	
	public static function getPageList($page = 1, $limit = 10, $searchParams = array(), $sortParams = array('create_time' => 'desc')) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList($start, $limit, $searchParams, $sortParams);
		$total = self::getDao()->count($searchParams);
		return array($total, $ret);
	}
	
	public static function getRecommendEditLog($id) {
		if (!$id) return null;		
		$keyParams = array('id' => $id);
		return self::getDao()->getBy($keyParams);
	}
	
	public static function updateRecommendEditLog($data, $id) {
		if (!$id) return false;
		$dbData = self::checkField($data);
		if (!is_array($dbData)) return false;
		$keyParams = array('id' => $id);
		return self::getDao()->updateBy($dbData, $keyParams);
	}
	
	public static function deleteRecommendEditLog($id) {
		if (!$id) return false;
		$keyParams = array('id' => $id);
		return self::getDao()->deleteBy($keyParams);
	}
	
	public static function deleteRecommendEditLogList($keyList) {
		if (!is_array($keyList)) return false;
		return self::getDao()->deletes('id', $keyList);
	}

	public static function deleteRecommendEditLogByDayId($day_id) {
	    $keyParams = array('day_id' => $day_id);
	    return self::getDao()->deleteBy($keyParams);
	}
	
	public static function addRecommendEditLog($data) {
		if (!is_array($data)) return false;
		$dbData = self::checkField($data);
		return self::getDao()->insert($dbData);
	}
	
	private static function checkField($data) {
		$dbData = array();
		if(isset($data['id'])) $dbData['id'] = $data['id'];
		if(isset($data['day_id'])) $dbData['day_id'] = $data['day_id'];
		if(isset($data['uid'])) $dbData['uid'] = $data['uid'];
		if(isset($data['create_time'])) $dbData['create_time'] = $data['create_time'];
		return $dbData;
	}

	private static function getDao() {
		return Common::getDao("Game_Dao_RecommendEditLog");
	}
	
}
