<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Admin_Service_Group{
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $groupid
	 */
	public static function getGroup($groupid) {
		$result = self::_getDao()->get(intval($groupid));
		$result['rvalue'] = json_decode($result['rvalue'], true);
		return $result;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addGroup($data) {
		if (!is_array($data)) return false;
		$data['createtime'] = Common::getTime();
		$data = self::_cookData($data);
		return self::_getDao()->addGroup($data);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $groupid
	 */
	public static function updateGroup($data, $groupid) {
		if (!is_array($data)) return false; 
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($groupid));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllGroup() {
		return self::_getDao()->getGroups();
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public static function getList($page = 1, $limit = 20, $params = array()) {
		if ($page < 1) $page = 1;
		$start = ($page -1) * $limit;
		$params = self::_cookData($params);
		$ret = self::_getDao()->getList(intval($start), intval($limit), $params);
		$total = self::_getDao()->count($params);
		return array($total, $ret); 
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $groupid
	 */
	public static function deleteGroup($groupid) {
		return self::_getDao()->deleteGroup(intval($groupid));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['name'])) $tmp['name'] = $data['name'];
		if(isset($data['descrip'])) $tmp['descrip'] = $data['descrip'];
		if(isset($data['rvalue'])) $tmp['rvalue'] = json_encode($data['rvalue']);
		if(isset($data['createtime'])) $tmp['createtime'] = $data['createtime'];
		$tmp['ifdefault'] = 1;
		return $tmp;
	}
	
	/**
	 * 
	 * @return Admin_Dao_Group
	 */
	private static function _getDao() {
		return Common::getDao("Admin_Dao_Group");
	}
}