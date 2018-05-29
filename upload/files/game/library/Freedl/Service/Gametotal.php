<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Freedl_Service_Gametotal{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAll() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array('id'=>'DESC','refresh_time'=>'DESC')) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	public static function getCount($params = array()) {
		return self::_getDao()->getCount($params);
	}
	
	public static function getBy($params, $orderBy=array()) {
		if(!is_array($params)) return false;
		return self::_getDao()->getBy($params,$orderBy);
	}
	
	public static function updateBy($data, $params){
		if (!is_array($data) || !is_array($params)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->updateBy($data, $params);
	}
	
	public static function insert($data){
		if(!is_array($data)) return false;
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
		if(isset($data['id'])) $tmp['id'] = intval($data['id']);
		if(isset($data['year_month'])) $tmp['year_month'] = $data['year_month'];
		if(isset($data['activity_id'])) $tmp['activity_id'] = intval($data['activity_id']);
		if(isset($data['activity_name'])) $tmp['activity_name'] = $data['activity_name'];
		if(isset($data['game_id'])) $tmp['game_id'] = $data['game_id'];
		if(isset($data['game_name'])) $tmp['game_name'] = $data['game_name'];
		if(isset($data['month_consume'])) $tmp['month_consume'] = $data['month_consume'];
		if(isset($data['refresh_time'])) $tmp['refresh_time'] = $data['refresh_time'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Freedl_Dao_Gametotal
	 */
	private static function _getDao() {
		return Common::getDao("Freedl_Dao_Gametotal");
	}
}
