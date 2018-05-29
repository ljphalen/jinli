<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * @author rainkid
 *
 */
class Gionee_Service_User {
	static private $hash       = 'gixsza'; //hash值
	static private $cookieTime = 2592000; //默认设置为30天
	static private $cookieName = 'GioneeUser';

	/**
	 *
	 * 获取所有用户
	 */
	public static function getAllUser() {
		return self::_getDao()->getAllUser();
	}

	public static function getsBy($params = array(), $order = array()) {
		return self::_getDao()->getsBy($params, $order);
	}
	public static function getBy($params = array(), $order = array()) {
		return self::_getDao()->getBy($params, $order);
	}

	/**
	 *
	 * 分页取用户列表
	 *
	 * @param array $params
	 * @param int   $page
	 * @param int   $limit
	 *
	 * @return array
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $order = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret   = self::_getDao()->getList($start, $limit, $params, $order);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 *
	 * Enter description here ...
	 *
	 * @param array $params
	 * @param int   $page
	 * @param int   $limit
	 *
	 * @return array
	 */
	public static function search($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start    = ($page - 1) * $limit;
		$sqlWhere = self::_getDao()->_cookParams($params);
		$ret      = self::_getDao()->searchBy($start, $limit, $sqlWhere, array('id' => 'DESC'));
		$total    = self::_getDao()->searchCount($sqlWhere);
		return array($total, $ret);
	}


