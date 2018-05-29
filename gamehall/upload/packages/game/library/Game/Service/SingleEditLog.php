<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 编辑记录
 * Game_Service_SingleEditLog
 * @author wupeng
 */
class Game_Service_SingleEditLog {

	public static function getSingleEditLogListBy($searchParams, $sortParams = array()) {
	    return self::getDao()->getsBy($searchParams, $sortParams);
	}

	public static function getSingleEditLogBy($searchParams, $sortParams = array()) {
	    return self::getDao()->getBy($searchParams, $sortParams);
	}

	public static function getPageList($page = 1, $limit = 10, $searchParams = array(), $sortParams = array()) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList($start, $limit, $searchParams, $sortParams);
		$total = self::getDao()->count($searchParams);
		return array($total, $ret);
	}

	public static function getSingleEditLog($id) {
		if (!$id) return null;		
		$keyParams = array('id' => $id);
		return self::getDao()->getBy($keyParams);
	}

	public static function updateSingleEditLog($data, $id) {
		if (!$id) return false;
		$data = self::checkField($data);
		if (!is_array($data)) return false;
		$keyParams = array('id' => $id);
		return self::getDao()->updateBy($data, $keyParams);
	}

	public static function updateSingleEditLogBy($data, $searchParams) {
	    $data = self::checkField($data);
	    if (!is_array($data)) return false;
	    return self::getDao()->updateBy($data, $searchParams);
	}

	public static function deleteSingleEditLogBy($searchParams) {
	    if (!$searchParams) return false;
	    return self::getDao()->deleteBy($searchParams);
	}

	public static function deleteSingleEditLog($id) {
		if (!$id) return false;
		$keyParams = array('id' => $id);
		return self::getDao()->deleteBy($keyParams);
	}

	public static function deleteSingleEditLogList($keyList) {
		if (!is_array($keyList)) return false;
		return self::getDao()->deletes('id', $keyList);
	}

	public static function addSingleEditLog($data) {
		if (!is_array($data)) return false;
		$data = self::checkNewField($data);
		return self::getDao()->insert($data);
	}

	public static function addMutiSingleEditLog($list) {
	    if (! $list) return false;
	    foreach ($list as $key => $data) {
	        $list[$key] = self::checkNewField($data);
	    }
	    return self::getDao()->mutiFieldInsert($list);
	}

	public static function checkNewField($data) {
	    $record = array();
		$record['day_id'] = isset($data['day_id']) ? $data['day_id'] : "";
		$record['uid'] = isset($data['uid']) ? $data['uid'] : 0;
		$record['create_time'] = isset($data['create_time']) ? $data['create_time'] : 0;
		return $record;
	}

	private static function checkField($data) {
	    $record = array();
		if(isset($data['id'])) $record['id'] = $data['id'];
		if(isset($data['day_id'])) $record['day_id'] = $data['day_id'];
		if(isset($data['uid'])) $record['uid'] = $data['uid'];
		if(isset($data['create_time'])) $record['create_time'] = $data['create_time'];
		return $record;
	}

	private static function getDao() {
		return Common::getDao("Game_Dao_SingleEditLog");
	}

}
