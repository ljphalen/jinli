<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
class Account_Service_User{
	
	/**
	 * 判断当前用户是否在线
	 * @param string $uname
	 * @param string $imei
	 * @param string $type [uname|uuid]
	 * @return boolean
	 */
	public static function checkOnline($data, $imei="", $type='uname'){
		$time = Common::getTime();
		//v1.5.4增加使用uuid判断用户是否登陆
		if(!$data) return false;
		$user = self::getUser(array($type=>$data));
		//用户不存在
		if (!$user) return false;
		//客户端-设备登录来源检测-保证只有最后一次登陆的设备有效
		if ($imei && ($imei != $user['imei'])) return false;
		//账户不在线
		if (!$user['online']) return false;
		//登陆过身份信息 15天 过期
		$lltime = $user['last_login_time'];
		if ($time > ($lltime + 86400 * 15)) return false;
		return true;
	}
	
	/**
	 * 判断网页版用户是否在线,换绑用户需要重新登陆。
	 * GAME-TC :md5('PASSPORT:'.$uuid.':'.$uname).':'.$uuid
	 * @return boolean|array()
	 */
	public static function checkOnline2(){
		$tgc= Util_Cookie::get('GAME-TGC', true);
		if(!$tgc) return false;
		list($token, $uuid) = explode(':', $tgc);
		if(!$uuid) return false;
		
		$user = self::getUser(array('uuid'=>$uuid));
		//用户不存在
		if (!$user) return false;
		//tgc保证未被修改-并且用户未换换绑账号
		if ($token != md5('PASSPORT:' . $uuid . ':' . $user['uname'])) return false;
		
		$time = Common::getTime();
		//账户不在线
		if (!$user['online']) return false;
		//登陆过身份信息 7天 过期
		$lltime = $user['last_login_time'];
		if ($time > ($lltime + 86400 * 7)) return false;
		//用户信息表
		$userInfo = self::getUserInfo(array('uname'=>$user['uname']));
		$data = array('uuid'=>$user['uuid'], 'uname'=>$user['uname'], 'nickname' => $userInfo['nickname'], 'optime' => $time);
		return $data ;
	}
	
	/**
	 * 获取指定用户信息
	 * @param array $params
	 * @return boolean
	 */
	public static function getUser($params){
		if (!is_array($params)) return false;
		return self::_getUserDao()->getBy($params);
	}
	
	public static function getUsersList($params) {
		if (!is_array($params)) return false;
		return self::_getUserDao()->getsBy($params);
	}
	
	/**
	 * 获取指定用户详细信息
	 * @param array $params
	 * @return boolean
	 */
	public static function getUserInfo($params){
		if (!is_array($params)) return false;
		return self::_getInfoDao()->getBy($params);
	}
	
	/**
	 * 获取批量用户信息
	 * @param array $params
	 * @return boolean
	 */
	public static function getUsers($params){
		if (!is_array($params)) return false;
		return self::_getInfoDao()->getsBy($params);
	}
	
	/**
	 * 获取单条【注册|登陆|检测|退出】日志
	 * @param array $params
	 * @return boolean
	 */
	public static function getUserLog($params, $orderBy=array()){
		if (!is_array($params)) return false;
		return self::_getLogDao()->getBy($params, $orderBy);
	}
	
