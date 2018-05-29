<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

//用户打卡记录
class User_Service_Punch {
	
	public static function checkSign($params){
		if(!is_array($params)) return false;
		return self::_getDao()->checkSign($params);
	}
	private static function _getDao(){
		return Common::getDao("User_Dao_Punch");
	}
}