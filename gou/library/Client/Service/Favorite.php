<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Client_Service_Favorite{

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, array('id'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public static function search($page = 1, $limit = 20, $params = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$sqlWhere  = self::_getDao()->_cookParams($params);
		$ret = self::_getDao()->searchBy($start, $limit, $sqlWhere);
		$total = self::_getDao()->searchCount($sqlWhere);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addFavorite($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['keyword'])) $tmp['keyword'] = $data['keyword'];
		if(isset($data['keyword_md5'])) $tmp['keyword_md5'] = $data['keyword_md5'];
		if(isset($data['create_time'])) $tmp['create_time'] = intval($data['create_time']);
		if(isset($data['dateline'])) $tmp['dateline'] = $data['dateline'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Gou_Dao_Ad
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_Favorite");
	}
}
