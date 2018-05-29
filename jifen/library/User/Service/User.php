<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class User_Service_User{
	
	/**
	 * 
	 * 获取所有用户
	 */
	public static function getAllUser() {
		return self::_getDao()->getAllUser();
	}
	
	/**
	 * 
	 * 分页取用户列表
	 * @param array $params
	 * @param int $page
	 * @param int $limit
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
	 * @param money $coin
	 * @param unknown_type $uid
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updateCoin($coin, $out_uid, $coin_type) {
		if (!$coin || !$out_uid || !$coin_type) return false;
		$coin_types = Common::getConfig('coinConfig' ,'coin_types');
		if (!in_array($coin_type, array_keys($coin_types))) return false;
		
		return self::_getDao()->incrementFloat($coin_types[$coin_type], array('out_uid'=>$out_uid), $coin);
	}
	
	/**
	 *
	 * @param money $coin
	 * @param unknown_type $uid
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updateFreezeCoin($coin, $out_uid, $coin_type) {
		if (!$coin || !$out_uid || !$coin_type) return false;
		$coin_types = Common::getConfig('coinConfig' ,'coin_types');
		if (!in_array($coin_type, array_keys($coin_types))) return false;
	
		return self::_getDao()->incrementFloat('freeze_'.$coin_types[$coin_type], array('out_uid'=>$out_uid), $coin);
	}
	
	/**
	 * 
	 * 读取一条用户信息
	 * @param int $id
	 */
	public static function getUser($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * 根据用户名查用户信息
	 * @param srting username
	 */
	public static function getUserByName($username) {
		if (!$username) return false;
		return self::_getDao()->getByUserName($username);
	}
	

	/**
	 *
	 * 根据用户外部uid查用户
	 * @param int $out_uid
	 */
	public static function getByOutUid($out_uid) {
		if (!$out_uid) return false;
		return self::_getDao()->getByOutUid($out_uid);
	}
	
	/**
	 * 
	 * 更新用户信息
	 * @param array $data
	 * @param int $id
	 */
	public static function updateUser($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * add user
	 * @param array $data
	 */
	public static function addUser($data) {
		if (!is_array($data)) return false;
		$data['create_time'] = Common::getTime();
		$data = self::_cookData($data);
		$ret = self::_getDao()->insert($data);
		if (!$ret) return false;
		return self::_getDao()->getLastInsertId();
	}
		
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['out_uid'])) $tmp['out_uid'] = $data['out_uid'];
		if(isset($data['username'])) $tmp['username'] = $data['username'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		if(isset($data['gold_coin'])) $tmp['gold_coin'] = $data['gold_coin'];
		if(isset($data['silver_coin'])) $tmp['silver_coin'] = $data['silver_coin'];
		if(isset($data['freeze_gold_coin'])) $tmp['freeze_gold_coin'] = $data['freeze_gold_coin'];
		if(isset($data['freeze_silver_coin'])) $tmp['freeze_silver_coin'] = $data['freeze_silver_coin'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return User_Dao_User
	 */
	private static function _getDao() {
		return Common::getDao("User_Dao_User");
	}
}
