<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Browser_Service_IndexChannel{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllIndexChannel() {
		return array(self::_getDao()->count(), self::_getDao()->getAllChannel());
	}
	
	/**
	 *
	 */
	public function getCanUseChannels() {
		return self::_getDao()->getCanUseChannels();
	}
	
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
		$ret = self::_getDao()->getList($start, $limit, $params, array('sort'=>'DESC'));
		$total = self::_getDao()->count();
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getIndexChannel($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateIndexChannel($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteIndexChannel($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addIndexChannel($data) {
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
		if(isset($data['name'])) $tmp['name'] = $data['name'];
		if(isset($data['link'])) $tmp['link'] = $data['link'];
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['sort'])) $tmp['sort'] = $data['sort'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['click_type'])) $tmp['click_type'] = $data['click_type'];
		if(isset($data['is_rand'])) $tmp['is_rand'] = $data['is_rand'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Browser_Dao_Ad
	 */
	private static function _getDao() {
		return Common::getDao("Browser_Dao_IndexChannel");
	}
}
