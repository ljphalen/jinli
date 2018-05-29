<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
//管理员操作订单记录
class User_Service_Actions {
	
	public static function getBy($params = array(),$orderBy= array()){
		if(!is_array($params)) return false;
		return self::_getDao()->getBy($params,$orderBy);
	}
	
	public static function getsBy($params = array(),$orderBy = array()){
		if(!is_array($params)) return false;
		return  self::_getDao()->getsBy($params,$orderBy);
	}
	public static function add($params){
		if(!is_array($params)) return false;
		return self::_getDao()->insert($params);
	}
	
	public static function getList($page,$pageSize,$where =array(),$orderBy = array()){
		if(!is_array($where)) return false;
		return array(self::_getDao()->count($where),self::_getDao()->getList($pageSize *($page-1),$pageSize,$where,$orderBy));
	}
	private static function _getDao(){
		return Common::getDao("User_Dao_Actions");
	}
}
