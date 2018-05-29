<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author rainkid
 *
 */
class Widget_Service_Config {

	/**
	 *
	 * get all configs
	 */
	public static function getAllConfig() {
		$ret  = self::_getDao()->getAll();
		$temp = array();
		foreach ($ret as $key => $value) {
			$temp[$value['3g_key']] = $value['3g_value'];
		}
		return $temp;
	}

	/**
	 *
	 * @param unknown_type $key
	 * @return mixed
	 */
	public static function getValue($key) {
		$ret = self::_getDao()->getBy(array('3g_key' => $key));
		return $ret['3g_value'];
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
	 * @return Gou_Dao_Config
	 */
	private static function _getDao() {
		return Common::getDao("Widget_Dao_Config");
	}
}
