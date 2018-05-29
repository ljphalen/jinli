<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 用户vip标识
 * User_Service_VipFlg
 * @author wupeng
 */
class User_Service_VipFlg {
	
	const F_UUID = 'uuid';
	const F_GIFT = 'gift';
	const F_TICKET = 'ticket';

	public static function getVipFlgListBy($searchParams, $sortParams = array()) {
	    return self::getDao()->getsBy($searchParams, $sortParams);
	}

	public static function getVipFlgBy($searchParams, $sortParams = array()) {
	    return self::getDao()->getBy($searchParams, $sortParams);
	}

	public static function getPageList($page = 1, $limit = 10, $searchParams = array(), $sortParams = array()) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList($start, $limit, $searchParams, $sortParams);
		$total = self::getDao()->count($searchParams);
		return array($total, $ret);
	}

	public static function getVipFlg($uuid) {
		if (!$uuid) return null;		
		$keyParams = array(self::F_UUID => $uuid);
		return self::getDao()->getBy($keyParams);
	}

	public static function updateVipFlg($data, $uuid) {
		if (!$uuid) return false;
		$data = self::checkField($data);
		if (!is_array($data)) return false;
		$keyParams = array(self::F_UUID => $uuid);
		return self::getDao()->updateBy($data, $keyParams);
	}

	public static function updateVipFlgBy($data, $searchParams) {
	    $data = self::checkField($data);
	    if (!is_array($data)) return false;
	    return self::getDao()->updateBy($data, $searchParams);
	}

	public static function deleteVipFlg($uuid) {
		if (!$uuid) return false;
		$keyParams = array(self::F_UUID => $uuid);
		return self::getDao()->deleteBy($keyParams);
	}

	public static function deleteVipFlgList($keyList) {
		if (!is_array($keyList)) return false;
		return self::getDao()->deletes(self::F_UUID, $keyList);
	}

	public static function addVipFlg($data) {
		if (!is_array($data)) return false;
		$data = self::checkNewField($data);
		return self::getDao()->insert($data);
	}

	public static function addMutiVipFlg($list) {
	    if (! $list) return false;
	    foreach ($list as $key => $data) {
	        $list[$key] = self::checkNewField($data);
	    }
	    return self::getDao()->mutiFieldInsert($list);
	}

	public static function checkNewField($data) {
	    $record = array();
		$record[self::F_UUID] = isset($data[self::F_UUID]) ? $data[self::F_UUID] : 0;
		$record[self::F_GIFT] = isset($data[self::F_GIFT]) ? $data[self::F_GIFT] : 0;
		$record[self::F_TICKET] = isset($data[self::F_TICKET]) ? $data[self::F_TICKET] : 0;
		return $record;
	}

	private static function checkField($data) {
	    $record = array();
		if(isset($data[self::F_UUID])) $record[self::F_UUID] = $data[self::F_UUID];
		if(isset($data[self::F_GIFT])) $record[self::F_GIFT] = $data[self::F_GIFT];
		if(isset($data[self::F_TICKET])) $record[self::F_TICKET] = $data[self::F_TICKET];
		return $record;
	}

	private static function getDao() {
		return Common::getDao("User_Dao_VipFlg");
	}

}
