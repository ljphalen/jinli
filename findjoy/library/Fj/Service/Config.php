<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author Terry
 *
 */
class Fj_Service_Config{
	
	/**
	 * 获取所有配置
	 */
	public static function getAllConfig() {
		$ret = self::_getDao()->getAll();
		$temp = array();
		foreach($ret as $key=>$value) {
			$temp[$value['fj_key']] = $value['fj_value'];
		}

		return $temp;
	}

	/**
	 * 获取某个配置
	 * @param string $key
	 * @return mixed
	 */
	public static function getValue($key) {
		$ret = self::_getDao()->getBy(array('fj_key'=>$key));
		return $ret['fj_value'];
	}

	/**
	 * 更新某个配置
	 * @param string $key
	 * @param mix $value
	 */
	public static function setValue($key, $value) {
		if (!$key) return false;
		return self::_getDao()->updateByKey($key, $value);
	}

	/**
	 * @return Fj_Dao_Config
	 */
	private static function _getDao() {
		return Common::getDao("Fj_Dao_Config");
	}
}
