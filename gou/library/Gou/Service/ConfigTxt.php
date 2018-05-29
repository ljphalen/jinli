<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Gou_Service_ConfigTxt{
	
	/**
	 * 
	 * get all configs
	 */
	public static function getAllConfig() {
		$ret = self::_getDao()->getAll();
		$temp = array();
		foreach($ret as $key=>$value) {
			$temp[$value['gou_key']] = $value['gou_value'];
		}
		return $temp;
	}

	/**
	 * 
	 * @param unknown_type $key
	 * @return mixed
	 */
	public static function getValue($key) {
		$ret = self::_getDao()->getBy(array('gou_key'=>$key));
		return $ret['gou_value'];
	}

	/**
	 * 
	 * @param unknown_type $key
	 * @param unknown_type $value
	 */
	public static function setValue($key, $value) {
		if (!$key) return false;
		return self::_getDao()->replace(array("gou_key"=>$key, "gou_value"=>$value));
	}
	
	/**
	 * 
	 * @return Gou_Dao_ConfigTxt
	 */
	private static function _getDao() {
		return Common::getDao("Gou_Dao_ConfigTxt");
	}
}
