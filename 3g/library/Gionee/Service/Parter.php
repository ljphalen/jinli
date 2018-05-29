<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Gionee_Service_Parter {
	
	private static $sessionKey = 'CPMONTHCHECK';
	
	public   static $cpSessionName = "USERNAME";
	
	/**
	 *
	 * Enter desAreaiption here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	
	public static function getList($page = 1, $limit = 10, $params = array(),$orderBy=array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret   = self::_getDao()->getList($start, $limit, $params,$orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	public static function getsBy($params = array() , $orderBy = array()) {
		
		return self::_getDao()->getsBy($params,$orderBy);
	}
	
	/**
	 * @param string $data
	 */
	public static function get($id) {
		return self::_getDao()->get($id);
	}
	/**
	 * 
	 */
	public static function getAll($orderBy = array()) {
		return self::_getDao()->getAll($orderBy = array());
	}
	
	public static function isLogin() {
		$session = Common::getSession();
		if (!$session->has(self::$cpSessionName)) return false;
		$sessionInfo = $session->get(self::$cpSessionName);
		$sessionInfo = self::_cookieEncrypt($sessionInfo, 'DECODE');
		if (!$sessionInfo || !$sessionInfo [1] || !$sessionInfo [3]) return false;
		if (!$userInfo = self::_getDao()->getBy(array('account'=>$sessionInfo [1]))) return false;
		if ($sessionInfo [2] != $userInfo ['id'] || $sessionInfo [3] != $userInfo ['password']) {
			return false;
		}
		//self::_cookieUser($userInfo);
		return $userInfo;
	}  
	
	/**
	 * 登陆
	 */
	public static function login($username,$password){
		$ret = self::_checkUser($username, $password);
		if($ret['code'] == -1){
			return $ret;
		}
		self::_cookieUser($ret['data']);
		return  Common::formatMsg(0, '登陆成功.');;
	}
	/**
	 * 检测用户
	 */
	private static function _checkUser($username,$password){
		if(!$username || !$password ) return false;
		$userInfo = self::getBy(array("account"=>$username));
		if(!$userInfo) return Common::formatMsg(-1, '用户不存在.');
		if(md5($password) != $userInfo['password']) return Common::formatMsg(-1, '登陆密码不正确.');
		return Common::formatMsg(0,'',$userInfo);
	}
	
	/**
	 * 设置缓存
	 * @param unknown $userInfo
	 */
	private static function _cookieUser($userInfo){
		$str = Common::getTime() . '\t';
		$str .= $userInfo ['account'] . '\t';
		$str .= $userInfo ['id'] . '\t';
		$str .= $userInfo ['password'] . '\t';
		$sessionStr = self::_cookieEncrypt($str);
		$session    = Common::getSession();
		$session->set(self::$cpSessionName, $sessionStr);
	}
	
	static private function _cookieEncrypt($str, $encode = 'ENCODE') {
		if ($encode == 'ENCODE') return Common::encrypt($str);
		$result = Common::encrypt($str, 'DECODE');
		return explode('\t', $result);
	}
	/**
	 *
	 * Enter desAreaiption here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id']))					$tmp['id'] = $data['id'];
		if (isset($data['name'])) 		$tmp['name'] = $data['name'];
		if (isset($data['account'])) 	$tmp['account'] = $data['account'];
		if (isset($data['password'])) 	$tmp['password'] = $data['password'];
		if (isset($data['status'])) 		$tmp['status'] = $data['status'];
		if (isset($data['created_time'])) 	$tmp['created_time'] = $data['created_time'];
		if (isset($data['edit_time']))	$tmp['edit_time'] = $data['edit_time'];
		return $tmp;
	}
	
	/**
	 * 
	 * @param unknown $data
	 * @param unknown $id
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function update($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	public static function getBy($searchArr = array() , $order = array()) {		
		
		return self::_getDao()->getBy($searchArr , $order);
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
		return self::_getDao()->insert($data);
	}
	
	/**
	 *
	 * @return Gionee_Dao_Area
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_Parter");
	}
	
	/**
	 * check login
	 * @param unknown $input
	 * @return boolean|unknown
	 */
	public function checkPass($input = array()) {
		
		$account = $input['username'];
		$pass	 = $input['password'];
		
		$accountInfo = self::_getDao()->getBy(array('account'=>$account));
		
		if(empty($accountInfo) || 0 !== strcmp($accountInfo['pass'],$pass)) {
			return false;
		}
		
		$session    = Common::getSession();
		$session->set(self::$sessionKey, serialize($accountInfo));
		
		return $accountInfo;
	}
	
	/**
	 * check is login
	 * @return boolean
	 */
	/* public static function isLogin() {		
		$session = Common::getSession();	
		if (!$session->has(self::$sessionKey)) return false;
		$sessionInfo = $session->get(self::$sessionKey);	
		$sessionInfo = unserialize($sessionInfo);
		return !empty($sessionInfo);
	}
	 */
	public static function logout($type=1) {		
		$session = Common::getSession();
		$key =self::$sessionKey;
		switch ($type){
			case 2: {
				$key = self::$cpSessionName;
				break;
			}
			default:break;
		}
		$session->del($key);		
	}
	
	public static function getSessionCP() {		
		$session = Common::getSession();
		if (!$session->has(self::$sessionKey)) return false;
		$sessionInfo = $session->get(self::$sessionKey);		
		$sessionInfo = unserialize($sessionInfo);
		
		return $sessionInfo;
	}
}