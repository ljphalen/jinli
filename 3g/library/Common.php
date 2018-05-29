<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * @author rainkid
 *
 */
class Common {
    static $urlPwd = '39djg0c7b';

    static $status = array(0 => '关闭', 1 => '开启');
    const T_ONE_DAY  = 86400;
    const T_TEN_MIN  = 600;
    const T_ONE_HOUR = 3600;
    const T_TEN_HOUR = 3600;

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
     *
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
     *
     * @param string $string 需要处理的字符串
     * @param string $action {ENCODE:加密,DECODE:解密}
     *
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
     * 金额格式化
     *
     * @param  $num
     *
     * @return string
     */
    static public function money($num) {
        if (function_exists("money_format")) {
            return money_format('%.2n', $num);
        } else {
            return number_format($num, 2, '.', '');
        }
    }

    /**
     * 获得token  表单的验证
     * @return string
     */
    static public function getToken() {
        $code = intval($_COOKIE['_securityCode']);
        if (!isset($_COOKIE['_securityCode']) || '' == $_COOKIE['_securityCode'] || strlen($code) < 8) {
            /*用户登录的会话ID*/
            $key = crc32(md5(time() . ':' . $_SERVER['HTTP_USER_AGENT']));
            setcookie('_securityCode', $key, null, '/'); //
            $_COOKIE['_securityCode'] = $key; //IEbug
        }
        return intval($_COOKIE['_securityCode']);
    }

    /**
     * 验证token
     *
     * @param string $token
     *
     * @return mixed
     */
    static public function checkToken($token) {
        $code = intval($_COOKIE['_securityCode']);
        if (empty($code)) return self::formatMsg(-1, '非法请求'); //没有token的非法请求
        if (!$token || ($token != $code)) return self::formatMsg(-1, '非法请求'); //token错误非法请求
        return true;
    }

    /**
     * 分页方法
     *
     * @param int    $count
     * @param int    $page
     * @param int    $perPage
     * @param string $url
     * @param string $ajaxCallBack
     *
     * @return string
     */
    static public function getPages($count, $page, $perPage, $url, $ajaxCallBack = '') {
        return Util_Page::page($count, $page, $perPage, $url, '=', $ajaxCallBack = '');
    }

    /**
     *
     * @param string $code
     * @param string $msg
     * @param array  $data
     */
    static public function formatMsg($code, $msg = '', $data = array()) {
        return array(
            'code' => $code,
            'msg'  => $msg,
            'data' => $data
        );
    }

    static public function formatDate($t) {
        $formatTime = date('m-d H:i', $t);
        if (date('Ymd', $t) == date('Ymd')) {
            $formatTime = '今天 ' . date('H:i', $t);
        } else if (date('Ymd', $t) == date('Ymd', time() - 86400)) {
            $formatTime = '昨天 ' . date('H:i', $t);
        }
        return $formatTime;
    }

    static public function formatHMDate($t) {
        $formatTime = date('H:i', $t);
        return $formatTime;
    }

