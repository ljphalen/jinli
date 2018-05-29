<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

class  Gionee_Service_MobileCode{ 
	
	public static function getBy($params =array(),$sort= array()){
		if(!is_array($params)) return false;
		return self::_getDao()->getBy($params,$sort);
	} 
	
	public static function add($params){
		if(!is_array($params)) return false;
		return self::_getDao()->insert($params);
	}
	private static function _getDao(){
		return Common::getDao('Gionee_Dao_MobileCode'); 
	}
}