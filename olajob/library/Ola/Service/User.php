<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Ola_Service_User extends Common_Service_Base{
	static private $hash = 'xysoza'; //hash值
	static private $cookieTime = 2592000; //默认设置为30天
	static private $cookieName = 'OLA-USER';

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
	 * 读取一条用户信息
	 * @param int $id
	 */
	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * 更新用户信息
	 * @param array $data
	 * @param int $id
	 */
	public static function update($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	

	/**
	 * 
	 * del user
	 * @param int $id
	 */
	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * add user
	 * @param array $data
	 */
	public static function add($data) {
		if (!is_array($data)) return false;
		$data['register_time'] = Common::getTime();
		$data['last_login_time'] = Common::getTime();
		$data['status'] = 1;
		$data['user_type'] = 2;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	/**
	 * 获取广告记录
	 * @param $params
	 * @param array $orderBy
	 * @return bool|mixed
	 */
	public static function getBy($params, $orderBy = array()) {
	    if (!is_array($params)) return false;
	    $params = self::_cookData($params);
	    return self::_getDao()->getBy($params, $orderBy);
	}
	
	/**
	 * @param array $params
	 * @return array
	 */
	public static function getsBy($params, $sort=array()) {
	    if (!is_array($params) || !is_array($sort)) return false;
	    $ret = self::_getDao()->getsBy($params, $sort);
	    $total = self::_getDao()->count($params);
	    return array($total, $ret);
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
	public static function login($phone, $password) {
		$result = self::check($phone, $password);
		if (!$result || Common::isError($result)) return $result;
		return self::cookie($result);
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
		
		list(,$id, $phone) =  self::_cookieEncrypt($cookie, 'DECODE');

        if (!$id || !$phone) return false;
		$userInfo = self::getBy(array('id'=>$id, 'phone'=>$phone));
		if (!$userInfo) return false;
		
		self::cookie($userInfo);
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
	public static function cookie($userInfo) {
		$str = Common::getTime().'\t'.$userInfo["id"].'\t'.$userInfo["phone"];
		
		$cookieStr = self::_cookieEncrypt($str);
		Util_Cookie::set(self::$cookieName, $cookieStr, true, Common::getTime() + self::$cookieTime, '/', self::getDomain());
		
		//更新最后登录时间
		return self::update(array('last_login_time'=>Common::getTime()), $userInfo['id']);
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
     * @param $phone
     * @param $password
     * @return array|bool|mixed
     */
	public static function check($phone, $password) {
		if (!$phone) return false;
		$userInfo = self::getBy(array("phone"=>$phone));
		if (!$userInfo)  return Common::formatMsg(-1, '用户不存在，请先注册.');
        //
        $passwd = self::_password($password, $userInfo["hash"]);
        if ($passwd != $userInfo["password"]) return Common::formatMsg(-1, "用户密码错误.");
		//更新最后登录时间
		self::update(array('last_login_time'=>Common::getTime()), $userInfo['id']);
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
	 * @param unknown_type $id
	 */
	public static function updateTJ($field ,$id) {
		if (!$id || !$field) return false;
		return self::_getDao()->increment($field, array('id'=>intval($id)));
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['phone'])) $tmp['phone'] = $data['phone'];
		if(isset($data['realname'])) $tmp['realname'] = $data['realname'];
		if(isset($data['password'])) {
			list($tmp['hash'], $tmp['password']) = self::_cookPasswd($data['password']); 
		}
		if(isset($data['register_time'])) $tmp['register_time'] = $data['register_time'];
		if(isset($data['nickname'])) $tmp['nickname'] = $data['nickname'];
		if(isset($data['sex'])) $tmp['sex'] = $data['sex'];
		if(isset($data['birthday'])) $tmp['birthday'] = $data['birthday'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['education'])) $tmp['education'] = $data['education'];
		if(isset($data['headimgurl'])) $tmp['headimgurl'] = $data['headimgurl'];
		if(isset($data['last_login_time'])) $tmp['last_login_time'] = $data['last_login_time'];
		if(isset($data['weixin_open_id'])) $tmp['weixin_open_id'] = $data['weixin_open_id'];
		if(isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
		if(isset($data['end_time'])) $tmp['end_time'] = $data['end_time'];
		if(isset($data['user_type'])) $tmp['user_type'] = $data['user_type'];
		if(isset($data['favorite_num'])) $tmp['favorite_num'] = $data['favorite_num'];
		if(isset($data['publish_num'])) $tmp['publish_num'] = $data['publish_num'];
		if(isset($data['signup_num'])) $tmp['signup_num'] = $data['signup_num'];
		if(isset($data['pass_num'])) $tmp['pass_num'] = $data['pass_num'];
		if(isset($data['refuse_num'])) $tmp['refuse_num'] = $data['refuse_num'];
        if(isset($data['description'])) $tmp['description'] = $data['description'];
		return $tmp;
	}
	
	
	/**
	 * 
	 * @param number $sex
	 * @return Ambigous <multitype:string , string>
	 */
	public static function sex($key=0) {
	    $sex = array(
                1 => '男',
                2 => '女',
	    );
	    return $key ? $sex[$key] : $sex;
	}


    /**
     * @param $info
     */
    public static function deal_info(&$info) {
        $info["sex"] = self::sex($info["sex"]);
        $info["education"] = self::education($info["education"]);
    }


	
	/**
	 * 
	 * @param number $status
	 * @return Ambigous <multitype:string , string>
	 */
	public static function education($key=0) {
        $education = array(
	                    1 => '高中生',
	                    2 => '在校大学生',
	                    3 => '大专',
	                    4 => '本科',
	                    5 => '研究生',
	                    6 => '博士',
	                    7 => '其它'
	    );
	    return $key ? $education[intval($key)] : $education;
	}


	static public function getAllow() {
	    return array('jpg', 'jpeg', 'png');
	}
	
	static function tmpPath() {
	    return "/tmp/user";
	}
	
	static public function getSavePath($dir) {
	    return realpath(Common::getConfig("siteConfig", "attachPath")) . "/" . $dir;
	}
	
	
	/**
	 * 下载图片
	 * @param array $data
	 * @param string $name
	 * @param string $savepath
	 */
	static public function downImages($list, $name, $savepath) {
	    $data = array();
	    if (!file_exists(self::tmpPath())) {
	        $old = umask(0);
	        mkdir(self::tmpPath(), 0777, true);
	        umask($old);
	    }
	
	    $mh = new Util_Http_CurlMulti();
	    foreach ($list as $id => $url) {
	        $curl = new Util_Http_Curl($url);
	        $curl->Options("get", array());
	        $hd = $curl->getHttpHandler();
	        $mh->addHandler($id, $hd);
	    }
	    $result = $mh->execute();
	
	    $dir = $savepath . "/" . date('Ymd');
	    if (!file_exists($dir)) {
	        $old = umask(0);
	        mkdir($dir, 0777, true);
	        umask($old);
	    }
	
	    foreach ($list as $id => $url) {
	        $tmp_file = self::tmpPath() . "/" . md5($url);
	        if (!file_exists($tmp_file)) {
	            file_put_contents($tmp_file, $result[$id]);
	        }
	        $extension = end(explode("/", mime_content_type($tmp_file)));
	        
	        if (in_array($extension, self::getAllow())) {
	            $filename = md5($url) . "." . $extension;
	            $fullFile = $dir . "/" . $filename;
	            if (@copy($tmp_file, $fullFile)) {
	                $data[$id] = '/'.$name.'/'.date('Ymd') . "/" . $filename;
	            }
	
	        }
	    }
	    return $data;
	}
	
	/**
	 * 
	 * @return Ola_Dao_User
	 */
	private static function _getDao() {
		return Common::getDao("Ola_Dao_User");
	}
}
