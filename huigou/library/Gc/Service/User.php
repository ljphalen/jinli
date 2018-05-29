<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Gc_Service_User extends Common_Service_Base{
	static private $hash = 'xysoza'; //hash值
	static private $cookieTime = 2592000; //默认设置为30天
	static private $cookieName = 'Gou_Clent_User';
	static private $cookieSidName = 'Gou_Client_Sid';

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
	 * get bind taobao users
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getBindTaobaoUsers($page = 1, $limit = 20) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getBindTaobaoUsers($start, $limit);
		$total = self::_getDao()->countBindTaobaoUsers();
		return array($total, $ret);
	}
	
	/**
	 * 
	 * get by uids
	 * @param array $uids
	 */
	public static function getListByUids($uids) {
		if (!count($uids)) return false;
		return self::_getDao()->getListByUids($uids);
	}
	
	/**
	 * 
	 * @param string $out_uid
	 * @param int $uid
	 * @throws Exception
	 * @return boolean
	 */
	public static function gain($out_uid, $uid) {
		if (!$out_uid || !$uid) return false; 
		try {
			$trans = parent::beginTransaction();
			if (!$trans) throw new Exception("begin transaction failed.", -201);
			$ret = Api_Gionee_Pay::coinAdd(array(
					'out_uid'=>$out_uid,
					'coin_type'=>'2',
					'coin'=>'40.00',
					'msg'=>"恭喜您获得40银币（注册即送100银币，即时可用40银币，剩余60银币将分三个星期陆续赠送）。"));
			if($ret['status'] != 200) throw new Exception('gionee pay coin add failed.', -202);
			
			$ret = self::incrementTotalSliver('40.00', $out_uid);
			if (!$ret) throw new Exception('update user total sliver coin faild.');
			
			$ret = Gc_Service_User::updateUser(array('isgain'=>1), $uid);
			if (!$ret) throw new Exception('change isgain status failed.', -203);
			
			$ret = parent::commit();
			if (!$ret) throw new Exception("transactoin commit failed.", -204);
			return true;
		} catch(Exception $e) {
			parent::rollBack();
			Common::log(array($e->getCode(), $e->getMessage()), 'gain.log');
			return false;
		}
	}
	
	public static function incrementTotalSliver($sliver, $out_uid) {
		if (!$sliver || !$out_uid) return false;
		return self::_getDao()->increment('total_sliver_coin', array('out_uid'=>$out_uid), Common::money($sliver));
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
		return self::_getDao()->getBy(array('username'=>$username));
	}
	
	/**
	 * get user info by out_uid
	 * @param string $out_uid
	 * @return boolean|mixed
	 */
	public static function getUserByOutUid($out_uid) {
		if(!$out_uid) return false;
		return self::_getDao()->getBy(array('out_uid'=>$out_uid));
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
	 * @param array $uids
	 * @return boolean
	 */
	public static function getUserByUids($uids){
		if (!count($uids)) return false;
		return self::_getDao()->getUserByUids($uids);
	}
	
	/**
	 * update user want_num
	 */
	public function want($uid) {
		return self::_getDao()->increment('want_num', array('id'=>$uid));
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
	 * 批量修改免单数
	 */
	public static function updateFreeNumberByIds($ids) {
		if (!is_array($ids)) return false;
		return self::_getDao()->updateFreeNumberByIds($ids);
	}
	
	/**
	 * 更新用户订单数
	 * @param int $order_id
	 * @return boolean
	 */
	public static function updateOrderNum($out_uid) {
		if (!$out_uid) return false;
		return self::_getDao()->updateOrderNum($out_uid);
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
		$data['last_login_time'] = Common::getTime();
		$data['status'] = 1;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	public static function getUserSliverBetween($min_sliver, $max_sliver) {
		if (!$min_sliver || !$max_sliver) return false;
		return self::_getDao()->getUserSliverBetween($min_sliver, $max_sliver);
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
	public static function login($username, $password) {
		$result = self::checkUser($username, $password);
		if (!$result || Common::isError($result)) return $result;
		self::cookieUser($result);
		return true;
	}
	
	/**
	 * 
	 * logout
	 */
	public static function logout() {
		return Util_Cookie::delete(self::$cookieName,'/', self::getDomain());
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
		$str .= $userInfo['username'] . '\t';
		$str .= $userInfo['id'] . '\t';
		$str .= $userInfo['out_uid'] . '\t';
		
		$cookieStr = self::_cookieEncrypt($str);
		Util_Cookie::set(self::$cookieName, $cookieStr, true, Common::getTime() + self::$cookieTime, '/', self::getDomain());
		//cookie user sid
		if ($userInfo['taobao_mobile_token']) self::cookieUserSid($userInfo['taobao_mobile_token'], Common::getTime() + self::$cookieTime);
		
		//更新最后登录时间
		self::updateUser(array('last_login_time'=>Common::getTime()), $userInfo['id']);
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
	 * @param unknown_type $username
	 * @param unknown_type $password
	 */
	public static function checkUser($username, $password) {
		if (!$username || !$password) return false;
		$userInfo = self::getUserByName($username);
		if (!$userInfo)  return Common::formatMsg(-1, '用户不存在.');
		//更新最后登录时间
		self::updateUser(array('last_login_time'=>Common::getTime()), $userInfo['id']);
		return $userInfo;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $password
	 * @param unknown_type $hash
	 */
	private static function _password($password, $hash) {
		return md5(md5($password) . $hash);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['out_uid'])) $tmp['out_uid'] = $data['out_uid'];
		if(isset($data['username'])) $tmp['username'] = $data['username'];
		if(isset($data['password'])) {
			list($tmp['hash'], $tmp['password']) = self::_cookPasswd($data['password']); 
		}
		if(isset($data['register_time'])) $tmp['register_time'] = $data['register_time'];
		if(isset($data['realname'])) $tmp['realname'] = $data['realname'];
		if(isset($data['mobile'])) $tmp['mobile'] = $data['mobile'];
		if(isset($data['isgain'])) $tmp['isgain'] = intval($data['isgain']);
		if(isset($data['sex'])) $tmp['sex'] = $data['sex'];
		if(isset($data['birthday'])) $tmp['birthday'] = $data['birthday'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['last_login_time'])) $tmp['last_login_time'] = $data['last_login_time'];
		if(isset($data['taobao_nick'])) $tmp['taobao_nick'] = $data['taobao_nick'];
		if(isset($data['taobao_session'])) $tmp['taobao_session'] = $data['taobao_session'];
		if(isset($data['taobao_refresh'])) $tmp['taobao_refresh'] = $data['taobao_refresh'];
		if(isset($data['taobao_mobile_token'])) $tmp['taobao_mobile_token'] = $data['taobao_mobile_token'];
		if(isset($data['taobao_refresh_time'])) $tmp['taobao_refresh_time'] = $data['taobao_refresh_time'];
		if(isset($data['taobao_refresh_expires'])) $tmp['taobao_refresh_expires'] = $data['taobao_refresh_expires'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Admin_Dao_User
	 */
	private static function getDomain() {
		$domain = str_replace('http://','',Yaf_Application::app()->getConfig()->webroot);
		if($number = strrpos($domain,':')) $domain = Util_String::substr($domain, 0, $number);
		return $domain;
	}
	
	/**
	 * 
	 * @return Gc_Dao_User
	 */
	private static function _getDao() {
		return Common::getDao("Gc_Dao_User");
	}
	
}
