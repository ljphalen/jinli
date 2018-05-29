<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * vip图标
 * User_Service_VipIcon
 * @author wupeng
 */
class User_Service_VipIcon {
	
	const F_VIP = 'vip';
	const F_IMG = 'img';

	public static function getVipIconListBy($searchParams, $sortParams = array()) {
	    return self::getDao()->getsBy($searchParams, $sortParams);
	}

	public static function getVipIconBy($searchParams, $sortParams = array()) {
	    return self::getDao()->getBy($searchParams, $sortParams);
	}

	public static function getPageList($page = 1, $limit = 10, $searchParams = array(), $sortParams = array()) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList($start, $limit, $searchParams, $sortParams);
		$total = self::getDao()->count($searchParams);
		return array($total, $ret);
	}

	public static function getVipIcon($vip) {
		if (!$vip) return null;		
		$keyParams = array(self::F_VIP => $vip);
		return self::getDao()->getBy($keyParams);
	}

	public static function updateVipIcon($data, $vip) {
		if (!$vip) return false;
		$data = self::checkField($data);
		if (!is_array($data)) return false;
		$keyParams = array(self::F_VIP => $vip);
		return self::getDao()->updateBy($data, $keyParams);
	}

	public static function updateVipIconBy($data, $searchParams) {
	    $data = self::checkField($data);
	    if (!is_array($data)) return false;
	    return self::getDao()->updateBy($data, $searchParams);
	}

	public static function deleteVipIcon($vip) {
		if (!$vip) return false;
		$keyParams = array(self::F_VIP => $vip);
		return self::getDao()->deleteBy($keyParams);
	}

	public static function deleteVipIconList($keyList) {
		if (!is_array($keyList)) return false;
		return self::getDao()->deletes(self::F_VIP, $keyList);
	}

	public static function addVipIcon($data) {
		if (!is_array($data)) return false;
		$data = self::checkNewField($data);
		return self::getDao()->insert($data);
	}

	public static function addMutiVipIcon($list) {
	    if (! $list) return false;
	    foreach ($list as $key => $data) {
	        $list[$key] = self::checkNewField($data);
	    }
	    return self::getDao()->mutiFieldInsert($list);
	}

	private static function checkNewField($data) {
	    $record = array();
	    $record[self::F_VIP] = isset($data[self::F_VIP]) ? $data[self::F_VIP] : "";
		$record[self::F_IMG] = isset($data[self::F_IMG]) ? $data[self::F_IMG] : "";
		return $record;
	}

	private static function checkField($data) {
	    $record = array();
		if(isset($data[self::F_VIP])) {
			$record[self::F_VIP] = $data[self::F_VIP];
		}
		if(isset($data[self::F_IMG])) {
			$record[self::F_IMG] = $data[self::F_IMG];
		}
		return $record;
	}

	private static function getDao() {
		return Common::getDao("User_Dao_VipIcon");
	}

}
