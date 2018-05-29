<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * Enter desChannelCodeiption here ...
 * @author tiansh
 *
 */
class Gou_Service_ApiLog{
	

	/**
	 *
	 * Enter getChannelCode 
	 */
	public static function get($id) {
		return self::_getDao()->get(intval($id));
	}
	
	
	/**
	 *
	 * Enter desChannelCodeiption here ...
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
	public static function add($data) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	
	public static function update($data, $id){
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	
	/**
	 * get by
	 */
	public static function getBy($params = array()) {
		if(!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}
	
	/**
	 *
	 * @param array $params
	 * @return array
	 */
	public static function getsBy($params, $sort) {
		if (!is_array($params) || !is_array($sort)) return false;
		$ret = self::_getDao()->getsBy($params, $sort);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 *
	 * Enter desChannelCodeiption here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['mark'])) $tmp['mark'] = $data['mark'];
		if(isset($data['url'])) $tmp['url'] = $data['url'];
		if(isset($data['api_type'])) $tmp['api_type'] = $data['api_type'];
		if(isset($data['request'])) $tmp['request'] = $data['request'];
		if(isset($data['response'])) $tmp['response'] = $data['response'];
		if(isset($data['create_time'])) $tmp['create_time'] = intval($data['create_time']);
		return $tmp;
	}
		
	/**
	 *
	 * @return Gou_Dao_ApiLog
	 */
	private static function _getDao() {
		return Common::getDao("Gou_Dao_ApiLog");
	}
}