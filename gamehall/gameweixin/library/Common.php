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
			return $encrypt->desEncrypt($string);
		} else { //解密
			return $encrypt->desDecrypt($string);
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
		if (!$token || ($token !== $_COOKIE['_securityCode'])) return self::formatMsg(-1, '非法请求2'); //token错误非法请求
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
		$config = Common::getConfig('redisConfig', ENV);
		return Cache_Factory::getCache($config);
	}
	
	/**
	 * 
	 * @param unknown_type $name
	 * @param unknown_type $dir
	 * @return multitype:unknown_type
	 */
	static public function upload($name, $dir, $maxSize=10240, $allowType = array('jpg','jpeg','png','gif'), $imgWH=array()) {
		$img = $_FILES[$name];
		if($img['error']) {
		    return self::uploadMsg(false, '上传失败:'.$img['error']);
		}
		
	    $attachPath = Common::getConfig('siteConfig', 'attachPath');
	    $savePath = sprintf('%s/%s/%s', $attachPath, $dir, date('Ym'));
	    $uploader = new Util_Upload(array('maxSize'=>$maxSize,'allowFileType'=>$allowType, 'imgWH'=>$imgWH));
	    $ret = $uploader->upload($name, uniqid(), $savePath);
	    if ($ret < 0) {
		    return self::uploadMsg(false, '上传失败:'.$uploader->getErrorMsg($ret));
	    }
	    $attachroot = Yaf_Application::app()->getConfig()->attachroot;
	    $ret["source"] = str_replace($attachPath, "", $ret["source"]);
	    $data= array(
	        "name"=> $ret["newName"],
	        "size"=> $ret["size"],
	        "type"=> $ret["type"],
	        "url"=> $ret["source"],
	    );
		return self::uploadMsg(true, '', $data);
	}
	
	public static function uploadTmpFile($name, $fileType) {
	    $tmpFilePath = Common::getConfig('siteConfig', 'tmpFilePath');
	    $allowType = array('txt');
	    $uploader = new Util_Upload(array('allowFileType'=>$allowType));
	    $savePath = sprintf('%s/%s', $tmpFilePath);
	    $ret = $uploader->upload($name, 'code'.time(), $savePath);
	    return $ret;
	}
	
	private static function uploadMsg($flag, $msg="", $data=array()) {
	    $output = array(
	        "success"=> strval($flag),
	        "msg"=> $msg,
	        "data"=> array(
	            "status"=>strval($flag ? 1:0),
	        )
	    );
	    foreach ($data as $key=>$value) {
	        $output["data"][$key] = $value;
	    }
	    return $output;
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	static public function getTime($fmt = 'Y-m-d H:i:s') {
		return strtotime(date($fmt));
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
		if (function_exists("money_format")) {
			return money_format('%.2n', $num);
		} else {
			return number_format($num, 2, '.', '');
		}
	}
	
	/**
	 * 
	 * @author yinjiayan
	 * @param unknown $error
	 * @param unknown $file
	 */
	static public function log($error, $file, $tag='') {
	    $msg = is_array($error) ? implode(" ", $error) : $error;
	    $msg = $tag.$msg;
		error_log($msg."\n", 3, Common::getConfig('siteConfig', 'logPath') . $file);
	}
	
	public static function getAttachPath() {
		$attachroot = Yaf_Application::app()->getConfig()->attachroot;
		return $attachroot . '/attachs/gameweixin/';
	}
	
	public static function getWebRoot() {
		if (DEFAULT_MODULE == "Admin") {
			return Yaf_Application::app()->getConfig()->adminroot;
		}
		return Yaf_Application::app()->getConfig()->webroot;
	}
	
	public static function getDownloadPath(){
		return Yaf_Application::app()->getConfig()->downloadroot;
	}
	
	public static function getIniConfig($name) {
		return Yaf_Application::app()->getConfig()->$name;
	}
}