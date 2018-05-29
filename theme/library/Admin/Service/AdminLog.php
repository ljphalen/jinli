<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Admin_Service_AdminLog{
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		$data = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $data);
		$total = self::_getDao()->count($data);
		return array($total, $ret);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addAdminLog($data) {
		if (!is_array($data)) return false;
		$data['create_time'] = Common::getTime();
		$data['ip'] = Util_Http::getClientIp();
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['username'])) $tmp['username'] = $data['username'];
		if(isset($data['uid'])) $tmp['uid'] = intval($data['uid']);
		if(isset($data['file_id'])) $tmp['file_id'] = intval($data['file_id']);
		if(isset($data['message'])) $tmp['message'] = $data['message'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		if(isset($data['ip'])) $tmp['ip'] = $data['ip'];
		return $tmp;
	}
		
	/**
	 *
	 * @return Admin_Dao_AdminLog
	 */
	private static function _getDao() {
		return Common::getDao("Admin_Dao_AdminLog");
	}
}
