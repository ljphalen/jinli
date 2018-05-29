<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Glog_Service_FreedlProgress
 * @author fanch
 *
 */
class Glog_Service_FreedlProgress{
	
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
		$data = self::_cookData($data);
		return self::_getDao()->updateBy($data, $params);
	}
	
	/**
	 * 增加表数据
	 * @param unknown $data
	 * @return boolean
	 */
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
		if(isset($data['table_ymd'])) $tmp['table_ymd'] = $data['table_ymd'];
		if(isset($data['last_id'])) $tmp['last_id'] = $data['last_id'];
		if(isset($data['progress_id'])) $tmp['progress_id'] = $data['progress_id'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Freedl_Dao_OriginalLog
	 */
	private static function _getDao() {
		return Common::getDao("Glog_Dao_FreedlProgress");
	}
}
