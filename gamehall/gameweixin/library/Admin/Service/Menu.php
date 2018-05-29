<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * @author yinjiayan
 *
 */
class Admin_Service_Menu {
	
	public static function getAll() {
	    return self::getDao()->getAll();
	}
	
	public static function add($data) {
	    return self::getDao()->insert($data);
	}
	
	public static function update($data, $id) {
	    return self::getDao()->update($data, $id);
	}
	
	/**
	 * 
	 * @author yinjiayan
	 * @return Admin_Dao_Gift
	 */
	private static function getDao() {
		return Common::getDao("Admin_Dao_Menu");
	}
	
}