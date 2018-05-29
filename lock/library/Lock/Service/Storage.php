<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Lock_Service_Storage{
	
	/**
	 * 
	 * get all configs
	 */
	public static function getAllStorage() {
		$ret = self::_getDao()->getAll();
		$temp = array();
		foreach($ret as $key=>$value) {
			$temp[$value['storage_key']] = $value['storage_value'];
		}
		return $temp;
	}

	/**
	 * 
	 * @param unknown_type $key
	 * @return mixed
	 */
	public static function getValue($key) {
		$ret = self::_getDao()->where(array('storage_key'=>$key));
		return $ret['storage_value'];
	}

	/**
	 * 
	 * @param unknown_type $key
	 * @param unknown_type $value
	 */
	public static function setValue($key, $value) {
		if (!$key) return false;
		return self::_getDao()->updateByKey($key, $value);
	}
	
	/**
	 * 
	 * @return Gou_Dao_Storage
	 */
	private static function _getDao() {
		return Common::getDao("Lock_Dao_Storage");
	}
}
