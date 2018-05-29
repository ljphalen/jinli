<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * @author yinjiayan
 *
 */
class Admin_Service_Automsg {
	
    public static function getby($params) {
        return self::getDao()->getBy($params);
    }
    
    public static function update($data, $id) {
        return self::getDao()->update($data, $id);
    }
    
    public static function install($data) {
        return self::getDao()->insert($data);
    }
	
	/**
	 * 
	 * @author yinjiayan
	 * @return Admin_Dao_Gift
	 */
	private static function getDao() {
		return Common::getDao("Admin_Dao_Automsg");
	}
	
}