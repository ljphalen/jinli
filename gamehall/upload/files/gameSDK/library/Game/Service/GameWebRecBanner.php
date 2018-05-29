<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 网游banner图
 * Game_Service_GameWebRecBanner
 * @author wupeng
 */
class Game_Service_GameWebRecBanner {

    const STATUS_CLOSE = 0;
    const STATUS_OPEN = 1;
    
	public static function getGameWebRecBannerListBy($searchParams, $sortParams = array('sort' => 'desc')) {
	    return self::getDao()->getsBy($searchParams, $sortParams);
	}
	
	public static function getGameWebRecBannerBy($searchParams, $sortParams = array()) {
	    return self::getDao()->getBy($searchParams, $sortParams);
	}
	
	public static function getPageList($page = 1, $limit = 10, $searchParams = array(), $sortParams = array()) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList($start, $limit, $searchParams, $sortParams);
		$total = self::getDao()->count($searchParams);
		return array($total, $ret);
	}
	
	public static function getGameWebRecBanner($id) {
		if (!$id) return null;		
		$keyParams = array('id' => $id);
		return self::getDao()->getBy($keyParams);
	}
	
	public static function updateGameWebRecBanner($data, $id) {
		if (!$id) return false;
		$dbData = self::checkField($data);
		if (!is_array($dbData)) return false;
		$keyParams = array('id' => $id);
		return self::getDao()->updateBy($dbData, $keyParams);
	}
	
	public static function deleteGameWebRecBanner($id) {
		if (!$id) return false;
		$keyParams = array('id' => $id);
		return self::getDao()->deleteBy($keyParams);
	}

	public static function deleteGameWebRecBannerList($keyList) {
		if (!is_array($keyList)) return false;
		return self::getDao()->deletes('id', $keyList);
	}

	public static function addGameWebRecBanner($data) {
		if (!is_array($data)) return false;
		$dbData = self::checkNewField($data);
		return self::getDao()->insert($dbData);
	}

	public static function addMutiGameWebRecBanner($list) {
	    if (! $list) return false;
	    foreach ($list as $key => $data) {
	        $list[$key] = self::checkNewField($data);
	    }
	    return self::getDao()->mutiFieldInsert($list);
	}

	public static function updateGameWebRecBannerBy($data, $searchParams) {
	    $dbData = self::checkField($data);
	    if (!is_array($dbData)) return false;
	    return self::getDao()->updateBy($dbData, $searchParams);
	}
	
	private static function getDao() {
		return Common::getDao("Game_Dao_GameWebRecBanner");
	}

	private static function checkField($data) {
		$dbData = array();
		if(isset($data['id'])) $dbData['id'] = $data['id'];
		if(isset($data['sort'])) $dbData['sort'] = $data['sort'];
		if(isset($data['title'])) $dbData['title'] = $data['title'];
		if(isset($data['link_type'])) $dbData['link_type'] = $data['link_type'];
		if(isset($data['link'])) $dbData['link'] = $data['link'];
		if(isset($data['img'])) $dbData['img'] = $data['img'];
		if(isset($data['img_high'])) $dbData['img_high'] = $data['img_high'];
		if(isset($data['day_id'])) $dbData['day_id'] = $data['day_id'];
		if(isset($data['status'])) $dbData['status'] = $data['status'];
		if(isset($data['create_time'])) $dbData['create_time'] = $data['create_time'];
		return $dbData;
	}

	public static function checkNewField($data) {
	    $record = array();
	    $record['sort'] = isset($data['sort']) ? $data['sort'] : 0;
	    $record['title'] = isset($data['title']) ? $data['title'] : "";
	    $record['link_type'] = isset($data['link_type']) ? $data['link_type'] : 0;
	    $record['link'] = isset($data['link']) ? $data['link'] : "";
	    $record['img'] = isset($data['img']) ? $data['img'] : "";
	    $record['img_high'] = isset($data['img_high']) ? $data['img_high'] : "";
	    $record['day_id'] = isset($data['day_id']) ? $data['day_id'] : 0;
	    $record['status'] = isset($data['status']) ? $data['status'] : self::STATUS_CLOSE;
	    $record['create_time'] = isset($data['create_time']) ? $data['create_time'] : time();
		return $record;
	}

}
