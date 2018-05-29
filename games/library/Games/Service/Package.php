<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Games_Service_Package{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllPackage() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 * 
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @return multitype:unknown
	 */
	public static function getPackagesByTime($page, $limit, $time) {
		if(intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getPackageByTime(intval($start), intval($limit), intval($time));
		$total = self::_getDao()->getCountByTime(intval($time));
		return array($total, $ret); 
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
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getPackage($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updatePackage($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$data['update_time'] = Common::getTime();
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deletePackage($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addPackage($data) {
		if (!is_array($data)) return false;
		$data['update_time'] = Common::getTime();
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	/**
	 * mutiAddPackage
	 * @param array $data
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function mutiAddPackage($data) {
		if (!is_array($data)) return false;
		$temp = array();
		foreach($data as $key=>$value) {
			if ($value) {
				array_push($temp, array(
					'id' => '',
					'pacakge' => $value,
					'status'=>1,
					'update_time' => Common::getTime()
				));
			}
		} 
		return self::_getDao()->mutiInsert($temp);
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['package'])) $tmp['package'] = $data['package'];
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Games_Dao_Package
	 */
	private static function _getDao() {
		return Common::getDao("Games_Dao_Package");
	}
}
