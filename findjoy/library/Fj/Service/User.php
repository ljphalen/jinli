<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Fj_Service_User extends Common_Service_Base{
	static private $hash = 'xysoza'; //hash值
	static private $cookieTime = 2592000; //默认设置为30天
	static private $cookieName = 'FINDJOY-OPEN-ID';

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
	 * 读取一条用户信息
	 * @param int $id
	 */
	public static function getUser($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * get user info by out_uid
	 * @param string $out_uid
	 * @return boolean|mixed
	 */
	public static function getUserBy($params) {
		if(!is_array($params)) return false;
		$data = self::_cookData($params);
		return self::_getDao()->getBy($data);
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
	 * @param $data
	 * @param $params
	 * @return bool
	 */
	public static function updateUserBy($data, $params) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->updateBy($data, $params);
	}

	/**
	 * 
	 * del user
	 * @param int $id
	 */
	public static function deleteUser($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * add user
	 * @param array $data
	 */
	public static function addUser($data) {
		if (!is_array($data)) return false;
		$data['register_time'] = Common::getTime();
		$data = self::_cookData($data);
		Common::log($data, "weixin.log");
		return self::_getDao()->insert($data);
	}
	
	/**
	 *
	 * islogin
	 */
	public static function isLogin() {
	    $cookie = Util_Cookie::get(self::$cookieName, true);
	    if(!$cookie) return false;
	
	    $cookie =  self::_cookieEncrypt($cookie, 'DECODE');
	    if (!$cookie[1] || !$cookie[2]) return false;
	    $userInfo = self::getUserBy(array('open_id'=>$cookie['2'], 'id'=>$cookie['1']));
	    if (!$userInfo) return false;
	
	    self::cookieUser($userInfo);
	    return $userInfo;
	}
	
	/**
	 * cookie字符串加密解密方式
	 * @param string $str      加密方式
	 * @param string $encode   ENCODE-加密|DECODE-解密
	 * @return array
	 */
	static private function _cookieEncrypt($str, $encode = 'ENCODE') {
	    if ($encode == 'ENCODE') return Common::encrypt($str);
	    $result = Common::encrypt($str, 'DECODE');
	    return explode('\t', $result);
	}
	
	/**
	 * cookie添加
	 * @param string $userInfo  用户信息
	 * @return array
	 */
	public static function cookieUser($userInfo) {
	    $str = Common::getTime() . '\t';
	    $str .= $userInfo['id'] . '\t';
	    $str .= $userInfo['out_uid'] . '\t';
	
	    $cookieStr = self::_cookieEncrypt($str);
	    Util_Cookie::set(self::$cookieName, $cookieStr, true, Common::getTime() + self::$cookieTime, '/', self::getDomain());
	}
	
	/**
	 *
	 * @return Admin_Dao_User
	 */
	private static function getDomain() {
	    $domain = str_replace('http://','',Common::getWebRoot());
	    if($number = strrpos($domain,':')) $domain = Util_String::substr($domain, 0, $number);
	    return $domain;
	}
	
	/**
	 * cookie user sid
	 * @param array $token
	 */
	public static function cookieUserSid($token, $time) {
	    Util_Cookie::set(self::$cookieSidName, $token, true, $time, '/', self::getDomain());
	}
	
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $username
	 * @param unknown_type $password
	 */
	public static function checkUser($out_uid) {
		if (!$out_uid) return false;
		$userInfo = self::getUserBy(array('open_id' => $out_uid));
		if (!$userInfo) {
		    self::addUser(array('open_id'=> $out_uid));
		    $userInfo = self::getUserBy(array('open_id' => $out_uid));
		}
		return $userInfo;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['phone'])) $tmp['phone'] = $data['phone'];
		if(isset($data['username'])) $tmp['username'] = $data['username'];
		if(isset($data['nickname'])) $tmp['nickname'] = $data['nickname'];
		if(isset($data['sex'])) $tmp['sex'] = $data['sex'];
		if(isset($data['province'])) $tmp['province'] = $data['province'];
		if(isset($data['city'])) $tmp['city'] = $data['city'];
		if(isset($data['country'])) $tmp['country'] = $data['country'];
		if(isset($data['headimgurl'])) $tmp['headimgurl'] = $data['headimgurl'];
		if(isset($data['unionid'])) $tmp['unionid'] = $data['unionid'];
		if(isset($data['open_id'])) $tmp['open_id'] = $data['open_id'];
		if(isset($data['register_time'])) $tmp['register_time'] = $data['register_time'];
		if(isset($data['realname'])) $tmp['realname'] = $data['realname'];
		if(isset($data['mobile'])) $tmp['mobile'] = $data['mobile'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Fj_Dao_User
	 */
	private static function _getDao() {
		return Common::getDao("Fj_Dao_User");
	}
}
