<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Gou_Service_FateUser{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllFateUser() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params);
		$total = self::_getDao()->count();
		return array($total, $ret);
	}
	
	/**
	 * 
	 * @param unknown_type $mobile
	 * @return boolean|mixed
	 */
	public static function getByMobile($mobile) {
		if (!$mobile) return false;
		return self::_getDao()->getBy(array('mobile'=>$mobile));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getFateUser($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateFateUser($data, $id) {
		if (!is_array($data)) return false;
		$data['last_time'] = Common::getTime();
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteFateUser($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addFateUser($data) {
		if (!is_array($data)) return false;
		$data['create_time'] = Common::getTime();
		$data['last_time'] = Common::getTime();
		$data['total_times'] = 1;
		$data['fate_times'] = 0;
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
		if(isset($data['mobile'])) $tmp['mobile'] = $data['mobile'];
		if(isset($data['total_times'])) $tmp['total_times'] = $data['total_times'];
		if(isset($data['fate_times'])) $tmp['fate_times'] = $data['fate_times'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		if(isset($data['last_time'])) $tmp['last_time'] = $data['last_time'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Gou_Dao_FateUser
	 */
	private static function _getDao() {
		return Common::getDao("Gou_Dao_FateUser");
	}
}
