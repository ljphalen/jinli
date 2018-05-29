<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Browser_Service_Nav{
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */

	public static function getNav($id) {
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
	public static function addNav($data) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
		
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */

	public static function updateNav($data, $id){
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function deleteNav($id) {
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
		if (isset($data['sort'])) $tmp['sort'] = $data['sort'];
		if (isset($data['link'])) $tmp['link'] = $data['link'];
		if (isset($data['navtype'])) $tmp['navtype'] = $data['navtype'];
		if (isset($data['type'])) $tmp['type'] = $data['type'];
		return $tmp;
	}

	
	
	/**
	 * 
	 * @return Admin_Dao_Task
	 */
	private static function _getDao() {
		return Common::getDao("Browser_Dao_Nav");
	}
}
