<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Gou_Service_ForumUser{

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function getBy($params) {
		if (!is_array($params)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->getBy($params);
	}
	
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getForumUser($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateForumUser($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteForumUser($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addForumUser($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$ret =  self::_getDao()->insert($data);
		if (!$ret) return $ret;
		return self::_getDao()->getLastInsertId();
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['user_mac'])) $tmp['user_mac'] = $data['user_mac'];
		if(isset($data['md5_mac'])) $tmp['md5_mac'] = $data['md5_mac'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Gou_Dao_ForumUser
	 */
	private static function _getDao() {
		return Common::getDao("Gou_Dao_ForumUser");
	}
}
