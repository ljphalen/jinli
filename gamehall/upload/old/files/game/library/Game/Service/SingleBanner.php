<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 单机banner图
 * Game_Service_SingleBanner
 * @author wupeng
 */
class Game_Service_SingleBanner {

	public static function getSingleBannerListBy($searchParams, $sortParams = array('sort' => 'desc', 'id' => 'asc')) {
	    return self::getDao()->getsBy($searchParams, $sortParams);
	}

	public static function getSingleBannerBy($searchParams, $sortParams = array()) {
	    return self::getDao()->getBy($searchParams, $sortParams);
	}

	public static function getPageList($page = 1, $limit = 10, $searchParams = array(), $sortParams = array()) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList($start, $limit, $searchParams, $sortParams);
		$total = self::getDao()->count($searchParams);
		return array($total, $ret);
	}

	public static function getSingleBanner($id) {
		if (!$id) return null;		
		$keyParams = array('id' => $id);
		return self::getDao()->getBy($keyParams);
	}

	public static function updateSingleBanner($data, $id) {
		if (!$id) return false;
		$data = self::checkField($data);
		if (!is_array($data)) return false;
		$keyParams = array('id' => $id);
		return self::getDao()->updateBy($data, $keyParams);
	}

	public static function updateSingleBannerBy($data, $searchParams) {
	    $data = self::checkField($data);
	    if (!is_array($data)) return false;
	    return self::getDao()->updateBy($data, $searchParams);
	}

	public static function deleteSingleBannerBy($searchParams) {
		if (! $searchParams) return false;
		return self::getDao()->deleteBy($searchParams);
	}

	public static function deleteSingleBanner($id) {
		if (!$id) return false;
		$keyParams = array('id' => $id);
		return self::getDao()->deleteBy($keyParams);
	}

	public static function deleteSingleBannerList($keyList) {
		if (!is_array($keyList)) return false;
		return self::getDao()->deletes('id', $keyList);
	}

	public static function addSingleBanner($data) {
		if (!is_array($data)) return false;
		$data = self::checkNewField($data);
		return self::getDao()->insert($data);
	}

	public static function addMutiSingleBanner($list) {
	    if (! $list) return false;
	    foreach ($list as $key => $data) {
	        $list[$key] = self::checkNewField($data);
	    }
	    return self::getDao()->mutiFieldInsert($list);
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
		$record['status'] = isset($data['status']) ? $data['status'] : 0;
		$record['create_time'] = isset($data['create_time']) ? $data['create_time'] : time();
		return $record;
	}

	private static function checkField($data) {
	    $record = array();
		if(isset($data['id'])) $record['id'] = $data['id'];
		if(isset($data['sort'])) $record['sort'] = $data['sort'];
		if(isset($data['title'])) $record['title'] = $data['title'];
		if(isset($data['link_type'])) $record['link_type'] = $data['link_type'];
		if(isset($data['link'])) $record['link'] = $data['link'];
		if(isset($data['img'])) $record['img'] = $data['img'];
		if(isset($data['img_high'])) $record['img_high'] = $data['img_high'];
		if(isset($data['day_id'])) $record['day_id'] = $data['day_id'];
		if(isset($data['status'])) $record['status'] = $data['status'];
		if(isset($data['create_time'])) $record['create_time'] = $data['create_time'];
		return $record;
	}

	private static function getDao() {
		return Common::getDao("Game_Dao_SingleBanner");
	}

}
