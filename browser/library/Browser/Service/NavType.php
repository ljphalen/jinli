<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Browser_Service_NavType{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllType() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */

	public static function getNavType($id) {
		return self::_getDao()->get(intval($id));
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
		$ret = self::_getDao()->getList(intval($start), intval($limit), $params, array('sort'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret); 
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addNavType($data) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
		
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */

	public static function updateNavType($data, $id){
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function deleteNavType($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private function _cookData($data) {
		$tmp = array();
		if (isset($data['name'])) $tmp['name'] = $data['name'];
		if (isset($data['type'])) $tmp['type'] = $data['type'];
		if (isset($data['sort'])) $tmp['sort'] = $data['sort'];
		if (isset($data['descrip'])) $tmp['descrip'] = $data['descrip'];
		return $tmp;
	}

	
	
	/**
	 * 
	 * @return Admin_Dao_Task
	 */
	private static function _getDao() {
		return Common::getDao("Browser_Dao_NavType");
	}
}
