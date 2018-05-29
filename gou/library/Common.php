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
     * @return Cache_Redis
     * @throws Exception
     */
	static public function getCache() {
		$config = Common::getConfig('redisConfig');
		$redis = Cache_Factory::getCache($config);
        return $redis;
	}
	
	/**
	 *
	 * queue对象
	 */
	static public function getQueue() {
	   $config = Common::getConfig('redisConfig');
	    return Queue_Factory::getQueue($config);
	}

	/**
	 * 获取MongoDB对象
	 * @return mixed
	 * @throws MongoException
	 */
	static public function getMongo(){
		$config = Common::getConfig('mongoConfig');
		return Db_Mongo::factory($config);
	}
    public static function getImageUrl($url){
        $attachPath = static::getAttachPath();
        if (strpos($url, 'http://') === false) {
            return $attachPath . $url;
        }
        return $url;
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
		$ret = $uploader->upload($name, uniqid(), $savePath);
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
        return $uploader->upload($name, uniqid(), $savePath);
    }
	
	/**
	 * 
	 * Enter description here ...
	 */
	static public function getTime() {
		return time();
	}
	
	/**
	 * 验证手机号
	 * @param unknown_type $mobile
	 * @return number
	 */
	static public function checkMobile($mobile) {
		return preg_match("/^13[0-9]{1}[0-9]{8}$|15[012356789]{1}[0-9]{8}$|17[023456789]{1}[0-9]{8}$|18[023456789]{1}[0-9]{8}$|147[0-9]{8}$/",$mobile);
	}

	/**
	 * @param array  $source
	 * @param string $name
	 * @return array
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
	 * error log
	 * @param string $error
	 * @param string $file
	 */
	static public function log($error, $file) {
		error_log(date('Y-m-d H:i:s') .' '. json_encode($error, JSON_UNESCAPED_UNICODE)."\n", 3, Common::getConfig('siteConfig', 'logPath') . $file);
	}

    /**
     * set error log handler
     */
    static public function setLogHandler(){
        set_error_handler(array('Common', 'mongoLog'), E_ALL & ~E_NOTICE & ~E_WARNING);
        register_shutdown_function(array('Common', 'setLogInRam'));
    }

    /**
     * save log into mongoDB
     * @param $type 日志类型编码
     * @param $msg  日志信息
     * @param $file_path  所在文件
     * @param $line 行数
     */
    static public function mongoLog($type, $msg, $file_path, $line){
        $ip = Common::get_server_ip();
        Common::getMongo()->insert('log',
            array(
                'type'  => intval($type),
                'msg'   => $msg,
                'file'  => $file_path,
                'line'  => $line,
                'time'  => time(),
                'ip'    => $ip
            )
        );
    }

    /**
     * 通过注册为内存函数来记录日志
     * 只记录E_ERROR类型的错误日志
     */
    static public function setLogInRam(){
        if($e = error_get_last()){
            if(intval($e['type']) == 1){
                $ip = Common::get_server_ip();
                Common::getMongo()->insert('log',
                    array(
                        'type'  => intval($e['type']),
                        'msg'   => $e['message'],
                        'file'  => $e['file'],
                        'line'  => $e['line'],
                        'time'  => time(),
                        'ip'    => $ip
                    )
                );
            }
        }
    }
	
	/**
	 * 短信接口
	 * @param string $mobile
	 * @param string $content
	 */
	static public function sms($mobile, $content) {
		if (!$mobile || !$content) return false;
		$params = array(
			'__ext'=>'GOU',
			'__tel'=>$mobile,
			'__content'=>$content
		);
		$url = sprintf("%s?%s", Common::getConfig('apiConfig', 'sms_url'), http_build_query($params));
		return Util_Http::get($url);
	}
	
	/**
	 * 点击统计
	 * @param string $mobile
	 * @param string $content
	 */
	static public function tjurl($tj_url, $version_id, $model_id, $channel_id, $item_id, $link, $name, $channel_code) {
	    $statroot = Yaf_Application::app()->getConfig()->statroot;
        $data = array(
            'version_id'=>$version_id,
            'model_id'=>$model_id,
            'channel_id'=>$channel_id,
            'item_id'=>$item_id,
            'link'=>html_entity_decode($link),
            'channel_code'=>$channel_code,
            'name'=>$name);
        //make hash
        $hash = crc32(implode(',', $data));
        $data['hash'] = $hash;

        //如果短信hash表不存在，入队列插入
        $queue = Common::getQueue();
        $cache = Common::getCache();
        if (!Stat_Service_ShortUrl::getUrl($hash)) {
            //放入消息队列进行入库
            $queue->noRepeatPush('tjhash', $data);
            $cache->set($data['hash'] . '-link', $data['link'], Stat_Service_ShortUrl::HASH_CACHE_TIME);
         }

        return sprintf("%s%s?t=%d", $statroot, $tj_url, $hash);
	}

	/**
	 * 获取当前页面地址
	 * @return string
	 */
	static public function getCurPageURL() {
		$url = 'http';
		if (Util_Http::getServer("HTTPS") == "on") {$url .= "s";}
		$url .= "://".Util_Http::getServer("HTTP_HOST").Util_Http::getServer("REQUEST_URI");
		return $url;
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
	 * getAttachPath
	 * @return string
	 */
	public static function getAttachPath() {
		$attachroot = Yaf_Application::app()->getConfig()->attachroot;
		return $attachroot . '/attachs/gou';
	}

	/**
	 *
	 * @return Util_Lock
	 */
	static public function getLockHandle() {
		static $lock = null;
		if ($lock === null) {
			$lock = Util_Lock::getInstance();
		}
		return $lock;
	}
	
	/**
	 * @return Beanstalk
	 */
	static public function getBeanstalkHandle() {
		static $beanstalk = null;
		if ($beanstalk === null) {
			$beanstalk = new Util_Beanstalk();
			$beanstalk->config(Common::getConfig('beanstalkConfig'));
		}
		return $beanstalk;
	}
	
	/**
	 * 获取IMEI号
	 * 返回的IMEI是php md5 加密串
	 * 
	 * @return string
	 */
	public static function getIMEI(){
		$ua = Util_Http::getServer('HTTP_USER_AGENT');
		$ua = explode(" ", $ua);
		$imei = '';
		foreach ($ua as $val){
			if (strpos($val, "Id/") !== false) {
				$imeiArr = explode("/", $val);
				$imei = $imeiArr[1];
			}
		}
		return $imei; 
	}
	
	/**
	 * 获取android udi
	 * 返回的uid
	 *
	 * @return string
	 */
	public static function getAndroidtUid(){
	    $ua = Util_Http::getServer('HTTP_USER_AGENT');
	    preg_match('/uid\/([a-z0-9]*)/', $ua, $match);
	    return $match[1] ? $match[1] : '';
	}
	
	
	/**
	 * get ios uid
	 */
	public static function getIosUid() {
	    $ua = Util_Http::getServer('HTTP_USER_AGENT');
	    preg_match('/Id\/([a-z0-9A-Z]*)/', $ua, $match);
        return $match[1] ? $match[1] : '';
	}

    /**
     * 获取来源设备
     * @return bool|string
     */
    public static function getDevice(){
        $dev = '';
        $ua = Util_Http::getServer('HTTP_USER_AGENT');
        preg_match('/Android/', $ua, $match);
        if($match[0]) $dev = 'apk';

        preg_match('/iPhone/', $ua, $match);
        if($match[0]) $dev = 'ios';

        if($dev) return $dev;
        return false;
    }

    /**
     * 创建UID用户
     * @return bool
     */
    public static function createUserUid(){
        $dev = self::getDevice();
        if($dev){
            list($uid, ) = User_Service_Uid::checkUid($dev);
            if($uid) return true;
        }
        return false;
    }
	
	/**
	 * 获取iOS版本号
	 * 返回 int version
	 *
	 * @return string
	 */
	public static function getIosClientVersion(){
	    $ua = Util_Http::getServer('HTTP_USER_AGENT');
	    preg_match('/(.*)\/gouwudating/',$ua,$match);
	    return $match[1] ? str_replace('.', '', $match[1]) : '';
	}

	/**
	 * 获取Android版本号
	 * 返回 int version
	 *
	 * @return string
	 */
	public static function getAndroidClientVersion(){
	    $ua = Util_Http::getServer('HTTP_USER_AGENT');
	    preg_match('/gngouua(\d+.\d+.\d+)/',$ua,$match);
	    return $match[1] ? str_replace('.', '', $match[1]) : '';
	}

	/**
	 * 获取Android版本号
	 * 返回 int version
	 *
	 * @return string
	 */
	public static function getApkClientVersion(){
	    $ua = Util_Http::getServer('HTTP_USER_AGENT');
	    preg_match('/gngouua(\d+.\d+.\d+.\w)/',$ua,$match);
	    return $match[1] ?  $match[1] : '';
	}

	/**
	 * 获取手机型号
	 * 返回 int version
	 *
	 * @return string
	 */
	public static function getApkClientModel(){
	    $ua = Util_Http::getServer('HTTP_USER_AGENT');
	    preg_match('/([\w]+-[\w]+)\//',$ua,$match);
	    return $match[1] ?$match[1] : '';
	}

    /**
     * 判断是否是通过手机访问
     * @return bool 是否是移动设备
     */
    public static function isMobile() {
        //判断手机发送的客户端标志
        $ua = strtolower(Util_Http::getServer('HTTP_USER_AGENT'));
        if(!isset($ua)) return false;

        $mk = array(
            'nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-'
        ,'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu',
            'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini',
            'operamobi', 'opera mobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile'
        );
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if(preg_match("/(".implode('|',$mk).")/i",$ua) && strpos($ua,'ipad') === false) {
            return true;
        }
        return false;
    }
    
    
     /**
     * 显示点赞数量
     * @return int
     */
    public static function parise($num) {
        if($num<=0)$num =0;
        if($num < 10000) return $num;
        //if($num >= 1000 && $num < 10000) return floor($num / 1000).'千+';
        if($num >= 10000) return floor($num / 10000).'万+';
    }

    /**
     * @param $time
     * Name:     time_ago
     * Purpose:  将时间戳专为距当前时间的表现形式
     *           1分钟内按秒
     *           1小时内按分钟显示
     *           1天内按时分显示
     *           3天内以昨天，前天显示
     *           超过3天显示具体日期
     *
     * @author Peter Pan
     * @param int  $time      input int
     */

    /**
     * 1小时之内刚刚
     * 1天内显示几小时前
     *
     * 3天内以昨天，前天显示
     * 超过3天显示具体日期
     * @param int $time
     * @return bool|string
     */
    public static function fmtTime($time){
        if (!is_numeric($time)) {
            $time = strtotime($time);
        }

        $time_diff = time() - $time;

        //大于2天
        if($time_diff >= 172800){
            return  date('m-d', $time);
        }

        //大于1天
        if($time_diff >= 86400){
            return  "昨天";
        }

        //大于1小时
        if($time_diff >= 3600){
            $hour = intval($time_diff / 3600);
            return sprintf("%d小时之前", $hour);
        }else{
            return "刚刚";
        }
    }
    
    
    /**
     * 通过string进行hash
     * @param $str
     * @return int
     */
    public static function hash_crc32($str){
        if(!$str) return false;
        return floatval(sprintf('%u', crc32($str)));
    }

    /**
     * 获取服务器IP
     * @return string|bool
     */
    public static function get_server_ip(){
        if(!empty($_SERVER['SERVER_ADDR']))
            return $_SERVER['SERVER_ADDR'];
        $result = shell_exec("/sbin/ifconfig");
        if(preg_match_all("/addr:(\d+\.\d+\.\d+\.\d+)/", $result, $match) !== 0){
            foreach($match[0] as $k=>$v){
                if($match[1][$k] != "127.0.0.1")
                    return $match[1][$k];
            }
        }
        return false;
    }
}
