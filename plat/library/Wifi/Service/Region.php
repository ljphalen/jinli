<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Wifi_Service_Region{
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function getBy($params) {
		if (!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function getsBy($params) {
		if (!is_array($params)) return false;
		return self::_getDao()->getsBy($params);
	}
	
	/**
	 *
	 * @return Wifi_Dao_Device
	 */
	private static function _getDao() {
		return Common::getDao("Wifi_Dao_Region");
	}
}
