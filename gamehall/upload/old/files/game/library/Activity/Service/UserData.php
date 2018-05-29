<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * @author zhengzw
 *
 */
class Activity_Service_UserData {
	const ACTIVITY_ID 	= 'activity_id';
	const UUID 			= 'uuid';
	const DATA		    = 'data';
	
	static $All_FIELDS = array(
			self::ACTIVITY_ID, 
			self::UUID, 
			self::DATA
	);
		
	/**
	 * 
	 * @param number $page
	 * @param number $limit
	 * @param unknown $params
	 * @param unknown $orderBy
	 * @return multitype:string multitype:
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy=array('id'=>'DESC')) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 *
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @return multitype:unknown multitype:
	 */
	
	public static function getBy($params = array() , $orderBy=array('id'=>'DESC')){
		$ret = self::_getDao()->getBy($params ,$orderBy);
		if(!$ret) return false;
		return $ret;
	}
	
	/**
	 *
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @return multitype:unknown multitype:
	 */
	
	public static function getsBy($params = array(), $orderBy=array('id'=>'DESC') ){
		$ret = self::_getDao()->getsBy($params, $orderBy);
		if(!$ret) return false;
		return $ret;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getByID($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateByID($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	
	public static function updateBy($data, $params) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->updateBy($data, $params);
	}
	
	public static function insert($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$ret =  self::_getDao()->insert($data);
		if(!$ret) return false;
		return self::_getDao()->getLastInsertId();
	}
	
	
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteByID($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		foreach (self::$All_FIELDS as $field) {
			if (isset($data[$field]))  $tmp[$field] = $data[$field];
		}
		return $tmp;
	}
	
	/**
	 * 
	 * @return Sdk_Dao_Ad
	 */
	private static function _getDao() {
		return Common::getDao("Activity_Dao_UserData");
	}
}
