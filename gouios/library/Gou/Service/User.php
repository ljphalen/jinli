<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Gou_Service_User extends Common_Service_Base{
	static private $hash = 'xysoza'; //hash值
	static private $cookieTime = 2592000; //默认设置为30天
	static private $cookieName = 'GIONEE-GOU-USER';
	static private $cookieSidName = 'GIONEE-GOU-SID';

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
	 * @param string $out_uid
	 * @return array
	 */
	public static function getByOutUid($out_uid) {
		if (!$out_uid) return false;
		return self::_getDao()->getBy(array('out_uid'=>$out_uid));
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
	 * get by uids
	 * @param array $uids
	 */
	public static function getListByUids($uids) {
		if (!count($uids)) return false;
		return self::_getDao()->getListByUids($uids);
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
	public static function gain($out_uid, $uid) {
		if (!$out_uid || !$uid) return false;
		try {
			$trans = parent::beginTransaction();
			if (!$trans) throw new Exception("begin transaction failed.", -201);
			
			list($count, $rule) = Activity_Service_Coin::getCanUses(0, 1);
			if (!$count) throw new Exception("get rule failed.", -205);
			if ($count) {
				$rule = $rule[0];
				$coin = $rule['first'];
				$msg = sprintf("注册送红包（购物红包以银币的形式赠送，注册即到账%s银币，剩余%s银币将分%s次陆续赠送）。", $rule['first'], $rule['later'] * $rule['times'], $rule['times']);
			}
			$ret = Api_Gionee_Pay::coinAdd(array(
					'out_uid'=>$out_uid,
					'coin_type'=>'2',
					'coin'=>$coin,
					'msg'=>$msg));
			if($ret['status'] != 200) throw new Exception('gionee pay coin add failed.', -202);
				
			$ret = Gou_Service_User::updateUser(array('freeze_sliver_coin'=>$rule['later'].'/'.$rule['times']), $uid);
			if (!$ret) throw new Exception('update user total sliver coin faild.');
				
			$ret = Gou_Service_User::updateUser(array('isgain'=>1), $uid);
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
	
	public static function laterSliverCoin($uid) {
		if (!$uid) return false;
		try {
			$trans = parent::beginTransaction();
			if (!$trans) throw new Exception("begin transaction failed.", -301);
				
			$user = Gou_Service_User::getUser($uid);
			if (!$user) throw new Exception("get user info failed.", -302);
			if (strpos($user['freeze_sliver_coin'], '/') === false) throw new Exception("the user has no freeze_silver_coin.", -303);
			$coin_rule = explode('/', $user['freeze_sliver_coin']);
			/* print_r(array(
					'out_uid'=>$user['out_uid'],
					'coin_type'=>'2',
					'coin'=>$coin_rule[0],
					'msg'=>"恭喜您获得$coin_rule[0]银币（注册即送100银币，本周赠送给您20银币）。"));
			exit; */
			$ret = Api_Gionee_Pay::coinAdd(array(
					'out_uid'=>$user['out_uid'],
					'coin_type'=>'2',
					'coin'=>$coin_rule[0],
					'msg'=>"恭喜您获得".$coin_rule[0]."银币（注册即送银币，本周赠送给您".$coin_rule[0]."银币）。"));
			if (!$ret) throw new Exception('update gain user faild');
			$coin_rule[1] -= 1;
			
			if ($coin_rule[1] == 0) {
				$ret = Gou_Service_User::updateUser(array('freeze_sliver_coin'=>0), $uid);
			} else {
				$ret = Gou_Service_User::updateUser(array('freeze_sliver_coin'=>implode($coin_rule, '/')), $uid);
			}
			if (!$ret) throw new Exception('update freeze_sliver_coin status failed.', -302);
		
			$ret = parent::commit();
			if (!$ret) throw new Exception("transactoin commit failed.", -303);
			return true;
		} catch(Exception $e) {
			parent::rollBack();
			Common::log(array($e->getCode(), $e->getMessage()), 'gain.log');
			return false;
		}
	}
	

	public static function getHasFreezeUser($page, $limit) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getFreezeUser($start, $limit);
		$total = self::_getDao()->getCountFreezeUser();
		return array($total, $ret);
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
	 * @param array $uids
	 * @return boolean
	 */
	public static function getUserByUids($uids){
		if (!count($uids)) return false;
		return self::_getDao()->getUserByUids($uids);
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
	 * update user want_num
	 */
	public function want($uid) {
		return self::_getDao()->increment('want_num', array('id'=>$uid));
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
	public static function login($out_uid, $password) {
		$result = self::checkUser($out_uid, $password);
		if (!$result || Common::isError($result)) return $result;
		return self::cookieUser($result);
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
	 * @return boolean
	 */
	public static function getToday() {
		$is_gou_user = Util_Cookie::get('ISGOUUSER', true);
		if ($is_gou_user) return false;
		return Util_Cookie::set('ISGOUUSER', 1, true, strtotime(date('Y-m-d 23:59:59')), '/', self::getDomain());
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
		$str .= $userInfo['username'] . '\t';
		$str .= $userInfo['id'] . '\t';
		$str .= $userInfo['out_uid'] . '\t';
		
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
	 * @param unknown_type $username
	 * @param unknown_type $password
	 */
	public static function checkUser($out_uid, $password) {
		if (!$out_uid) return false;
		$userInfo = self::getByOutUid($out_uid);
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
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['username'])) $tmp['username'] = $data['username'];
		if(isset($data['out_uid'])) $tmp['out_uid'] = $data['out_uid'];
		if(isset($data['password'])) {
			list($tmp['hash'], $tmp['password']) = self::_cookPasswd($data['password']); 
		}
		if(isset($data['register_time'])) $tmp['register_time'] = $data['register_time'];
		if(isset($data['realname'])) $tmp['realname'] = $data['realname'];
		if(isset($data['mobile'])) $tmp['mobile'] = $data['mobile'];
		if(isset($data['isgain'])) $tmp['isgain'] = $data['isgain'];
		if(isset($data['sex'])) $tmp['sex'] = $data['sex'];
		if(isset($data['birthday'])) $tmp['birthday'] = $data['birthday'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['last_login_time'])) $tmp['last_login_time'] = $data['last_login_time'];
		if(isset($data['freeze_sliver_coin'])) $tmp['freeze_sliver_coin'] = $data['freeze_sliver_coin'];
		if(isset($data['taobao_nick'])) $tmp['taobao_nick'] = $data['taobao_nick'];
		if(isset($data['taobao_session'])) $tmp['taobao_session'] = $data['taobao_session'];
		if(isset($data['taobao_refresh'])) $tmp['taobao_refresh'] = $data['taobao_refresh'];
		if(isset($data['taobao_mobile_token'])) $tmp['taobao_mobile_token'] = $data['taobao_mobile_token'];
		if(isset($data['taobao_refresh_time'])) $tmp['taobao_refresh_time'] = $data['taobao_refresh_time'];
		if(isset($data['taobao_refresh_expires'])) $tmp['taobao_refresh_expires'] = $data['taobao_refresh_expires'];
		
		if(isset($data['order_num'])) $tmp['order_num'] = $data['order_num'];
		if(isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
		if(isset($data['end_time'])) $tmp['end_time'] = $data['end_time'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Gou_Dao_User
	 */
	private static function _getDao() {
		return Common::getDao("Gou_Dao_User");
	}
}
