<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * @author zhengzw
 *
 */
class Activity_Service_Cfg {

	const ID            = 'id';
	const ACTIVITY_TYPE = 'activity_type';
	const NAME 			= 'name';
	const PREHEAT_TIME 	= 'preheat_time';
	const PREHEAT_IMG   = 'preheat_img';
	const START_TIME 	= 'start_time';
	const END_TIME 		= 'end_time';
	const IMG 			= 'img';
	const EXPLAIN 		= 'explain';
	const CLIENT_VER 	= 'client_version';
	const STATUS 		= 'status';
	const ACTIVITY 		= 'activity';
	const REWARD 		= 'reward';
	
	static $All_FIELDS = array(
			self::ACTIVITY_TYPE, 
			self::NAME, 
			self::PREHEAT_TIME,
			self::PREHEAT_IMG,
			self::START_TIME,
			self::END_TIME,
			self::IMG,
			self::EXPLAIN,
			self::CLIENT_VER,
			self::STATUS,
			self::ACTIVITY,
			self::REWARD
	);
	
	// ACTIVITY_TYPE的值定义
	const ACTTYPE_SUMMER = 0; // 2015暑假活动
	
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
	 * @param unknown $data
	 * @return boolean|Ambigous <boolean, number, unknown>
	 */
	public static function insert($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$ret = self::_getDao()->insert($data);
		if(!$ret) return false;
		return self::_getDao()->getLastInsertId();
		
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
	 * @return Activity_Dao_Cfg
	 */
	private static function _getDao() {
		return Common::getDao("Activity_Dao_Cfg");
	}
}
