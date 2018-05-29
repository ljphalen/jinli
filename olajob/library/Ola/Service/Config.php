<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author Terry
 *
 */
class Ola_Service_Config{
	
	/**
	 * 获取所有配置
	 */
	public static function getAllConfig() {
		$ret = self::_getDao()->getAll();
		$temp = array();
		foreach($ret as $key=>$value) {
			$temp[$value['ola_key']] = $value['ola_value'];
		}

		return $temp;
	}

	/**
	 * 获取某个配置
	 * @param string $key
	 * @return mixed
	 */
	public static function getValue($key) {
		$ret = self::_getDao()->getBy(array('ola_key'=>$key));
		return $ret['ola_value'];
	}

	/**
	 * 更新某个配置
	 * @param string $key
	 * @param mix $value
	 */
	public static function setValue($key, $value) {
		if (!$key) return false;
		return self::_getDao()->replace(array("ola_key"=>$key, "ola_value"=>$value));
	}

	/**
	 * @return Ola_Dao_Config
	 */
	private static function _getDao() {
		return Common::getDao("Ola_Dao_Config");
	}
}
