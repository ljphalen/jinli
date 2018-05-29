<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author 
 *
 */
class Resource_Service_Type{
	
	/**
	 * 
	 * @return Resource_Dao_Type
	 */
	private static function _getDao() {
		return Common::getDao("Resource_Dao_Type");
	}
}
