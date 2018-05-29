<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author fanch
 *
 */
class Resource_Service_Games extends Common_Service_Base{
	
	/**
	 * 分页检索游戏资源表数据
	 * 
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array('id'=>'DESC')) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	

	/**
	 * 
	 * @return Resource_Dao_Games
	 */
	private static function _getDao() {
		return Common::getDao("Resource_Dao_Games");
	}
	
	
}
