<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 推荐图片
 * Game_Service_GameWebRecImgs
 * @author wupeng
 */
class Game_Service_GameWebRecImgs {
    
	public static function getGameWebRecImgsListBy($searchParams, $sortParams = array()) {
	    return self::getDao()->getsBy($searchParams, $sortParams);
	}
	
	public static function getGameWebRecImgsBy($searchParams, $sortParams = array()) {
	    return self::getDao()->getBy($searchParams, $sortParams);
	}
	
	public static function getPageList($page = 1, $limit = 10, $searchParams = array(), $sortParams = array()) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList($start, $limit, $searchParams, $sortParams);
		$total = self::getDao()->count($searchParams);
		return array($total, $ret);
	}
	
	public static function getGameWebRecImgs($id) {
		if (!$id) return null;		
		$keyParams = array('id' => $id);
		return self::getDao()->getBy($keyParams);
	}
	
	public static function updateGameWebRecImgs($data, $id) {
		if (!$id) return false;
		$dbData = self::checkField($data);
		if (!is_array($dbData)) return false;
		$keyParams = array('id' => $id);
		return self::getDao()->updateBy($dbData, $keyParams);
	}
	
	public static function deleteGameWebRecImgs($id) {
		if (!$id) return false;
		$keyParams = array('id' => $id);
		return self::getDao()->deleteBy($keyParams);
	}

	public static function deleteGameWebRecImgsList($keyList) {
		if (!is_array($keyList)) return false;
		return self::getDao()->deletes('id', $keyList);
	}

	public static function deleteGameWebRecImgsBy($params) {
	    if (! $params) return false;
	    return self::getDao()->deleteBy($params);
	}

	public static function addGameWebRecImgs($data) {
		if (!is_array($data)) return false;
		$dbData = self::checkNewField($data);
		return self::getDao()->insert($dbData);
	}

	private static function checkField($data) {
		$dbData = array();
		if(isset($data['id'])) $dbData['id'] = $data['id'];
		if(isset($data['recommend_id'])) $dbData['recommend_id'] = $data['recommend_id'];
		if(isset($data['link_type'])) $dbData['link_type'] = $data['link_type'];
		if(isset($data['link'])) $dbData['link'] = $data['link'];
		if(isset($data['img'])) $dbData['img'] = $data['img'];
		return $dbData;
	}

	public static function checkNewField($data) {
	    $record = array();
	    $record['recommend_id'] = isset($data['recommend_id']) ? $data['recommend_id'] : 0;
	    $record['link_type'] = isset($data['link_type']) ? $data['link_type'] : 0;
	    $record['link'] = isset($data['link']) ? $data['link'] : "";
	    $record['img'] = isset($data['img']) ? $data['img'] : "";
		return $record;
	}

	private static function getDao() {
		return Common::getDao("Game_Dao_GameWebRecImgs");
	}

}
