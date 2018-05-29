<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Spider_Service_Data
 * @author rainkid
 *
 */
class Spider_Service_Data extends Common_Dao_Base{
	
	/**
	 *
	 * Enter description here ...
	 */
	public static function getAllRealtionData() {
		return array(self::_getRelationDao()->count(), self::_getRelationDao()->getAll());
	}

	/**
	 *
	 * Enter description here ...
	 */
	public static function getRealtionItems($id) {
		return self::_getRelationDao()->get($id);
	}	
	
	/**
	 * 
	 * @param unknown $data
	 */
	public static function addRealtionItems($data) {
		return self::_getRelationDao()->insert($data);
	}
	
	/**
	 * 
	 * @param unknown $data
	 * @param unknown $params
	 */
	public static function updateByRealtionItems($data, $params) {
		return self::_getRelationDao()->insert($data, $params);
	}	
	
	/**
	 *
	 * Enter description here ...
	 */
	public static function getGnOwnItems() {
		return self::_getGnOwnDao()->get();
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public static function getAllGnOwnData() {
		return array(self::_getGnOwnDao()->count(), self::_getGnOwnDao()->getAll());
	}

	/**
	 *
	 * Enter description here ...
	 */
	public static function getQhOwnItems() {
		return array(self::_getQhOwnDao()->get());
	}
	
	
	/**
	 *
	 * Enter description here ...
	 */
	public static function getAllQhOwnData() {
		return array(self::_getQhOwnDao()->count(), self::_getQhOwnDao()->getAll());
	}
	
	/**
	 * 
	 * @param unknown $data
	 * @return boolean|unknown
	 */
	public static function saveRelation($data){
		if (!is_array($data)) return false;
		$ret = self::_getRelationDao()->insert($data);
		return $ret;
	}
	
	/**
	 * 
	 * @param unknown $data
	 * @return boolean|unknown
	 */
	public static function saveGn($data){
		if (!is_array($data)) return false;
		$ret = self::_getGnOwnDao()->insert($data);
		return $ret;
	}
	
	/**
	 * 
	 * @param unknown $data
	 * @return boolean|unknown
	 */
	public static function saveQh($data){
		if (!is_array($data)) return false;
		$ret = self::_getQhOwnDao()->insert($data);
		return $ret;
	}
	
	private static function _getRelationDao() {
		return Common::getDao("Spider_Dao_Relation");
	}
	
	private static function _getGnOwnDao() {
		return Common::getDao("Spider_Dao_Gn");
	}
	
	private static function _getQhOwnDao() {
		return Common::getDao("Spider_Dao_Qh");
	}	
}