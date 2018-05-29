<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 推荐banner图
 * Game_Service_RecommendBanner
 * @author wupeng
 */
class Game_Service_RecommendBanner {

    const STATUS_CLOSE = 0;
    const STATUS_OPEN = 1;

	/**
	 * 查询记录
	 * @return array
	 */
	public static function getRecommendBannersBy($searchParams, $sortParams = array('sort' => 'desc', 'id' => 'asc')) {
	    return self::getDao()->getsBy($searchParams, $sortParams);
	}
	
	public static function getRecommendBanner($id) {
		if (!$id) return null;		
		$keyParams = array('id' => $id);
		return self::getDao()->getBy($keyParams);
	}
	
	public static function updateRecommendBanner($data, $id) {
		if (!$id) return false;
		$dbData = self::checkField($data);
		if (!is_array($dbData)) return false;
		$keyParams = array('id' => $id);
		return self::getDao()->updateBy($dbData, $keyParams);
	}
	
	
	public static function deleteRecommendBanner($id) {
		if (!$id) return false;
		$keyParams = array('id' => $id);
		return self::getDao()->deleteBy($keyParams);
	}
	
	public static function deleteRecommendBannerByDayId($day_id) {
	    $keyParams = array('day_id' => $day_id);
	    return self::getDao()->deleteBy($keyParams);
	}
	
	public static function addRecommendBanner($data) {
		if (!is_array($data)) return false;
		$dbData = self::checkNewField($data);
		return self::getDao()->insert($dbData);
	}
	
	public static function addMutiRecommendBanner($list) {
	    if (! $list) return false;
	    foreach ($list as $key => $data) {
	        $list[$key] = self::checkNewField($data);
	    }
	    return self::getDao()->mutiFieldInsert($list);
	}
	
	public static function updateRecommendBannerBy($data, $searchParams) {
	    $dbData = self::checkField($data);
	    if (!is_array($dbData)) return false;
	    return self::getDao()->updateBy($dbData, $searchParams);
	}
	
	private static function checkNewField($data) {
	    $record = array();
	    $record['sort'] = isset($data['sort']) ? $data['sort'] : 0;
	    $record['title'] = isset($data['title']) ? $data['title'] : "";
	    $record['link_type'] = isset($data['link_type']) ? $data['link_type'] : 0;
	    $record['link'] = isset($data['link']) ? $data['link'] : "";
	    $record['img1'] = isset($data['img1']) ? $data['img1'] : "";
	    $record['img2'] = isset($data['img2']) ? $data['img2'] : "";
	    $record['img3'] = isset($data['img3']) ? $data['img3'] : "";
	    $record['day_id'] = isset($data['day_id']) ? $data['day_id'] : 0;
	    $record['status'] = isset($data['status']) ? $data['status'] : self::STATUS_CLOSE;
	    $record['create_time'] = isset($data['create_time']) ? $data['create_time'] : time();
		return $record;
	}
	
	private static function checkField($data) {
		$dbData = array();
		if(isset($data['id'])) $dbData['id'] = $data['id'];
		if(isset($data['sort'])) $dbData['sort'] = $data['sort'];
		if(isset($data['title'])) $dbData['title'] = $data['title'];
		if(isset($data['link_type'])) $dbData['link_type'] = $data['link_type'];
		if(isset($data['link'])) $dbData['link'] = $data['link'];
		if(isset($data['img1'])) $dbData['img1'] = $data['img1'];
		if(isset($data['img2'])) $dbData['img2'] = $data['img2'];
		if(isset($data['img3'])) $dbData['img3'] = $data['img3'];
		if(isset($data['day_id'])) $dbData['day_id'] = $data['day_id'];
		if(isset($data['status'])) $dbData['status'] = $data['status'];
		if(isset($data['create_time'])) $dbData['create_time'] = $data['create_time'];
		return $dbData;
	}

	private static function getDao() {
		return Common::getDao("Game_Dao_RecommendBanner");
	}
	
}
