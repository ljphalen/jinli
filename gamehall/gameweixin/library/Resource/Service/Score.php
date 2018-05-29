<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author fanch
 *
 */
class Resource_Service_Score{

	
	public static function getByScore($params, $orderBy=array()){
		return self::_getDao()->getBy($params, $orderBy);
	}

	
	/**
	 * 
	 * @return Resource_Dao_Score
	 */
	private static function _getDao() {
		return Common::getDao("Resource_Dao_Score");
	}
}