	/**
	 * 获得帐号信息列表
	 * @param number $page
	 * @param number $limit
	 * @param unknown $params
	 * @return multitype:unknown multitype:
	 */
	public static function getUserList($page = 1, $limit = 10, $params = array()) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getUserDao()->getList($start, $limit, $params,array('reg_time'=>'DESC', 'id' => 'DESC'));
		$total = self::_getUserDao()->count($params);
		return array($total, $ret);
	}
	
	public static function getUserInfoList($page = 1, $limit = 10, $params = array()) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getInfoDao()->getList($start, $limit, $params,array('id' => 'DESC'));
		$total = self::_getInfoDao()->count($params);
		return array($total, $ret);
	}
	
	
	/**
	 * 添加用户信息
	 * @param array $data
	 * @return
	 */
	public static function addUser($data){
		if (!is_array($data)) return false;
		$data = self::_cookUserData($data);
		return self::_getUserDao()->insert($data);
	}
	
	/**
	 * 更新用户信息
	 * @param unknown $data
	 * @param unknown $params
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updateUser($data, $params) {
		if (!is_array($data) || !is_array($params)) return false;
		$data = self::_cookUserData($data);
		return self::_getUserDao()->updateby($data, $params);
	}
	
	/**
	 * 添加用户详细信息
	 * @param array $data
	 * @return
	 */
	public static function addUserInfo($data){
		if (!is_array($data)) return false;
		$data = self::_cookInfoData($data);
		return self::_getInfoDao()->insert($data);
	}
	
	/**
	 * 更新用户详细信息
	 * @param unknown $data
	 * @param unknown $params
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updateUserInfo($data, $params) {
		if (!is_array($data) || !is_array($params)) return false;
		$data = self::_cookInfoData($data);
		return self::_getInfoDao()->updateby($data, $params);
	}
	
	/**
	 * 添加【注册|登陆|退出】日志
	 * @param array $data
	 * @return 
	 */
	public static function addLog($data){
		if (!is_array($data)) return false;
		$data = self::_cookLogData($data);
		return self::_getLogDao()->insert($data);
	}
    
	/**
	 * 
	 * @param number $page
	 * @param number $limit
	 * @param unknown $params
	 * @return multitype:unknown multitype:
	 */
	public static function getUserLogList($page = 1, $limit = 10, $params = array()) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getLogDao()->getList($start, $limit, $params,array('id' => 'DESC'));
		$total = self::_getLogDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 更新【注册|登陆|退出】日志
	 * @param array $data
	 * @return
	 */
	public static function updateByUserLog($data, $params){
		if (!is_array($data)) return false;
		$data = self::_cookLogData($data);
		return self::_getLogDao()->updateby($data, $params);
	}	
	
	/**
	 * 获取用户的登录日志
	 * @param unknown_type $params
	 * @param unknown_type $orderBy
	 * @return boolean|Ambigous <boolean, mixed, multitype:>
	 */
	public static function getsUserLoginLog($params, $orderBy = array('id'=>'DESC')){
		if (!is_array($params)) return false;
		return self::_getLogDao()->getsBy($params, $orderBy);
		
		
	}
	
	/**
	 * 获取用户喜欢的所有分类
	 * @param array $params
	 */
	public static function getFavorCategory($params){
		if (!is_array($params)) return false;
		return self::_getFavorCategoryDao()->getsBy($params);
	}
	
	/**
	 * 保存用户喜欢的分类
	 * @param array $data
	 * @param array $data
	 * @return boolean
	 */
	public static function saveFavorCategory($uuid, $data){
		if ((!$uuid) || (!is_array($data))) return false;
		//清理旧的用户分类数据
		self::_getFavorCategoryDao()->deleteBy(array('uuid'=>$uuid));
		//添加用户id
		$time = Common::getTime();
		foreach ($data as $value){
			self::_getFavorCategoryDao()->insert(array('uuid'=>$uuid,'category_id'=>$value, 'create_time'=> $time));
		}
		return true;
	}
	


	/**
	 * 添加用户积分
	 * @param int $points
	 * @param array $params
	 * @return
	 */
	public static function addUserPoint($points, $params) {
		if(!is_array($params) || !$points)	return false;
		return self::_getInfoDao()->addPoints($points, $params);
	}
	

	/**
	 * 减少用户积分
	 * @param int $points
	 * @param array $params
	 * @return
	 */
	public static function subtractUserPoint($points, $params) {
		if(!is_array($params) || !$points)	return false;
		return self::_getInfoDao()->subtractPoints($points, $params);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param array $data
	 */
	private static function _cookUserData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = intval($data['id']);
		if(isset($data['uuid'])) $tmp['uuid'] = $data['uuid'];
		if(isset($data['passkey'])) $tmp['passkey'] = $data['passkey'];
		if(isset($data['uname'])) $tmp['uname'] = $data['uname'];
		if(isset($data['imei'])) $tmp['imei'] = $data['imei'];
		if(isset($data['client'])) $tmp['client'] = $data['client'];
		if(isset($data['web'])) $tmp['web'] = $data['web'];
		if(isset($data['reg_time'])) $tmp['reg_time'] = $data['reg_time'];
		if(isset($data['last_login_time'])) $tmp['last_login_time'] = $data['last_login_time'];
		if(isset($data['online'])) $tmp['online'] = $data['online'];
		if(isset($data['adult'])) $tmp['adult'] = $data['adult'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		return $tmp;
	}
	
	/**
	 * 
	 * @param unknown $data
	 * @return multitype:number unknown
	 */
	private static function _cookInfoData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = intval($data['id']);
		if(isset($data['uuid'])) $tmp['uuid'] = $data['uuid'];
		if(isset($data['uname'])) $tmp['uname'] = $data['uname'];
		if(isset($data['nickname'])) $tmp['nickname'] = $data['nickname'];
		if(isset($data['sex'])) $tmp['sex'] = $data['sex'];
		if(isset($data['avatar'])) $tmp['avatar'] = $data['avatar'];
		if(isset($data['realname'])) $tmp['realname'] = $data['realname'];
		if(isset($data['address'])) $tmp['address'] = $data['address'];
		if(isset($data['receiver'])) $tmp['receiver'] = $data['receiver'];
		if(isset($data['receiverphone'])) $tmp['receiverphone'] = $data['receiverphone'];
		if(isset($data['points'])) $tmp['points'] = $data['points'];
		if(isset($data['coin'])) $tmp['coin'] = $data['coin'];
		return $tmp;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param array $data
	 */
	private static function _cookLogData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = intval($data['id']);
		if(isset($data['uuid'])) $tmp['uuid'] = $data['uuid'];
		if(isset($data['uname'])) $tmp['uname'] = $data['uname'];
		if(isset($data['mode'])) $tmp['mode'] = $data['mode'];
		if(isset($data['act'])) $tmp['act'] = $data['act'];
		if(isset($data['device'])) $tmp['device'] = $data['device'];
		if(isset($data['game_ver'])) $tmp['game_ver'] = $data['game_ver'];
		if(isset($data['rom_ver'])) $tmp['rom_ver'] = $data['rom_ver'];
		if(isset($data['android_ver'])) $tmp['android_ver'] = $data['android_ver'];
		if(isset($data['pixels'])) $tmp['pixels'] = $data['pixels'];
		if(isset($data['channel'])) $tmp['channel'] = $data['channel'];
		if(isset($data['network'])) $tmp['network'] = $data['network'];
		if(isset($data['imei'])) $tmp['imei'] = $data['imei'];
		if(isset($data['sp'])) $tmp['sp'] = $data['sp'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		return $tmp;
	}
	
	/**
	 * 
	 * @param unknown $data
	 */
	private static function _cookFavorCategoryData($data){
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = intval($data['id']);
		if(isset($data['uuid'])) $tmp['uuid'] = $data['uuid'];
		if(isset($data['category_id'])) $tmp['category_id'] = $data['category_id'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		return $tmp;
	}
	
	/**
	 *
	 * @return Account_Dao_User
	 */
	private static function _getUserDao() {
		return Common::getDao("Account_Dao_User");
	}
	
	/**
	 *
	 * @return Account_Dao_UserInfo
	 */
	private static function _getInfoDao() {
		return Common::getDao("Account_Dao_UserInfo");
	}
	
	/**
	 * @return Account_Dao_FavorCategory
	 */
	private static function _getFavorCategoryDao(){
		return Common::getDao("Account_Dao_FavorCategory");
	}
	
	/**
	 *
	 * @return Account_Dao_UserLog
	 */
	private static function _getLogDao() {
		return Common::getDao("Account_Dao_UserLog");
	}
}