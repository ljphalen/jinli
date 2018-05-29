<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * Enter desUrliption here ...
 * @author tiansh
 *
 */
class Gou_Service_Url{
	

	/**
	 *
	 * Enter desUrliption here ...
	 */
	public static function getAllUrl() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}	
	
	public static function getUrl($id) {
		return self::_getDao()->get(intval($id));
	}
	
	
	/**
	 *
	 * Enter desUrliption here ...
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
	 * @param unknown_type $data
	 */
	public static function addUrl($data) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	
	public static function updateUrl($data, $id){
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function deleteUrl($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function updateTj($id) {
		if (!$id) return false;
		Gou_Service_ClickStat::increment(1, $id);
		return self::_getDao()->increment('hits', array('id'=>intval($id)));
	}
	
	/**
	 * get by
	 */
	public static function getBy($params = array()) {
		if(!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}

	/**
	 *
	 * Enter desUrliption here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['name'])) $tmp['name'] = $data['name'];
		if(isset($data['url'])) $tmp['url'] = $data['url'];
		if(isset($data['cid'])) $tmp['cid'] = intval($data['cid']);
		if(isset($data['channel'])) $tmp['channel'] = intval($data['channel']);
		return $tmp;
	}
		
	/**
	 *
	 * @return Gou_Dao_Url
	 */
	private static function _getDao() {
		return Common::getDao("Gou_Dao_Url");
	}
}
