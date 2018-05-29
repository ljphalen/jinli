<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Gionee_Service_AreaCode
 * @author rainkid
 *
*/
class Gionee_Service_AreaCode{
	
	public static function getBy($params = array(),$sort=array()){
		if(!is_array($params)) return false;
		return self::_getDao()->getBy($params,$sort);
	}
	
	private static function _getDao(){
		return Common::getDao('Gionee_Dao_AreaCode'); 
	}
}