<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Gou_Service_Forum{

	/**
	 * 
	 * get Forum list for page
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * get Forum info by id
	 * @param int $id
	 */
	public static function getForum($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * update Forum by id
	 * @param array $data
	 * @param int $id
	 */
	public static function updateForum($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	
	/**
	 * 
	 * delete Forum
	 * @param int $id
	 */
	public static function deleteForum($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * add Forum
	 * @param array $data
	 */
	public static function addForum($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$data['create_time'] = Common::getTime();
		$data['status'] = !$data['status'] ? 1 : $data['status'];
		$ret =  self::_getDao()->insert($data);
		if (!$ret) return $ret;
		return self::_getDao()->getLastInsertId();
	}

	/**
	 * 
	 * cook data 
	 * @param array $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['category_id'])) $tmp['category_id'] = intval($data['category_id']);
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['content'])) $tmp['content'] = $data['content'];
		if(isset($data['user_id'])) $tmp['user_id'] = intval($data['user_id']);
		if(isset($data['user_name'])) $tmp['user_name'] = $data['user_name'];
		if(isset($data['create_time'])) $tmp['create_time'] = intval($data['create_time']);
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['is_top'])) $tmp['is_top'] = intval($data['is_top']);
		if(isset($data['reply_count'])) $tmp['reply_count'] = intval($data['reply_count']);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Gou_Dao_Forum
	 */
	private static function _getDao() {
		return Common::getDao("Gou_Dao_Forum");
	}
}
