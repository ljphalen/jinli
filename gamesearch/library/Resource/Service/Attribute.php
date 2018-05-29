<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author 
 *
 */
class Resource_Service_Attribute extends Common_Service_Base{

	/**
	 * 按条件检索属性值
	 * @param unknown $params
	 */
	public static function getBy($params){
		 if(!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}

	/**
	 * 按条件检索属性值
	 * @param unknown $params
	 */
	public static function getsBy($params, $orderBy = array()){
		if(!is_array($params)) return false;
		return self::_getDao()->getsBy($params, $orderBy);
	}
	
	
	/**
	 * 
	 * @return Resource_Dao_Attribute
	 */
	private static function _getDao() {
		return Common::getDao("Resource_Dao_Attribute");
	}
}
