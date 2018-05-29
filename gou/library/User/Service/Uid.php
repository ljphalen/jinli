<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class User_Service_Uid extends Common_Service_Base{

	private static $user_uid = array(
		'uid'       => '',
		'scoreid' 	=> '',
		'nickname'  => '',
		'mobile'   	=> '',
	);

    private static $user_type = array(
        0 => '真实用户',
        1 => '虚拟用户'
    );

	/**
	 * 分页取用户列表
	 * @param int $table
	 * @param int $page
	 * @param int $limit
	 * @param array $params
	 * @param array $orderBy
	 * @return array]
	 */
	public static function getList($table = 0, $page = 1, $limit = 10, $params = array(), $orderBy = array()) {
        if(!$table) return false;
		$params = self::_cookData($params);
        unset($params['uid']);
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao(0, '', $table)->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao(0, '', $table)->count($params);
		return array($total, $ret);
	}

	/** 
	 * 读取一条用户信息
	 * @param float $id
	 * @return bool|mixed
	 */
	public static function getUser($id) {
		return self::_getDao($id)->get(floatval($id));
	}
	
	/**
	 * get user info by uid
	 * @param array $params
     * @param int $table 1-10表
	 * @return bool|mixed
	 */
	public static function getBy($params, $table = 0) {
		if(!is_array($params)) return false;
        $data = self::_cookData($params);
        if($table){
            return self::_getDao(0, '', $table)->getBy($data);
        }else{
            unset($data['uid']);
            if(!isset($data['id']) || !$data['id']) return false;
            return self::_getDao($data['id'])->getBy($data);
        }
	}

    /**
     * get users by params
     * @param array $params
     * @param int $table
     * @param array $sort
     * @return array|bool
     */
	public static function getsBy($params, $table = 0, $sort = array()) {
		if(!is_array($params)) return false;
        if($table){
            if(!is_array($sort)) return false;
            return self::_getDao(0, '', $table)->getsBy($params, $sort);
        }else{
            if(!isset($params['uid'])) return false;
            $uids = array();
            if(is_array($params['uid'])) {
                $uids = $params['uid'][1];
            }else{
                $uids[] = $params['uid'];
            }
            unset($params['uid']);
            $users = array();
            foreach($uids as $uid){
                $params['uid'] = $uid;
                $user = self::getBy($params);
                if($user) array_push($users, $user);
            }
            return $users;
        }
	}

    /**
     * (失效)
     * @param $params
     * @return array|bool
     */
    public static function count($params){
        return false;
        if(!is_array($params)) return false;
        $data = self::_cookData($params);
        return self::_getDao()->count($data);
    }

	/**
	 * 更新用户信息
	 * @param array $data
	 * @param float $id
	 * @return bool|int
	 */
	public static function updateUser($data, $id) {
		if (!is_array($data) || !$id) return false;
		$data = self::_cookData($data);
		return self::_getDao($id)->update($data, $id);
	}

	/**
	 * 更新用户信息
	 * @param array $data
	 * @param array $params
	 * @return bool
	 */
	public static function updateUserBy($data, $params) {
		if (!is_array($data) || !is_array($params) || !isset($params['uid'])) return false;
		$data = self::_cookData($data);
		$params = self::_cookData($params);
        unset($params['uid']);
        if(!isset($params['id']) || !$params['id']) return false;
		return self::_getDao($params['id'])->updateBy($data, $params);
	}
	
	/**
	 * del user
	 * @param float $id
	 * @return bool|int
	 */
	public static function deleteUser($id) {
		return self::_getDao($id)->delete(floatval($id));
	}

    /**
     * del user by uid
     * @param string $uid
     * @return bool|int
     */
    public static function deleteUserByUid($uid) {
        $id = self::_hash_crc32($uid);
        self::deleteUser($id);
    }

    /**
	 * add user
     * @param array $data nickname && uid .etc info
     * @return bool|string
     */
    public static function addUser(array $data) {
        if (!is_array($data) || !isset($data['uid'])) return false;
		$data = self::_cookData($data);
        $data['create_time'] = Common::getTime();
		$ret = self::_getDao($data['id'])->insert($data);
		if (!$ret) return false;
		return $data['id'];
	}

    /**
     * 获取虚拟用户
     * @return array|bool
     */
    public static function getVirtualUser(){
        $server_version = Gou_Service_Config::getValue('virtual_Version');
        $file = Common::getConfig('siteConfig', 'dataPath') . 'virtual_users.ini';
        $file_time = filemtime($file);

        if($file_time < $server_version){
            $users = array();
            for($t = 1; $t <= 10; $t++){
                $users = array_merge($users, self::getsBy(array('type' => 1), $t));
            }
            Util_File::write($file, json_encode($users));

        }else{
            $users = json_decode(Util_File::read($file), true);
        }

        return $users;
    }

	/**
     * 通过uid获取用户
	 * @param $uid
	 * @return bool|mixed
	 */
	public static function getByUid($uid){
        $id = self::_hash_crc32($uid);
        return self::getUser($id);
	}

    /**
     * 获取用户格式化信息
     * @param $uid
     * @return array
     */
    public static function getUserFmtByUid($uid){
        if(empty($uid)) return array('', '', '');
        $user = self::getByUid($uid);
        if(!$user) return array('', '', '');
        $user['scoreid'] = self::_createIDByUid($uid);
        $nickname = $user['nickname'] ? $user['nickname'] : $user['scoreid'];
        $attach = Common::getAttachPath();
        $avatar = $user['avatar']? $attach . $user['avatar']:"";
        $scoreid = $user['scoreid'];
        $is_edit = $user['nickname'] ? false : true;

        return array($nickname, $avatar, $scoreid, $is_edit);
    }

    /**
     * 获取多用户格式化信息
     * @param $uids
     * @return array|bool
     */
    public static function getUsersFmtByUid($uids){
        if(!is_array($uids) || empty($uids)) return false;
        $users = self::getsBy(array('uid'=>array('IN', $uids)));
        if(!$users) return false;

        $data = array();
        $attach = Common::getAttachPath();
        $users = Common::resetKey($users,'uid');
        foreach ($uids as $uid) {
            if (empty($users[$uid])) {
                $data[$uid] = array(
                    'nickname' => static::_createIDByUid($uid),
                    'avatar' => '',
                    'uid' => $uid,
                    'scoreid' => static::_createIDByUid($uid)
                );
            }else{
                $item =$users[$uid];
                $nickname = $item['nickname'] ? $item['nickname'] : $item['scoreid'];
                $data[$uid]['nickname'] = $nickname?$nickname:static::_createIDByUid($uid);
                $data[$uid]['avatar'] = $item['avatar']?$attach . $item['avatar']:"";
                $data[$uid]['scoreid'] = static::_createIDByUid($uid);
                $data[$uid]['uid'] = $uid;
            }
        }
        return $data;
    }

    /**
     * 检查user uid是否有记录以及scoreid是否存在, 如果没有, 则创建
     * @param string $system default apk, ios
     * @return array
     */
	public static function getUserInfo($system = 'apk'){
        $uid = $user_uid = '';
		switch($system){
			case 'apk':
				$uid = Common::getAndroidtUid();
				break;
			case 'ios':
				$uid = Common::getIosUid();
				break;
		}
		if($uid){
            $user = User_Service_Uid::getByUid($uid);
            if (empty($user['scoreid'])){
                $user['scoreid'] = self::_createIDByUid($uid);
                $user['uid'] = $uid;
            }
		}

		return array($uid, $user_uid);
	}

    /**
     * 检查user uid是否有记录以及scoreid是否存在, 如果没有, 则创建
     * @param string $system default apk, ios
     * @return array
     */
	public static function checkUid($system = 'apk'){
        $uid = $user_uid = '';
		switch($system){
			case 'apk':
				$uid = Common::getAndroidtUid();
				break;
			case 'ios':
				$uid = Common::getIosUid();
				break;
		}

		if($uid){
            $user = User_Service_Uid::getByUid($uid);
            if($user){
                if(empty($user['scoreid'])){
                    $user_uid['scoreid'] = self::_createIDByUid($uid);
                    User_Service_Uid::updateUser($user_uid, $user['id']);
                }
                $user_uid = $user;
            }else{
                $user_uid = self::$user_uid;
                $user_uid['uid'] = $uid;
                $user_uid['scoreid'] = self::_createIDByUid($uid);
                self::addUser($user_uid);
            }
		}

		return array($uid, $user_uid);
	}

	/**
	 * 创建用户唯一的积分ID
	 * @param string $uid
	 * @param int $length
	 * @param string $prefix
	 * @return string
	 */
	private static function _createIDByUid($uid, $length = 8, $prefix = 'g_'){
		return $prefix . substr(abs(crc32($uid)), 1, $length);
	}

    /**
     * @param $data
     * @return array
     */
    private static function _cookData($data) {
        $tmp = array();
        if (isset($data['id'])) $tmp['id'] = floatval($data['id']);
        if (isset($data['uid'])) {
            $tmp['id'] = self::_hash_crc32($data['uid']);
            $tmp['uid'] = $data['uid'];
        }
        if (isset($data['scoreid'])) $tmp['scoreid'] = $data['scoreid'];
        if (isset($data['nickname'])) $tmp['nickname'] = $data['nickname'];
        if (isset($data['mobile'])) $tmp['mobile'] = $data['mobile'];
        if (isset($data['baidu_uid'])) $tmp['baidu_uid'] = $data['baidu_uid'];
        if (isset($data['baidu_cid'])) $tmp['baidu_cid'] = $data['baidu_cid'];
        if (isset($data['avatar'])) $tmp['avatar'] = $data['avatar'];
        if (isset($data['type'])) $tmp['type'] = intval($data['type']);
        if (isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
        return $tmp;
	}
	
	/**
	 * @param $uid string
	 * @param $id int
	 * @param $table int
	 * @return User_Dao_Uid
	 */
	private static function _getDao($id = 0, $uid = '', $table = 0) {
        if($table > 0 && $table <= 10){
            $hash = $table-1;
        }else{
            if(empty($uid) && empty($id)) return false;
            $hash = $id ? $id : self::_hash_crc32($uid);
        }
        $dao = new User_Dao_Uid();
        $dao->hash = $hash;
        return $dao;
	}

    /**
     * 通过uid进行hash返回对应的ID
     * @param $uid
     * @return float|bool
     */
    private static function _hash_crc32($uid){
        if($uid) return floatval(sprintf('%u', crc32($uid)));
        return false;
    }
}
