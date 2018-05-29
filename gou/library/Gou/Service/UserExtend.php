<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Gou_Service_UserExtend{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllUserExtend() {
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
		$params = self::_cookData($params);
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getUserExtend($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	
	/**
	 *根据用户id查询个人兴趣爱好
	 * @param int $user_id
	 * @return array
	 */
	public function getUserExtendByUserId($user_id) {
		if (!$user_id) return false;
		return self::_getDao()->getUserExtendByUserId($user_id);
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateUserExtend($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteUserExtend($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addUserExtend($data) {
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
		if(isset($data['user_id'])) $tmp['user_id'] = intval($data['user_id']);
		if(isset($data['email'])) $tmp['email'] = $data['email'];
		if(isset($data['qq'])) $tmp['qq'] = intval($data['qq']);
		if(isset($data['job'])) $tmp['job'] = intval($data['job']);
		if(isset($data['love'])) $tmp['love'] = intval($data['love']);
		if(isset($data['age'])) $tmp['age'] = intval($data['age']);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Gou_Dao_UserExtend
	 */
	private static function _getDao() {
		return Common::getDao("Gou_Dao_UserExtend");
	}
}
