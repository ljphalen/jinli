<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * Enter desCrcategoryiption here ...
 * @author tiansh
 *
 */
class Lock_Service_Crcategory{
	

	/**
	 *
	 * Enter desCrcategoryiption here ...
	 */
	public static function getAllCrcategory() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	
	public static function getCrcategory($id) {
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * Enter desCrcategoryiption here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params);
		$total = self::_getDao()->count($data);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * @param unknown_type $parent_id
	 * @return multitype:
	 */
	public static function getListByParentId($parent_id) {	
		return self::_getDao()->getListByParentID($parent_id);
	}
	
	/**
	 * 
	 * @param unknown_type $parent_id
	 * @return multitype:
	 */
	public static function getListByParentIds($pids) {
		if (!count($pids)) return false;
		return self::_getDao()->getListByParentIds($pids);
	}
	
	/**
	 * 
	 * @param unknown_type $url
	 * @return multitype:
	 */
	public static function getDataByUrl($url) {
		return self::_getDao()->getDataByUrl($url);
	}
	
	
	/**
	 *
	 * @param unknown_type $Date
	 * @return multitype:
	 */
	public static function getParentsList() {
		return self::_getDao()->getParentList();
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addCrcategory($data) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	
	public static function updateCrcategory($data, $id){
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function deleteCrcategory($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * Enter desCrcategoryiption here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['name'])) $tmp['name'] = $data['name'];
		if(isset($data['parent_id'])) $tmp['parent_id'] = intval($data['parent_id']);
		if(isset($data['url'])) $tmp['url'] = html_entity_decode($data['url']);
		if(isset($data['order_id'])) $tmp['order_id'] = intval($data['order_id']);
		if(isset($data['url'])) $tmp['md5_url'] = md5(html_entity_decode(trim($data['url'])));
		return $tmp;
	}
		
	/**
	 *
	 * @return Lock_Dao_Crcategory
	 */
	private static function _getDao() {
		return Common::getDao("Lock_Dao_Crcategory");
	}
}
