<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * @author rainkid
 * KN = KEY NAME
 * KT = KEY TLL
 */
class Common {
	const TO_CACHE = true;
	const KT_IDS = 300;
	const KT_COLUMN = 60;
	const KT_SOURCE_INFO = 3600;
	const KT_CP_INFO = 3600;
	const KT_USER = 86400;

	const KN_WIDGET_SOURCE_DETAIL = 'WIDGET_SOURCE_DETAIL:';
	const KN_WIDGET_SOURCE_INFO = 'WIDGET_SOURCE_INFO:';
	const KN_W3_SOURCE_INFO = 'W3_SOURCE_INFO:';
	const KN_WIDGET_COLUMN = 'WIDGET_COLUMN:';
	const KN_W3_COLUMN = 'W3_COLUMN:';
	const KN_WIDGET_IDS = 'WIDGET_IDS:';
	const KN_W3_IDS = 'W3_IDS:';
	const KN_WIDGET_V1_INFO = 'WIDGET_V1_INFO:';
	const KN_WIDGET_V1_LIST = 'WIDGET_V1_LIST';


	const KN_W3_CP_INFO = 'W3_CP_INFO:';
	const KN_WIDGET_CP_DOWN = 'WIDGET_CP_DOWN:';
	const KN_WIDGET_CP_CLIENT = 'WIDGET_CP_CLIENT';
	const KN_WIDGET_CP_ALL_IDS = 'WIDGET_CP_ALL_IDS';


	/**
	 * @param string $serviceName
	 */
	static public function getService($serviceName) {
		return Common_Service_Factory::getService($serviceName);
	}

	/**
	 * @param string $daoName
	 */
	static public function getDao($daoName) {
		return Common_Dao_Factory::getDao($daoName);
	}

	/**
	 * @param string $fileName
	 * @param string $key
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
	 * @param string $string 需要处理的字符串
	 * @param string $action {ENCODE:加密,DECODE:解密}
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
		return Util_Page::page($count, $page, $perPage, $url, '=', $ajaxCallBack = '');
	}

	/**
	 *
	 * @param string $code
	 * @param string $msg
	 * @param array $data
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
	 * @param int $length
	 */
	static public function randStr($length) {
		$randstr = "";
		for ($i = 0; $i < (int)$length; $i++) {
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
	 * @param array $msg
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
	 * @return Cache_Redis
	 */
	static public function getCache() {
		$config = Common::getConfig('redisConfig', ENV);
		return Cache_Factory::getCache($config);
	}

	/**
	 *
	 * queue对象
	 */
	static public function getQueue() {
		$config = Common::getConfig('redisConfig', ENV);
		return Queue_Factory::getQueue($config);
	}

	/**
	 *
	 * @param string $name
	 * @param string $dir
	 */
	static public function upload($name, $dir) {
		$img        = $_FILES[$name];
		$attachPath = Common::getConfig('siteConfig', 'attachPath');
		if ($img['error']) {
			return Common::formatMsg(-1, '上传图片失败:' . $img['error']);
		}
		$allowType = array('jpg' => '', 'jpeg' => '', 'png' => '', 'gif' => '');
		$savePath  = sprintf('%s/%s/%s', $attachPath, $dir, date('Ym'));
		$uploader  = new Util_Upload($allowType);
		$ret       = $uploader->upload($name, uniqid(), $savePath);
		if ($ret < 0) {
			return Common::formatMsg(-1, '上传失败:' . $ret);
		}
		$url = sprintf('/%s/%s/%s', $dir, date('Ym'), $ret['newName']);
		return Common::formatMsg(0, '', $url);
	}

	/**
	 */
	static public function getTime() {
		return time();
	}

	/**
	 * @param array $source
	 * @param string $name
	 */
	static public function resetKey($source, $name) {
		if (!is_array($source)) return array();
		$tmp = array();
		foreach ($source as $key => $value) {
			if (isset($value[$name])) $tmp[$value[$name]] = $value;
		}
		return $tmp;
	}

	/**
	 * error log
	 * @param string $error
	 * @param string $file
	 */
	static public function log($error, $file) {
		error_log(date('Y-m-d H:i:s') . ' ' . json_encode($error) . "\n", 3, Common::getConfig('siteConfig', 'logPath') . $file);
	}

	/**
	 * 获取附件图片路径
	 * @return string
	 */
	public static function getAttachPath() {
		$attachroot = Yaf_Application::app()->getConfig()->attachroot;
		return $attachroot . '/attachs/fanfan';
	}

	//时间转换函数
	static public function tranTime($time) {
		$rtime = date("m-d H:i", $time);
		$htime = date("H:i", $time);
		$time  = time() - $time;
		if ($time < 60) {
			$str = '刚刚';
		} elseif ($time < 60 * 60) {
			$min = floor($time / 60);
			$str = $min . '分钟前';
		} elseif ($time < 60 * 60 * 24) {
			$h   = floor($time / (60 * 60));
			$str = $h . '小时前 ' . $htime;
		} elseif ($time < 60 * 60 * 24 * 3) {
			$d = floor($time / (60 * 60 * 24));
			if ($d == 1) {
				$str = '昨天 ' . $rtime;
			} else {
				$str = '前天 ' . $rtime;
			}
		} else {
			$str = $rtime;
		}
		return $str;
	}

	static public function cookData($data, $fileds) {
		$tmp = array();
		foreach ($fileds as $key) {
			if (isset($data[$key])) {
				$tmp[$key] = $data[$key];
			}
		}
		return $tmp;
	}


	/**
	 * 日志记录
	 *
	 * @author william.hu
	 * @param array $arr
	 */
	static public function debug($args) {
		if (ENV == 'develop' || ENV == 'test') {
			array_push($args, $_SERVER['HTTP_USER_AGENT']);
			array_push($args, $_SERVER['REMOTE_ADDR']);

			$logFile = '/tmp/fanfan_debug_' . date('Ymd');
			$logText = date('Y-m-d H:i:s') . ' ' . json_encode($args, JSON_UNESCAPED_UNICODE) . "\n";
			error_log($logText, 3, $logFile);
		}
	}

}