<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class User_Service_UMoneyApiLog{
	public static function add($params = array()){
		if(!is_array($params)) return false;
		return self::_getDao()->insert($params);
	}
	public static function count($params = array()){
		if(!is_array($params)) return false;
		return self::_getDao()->count($params);
	}
	
	public static function getBy($params = array(),$sort=array()){
		if(!is_array($params)) return false;
		return self::_getDao()->getBy($params,$sort);
	}
	
	public static function get($id){
		return self::_getDao()->get($id);
	}
	
	public static function deletesBy($params){
		if(!is_array($params)) return false;
		return self::_getDao()->deleteBy($params);
	}
	
	public static function getList($page,$limit,$where =array(),$orderBy= array()){
		if(!is_array($where)) return flase;
		$start = (max(intval($page), 1) - 1) * $limit;
		return array(self::_getDao()->count($where),self::_getDao()->getList($start,$limit,$where,$orderBy));
	}
	
	public static function edit($params,$id){
		if(!is_array($params)) return false;
		return self::_getDao()->update($params, $id);
	}
	private static function _checkData($params=array()){
		$temp = array();
		if(isset($params['type'])) 					$temp['type'] = $params['type'];
		if(isset($params['add_time'])) 			$temp['add_time'] = $params['add_time'];
		if(isset($params['msg'])) 					$temp['msg'] = $params['msg'];
	}
	
	/**
	 *
	 * @return User_Dao_UMoneyApiLog
	 */
	private static function _getDao(){
		return Common::getDao("User_Dao_UMoneyApiLog");
	}
}