<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Wifi_Service_Ptner{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAll() {
		return self::_getDao()->getAll();
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
	 * @param unknown_type $uid
	 */
	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function getBy($params) {
		if (!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function getsBy($params) {
		if (!is_array($params)) return false;
		return self::_getDao()->getsBy($params);
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
		$data['create_time'] = Common::getTime();
		$data['passwd'] = md5($data['passwd']);
		$ret = self::_getDao()->insert($data);
        if (!$ret) return false;
        return self::_getDao()->getLastInsertId();
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function update($data, $id) {
		if (!is_array($data) && !intval($id)) return false;
		$data = self::_cookData($data);
        if ($data['passwd']) {
            $data['passwd'] = md5($data['passwd']);
        }
		return self::_getDao()->update($data, $id);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['ht'])) $tmp['ht'] = $data['ht'];
		if(isset($data['username'])) $tmp['username'] = $data['username'];
		if(isset($data['phone'])) $tmp['phone'] = $data['phone'];
		if(isset($data['passwd'])) $tmp['passwd'] = $data['passwd'];
		if(isset($data['s_name'])) $tmp['s_name'] = $data['s_name'];
		if(isset($data['s_type'])) $tmp['s_type'] = $data['s_type'];
		if(isset($data['s_address'])) $tmp['s_address'] = $data['s_address'];
		if(isset($data['s_detail'])) $tmp['s_detail'] = $data['s_detail'];
		if(isset($data['login_mode'])) $tmp['login_mode'] = $data['login_mode'];
		if(isset($data['login_passwd'])) $tmp['login_passwd'] = $data['login_passwd'];
        if(isset($data['weixin_name'])) $tmp['weixin_name'] = $data['weixin_name'];
        if(isset($data['weixin_passwd'])) $tmp['weixin_passwd'] = $data['weixin_passwd'];
		if(isset($data['data'])) $tmp['data'] = $data['data'];
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		return $tmp;
	}
	
	/**
	 *
	 * @return Wifi_Dao_Device
	 */
	private static function _getDao() {
		return Common::getDao("Wifi_Dao_Ptner");
	}
}
