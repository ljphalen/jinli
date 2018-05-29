<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class User_Service_App{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllApp() {
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
		$total = self::_getDao()->count();
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getApp($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param string $name
	 */
	public static function getAppByName($name) {
		if (!$name) return false;
		return self::_getDao()->where(array('name'=>$name));
	}
	
	/**
	 * 
	 * @param unknown_type $coin
	 * @param unknown_type $out_uid
	 * @param unknown_type $coin_type
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updateCoin($coin, $appid, $coin_type) {
		if (!$coin || !$appid || !$coin_type) return false;
		$coin_types = Common::getConfig('coinConfig' ,'coin_types');
		if (!in_array($coin_type, array_keys($coin_types))) return false;
		
		return self::_getDao()->incrementFloat($coin_types[$coin_type], array('appid'=>$appid), Common::money($coin));
	}
	
	/**
	 *
	 * check app by appid and secret
	 * @param string $name
	 */
	public static function checkApp($appid) {
		if (!$appid) return false;
		return self::_getDao()->where(array('appid'=>$appid));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateApp($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteApp($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addApp($data) {
		if (!is_array($data)) return false;
// 		$data['appid'] = self::createAppId();
		$data['create_time'] = Common::getTime();
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
		if(isset($data['appid'])) $tmp['appid'] = $data['appid'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		return $tmp;
	}
	
	private static function createAppId(){
		list($usec, $sec) = explode(" ", microtime());
		$usec = substr(str_replace('0.', '', $usec), 0, 4);
		$str = rand(10, 99);
		return $usec . $str;
	}
	
	/**
	 * 
	 * @return User_Dao_App
	 */
	private static function _getDao() {
		return Common::getDao("User_Dao_App");
	}
}
