<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 礼包推荐
 * Game_Service_SingleListGift
 * @author wupeng
 */
class Game_Service_SingleListGift {

	public static function getSingleListGiftListBy($searchParams, $sortParams = array('sort' => 'desc', 'id' => 'asc')) {
	    return self::getDao()->getsBy($searchParams, $sortParams);
	}

	public static function getSingleListGiftBy($searchParams, $sortParams = array()) {
	    return self::getDao()->getBy($searchParams, $sortParams);
	}

	public static function getPageList($page = 1, $limit = 10, $searchParams = array(), $sortParams = array()) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList($start, $limit, $searchParams, $sortParams);
		$total = self::getDao()->count($searchParams);
		return array($total, $ret);
	}

	public static function getSingleListGift($id) {
		if (!$id) return null;		
		$keyParams = array('id' => $id);
		return self::getDao()->getBy($keyParams);
	}

	public static function updateSingleListGift($data, $id) {
		if (!$id) return false;
		$data = self::checkField($data);
		if (!is_array($data)) return false;
		$keyParams = array('id' => $id);
		return self::getDao()->updateBy($data, $keyParams);
	}

	public static function updateSingleListGiftBy($data, $searchParams) {
	    $data = self::checkField($data);
	    if (!is_array($data)) return false;
	    return self::getDao()->updateBy($data, $searchParams);
	}

	public static function deleteSingleListGift($id) {
		if (!$id) return false;
		$keyParams = array('id' => $id);
		return self::getDao()->deleteBy($keyParams);
	}

	public static function deleteSingleListGiftList($keyList) {
		if (!is_array($keyList)) return false;
		return self::getDao()->deletes('id', $keyList);
	}

	public static function deleteSingleListGiftBy($searchParams) {
		if (!$searchParams) return false;
		return self::getDao()->deleteBy($searchParams);
	}

	public static function addSingleListGift($data) {
		if (!is_array($data)) return false;
		$data = self::checkNewField($data);
		return self::getDao()->insert($data);
	}

	public static function addMutiSingleListGift($list) {
	    if (! $list) return false;
	    foreach ($list as $key => $data) {
	        $list[$key] = self::checkNewField($data);
	    }
	    return self::getDao()->mutiFieldInsert($list);
	}

	public static function checkNewField($data) {
	    $record = array();
		$record['recommend_id'] = isset($data['recommend_id']) ? $data['recommend_id'] : "";
		$record['gift_id'] = isset($data['gift_id']) ? $data['gift_id'] : "";
		$record['gift_status'] = isset($data['gift_status']) ? $data['gift_status'] : 0;
		$record['sort'] = isset($data['sort']) ? $data['sort'] : 0;
		return $record;
	}

	private static function checkField($data) {
	    $record = array();
		if(isset($data['id'])) $record['id'] = $data['id'];
		if(isset($data['recommend_id'])) $record['recommend_id'] = $data['recommend_id'];
		if(isset($data['gift_id'])) $record['gift_id'] = $data['gift_id'];
		if(isset($data['gift_status'])) $record['gift_status'] = $data['gift_status'];
		if(isset($data['sort'])) $record['sort'] = $data['sort'];
		return $record;
	}

	private static function getDao() {
		return Common::getDao("Game_Dao_SingleListGift");
	}

}