	/**
	 *
	 * 分页取用户列表
	 *
	 * @param array $params
	 * @param int   $page
	 * @param int   $limit
	 *
	 * @return array
	 */
	public static function getListByTime($page = 1, $limit = 10) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret   = self::_getDao()->getListByTime($start, $limit);
		$total = self::_getDao()->countByTime();
		return array($total, $ret);
	}

	/**
	 *
	 * get by uids
	 *
	 * @param array $uids
	 *
	 * @return array
	 */
	public static function getListByUids($uids) {
		if (!count($uids)) return false;
		return self::_getDao()->getListByUids($uids);
	}

	/**
	 *
	 * @param string $out_uid
	 *
	 * @return array
	 */
	public static function getByOutUid($out_uid) {
		if (!$out_uid) return false;
		return self::_getDao()->getBy(array('out_uid' => $out_uid));
	}

	/**
	 *
	 * 读取一条用户信息
	 *
	 * @param int $id
	 *
	 * @return array
	 */
	public static function getUser($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

	/**
	 *
	 * 根据用户名查用户信息
	 *
	 * @param srting username
	 *
	 * @return array
	 */
	public static function getUserByName($username) {
		if (!$username) return false;
		return self::_getDao()->getByUserName($username);
	}


	/**
	 *
	 * @param array $uids
	 *
	 * @return boolean
	 */
	public static function getUserByUids($uids) {
		if (!count($uids)) return false;
		return self::_getDao()->getUserByUids($uids);
	}

	/**
	 * update user want_num
	 */
	public function signin($uid) {
		return self::_getDao()->increment('signin_num', array('id' => $uid));
	}

	/**
	 * get user info by out_uid
	 *
	 * @param string $out_uid
	 *
	 * @return boolean|mixed
	 */
	public static function getUserBy($params) {
		if (!is_array($params)) return false;
		$data = self::_cookData($params);
		return self::_getDao()->getBy($data);
	}

	/**
	 *
	 * 更新用户信息
	 *
	 * @param array $data
	 * @param int   $id
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
	 *
	 * @param int $id
	 */
	public static function deleteUser($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * 会员总数
	 */
	public static function getCount() {
		return self::_getDao()->getCount();
	}

	public static function count($params){
		return self::_getDao()->count($params);
	}
	/**
	 *
	 * 签到总数
	 */
	public static function getSignCount() {
		return self::_getDao()->getSignCount();
	}

	/**
	 *
	 * 添加用户并获得该用户ID
	 *
	 * @param array $data
	 */
	public static function addUser($data) {
		if (!is_array($data)) return false;
		$data['register_time']   = Common::getTime();
		$data['last_login_time'] = Common::getTime();
		$data['status']          = 1;
		$data                    = self::_cookData($data);
		$res                     = self::_getDao()->insert($data);
		return $res ? self::_getDao()->getLastInsertId() : false;
	}

	/**
	 *
	 * cookpasswd
	 *
	 * @param string $password
	 */
	static private function _cookPasswd($password) {
		$hash   = Common::randStr(6);
		$passwd = self::_password($password, $hash);
		return array($hash, $passwd);
	}

	/**
	 *
	 * login
	 *
	 * @param string $username
	 * @param string $passwd
	 */
	public static function login($out_uid, $password) {
		if (!$out_uid) return false;
		$userInfo = self::getByOutUid($out_uid);
		if (!$userInfo) return false;
		//更新最后登录时间
		self::updateUser(array('last_login_time' => Common::getTime()), $userInfo['id']);
		return self::cookieUser($userInfo);
	}

	/**
	 *
	 * logout
	 */
	public static function logout() {
		return Util_Cookie::delete(self::$cookieName, '/', self::getDomain());
	}

	public static function checkLogin($callback_url) {
		$user = Gionee_Service_User::ckLogin();
		if (!$user) {
			$webroot  = Common::getCurHost();
			$callback = $webroot . '/user/login/login';
			$url      = Api_Gionee_Oauth::requestToken($callback);
			Util_Cookie::set('GIONEE_LOGIN_REFER', $callback_url, true, Common::getTime() + (5 * 3600), '/');
			header('Location: ' . $url);
			exit;
		}
		return $user;
	}

	static public function checkUser($info, $from = 0) {
		$user     = Gionee_Service_User::getByOutUid($info['u']);
		$username = str_replace('+86', '', $info['tn']);
		$upData   = array('last_login_time' => Common::getTime());
		$uaArr    = Util_Http::ua();
		if (empty($user['id'])) {
			$mobile = '';
			if (Common::checkIllPhone($username)) {
				$mobile = $username;
			}
			$parms = array(
				'username' => $username,
				'mobile'   => $mobile,
				'out_uid'  => $info['u'],
				'imei_id'  => $uaArr['uuid']
			);
			if (!empty($from)) {
				$parms['come_from'] = $from;
			}
			$ret = Gionee_Service_User::addUser($parms);
			if (!$ret) {
				return false;
			}
			$user = Gionee_Service_User::getByOutUid($info['u']);
		} else {
			//用户存在　手机号码为空　用户名是手机号码
			if (empty($user['mobile']) && Common::checkIllPhone($user['username'])) {
				$upData['mobile'] = $user['username'];
				$user['mobile']   = $user['username'];
			}else if(!empty($user['username']) && $user['username'] != $username){ //用户更换绑定手机号
					$voipUser = Gionee_Service_VoIPUser::getBy(array('user_phone'=>$user['username']));
					if(!empty($voipUser)){
						$s = Gionee_Service_VoIPUser::update(array('user_phone'=>$username), $voipUser['id']);
					}
					$upData['mobile'] = $username;
					$upData['username'] = $username;
					$user['username']  = $user['mobile'] = $username;
					Gionee_Service_VoIPUser::getInfoByPhone($username,true);
				}
		}
		$ret = Gionee_Service_User::updateUser($upData, $user['id']);
		self::getUserInfo($user['id'],true);
		Gionee_Service_User::cookieUser($user);
		return $user;
	}


	/**
	 * cookie字符串加密解密方式
	 *
	 * @param string $str    加密方式
	 * @param string $encode ENCODE-加密|DECODE-解密
	 *
	 * @return array
	 */
	static private function _cookieEncrypt($str, $encode = 'ENCODE') {
		if ($encode == 'ENCODE') return Common::encrypt($str);
		$result = Common::encrypt($str, 'DECODE');
		return explode('\t', $result);
	}


	/**
	 * cookie添加
	 *
	 * @param string $userInfo 用户信息
	 *
	 * @return array
	 */
	public static function cookieUser($userInfo) {
		$str = Common::getTime() . '\t';
		$str .= $userInfo['username'] . '\t';
		$str .= $userInfo['id'] . '\t';
		$str .= $userInfo['out_uid'] . '\t';

		$cookieStr = self::_cookieEncrypt($str);
		Util_Cookie::set(self::$cookieName, $cookieStr, true, Common::getTime() + self::$cookieTime, '/', self::getDomain());
		//更新最后登录时间
		//return self::updateUser(array('last_login_time' => Common::getTime()), $userInfo['id']);
		return true;
	}


	/**
	 *
	 * @return string
	 */
	private static function getDomain() {
		$domain = str_replace('http://', '', Common::getCurHost());
		if ($number = strrpos($domain, ':')) {
			$domain = Util_String::substr($domain, 0, $number);
		}
		return $domain;
	}


	/**
	 * 检查cookie信息
	 */
	public static function ckLogin() {
		$ret    = false;
		$cookie = Util_Cookie::get(self::$cookieName, true);
		if (empty($cookie)) {
			return $ret;
		}
		$ckVals = self::_cookieEncrypt($cookie, 'DECODE');
		if (!empty($ckVals)) {
			$ret = array('username' => $ckVals[1], 'id' => $ckVals[2], 'out_uid' => $ckVals[3]);
		}
		return $ret;
	}

    public static function getCurUserInfoForTest($mobile,$sync = false) {
        $userInfo = self::getUserInfoForTest($mobile,$sync);
        if($userInfo['experience_level'] == 1){ //首次登陆时记录
            User_Service_ExperienceLevelLog::addFirstLevelData($userInfo['id'],$userInfo['experience_level']);
        }
       // User_Service_UserVisit::initInfo($userInfo['id']);
        return $userInfo;
    }

	public static function getCurUserInfo($sync = false,$testMobile='') {
        if(empty($testMobile)){
            $ckArr = self::ckLogin();
            if (empty($ckArr['id'])) {
                return false;
            }
            $userInfo = self::getUserInfo($ckArr['id'],$sync);
            if($userInfo['experience_level'] == 1){ //首次登陆时记录
                User_Service_ExperienceLevelLog::addFirstLevelData($userInfo['id'],$userInfo['experience_level']);
            }
           // User_Service_UserVisit::initInfo($userInfo['id']);
        }else{
            $userInfo = Gionee_Service_User::getCurUserInfoForTest($testMobile,$sync);
        }
		return $userInfo;
	}
	
	public static function getUserInfo($uid,$sync=false){
		$uKey     = "USER:INFO:KEY" . $uid;
		$userInfo = Common::getCache()->get($uKey);
		if (empty($userInfo) || $sync) {
			$userInfo = self::getUser($uid);
			Common::getCache()->set($uKey, $userInfo, Common::T_ONE_DAY);
		}
		return $userInfo;
	}

    public static function getUserInfoForTest($username,$sync=false){
        $uKey     = "USER:INFO:KEY" . $username;
        $userInfo = Common::getCache()->get($uKey);
        if (empty($userInfo) || $sync) {
            $userInfo = self::getUserByName($username);
            Common::getCache()->set($uKey, $userInfo, Common::T_ONE_DAY);
            self::getUserInfo($userInfo['id'],true);
        }
        return $userInfo;
    }


	/**
	 * 每天新增注册用户数
	 */
	public static function countByDays($page, $pageSize, $where = array()) {
		if (!is_array($where)) return false;
		$sumInfo  = self::_getDao()->countBy($where);
		$dataList = self::_getDao()->countByDays(($page - 1) * $pageSize, $pageSize, $where);
		return array($sumInfo, $dataList);
	}

	/**
	 * @param string $password
	 * @param string $hash
	 *
	 * @return string
	 */
	private static function _password($password, $hash) {
		return md5(md5($password) . $hash);
	}

	public static function updatesBy($field,$values,$data){
		return self::_getDao()->updates($field, $values, $data);
	}

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	private static function _cookData($data) {
		$tmp = array();
		if (isset($data['id'])) $tmp['id'] = $data['id'];
		if (isset($data['username'])) $tmp['username'] = $data['username'];
		if (isset($data['out_uid'])) $tmp['out_uid'] = $data['out_uid'];
		if (isset($data['mobile'])) $tmp['mobile'] = $data['mobile'];
		if (isset($data['password'])) {
			list($tmp['hash'], $tmp['password']) = self::_cookPasswd($data['password']);
		}

		if (isset($data['register_time'])) $tmp['register_time'] = $data['register_time'];
		if (isset($data['nickname'])) $tmp['nickname'] = $data['nickname'];
		if (isset($data['sex'])) $tmp['sex'] = $data['sex'];
		if (isset($data['birthday'])) $tmp['birthday'] = $data['birthday'];
		if (isset($data['status'])) $tmp['status'] = $data['status'];
		if (isset($data['last_login_time'])) $tmp['last_login_time'] = $data['last_login_time'];
		if (isset($data['qq'])) $tmp['qq'] = $data['qq'];
		if (isset($data['signin_num'])) $tmp['signin_num'] = $data['signin_num'];
		if (isset($data['email'])) $tmp['email'] = $data['email'];
		if (isset($data['model'])) $tmp['model'] = intval($data['model']);

		if (isset($data['user_level'])) $tmp['user_level'] = $data['user_level'];
		if (isset($data['user_group'])) $tmp['user_group'] = $data['user_group'];
		if (isset($data['province_id'])) $tmp['province_id'] = $data['province_id'];
		if (isset($data['city_id'])) $tmp['city_id'] = $data['city_id'];
		if (isset($data['address'])) $tmp['address'] = $data['address'];

		if (isset($data['register_date'])) $tmp['register_date'] = $data['register_date'];
		if (isset($data['come_from'])) $tmp['come_from'] = $data['come_from'];
		if (isset($data['imei_id'])) $tmp['imei_id'] = $data['imei_id'];
		if (isset($data['is_black_user'])) $tmp['is_black_user'] = $data['is_black_user'];
		if (isset($data['is_frozed'])) $tmp['is_frozed'] = $data['is_frozed'];
		if(isset($data['experience_level'])) $tmp['experience_level'] = $data['experience_level'];
		return $tmp;
	}

	public static function phonevest($page, $limit) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$list  = self::_getDao()->vestList($start, $limit);
		return $list;
	}

	/**
	 *
	 * @return Gionee_Dao_User
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_User");
	}

	static public function upImei($id, $imei_id) {
		$uaUUID = Util_Http::ua('uuid');
		$upData = array();
		if (empty($imei_id) && !empty($uaUUID)) {
			$upData['imei_id'] = $uaUUID;
		} else if (!empty($imei_id) && !empty($uaUUID) && $imei_id != $uaUUID) {
			$upData['imei_id'] = $uaUUID;
		}
		if (!empty($upData)) {//更新用户 imei
			Gionee_Service_User::updateUser($upData, $id);
		}
	}
}
