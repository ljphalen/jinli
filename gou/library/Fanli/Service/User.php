<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Fanli_Service_User extends Common_Service_Base{
	static private $hash = 'xysoza'; //hash值
	static private $cookieTime = 2592000; //默认设置为30天
	static private $cookieName = 'GIONEE-FANLI-USER';
	static private $cookieSidName = 'GIONEE-GOU-SID';

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
	 * Enter description here ...
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public static function search($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$sqlWhere  = self::_getDao()->_cookParams($params);
		$ret = self::_getDao()->searchBy($start, $limit, $sqlWhere, array('id'=>'DESC'));
		$total = self::_getDao()->searchCount($sqlWhere);
		return array($total, $ret);
	}
	
	
	/**
	 *
	 * 分页取用户列表
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public static function getListByTime($page = 1, $limit = 10) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getListByTime($start, $limit);
		$total = self::_getDao()->countByTime();
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
	 *
	 * @param string $out_uid
	 * @param int $uid
	 * @throws Exception
	 * @return boolean
	 */
	public static function register($data, $passport) {
		if(!is_array($data)) return false;
		try {
			$trans = parent::beginTransaction();
			if (!$trans) throw new Exception("begin transaction failed.", -201);
			
			//第三方注册
			if($passport) {
				$passport_data = array(
					'passport_id'=>$data['passport_id'],
					'passport_name'=>$data['passport_name'],
					'passport_username'=>$data['auth_username'],
				);
				$ret_passport = Fanli_Service_Passport::addPassport($passport_data);
				if (!$ret_passport) throw new Exception("register passport failed.", -207);
				
				$register_data = array(
					'passport_id'=>$ret_passport,
					'register_imei'=>$data['register_imei'],
					'username'=>$data['passport_name'].'_'.crc32(uniqid(mt_rand(0, 99)))
				);
				$result = self::addUser($register_data);
				if (!$result) throw new Exception("register failed.", -209);
				
			} else {
				$result = self::addUser($data);
				if (!$result) throw new Exception("register failed.", -205);
			}
			$user = self::getUser($result);
			
			$token  = self::_token($user['id']);
			$update_data  = array(
				'last_login_imei'=>$data['register_imei'],
				'token'=>$token,
				'token_expire_time'=>Common::getTime() + 2592000,
			);
			$ret = self::updateUser($update_data, $result);
			if (!$ret) throw new Exception('update user faild.', -202);

			//事务提交
			if ($trans) {
				$return = parent::commit();
				if(!$return) {
					return false;
				}
				return $token;
			}
		} catch(Exception $e) {
			parent::rollBack();
			Common::log(array($e->getCode(), $e->getMessage()), 'register.log');
			return false;
		}
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
	 *
	 * 更新token到期时间
	 * @param array $data
	 * @param int $id
	 */
	public static function updateUserExpiretime($id) {
		if (!$id) return false;
		$user  = self::getUser($id);
		$time = Common::getTime();
		if($user && ($user['token_expire_time'] - $time < 172800)) {
			self::updateUser(array('token_expire_time'=>$time + '2592000'), $id);
		}
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
		$data['register_date'] = date('Y-m-d', Common::getTime());
		$data['last_login_time'] = Common::getTime();
		$data = self::_cookData($data);
		$ret = self::_getDao()->insert($data);
		if (!$ret) return false;
		return self::_getDao()->getLastInsertId();
	}
	
	/**
	 * 
	 * cookpasswd
	 * @param string $password
	 */
	static private function _cookPasswd($password) {
		$hash = Common::randStr(6);
		$passwd = self::_password($password, $hash);
		return array($hash, $passwd);
	}
	
	/**
	 * 
	 * login
	 * @param string $username
	 * @param string $passwd
	 */
	public static function login($data, $passport) {
		if($passport) {
			//第三方登录
			$passport_info = Fanli_Service_Passport::getBy(array('passport_username'=>$data['auth_username'], 'passport_id'=>$passport));
			if($passport_info) {
				$user = self::getUserBy(array('passport_id'=>$passport_info['id']));
			} else {
				$register_data = array(
					'passport_id'=>$passport,
					'passport_name'=>$data['passport_name'],
					'auth_username'=>$data['auth_username'],
					'register_imei'	=>$data['imei']
				);
				$ret = self::register($register_data, $passport);
				if(!$ret) return Common::formatMsg(303, 'ERROR_LOGIN_FAIL'); //登录失败
				return Common::formatMsg(0, $ret); //return token
			}
			
		} else {
			$user = self::getUserBy(array('phone'=>$data['phone']));
			if (!$user)  return Common::formatMsg(305, 'ERROR_USER_UNREGISTER');//用户不存在
			
			//验证密码
			if($user['password'] != self::_password($data['password'], $user['hash'])) return Common::formatMsg(306, 'ERROR_PHONE_OR_PASSWORD');
		}
		
		//更新登录信息
		$token  = self::_token($user['id']);
		$update_data  = array(
				'last_login_imei'=>$data['imei'],
				'token'=>$token,
				'token_expire_time'=>Common::getTime() + 2592000,
				'last_login_time'=>Common::getTime(),
		);
		
		self::updateUser($update_data, $user['id']);
		return Common::formatMsg(0, $token); //return token
	}
	
	/**
	 * 
	 * logout
	 */
	public static function logout() {
		return Util_Cookie::delete(self::$cookieName, '/', self::getDomain());
	}
	
	
	/**
	 * 
	 * islogin
	 */
	public static function isLogin() {		
		$cookie = Util_Cookie::get(self::$cookieName, true);
		if(!$cookie) return false;
		
		$cookie =  self::_cookieEncrypt($cookie, 'DECODE');
		if (!$cookie[1] || !$cookie[3]) return false;
		$userInfo = self::getUserBy(array('out_uid'=>$cookie['3'], 'id'=>$cookie['2']));
		if (!$userInfo) return false;
		
		//order count
		$count = Gou_Service_Order::getOrderCountByUid($userInfo['id']);
		if($userInfo['order_num'] != $count) {
			Gou_Service_User::updateUser(array('order_num'=>$count), $userInfo['id']);
			$userInfo['order_num'] == $count;
		}
		
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
		
		$cookieStr = self::_cookieEncrypt($str);
		Util_Cookie::set(self::$cookieName, $cookieStr, true, Common::getTime() + self::$cookieTime, '/', self::getDomain());
		//cookie user sid
		if ($userInfo['taobao_mobile_token']) self::cookieUserSid($userInfo['taobao_mobile_token'], Common::getTime() + self::$cookieTime);
		
		//更新最后登录时间
		return self::updateUser(array('last_login_time'=>Common::getTime()), $userInfo['id']);
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
	 * get user sid
	 */
	public static function getUserSid() {
		$sid = Util_Cookie::get(self::$cookieSidName, true);
		if (!$sid) {
			$sid = 't'.Common::getTime().'9699';
			self::cookieUserSid($sid, strtotime(date('Y-m-d 23:59:59')));
		}
		return $sid;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $password
	 * @param unknown_type $hash
	 */
	public static function _password($password, $hash) {
		return md5(md5($password) . $hash);
	}
	
	
	/**
	 * 
	 * @param int $user_id
	 * @return string
	 */
	private static function _token($prefix) {
		return md5(uniqid($prefix));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['username'])) $tmp['username'] = $data['username'];
		if(isset($data['phone'])) $tmp['phone'] = $data['phone'];
		if(isset($data['password'])) {
			list($tmp['hash'], $tmp['password']) = self::_cookPasswd($data['password']); 
		}
		if(isset($data['truename'])) $tmp['truename'] = $data['truename'];
		if(isset($data['alipay'])) $tmp['alipay'] = $data['alipay'];
		if(isset($data['register_time'])) $tmp['register_time'] = $data['register_time'];
		if(isset($data['register_date'])) $tmp['register_date'] = $data['register_date'];
		if(isset($data['register_imei'])) $tmp['register_imei'] = $data['register_imei'];
		if(isset($data['last_login_time'])) $tmp['last_login_time'] = $data['last_login_time'];
		if(isset($data['last_login_imei'])) $tmp['last_login_imei'] = $data['last_login_imei'];
		if(isset($data['passport_id'])) $tmp['passport_id'] = $data['passport_id'];
		if(isset($data['money'])) $tmp['money'] = $data['money'];
		if(isset($data['token'])) $tmp['token'] = $data['token'];
		if(isset($data['token_expire_time'])) $tmp['token_expire_time'] = $data['token_expire_time'];
		if(isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
		if(isset($data['end_time'])) $tmp['end_time'] = $data['end_time'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Fanli_Dao_User
	 */
	private static function _getDao() {
		return Common::getDao("Fanli_Dao_User");
	}
}
