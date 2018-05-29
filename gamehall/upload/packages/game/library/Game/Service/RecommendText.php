<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 推荐Text公告
 * Game_Service_RecommendText
 * @author wupeng
 */
class Game_Service_RecommendText {

    const STATUS_CLOSE = 0;
    const STATUS_OPEN = 1;
    
	public static function getRecommendTextBy($searchParams, $sortParams = array('id' => 'asc')) {
	    return self::getDao()->getBy($searchParams, $sortParams);
	}

	public static function getRecommendTextsBy($searchParams, $sortParams = array('id' => 'asc')) {
	    return self::getDao()->getsBy($searchParams, $sortParams);
	}
	
	public static function getRecommendText($id) {
		if (!$id) return null;		
		$keyParams = array('id' => $id);
		return self::getDao()->getBy($keyParams);
	}
	
	public static function updateRecommendText($data, $id) {
		if (!$id) return false;
		$dbData = self::checkField($data);
		if (!is_array($dbData)) return false;
		$keyParams = array('id' => $id);
		return self::getDao()->updateBy($dbData, $keyParams);
	}

	public static function updateRecommendTextBy($data, $searchParams) {
	    $dbData = self::checkField($data);
	    if (!is_array($dbData)) return false;
	    return self::getDao()->updateBy($dbData, $searchParams);
	}
	
	public static function deleteRecommendText($id) {
		if (!$id) return false;
		$keyParams = array('id' => $id);
		return self::getDao()->deleteBy($keyParams);
	}
	
	public static function deleteRecommendTextList($keyList) {
		if (!is_array($keyList)) return false;
		return self::getDao()->deletes('id', $keyList);
	}

	public static function deleteRecommendTextByDayId($day_id) {
	    $keyParams = array('day_id' => $day_id);
	    return self::getDao()->deleteBy($keyParams);
	}
	
	public static function addRecommendText($data) {
		if (!is_array($data)) return false;
		$dbData = self::checkNewField($data);
		return self::getDao()->insert($dbData);
	}
	
	private static function checkNewField($data) {
	    $record = array();
	    $record['title'] = isset($data['title']) ? $data['title'] : "";
	    $record['link_type'] = isset($data['link_type']) ? $data['link_type'] : 0;
	    $record['link'] = isset($data['link']) ? $data['link'] : "";
	    $record['day_id'] = isset($data['day_id']) ? $data['day_id'] : 0;
	    $record['status'] = isset($data['status']) ? $data['status'] : self::STATUS_CLOSE;
	    $record['create_time'] = isset($data['create_time']) ? $data['create_time'] : time();
		return $record;
	}
	
	private static function checkField($data) {
		$dbData = array();
		if(isset($data['id'])) $dbData['id'] = $data['id'];
		if(isset($data['title'])) $dbData['title'] = $data['title'];
		if(isset($data['link_type'])) $dbData['link_type'] = $data['link_type'];
		if(isset($data['link'])) $dbData['link'] = $data['link'];
		if(isset($data['day_id'])) $dbData['day_id'] = $data['day_id'];
		if(isset($data['status'])) $dbData['status'] = $data['status'];
		if(isset($data['create_time'])) $dbData['create_time'] = $data['create_time'];
		return $dbData;
	}

	private static function getDao() {
		return Common::getDao("Game_Dao_RecommendText");
	}
	
}
