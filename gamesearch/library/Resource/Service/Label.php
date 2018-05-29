<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * Enter desLabeliption here ...
 * @author
 *
 */
class Resource_Service_Label{

	
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
	 * Enter desLabeliption here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
    public static function getList($page = 1, $limit = 10, $params = array()) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, array('id'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
		
	/**
	 *
	 * @return Resource_Dao_Label
	 */
	private static function _getDao() {
		return Common::getDao("Resource_Dao_Label");
	}
}
