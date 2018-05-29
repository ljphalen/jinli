<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Browser_Service_Redirect{
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */

	public static function getRedirect($id) {
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	
	public static function getRedirectByUrl($url) {
		return self::_getDao()->getRedictByUrl($url);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public static function getList($page = 1, $limit = 20, $params = array()) {
		if ($page < 1) $page = 1;
		$start = ($page -1) * $limit;
		$params = self::_cookData($params);
		$ret = self::_getDao()->getList(intval($start), intval($limit), $params);
		$total = self::_getDao()->count($params);
		return array($total, $ret); 
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addRedirect($data) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
		
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */

	public static function updateRedirect($data, $id){
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function deleteRedirect($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private function _cookData($data) {
		$tmp = array();
		if (isset($data['sort'])) $tmp['sort'] = $data['sort'];
		if (isset($data['name'])) $tmp['name'] = $data['name'];
		if (isset($data['url'])) $tmp['url'] = html_entity_decode(trim($data['url']));
		if (isset($data['url'])) $tmp['md5_url'] = md5(html_entity_decode(trim($data['url'])));
		if (isset($data['redirect_name'])) $tmp['redirect_name'] = $data['redirect_name'];
		if (isset($data['redirect_url'])) $tmp['redirect_url'] = html_entity_decode($data['redirect_url']);
		return $tmp;
	}

	
	
	/**
	 * 
	 * @return Admin_Dao_Task
	 */
	private static function _getDao() {
		return Common::getDao("Browser_Dao_Redirect");
	}
}
