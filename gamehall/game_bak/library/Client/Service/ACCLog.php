<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Client_Service_ACCLog
 * @author lichanghua
 *
 */
class Client_Service_ACCLog{
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params,array('LogTime'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 表数据查询
	 * @param array $params
	 * @param array $orderBy
	 * @return boolean
	 */
	public static function getBy($params, $orderBy = array()){
		if(!is_array($params)) return false;
		return self::_getDao()->getBy($params, $orderBy);
	}
	
	/**
	 * 表数据更新
	 * @param unknown $data
	 * @param unknown $params
	 * @return boolean
	 */
	public static function updateBy($data, $params){
		if (!is_array($data) || !is_array($params)) return false;
		$params = self::_cookData($params);
		return self::_getDao()->updateBy($data, $params);
	}
	
	/**
	 * 增加表数据
	 * @param unknown $data
	 * @return boolean
	 */
	public static function insert($data){
		if(!is_array($data)) return false;
		$params = self::_cookData($params);
		return self::_getDao()->insert($data);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['LogTime'])) $tmp['LogTime'] = $data['LogTime'];
		if(isset($data['UserId'])) $tmp['UserId'] = intval($data['UserId']);
		if(isset($data['MobileNo'])) $tmp['MobileNo'] = intval($data['MobileNo']);
		if(isset($data['AppId'])) $tmp['AppId'] = $data['AppId'];
		if(isset($data['Type'])) $tmp['Type'] = $data['Type'];
		if(isset($data['Content'])) $tmp['Content'] = $data['Content'];
		if(isset($data['Ver'])) $tmp['Ver'] = $data['Ver'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_ACCLog
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_ACCLog");
	}
}
