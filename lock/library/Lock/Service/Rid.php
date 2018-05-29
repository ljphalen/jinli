<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Lock_Service_Rid{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllRid() {
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
		$ret = self::_getDao()->getList($start, $limit, $params, array('id'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getRid($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * @param string $out_uid
	 * @return array
	 */
	public static function getByRid($rid) {
		if (!$rid) return false;
		return self::_getDao()->getBy(array('rid'=>$rid));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateRid($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}	
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addRid($data) {
		if (!is_array($data)) return false;
		$data['create_time'] = Common::getTime();
		$data['status'] = 1;
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
		if(isset($data['rid'])) $tmp['rid'] = $data['rid'];
		if(isset($data['at'])) $tmp['at'] = $data['at'];
		if(isset($data['mod'])) $tmp['mod'] = $data['mod'];
		if(isset($data['sr'])) $tmp['sr'] = $data['sr'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Lock_Dao_Rid
	 */
	private static function _getDao() {
		return Common::getDao("Lock_Dao_Rid");
	}
}
