<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author 
 *
 */
class Resource_Service_Pgroup{
	
	/**
	 * 
	 * @return Resource_Dao_Pgroup
	 */
	private static function _getDao() {
		return Common::getDao("Resource_Dao_Pgroup");
	}
}
