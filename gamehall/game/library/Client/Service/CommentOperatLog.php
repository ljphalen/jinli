<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Client_Service_CommentOperatLog{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAll() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 * 
	 * Enter description here ...
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
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getBy($params) {
		return self::_getDao()->getBy($params);
	}
	
	/**
	 *
	 * @param unknown_type $mobile
	 * @return boolean
	 */
	public static function getsByLog($params) {
		return self::_getDao()->getsBy($params,array('check_time'=>'DESC'));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function update($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function add($data) {
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
		if(isset($data['id'])) $tmp['id'] = intval($data['id']);
		if(isset($data['comment_id'])) $tmp['comment_id'] = intval($data['comment_id']);
		if(isset($data['comment_log_id'])) $tmp['comment_log_id'] = intval($data['comment_log_id']);
		if(isset($data['check_name'])) $tmp['check_name'] = $data['check_name'];
		if(isset($data['check_time'])) $tmp['check_time'] = $data['check_time'];
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['operate'])) $tmp['operate'] = intval($data['operate']);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_CommentOperatLog
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_CommentOperatLog");
	}
}
