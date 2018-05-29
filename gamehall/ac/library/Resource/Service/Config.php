<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author fanch
 *
 */
class Resource_Service_Config{
	/**
	 * 
	 * @param unknown_type $key
	 * @return mixed
	 */
	public static function getValue($key) {
		$ret = self::_getDao()->getBy(array('apps_key'=>$key));
		return $ret['apps_value'];
	}

	/**
	 * 
	 * @param unknown_type $key
	 * @param unknown_type $value
	 */
	public static function setValue($key, $value='') {
		if (!$key) return false;
		return self::_getDao()->replace(array('apps_key'=>$key,'apps_value'=>$value));
	}
	
	/**
	 * 
	 * @param unknown $key
	 */
	public static function delValue($key){
		return self::_getDao()->deleteBy(array('apps_key'=>$key));;
	}
	/**
	 * 
	 * @return game_Dao_Config
	 */
	private static function _getDao() {
		return Common::getDao("Resource_Dao_Config");
	}
}
