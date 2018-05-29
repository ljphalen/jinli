<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Gou_Service_Config{
	
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
		if(substr($key,-4)=='_txt'){
			return Gou_Service_ConfigTxt::getValue($key);
		}
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
		if(substr($key,-4)=='_txt'){
			return Gou_Service_ConfigTxt::setValue($key,$value);
		}
		return self::_getDao()->replace(array('gou_key'=>$key, 'gou_value'=>$value));
	}

	/**
	 * get all txt config
	 * @return array
	 */
	public static function getAllTxtConfig(){
		return Gou_Service_ConfigTxt::getAllConfig();
	}

	/**
	 *
	 * @param unknown_type $key
	 * @return mixed
	 */
	public static function getTxtValue($key) {
		return Gou_Service_ConfigTxt::getValue($key);
	}

	/**
	 *
	 * @param unknown_type $key
	 * @param unknown_type $value
	 */
	public static function setTxtValue($key, $value) {
		if (!$key) return false;
		return Gou_Service_ConfigTxt::setValue($key,$value);
	}

	
	/**
	 * 
	 * @return Gou_Dao_Config
	 */
	private static function _getDao() {
		return Common::getDao("Gou_Dao_Config");
	}
}
