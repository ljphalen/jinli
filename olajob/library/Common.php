<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Common {
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $serviceName
	 */
	static public function getService($serviceName) {
		return	Common_Service_Factory::getService($serviceName);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $daoName
	 */
	static public function getDao($daoName) {
		return Common_Dao_Factory::getDao($daoName);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $fileName
	 * @param unknown_type $key
	 */
	static public function getConfig($fileName, $key = '') {
		static $config = array();
		$name = md5($fileName);
		if (!isset($config[$name]) || !$config[$name]) {
			$file = realpath(BASE_PATH . 'configs/' . $fileName . '.php');
			if (is_file($file)) $config[$name] = include $file;
		}
		if ($key) {
			return isset($config[$name][$key]) ? $config[$name][$key] : '';
		} else {
			return isset($config[$name]) ? $config[$name] : '';
		}
	}
	
	/**
	 * 字符串加密解密
	 * @param string $string	需要处理的字符串
	 * @param string $action	{ENCODE:加密,DECODE:解密}
	 * @return string
	 */
	static public function encrypt($string, $action = 'ENCODE') {
		if (!in_array($action, array('ENCODE', 'DECODE'))) $action = 'ENCODE';
		$encrypt = new Util_Encrypt(self::getConfig('siteConfig', 'secretKey'));
		if ($action == 'ENCODE') { //加密
			return str_replace(array("+","/","="), array("_","-","."), $encrypt->desEncrypt($string));
		} else { //解密
			return $encrypt->desDecrypt(str_replace(array("_","-","."), array("+","/","="), $string));
		}
	}
	
	/**
	 * 获得token  表单的验证
	 * @return string
	 */
	static public function getToken($userInfo) {
		if (!isset($_COOKIE['_securityCode']) || '' == $_COOKIE['_securityCode']) {
			/*用户登录的会话ID*/
			$key = substr(md5(serialize($userInfo) . ':' . time() . ':' . $_SERVER['HTTP_USER_AGENT']), mt_rand(1, 8), 8);
			setcookie('_securityCode', $key, null, '/'); //
			$_COOKIE['_securityCode'] = $key; //IEbug
		}
		return $_COOKIE['_securityCode'];
	}
	
	/**
	 * 验证token
	 * @param string $token
	 * @return mixed
	 */
	static public function checkToken($token) {
		if (!$_COOKIE['_securityCode']) return self::formatMsg(-1, '非法请求'); //没有token的非法请求
		if (!$token || ($token !== $_COOKIE['_securityCode'])) return self::formatMsg(-1, '非法请求'); //token错误非法请求
		return true;
	}
	
	/**
	 * 分页方法
	 * @param int $count
	 * @param int $page
	 * @param int $perPage
	 * @param string $url
	 * @param string $ajaxCallBack
	 * @return string
	 */
	static public function getPages($count, $page, $perPage, $url, $ajaxCallBack = '') {
		return Util_Page::page($count, $page, $perPage, $url, '=',$ajaxCallBack = '');
	}

    /**
     * @param $curPage
     * @param $totalPage
     * @return array
     */
    static public function getNPages($curPage, $totalPage)
    {
        $pages = range(1, $totalPage);
        if ($totalPage>5) {
            if ($totalPage - $curPage >4) array_splice($pages, ($curPage+2), count($totalPage)-2, -1);
            if($curPage - 1 > 4) array_splice($pages, 1, $curPage-4, -1);
        }
        return $pages;
    }
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $code
	 * @param unknown_type $msg
	 * @param unknown_type $data
	 */
	static public function formatMsg($code, $msg = '', $data = array()) {
		return array(
			'code' => $code,
			'msg'  => $msg,
			'data' => $data
		);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $length
	 */
	static public function randStr($length) {
		$randstr = "";
		for ($i = 0; $i < (int) $length; $i++) {
			$randnum = mt_rand(0, 61);
			if ($randnum < 10) {
				$randstr .= chr($randnum + 48);
			} else if ($randnum < 36) {
				$randstr .= chr($randnum + 55);
			} else {
				$randstr .= chr($randnum + 61);
			}
		}
		return $randstr;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $msg
	 */
	static public function isError($msg) {
		if (!is_array($msg)) return false;
		$temp = array_keys($msg);
		return $temp == array('code', 'msg', 'data') ? true : false;
	}
	
	static public function getSession() {
		return Yaf_Session::getInstance();
	}
	
	/**
	 * 
	 */
	static public function getCache() {
		$config = Common::getConfig('redisConfig');
		return Cache_Factory::getCache($config);
	}
	
	/**
	 * 
	 * @param unknown_type $name
	 * @param unknown_type $dir
	 * @return multitype:unknown_type
	 */
	static public function upload($name, $dir) {
		$img = $_FILES[$name];
		$attachPath = Common::getConfig('siteConfig', 'attachPath');
		if ($img['error']) {
			return Common::formatMsg(-1, '上传图片失败:' . $img['error']);
		}
		$allowType = array('jpg' => '','jpeg' => '','png' => '','gif' => '');
		$savePath = sprintf('%s/%s/%s', $attachPath, $dir, date('Ym'));
		$uploader = new Util_Upload($allowType);
		$ret = $uploader->upload($name, date('His'), $savePath);
		if ($ret < 0) {
			return Common::formatMsg(-1, '上传失败:'.$ret);
		}
		$url = sprintf('/%s/%s/%s', $dir, date('Ym'), $ret['newName']);
		return Common::formatMsg(0, '', $url);
	}

    /**
     * @param $name
     * @param $dir
     * @return array
     */
    static public function upload2($name, $dir) {
        $img = $_FILES[$name];
        $attachPath = Common::getConfig('siteConfig', 'attachPath');
        if ($img['error']) {
            return Common::formatMsg(-1, '上传图片失败:' . $img['error']);
        }
        $allowType = array('jpg' => '','jpeg' => '','png' => '','gif' => '');
        $savePath = sprintf('%s/%s/%s', $attachPath, $dir, date('Ym'));
        $uploader = new Util_Upload($allowType);
        return $uploader->upload($name, date('His'), $savePath);
    }
	
	/**
	 * 短信接口
	 * @param string $mobile
	 * @param string $content
	 */
	static public function sms($mobile, $content) {
		if (!$mobile || !$content) return false;
		$params = array(
				'mobile'=>$mobile,
				'content'=>$content,
                'token'=>'8153fa24b617b0165740211f4965dd2f'
		);
		$url = sprintf("%s?%s", Common::getConfig('apiConfig', 'sms_url'), http_build_query($params));
		return Util_Http::get($url);
	}
	
	/**
	 * 获取webroot
	 */
	static public function getWebRoot() {
	    $webroot = Yaf_Application::app()->getConfig()->webroot;
	    $adminroot = Yaf_Application::app()->getConfig()->adminroot;
	    if (strpos($webroot, ",") === false) return $webroot;
	    $http_host = Util_Http::getServer('HTTP_HOST');
	    $current = sprintf('http://%s', $http_host);
	    $admin_current = sprintf('https://%s', $http_host);
	    if($current == $adminroot || $admin_current == $adminroot || !$http_host) {
	        $domains = explode(',', $webroot);
	        $current = $domains[0];
	    }
	    $webroots = explode(',', $webroot);
	    if (in_array($current, $webroots)) return $current;
	    return $current;
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	static public function getTime() {
		return time();
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $array
	 * @param unknown_type $name
	 */
	static public function resetKey($source, $name) {
		if (!is_array($source)) return array();
		$tmp = array();
		foreach ($source as $key=>$value) {
			if (isset($value[$name])) $tmp[$value[$name]] = $value;
		}
		return $tmp;
	}
	
	/**
	 * 金额转换
	 * @param float/int $num
	 * @return float
	 */
	static public function money($num) {
			return number_format($num, 2, '.', '');
	}
	
	/**
	 * error log
	 * @param string $error
	 * @param string $file
	 */
	static public function log($error, $file) {
		$error = is_array($error) ? json_encode($error) : $error;
		error_log($error."\n", 3, Common::getConfig('siteConfig', 'logPath') . $file);
	}
	
	/**
	 * 验证手机号
	 * @param unknown_type $mobile
	 * @return number
	 */
	static public function checkMobile($mobile) {
	    return preg_match("/^13[0-9]{1}[0-9]{8}$|15[012356789]{1}[0-9]{8}$|18[0123456789]{1}[0-9]{8}$|147[0-9]{8}$|145[0-9]{8}$|17[678]{1}[0-9]{8}$/",$mobile);
	}

	/**
	 * getAttachPath
	 * @return string
	 */
	public static function getAttachPath() {
		$attachroot = Yaf_Application::app()->getConfig()->attachroot;
		return $attachroot . '/attachs/olajob';
	}

	/**
	 * 获取两个时间相差天/小时/分/秒
	 * @param $startDay
	 * 开始时间
	 * @param $endDay
	 * 结束时间
	 * @param $timestamp
	 * 是否是时间戳, 默认为false
	 * @parame $format
	 * array(
	 *  'day' 	=> '86400',
	 * 	'hour' 	=> '3600',
	 * 	'min' 	=> '60',
	 * 	'second' => '1'
	 * )
	 * @return array
	 */
	public static function dateDiff($startDay, $endDay, $format = array(), $timestamp = false){
		$start	= $timestamp?strtotime($startDay):$startDay;
		$end	= $timestamp?strtotime($endDay):$endDay;
		$diff	= abs($start-$end);
		$rs		= array();
		$attrs	= array('day' => '86400', 'hour' => '3600', 'min' => '60', 'second' => '1');
		$attrs 	= !empty($format)?array_intersect_assoc($attrs, $format):$attrs;
		foreach($attrs as $key => $value){
			if($diff >= $value){
				$d = round($diff/$value);
				$diff %= $value;
				$rs[$key] = $d;
			}else{
				$rs[$key] = 0;
			}
		}
		return $rs;
	}

	public static function currentPageUrl(){
	    $ssl        = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? true:false;
	    $sp         = strtolower($_SERVER['SERVER_PROTOCOL']);
	    $protocol   = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
	    $port       = $_SERVER['SERVER_PORT'];
	    $port       = ((!$ssl && $port=='80') || ($ssl && $port=='443')) ? '' : ':'.$port;
	    $host       = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
	    return $protocol . '://' . $host . $port . $_SERVER['REQUEST_URI'];
	}

	/**
	 * is weixin client MicroMessenger
	 * @return boolean 
	 */
	public function isWeixin() {
		$ua = Util_Http::getServer('HTTP_USER_AGENT');
		if ($ua && strpos($ua, "MicroMessenger") !== false){
			return true;
		}
		return false;
	}
}