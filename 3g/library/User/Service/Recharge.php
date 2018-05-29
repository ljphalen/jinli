<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class User_Service_Recharge {
	
	public static function add($params =array()){
		if(!is_array($params)) return false;
		$data = self::_checkData($params);
		return self::_getDao()->insert($data);
	}
	
	public static function getsBy($params = array(),$orderBy = array()){
		if(!is_array($params)) return false;
		$data = self::_checkData($params);
		return self::_getDao()->getsBy($data,$orderBy);
	}
	
	public static function  getList($page,$pageSize,$where = array(),$orderBy = array()){
		if(!is_array($where)) return false;
		return array(self::_getDao()->count($where),self::_getDao()->getList(($page-1) *$pageSize,$pageSize,$where,$orderBy));
	}
	private static function   _checkData($params =array()){
		if(!is_array($params)) return false;
		$temp = array();
		if(isset($params['api_type'])) 		$temp['api_type'] 	= $params['api_type'];
		if(isset($params['order_id']))		$temp['order_id'] 		= $params['order_id'];
		if(isset($params['order_sn']))		$temp['order_sn']		= $params['order_sn'];
		if(isset($params['status']))			$temp['status']		= $params['status'];
		if(isset($params['add_time']))		$temp['add_time']	= $params['add_time'];
		if(isset($params['desc']))				$temp['desc']			= $params['desc'];
		return $temp;
		
	}
	private static function _getDao(){
		return Common::getDao("User_Dao_Recharge");
	}
}