    /**
     *
     * @param string $length
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
     *
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
    static public function getCache($i = 0) {
        $config = Common::getConfig('redisConfig', ENV);
        return Cache_Factory::getCache($config, $i);
    }

    /**
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
     *
     * @return string
     */
    static public function upload($name, $dir, $size = 0) {
        $img        = $_FILES[$name];
        $attachPath = Common::getConfig('siteConfig', 'attachPath');
        if ($img['error']) {
            return Common::formatMsg(-1, '上传图片失败:' . $img['error']);
        }
        $params = array(
            'allowFileType' => array('jpg', 'jpeg', 'png')
        );
        if (!empty($size)) {
            $params['maxSize'] = $size;
        }
        $savePath = sprintf('%s/%s/%s', $attachPath, $dir, date('Ym'));
        if (!file_exists($savePath)) {
            $old = umask(0);
            mkdir($savePath, 0777, true);
            umask($old);
        }
        $uploader = new Util_Upload($params);
        $ret      = $uploader->upload($name, uniqid(), $savePath);
        if ($ret < 0) {
            return Common::formatMsg(-1, '上传失败:' . $ret);
        }
        $url = sprintf('/%s/%s/%s', $dir, date('Ym'), $ret['newName']);
        return Common::formatMsg(0, '', $url);
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
     *
     */
    static public function getTime() {
        return time();
    }

    /**
     *
     * @param array  $source
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
     * 点击统计
     *
     * @param string $mobile
     * @param string $content
     */
    static public function clickUrl($id, $type, $redirect, $t_bi = '') {
        $redirect = trim($redirect);
        $t        = Gionee_Service_ShortUrl::genTVal($id . $redirect . $type . Common::$urlPwd);
        $key      = 'ToUrl:' . $t;
        $rc       = Common::getCache(1);
        $info     = $rc->get($key);
        if (empty($info)) {
            $info = array('id' => $id, 'type' => $type, '_url' => $redirect);
            $mark = json_encode($info);
            Gionee_Service_ShortUrl::make($t, $redirect, $mark);
            $rc->set($key, $mark);
        }

        $newUrl = sprintf('%s?t=%s&t_bi=%s', Common::getPrevShortUrl(), $t, $t_bi);
        return $newUrl;
    }


    static public function redirect($url) {
        header('Location:' . $url);
        exit;
    }

    /**
     *
     * @param string $link
     * @param string $t_bi
     *
     * @return string
     */
    static public function BI($link, $t_bi) {
        $link = trim(html_entity_decode($link));
        if (strpos($link, "3g.gionee.com") === true) {
            if (strpos($link, '?') !== false) {
                $link = $link . "&t_bi=" . $t_bi;
            } else {
                $link = $link . "?t_bi=" . $t_bi;
            }
        }
        return $link;
    }

    /**
     * error log
     *
     * @param string $error
     * @param string $file
     */
    static public function log($error, $file) {
        error_log(date('Y-m-d H:i:s') . ' ' . json_encode($error) . "\n", 3, Common::getConfig('siteConfig', 'logPath') . $file);
    }

    /**
     * @param $imgUrl
     * @param $path '/news/' . date('Ymd')
     *
     * @return bool|string
     */
    static public function downImg($imgUrl, $path) {
        if (!stristr($imgUrl, 'http')) {
            return $imgUrl;
        }
        $realPath = realpath(Common::getConfig("siteConfig", "attachPath"));
        $dir      = $realPath . $path;
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        //get remote file info
        $headerInfo = get_headers($imgUrl, 1);
        $size       = $headerInfo['Content-Length'];
        if (empty($size)) {
            return false;
        }
        $type = $headerInfo['Content-Type'];
        $ext  = end(explode("/", $type));
        $ext  = str_ireplace(';charset=UTF-8', '', $ext);

        if ($ext == 'jpeg') {
            $ext = 'jpg';
        }

        if (!in_array($ext, array('jpg', 'png'))) {
            return false;
        }

        $filename  = crc32($imgUrl) . "." . $ext;
        $ret       = $path . "/" . $filename;
        $localFile = $dir . "/" . $filename;

        if (file_exists($localFile)) {
            return $ret;
        }

        $imgContent = Common::getUrlContent($imgUrl);
        file_put_contents($localFile, $imgContent);
        if (!file_exists($localFile)) {
            return false;
        }
        return $ret;
    }


    static public function getUrlContent($url, $type = 1) {
        //获取远程文件资源
        if ($type) {
            $ch      = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $content = curl_exec($ch);
            curl_close($ch);
        } else {
            ob_start();
            readfile($url);
            $content = ob_get_contents();
            ob_end_clean();
        }
        return $content;
    }

    /**
     * 生成缩略图
     *
     * @param string $pathFileName 函数downImg的返回值
     * @param int    $dstW
     * @param int    $dstH
     * @param string $name
     * @param string $fix
     *
     * @return string
     */
    static public function genThumbImg($pathFileName, $dstW, $dstH, $fix = 1, $webp = false) {
        $path    = realpath(Common::getConfig("siteConfig", "attachPath"));
        $srcFile = $path . '/' . $pathFileName;
        if (!file_exists($srcFile)) {
            return false;
        }

        list($srcW, $srcH, $type,) = getimagesize($srcFile);
        $types = array(
            IMAGETYPE_GIF  => "gif",
            IMAGETYPE_JPEG => "jpg",
            IMAGETYPE_PNG  => "png",
        );

        if (!isset($types[$type])) {
            return false;
        }

        $imgType   = $types[$type];
        $thumbName = $pathFileName . "_" . $dstW . "x" . $dstH . "." . $imgType;
        $dstFile   = $path . '/' . $thumbName;
        if (file_exists($dstFile)) {
            return $thumbName;
        }

        $srcIm = null;
        if ($imgType == 'gif') {
            $srcIm = imagecreatefromgif($srcFile);
        } else if ($imgType == 'jpg' || $imgType == 'jpeg') {
            $srcIm = imagecreatefromjpeg($srcFile);
        } else if ($imgType == 'png') {
            $srcIm = imagecreatefrompng($srcFile);
        }

        if ($srcIm == null) {
            return false;
        }

        $srcX = $srcY = $dstX = $dstY = 0;

        //图像截取
        $dsDivision  = $srcH / $srcW;
        $fixDivision = $dstH / $dstW;

        if ($dsDivision > $fixDivision) {
            $tmp     = $srcW * $fixDivision;
            $diffDiv = $dsDivision - $fixDivision;
            //高比较大的图片 可能是人物 需要从顶部开始
            $r    = ($diffDiv > 0.3) ? 8 : 2;
            $srcY = round(($srcH - $tmp) / $r);
            $srcH = $tmp;
        } else {
            $tmp  = $srcH / $fixDivision;
            $srcX = round(($srcW - $tmp) / 2);
            $srcW = $tmp;
        }

        if ($fix) {
            if ($srcH < $dstH || $srcW < $dstW) {//图片小于规格 只裁剪
                if ($srcH > $srcW) {
                    $dstW = $srcW;
                    $dstH = floor($srcW * $fixDivision);
                } else {
                    $dstH = $srcH;
                    $dstW = floor($srcH / $fixDivision);
                }
            }
        }

        $dstIm = imagecreatetruecolor($dstW, $dstH);
        //重采样拷贝部分图像并调整大小
        imagecopyresampled($dstIm, $srcIm, $dstX, $dstY, $srcX, $srcY, $dstW, $dstH, $srcW, $srcH);

        //$black = imagecolorallocate($dstIm, 0, 0, 0);//分配颜色
        //imagecolortransparent($dstIm, $black);//将某个颜色定义为透明色

        if ($imgType == 'gif') {
            imagegif($dstIm, $dstFile);
        } else if ($imgType == 'jpg' || $imgType == 'jpeg') {
            imagejpeg($dstIm, $dstFile, 100);
        } else if ($imgType == 'png') {
            imagepng($dstIm, $dstFile);
        }

        imagedestroy($srcIm);
        imagedestroy($dstIm);
        if (file_exists($dstFile)) {
            $webp && @image2webp($dstFile, $dstFile . '.webp');
            return $thumbName;
        }

        return false;
    }


    /**
     * 域名替换
     */
    static public function getCurHost() {
        $webroot = Yaf_Application::app()->getConfig()->webroot;

        if (DEFAULT_MODULE == 'Front' && isset($_SERVER['HTTP_HOST'])) {
            $webroot = 'http://' . $_SERVER['HTTP_HOST'];
        }
        return $webroot;
    }

    static public function getStaticHost() {
        $webroot = Yaf_Application::app()->getConfig()->webroot;
        if (DEFAULT_MODULE == 'Front' && isset($_SERVER['HTTP_HOST'])) {
            $webroot = 'http://' . $_SERVER['HTTP_HOST'];
        }
        if (stristr(ENV, 'product')) {
            $webroot = 'http://static.3g.gionee.com';
        }
        return $webroot;
    }

    /**
     * 检测名字是否非法 最少2个汉字 最多5个
     *
     * @author william.hu
     *
     * @param string $val
     */
    public static function checkIllName($val) {
        $ret = false;
        $len = mb_strlen($val);
        if ($len <= 50 && $len >= 1) {
            //if (!empty($val) && preg_match('/^[\x{4e00}-\x{9fa5}]{2,5}$/u', $val)) {
            $ret = true;
        }
        return $ret;
    }

    /**
     * 检测电话是否非法
     *
     * @author william.hu
     *
     * @param string $val
     */
    public static function checkIllPhone($val, $type = 'phone') {
        $ret     = false;
        $pattern = array(
            'phone' => '/^[1][34578][0-9]{9}$/',
            'tel'   => '/^(010|02\d{1}|0[3-9]\d{2})\d{7,9}$/'
        );
        if (in_array($type, array_keys($pattern))) {
            if (!empty($val) && preg_match($pattern[$type], $val)) {
                $ret = true;
            }
        }
        return $ret;
    }

    public static function getAppc($cn) {
        $appcKey = "APPC:{$cn}";
        return Common::getCache()->get($appcKey);
    }

    public static function setAppc($cn, $data) {
        $appcKey = "APPC:{$cn}";
        return Common::getCache()->set($appcKey, $data, Common::T_ONE_DAY);
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

    public static function getImgPath() {
        return Yaf_Application::app()->getConfig()->attachroot . '/attachs/3g';
    }

    public static function jsonEncode($val) {
        return json_encode($val, JSON_UNESCAPED_UNICODE);
    }

    public static function export($list, $sdate, $edate, $filename = '报表',$header=array()) {
        ini_set('memory_limit', '1024M');
        header('Content-Type: application/vnd.ms-excel;charset=GB2312');
        $filename .= $sdate . '至' . $edate . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=' . iconv('utf8', 'gb2312', $filename));
        $out = fopen('php://output', 'w');

        fputcsv($out, array(chr(0xEF) . chr(0xBB) . chr(0xBF)));
        $i = 0;
        if(empty($header)){
	        foreach ($list as $v) {
	            if ($i == 0) {
	                fputcsv($out, array_keys($v));
	            }
	            $i++;
	            fputcsv($out, $v);
	        }
        }else{
        	fputcsv($out, $header);
        	foreach ($list as $v){
        		fputcsv($out, $v);
        	}
        }
        exit;
    }

    public static function getMonthLastDay($year, $month) {
        switch ($month) {
            case 4 :
            case 6 :
            case 9 :
            case 11 :
                $days = 30;
                break;
            case 2 :
                if ($year % 4 == 0) {
                    if ($year % 100 == 0) {
                        $days = $year % 400 == 0 ? 29 : 28;
                    } else {
                        $days = 29;
                    }
                } else {
                    $days = 28;
                }
                break;
            default :
                $days = 31;
                break;
        }
        return $days;
    }

    /**
     * 海外版
     * @return bool
     */
    public static function isOverseas() {
        return stristr(ENV, 'overseas') ? true : false;
    }

    /**
     * 外发版
     * @return bool
     */
    public static function isSige() {
        return stristr(ENV, 'sige') ? true : false;
    }

    public static function writeContentToFile($filename, $content) {
        $fileinfo = pathinfo($filename);
        if (!file_exists($fileinfo['dirname'])) {
            mkdir($fileinfo['dirname'], 0777, true);
        }
        file_put_contents($filename, $content);
        return $filename;
    }

    public static function getPrevShortUrl() {
        $pervUrl = Yaf_Application::app()->getConfig()->webroot;
        if (!empty(Yaf_Application::app()->getConfig()->clickroot)) {
            $pervUrl = Yaf_Application::app()->getConfig()->clickroot;
        }
        return $pervUrl . '/index/tj';
    }

    public static function getPrevSearchUrl() {
        $pervUrl = Yaf_Application::app()->getConfig()->webroot;
        if (!empty(Yaf_Application::app()->getConfig()->clickroot)) {
            $pervUrl = Yaf_Application::app()->getConfig()->clickroot;
        }
        return $pervUrl . '/index/keyword';
    }


    public static function getHttpReferer() {
        $str = '';
        if (isset($_SERVER['HTTP_REFERER']) && filter_var($_SERVER['HTTP_REFERER'], FILTER_VALIDATE_URL)) {
            $str = $_SERVER['HTTP_REFERER'];
        }
        return $str;
    }

    public static function getRequestUrl() {
        $str = Yaf_Application::app()->getConfig()->webroot;
        if (isset($_SERVER['REQUEST_URI']) && filter_var($_SERVER['REQUEST_URI'], FILTER_VALIDATE_URL)) {
            $str = $_SERVER['REQUEST_URI'];
        }
        return $str;
    }

    public static function object2array($object) {
        return @json_decode(@json_encode($object), 1);
    }

    public static function staticDir() {
        $dataPath = Common::getConfig('siteConfig', 'cachePath');
        $dir      = $dataPath . '3g_cache/static/';
        if (APP_VER == 'sige') {
            $dir = $dataPath . 'sige_cache/static/';
        }
        return $dir;
    }

    public static function getImgContent($v) {
        if (empty($v)) {
            return '';
        }

        if (!stristr($v, 'http')) {
            $imgUrl = Common::getImgPath() . $v;
        } else {
            $imgUrl = $v;
        }

        $apcKey = 'welcome_content:' . crc32($v);
        $img    = apc_fetch($apcKey);
        if (empty($img)) {
            $img = Util_Image::base64($imgUrl);
            apc_store($apcKey, $img, Common::T_ONE_DAY);
        }
        return $img;
    }


    public static function getUName() {
        $uName = Util_Cookie::get('U_NAME');
        if (empty($uName)) {
            $uaArr = Util_Http::ua();
            if (!empty($uaArr['uuid'])) {
                if ($uaArr['uuid'] == 'FD34645D0CF3A18C9FC4E2C49F11C510' || $uaArr['uuid'] == '080424806FE364E6EEE4C80A9AFDA27C') {//空的imei
                    $uaArr['uuid'] = '';
                }
            }

            $uName = !empty($uaArr['uuid']) ? crc32($uaArr['uuid']) : crc32(uniqid());
            Util_Cookie::set('U_NAME', $uName, false, strtotime("+360 day"), '/');
        }
        return $uName;
    }

    //php获取当前访问的完整url地址
    static public function getCurUrl() {
        $url = 'http://';
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $url = 'https://';
        }
        if ($_SERVER['SERVER_PORT'] != '80') {
            $url .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
        } else {
            $url .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        }
        return $url;
    }

    static public function substr($val, $len) {
        return mb_strlen($val, 'utf-8') > $len ? mb_substr($val, 0, $len, 'utf-8') . '...' : $val;
    }

}