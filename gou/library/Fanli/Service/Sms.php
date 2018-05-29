<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * Enter desChanneliption here ...
 * @author tiansh
 *
 */
class Fanli_Service_Sms{
	
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
		$ret = self::_getDao()->getList($start, $limit, $params, array('id'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getSms($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateSms($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addSms($data) {
		if (!is_array($data)) return false;
		$time = Common::getTime();
		$data['create_time'] = $time;
		$data['expire_time'] = $time + 600;
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
	public static function getBy($params) {
		if (!is_array($params)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->getBy($params);
	}
	
	/**
	 *
	 * @param array $params
	 * @return array
	 */
	public static function getsBy($params, $sort) {
		if (!is_array($params) || !is_array($sort)) return false;
		$ret = self::_getDao()->getsBy($params, $sort);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * chk verfiy
	 * @param unknown_type $token
	 * @param unknown_type $verify
	 * @param unknown_type $option
	 * @return multitype:unknown_type
	 */
	public static function verify($token, $verify, $option) {
		$sms = Fanli_Service_Sms::getBy(array('token'=>$token), array('id'=>'DESC'));
		if(!$sms || $sms['verify'] != $verify || $sms['operate'] != $option) {
			return Common::formatMsg(201, '验证码错误！', 0);
		}
		
		if($sms['status'] == 1) return Common::formatMsg(203, '该验证码已验证,请重新获取验证码', 0);
		if(Common::getTime() > $sms['expire_time']) return Common::formatMsg(202, '验证码已过期！', 0);
		return Common::formatMsg(0, "",$sms['id']);
	}
	
	/**
	 *
	 * @param array $params
	 * @return array
	 */
	public static function getCount() {
		return self::_getDao()->count();
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['phone'])) $tmp['phone'] = $data['phone'];
		if(isset($data['verify'])) $tmp['verify'] = $data['verify'];
		if(isset($data['imei'])) $tmp['imei'] = $data['imei'];
		if(isset($data['token'])) $tmp['token'] = $data['token'];
		if(isset($data['operate'])) $tmp['operate'] = $data['operate'];
		if(isset($data['create_time'])) $tmp['create_time'] = intval($data['create_time']);
		if(isset($data['expire_time'])) $tmp['expire_time'] = intval($data['expire_time']);
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		return $tmp;
	}
	
	/**
	 *
	 * @return Fanli_Dao_Sms
	 */
	private static function _getDao() {
		return Common::getDao("Fanli_Dao_Sms");
	}
}
