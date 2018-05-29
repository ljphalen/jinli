<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Theme_Service_Pushlogs{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllPushlogs() {
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
	 * @param unknown_type $data
	 */
	public static function addPushlogs($data) {
		if (!is_array($data)) return false;
		$data['create_time'] = Common::getTime();
		$data['create_date'] = date('Y-m-d', Common::getTime());
		$data = self::_cookData($data);
		$ret = self::_getDao()->insert($data);
		if (!$ret) return $ret;
		return self::_getDao()->getLastInsertId();
	}
	
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['push_content'])) $tmp['push_content'] = $data['push_content'];
		if(isset($data['return_content'])) $tmp['return_content'] = $data['return_content'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		if(isset($data['create_date'])) $tmp['create_date'] = $data['create_date'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Theme_Dao_Pushlogs
	 */
	private static function _getDao() {
		return Common::getDao("Theme_Dao_PushLogs");
	}
}
