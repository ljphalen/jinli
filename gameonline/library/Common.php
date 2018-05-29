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
	 * @param bool $flag
	 * @return string
	 */
	static public function getPages($count, $page, $perPage, $url, $ajaxCallBack = '', $flag = 0, $class='') {
		
		if( $flag) { 
			$page_str  = Util_Page::show_page_aimi($count, $page, $perPage, $url, '=',$ajaxCallBack , $class);
		} else {
			$page_str  = Util_Page::show_page($count, $page, $perPage, $url, '=',$ajaxCallBack );
		}
		  
		return $page_str;
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
	 * queue对象
	 */
	static public function getQueue() {
		$config = Common::getConfig('redisConfig', ENV);
		return Queue_Factory::getQueue($config);
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
			$config = Common::getConfig('beanstalkConfig', ENV);
			$beanstalk->config($config);
		}
		return $beanstalk;
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
		image2webp($attachPath.$url, $attachPath.$url.".webp");
		return Common::formatMsg(0, '', $url);
	}
	
	
	/**
	 *
	 * @param unknown_type $name
	 * @param unknown_type $dir
	 * @return multitype:unknown_type
	 */
	static public function uploadApk($name, $dir) {
		$img = $_FILES[$name];
		$attachPath = Common::getConfig('siteConfig', 'attachPath');
		if ($img['error']) {
			return Common::formatMsg(-1, '上传文件失败:' . $img['error']);
		}
		$allowType = array('apk' => '','APK' => '');
		$savePath = sprintf('%s/%s/%s', $attachPath, $dir, date('Ym'));
		$uploader = new Util_Upload($allowType);
		$ret = $uploader->upload($name, date('His'), $savePath);
		if ($ret < 0) {
			return Common::formatMsg(-1, '上传失败:'.$ret);
		}
		$url = sprintf('/%s/%s/%s', $dir, date('Ym'), $ret['newName']);
		return Common::formatMsg(0, '', $url);
	}
	
	static public function downloadImg($imgurl, $dir, $withWebp = true) {
		if (!file_exists($dir)) mkdir($dir, 0777, true);
	
		//get remote file info
		$headerInfo = get_headers($imgurl, 1);
		$size = $headerInfo['Content-Length'];
		if (!$size) return false;
		$type = $headerInfo['Content-Type'];
		$mimetypes = array(
				'bmp' => 'image/bmp',
				'gif' => 'image/gif',
				'jpeg' => 'image/jpeg',
				'jpg' => 'image/jpeg',
				'png' => 'image/png',
		);
		if(!in_array($type,$mimetypes)) return false;
		$ext = end(explode("/", $type));
		$filename = md5($imgurl).".".$ext;
	
		$localFile = $dir."/".$filename;
		//if is exists
		if (file_exists($localFile)) return $filename;
	
		//download
		ob_start();
		readfile($imgurl);
		$imgData = ob_get_contents();
		ob_end_clean();
		$fd = fopen($localFile , 'a');
		if (!$fd) {
			fclose($fd);
			return false;
		}
		fwrite($fd, $imgData);
		fclose($fd);
		if ($withWebp) image2webp($localFile, $localFile.".webp");
		return $filename;
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
	 * error log
	 * @param string $error
	 * @param string $file
	 */
	static public function log($error, $file) {
		error_log(implode(" ", $error)."\n", 3, Common::getConfig('siteConfig', 'logPath') . $file);
	}
	
	/**
	 * 点击统计
	 * @param string $mobile
	 * @param string $content
	 */
	static public function tjurl($url,$id, $type, $redirect, $tj_type = '') {
		$redirect = html_entity_decode($redirect);
		if ($tj_type) {
			if (strpos($redirect, '?') !== false) {
				$redirect.=sprintf('&intersrc=%s', $tj_type);
			} else {
				$redirect.=sprintf('?intersrc=%s', $tj_type);
			}
		}
		return sprintf('%s?id=%s&type=%s&_url=%s',$url, $id, $type, urlencode($redirect));
	}
	
	public static function getAttachPath() {
		$attachroot = Yaf_Application::app()->getConfig()->attachroot;
		return $attachroot . '/attachs/game';
	}
	
	public static function getWebRoot() {
	    if (DEFAULT_MODULE == "Front") {
			return Yaf_Application::app()->getConfig()->amiroot;
		}  else if (DEFAULT_MODULE == "Kingstone") {
			return Yaf_Application::app()->getConfig()->kingstoneroot;
		} else if (DEFAULT_MODULE == "Channel") {
			return Yaf_Application::app()->getConfig()->channelroot;
		} else if (DEFAULT_MODULE == "Admin") {
			return Yaf_Application::app()->getConfig()->adminroot;
		}
		
		return Yaf_Application::app()->getConfig()->webroot;
	}
	
	public static function getIniConfig($name) {
		return Yaf_Application::app()->getConfig()->$name;
	}
	
	/**
	 * 判断请求是否为手机客户端来源discuz方法
	 */
	public static function checkMobileRequest(){
		if((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') !== false)) return true;
		if(isset($_SERVER['HTTP_X_WAP_PROFILE'])) return true;
		if(isset($_SERVER['HTTP_PROFILE'])) return true;
		$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
		if(!isset($ua)) return false;
		$mk = array('iphone', 'android', 'phone', 'mobile', 'wap', 'netfront', 'java', 'opera mobi', 'opera mini',
				'ucweb', 'windows ce', 'symbian', 'series', 'webos', 'sony', 'blackberry', 'dopod', 'nokia', 'samsung',
				'palmsource', 'xda', 'pieplus', 'meizu', 'midp', 'cldc', 'motorola', 'foma', 'docomo', 'up.browser',
				'up.link', 'blazer', 'helio', 'hosin', 'huawei', 'novarra', 'coolpad', 'webos', 'techfaith', 'palmsource',
				'alcatel', 'amoi', 'ktouch', 'nexian', 'ericsson', 'philips', 'sagem', 'wellcom', 'bunjalloo', 'maui', 'smartphone',
				'iemobile', 'spice', 'bird', 'zte-', 'longcos', 'pantech', 'gionee', 'portalmmm', 'jig browser', 'hiptop',
				'benq', 'haier', '320x320', '240x320', '176x220', 'windows phone', 'cect', 'compal', 'ctl', 'lg',
				'nec', 'tcl', 'alcatel', 'ericsson', 'bird', 'daxian', 'dbtel', 'eastcom', 'pantech', 'dopod', 'philips', 'haier',
				'konka', 'kejian', 'lenovo', 'benq', 'mot', 'soutec', 'nokia', 'sagem', 'sgh',
				'sed', 'capitel', 'panasonic', 'sonyericsson', 'sharp', 'amoi', 'panda', 'zte');
	
		// 从HTTP_USER_AGENT中查找手机浏览器的关键字
		if((preg_match("/(".implode('|',$mk).")/i",$ua) || strpos($ua,'^lct') !== false)  && strpos($ua,'ipad') === false) {
			return true;
		}
		return false;
	}
		
	/**
	 * 百度跳转
	 * @param string $mobile
	 * @param string $content
	 */
	static public function bdurl($url,$id, $type, $redirect, $tj_type = '', $from, $gnname, $keyword, $stype) {
		$redirect = html_entity_decode($redirect);
		if ($tj_type) {
			if (strpos($redirect, '?') !== false) {
				$redirect.=sprintf('&intersrc=%s', $tj_type);
			} else {
				$redirect.=sprintf('?intersrc=%s', $tj_type);
			}
		}
		return sprintf('%s?id=%s&type=%s&from=%s&gname=%s&keyword=%s&intersrc=%s&stype=%s&_url=%s',$url, $id, $type, $from, $gnname, $keyword,$type, $stype, urlencode($redirect));
	}
	
	/**
	 * 获取用户的操作系统
	 * 
	 */
	static public function browserPlatform () {
		$agent = $_SERVER['HTTP_USER_AGENT'];
		
		$browser_platform=='';
		if (eregi('win', $agent) && strpos($agent, '95')) {
			$browser_platform=true;
		} elseif (eregi('win 9x', $agent) && strpos($agent, '4.90')) {
			$browserplatform=true;
		} elseif (eregi('win',$agent) && ereg('98', $agent)) {
			$browser_platform=true;
		} elseif (eregi('win', $agent) && eregi('nt 5.0', $agent)) {
			$browser_platform=true;
		} elseif (eregi('win', $agent) && eregi('nt 5.1', $agent)) {
			$browser_platform=true;
		} elseif (eregi('win',$agent) && eregi('nt 6.0',$agent)) {
			$browser_platform=true;
		} elseif (eregi('win', $agent) && eregi('nt 6.1', $agent)) {
			$browser_platform=true;
		} elseif (eregi('win', $agent) && ereg('32', $agent)) {
			$browser_platform=true;
		} elseif (eregi('win', $agent) && eregi('nt', $agent)) {
			$browser_platform=true;
		} elseif (eregi('Mac OS', $agent)) {
			$browser_platform=false;
		} elseif (eregi('linux', $agent)) {
			$browser_platform=false;
		} elseif (eregi('unix', $agent)) {
			$browser_platform=false;
		} elseif (eregi('sun', $agent) && eregi('os', $agent)) {
			$browser_platform=false;
		} elseif (eregi('ibm',$agent) && eregi('os', $agent)) {
			$browser_platform=false;
		} elseif (eregi('Mac', $agent) && eregi('PC', $agent)) {
			$browser_platform=false;
		} elseif (eregi('PowerPC', $agent)) {
			$browser_platform=false;
		} elseif (eregi('AIX', $agent)) {
			$browser_platform=false;
		} elseif (eregi('HPUX', $agent)) {
			$browser_platform=false;
		} elseif (eregi('NetBSD', $agent)) {
			$browser_platform=false;
		} elseif (eregi('BSD',$agent)) {
			$browser_platform=false;
		} elseif (ereg('OSF1', $agent)) {
			$browser_platform=false;
		} elseif (ereg('IRIX', $agent)) {
			$browser_platform=false;
		} elseif (eregi('FreeBSD', $agent)) {
			$browser_platform=false;
		}
		if ($browser_platform == '') {$browserplatform = false; }
		return $browser_platform;
	}
	
	/**
 	* google api 二维码生成【QRcode可以存储最多4296个字母数字类型的任意文本，具体可以查看二维码数据格式】
 	* @param string $chl 二维码包含的信息，可以是数字、字符、二进制信息、汉字。不能混合数据类型，数据必须经过UTF-8 URL-encoded.如果需要传递的信息超过2K个字节，请使用POST方式
 	* @param int $widhtHeight 生成二维码的尺寸设置
 	* @param string $EC_level 可选纠错级别，QR码支持四个等级纠错，用来恢复丢失的、读错的、模糊的、数据。
 	* 						   L-默认：可以识别已损失的7%的数据
 	* 						   M-可以识别已损失15%的数据
 	* 						   Q-可以识别已损失25%的数据
 	* 						   H-可以识别已损失30%的数据
 	* @param int $margin 生成的二维码离图片边框的距离
 	*/
	static public function  generateQRfromGoogle($chl,$widhtHeight ='100',$EC_level='H',$margin='0',$class='')
	{
		$chl = urlencode($chl);
		return  '<img src="http://chart.apis.google.com/chart?chs='.$widhtHeight.'x'.$widhtHeight.'&cht=qr&chld='.$EC_level.'|'.$margin.'&chl='.$chl.'" alt="QR code" width="'.$widhtHeight.'" Height="'.$widhtHeight.'" class="'.$class.'" />';
	}
	
	/**
	 * 二维码生成
	 * @param unknown_type $chl 二维码数据
	 * @param unknown_type $widhtHeight
	 * @param unknown_type $EC_level
	 * @param unknown_type $margin
	 * @param unknown_type $class
	 */
	static public function  generateQRfromLocal($chl,$widhtHeight ='100',$EC_level='H',$margin='0',$class='')
	{
		include_once "Util/PHPQRcode/qrlib.php";
		$cacheKey = "qr-co-" . md5($chl);
		$cache = Common::getCache ();
		$data = $cache->get($cacheKey );
		if (!$data) {
			$data = QRcode::png($chl,false,'QR_ECLEVEL_'.$EC_level,$size=3,$margin);
			$cache->set ( $cacheKey, $data, 3*3600);
		}
		return $data;
	}
	
	/**
	 * 发送邮件
	 * @param  $title 标题
	 * @param  $body 主体内容
	 * @param  $to 发送的邮箱地址
	 * @param  $author 作者
	 * @param  $type 邮件类型，HTML或TXT
	 * @return 布尔类型
	 */
	static public function sendEmail ($title = '', $body = '', $to = '', $author = '', $type = 'HTML' ) {
		$smtp_config = Common::getConfig('smtpConfig');
		
		$smtp = new  Util_Smtp( $smtp_config['mailhost'], $smtp_config['mailport'], $smtp_config['mailauth'], $smtp_config['companymail'], $smtp_config['mailpasswd']);
		$author = ($author == '') ?$smtp_config['mailauthor']: $author ;
		$send = $smtp->sendmail($to,$smtp_config['companymail'], $author, $title, $body, $type);
		
		return $send;
	}	
	
	/**
	 * 验证此应用是否安全mold=2腾讯
	 * @return 数组 返回的参数，1是安全，无广告 0 有病毒，有插件,有广告等等。-1未知
	 */
	static public function applyIsSafe($certificate) {
		if(!is_array($certificate)){
			return false;
		}
		$safe_arr = array();
		foreach ($certificate as $val){
		 	if($val['mold'] == 2) {
		 		$response_res = json_decode($val['response_res'],true);
				if($response_res){
					if($response_res['safetype'] == 1){
						$safe_flag = 1;
					}elseif($response_res['safetype'] == 0){
						$safe_flag = -1;
					}else{
						$safe_flag = 0;
					}
					if($response_res['banner'] == 1){
						$ad_flag = 0 ;
					}elseif($response_res['banner'] == -1){
						$ad_flag = -1 ;
					}elseif($response_res['banner'] == 0){
						$ad_flag = 1;
					}
				
					$safe_arr = array('safe_flag'  => $safe_flag,
							          'ad_flag'   => $ad_flag
							           );
				}else{
					$safe_arr = array('safe_flag'=> -1);
				}
				
			}
		}		
		return  $safe_arr;
	}
	
	/**
	 * 去掉html标签
	 * @param unknown_type $document
	 */
	static public function replaceHtmlAndJs( $document ){
		$search = array ("'<script[^>]*?>.*?</script>'si",  // 去掉 javascript
				"'<[\/\!]*?[^<>]*?>'si",           // 去掉 HTML 标记
				"'([\r\n])[\s]+'",                 // 去掉空白字符
				"'&(quot|#34);'i",                 // 替换 HTML 实体
				"'&(amp|#38);'i",
				"'&(lt|#60);'i",
				"'&(gt|#62);'i",
				"'&(nbsp|#160);'i",
				"'&(iexcl|#161);'i",
				"'&(cent|#162);'i",
				"'&(pound|#163);'i",
				"'&(copy|#169);'i",
				"'&#(\d+);'e");                    
	
		$replace = array ("","","\\1","\"","&","<",">"," ",chr(161),chr(162),chr(163),chr(169),"chr(\\1)");	
		return @preg_replace($search,$replace,$document);
	}
	
	/**
	 * 艾米的拼接链接
	 * @param unknown_type $url
	 * @param unknown_type $channel
	 * @param unknown_type $cku
	 * @param unknown_type $action
	 * @param unknown_type $object
	 * @param unknown_type $intersrc
	 * @param unknown_type $redirect
	 * @return string
	 */
	static public function aimiTjUrl($url,$channel,$cku,$action,$object,$intersrc,$redirect='',$type=1) {
		if (stripos($redirect, 'game.amigo.cn') || (stripos($redirect, 'gionee.com') &&  !stripos($redirect, 'dev.game.gionee.com') )) {	
			if($channel){
				if (stripos($redirect, '?') !== false) {
					$redirect.=sprintf('&channel=%s&cku=%s&action=%s&object=%s&intersrc=%s', $channel, $cku,$action, $object,$intersrc);
				} else {
					$redirect.=sprintf('?channel=%s&cku=%s&action=%s&object=%s&intersrc=%s', $channel, $cku,$action, $object,$intersrc);
				}
			}else{
				if (stripos($redirect, '?') !== false) {
					$redirect.=sprintf('&cku=%s&action=%s&object=%s&intersrc=%s', $cku,$action, $object,$intersrc);
				} else {
					$redirect.=sprintf('?cku=%s&action=%s&object=%s&intersrc=%s', $cku,$action, $object,$intersrc);
				}
			}
			return sprintf('%s?type=%s&_url=%s',$url, $type,urlencode($redirect));
		}else{
			if($channel){
				if (stripos($url, '?') !== false) {
					$url.=sprintf('&channel=%s&cku=%s&action=%s&object=%s&intersrc=%s', $channel, $cku,$action, $object,$intersrc);
				} else {
					$url.=sprintf('?channel=%s&cku=%s&action=%s&object=%s&intersrc=%s', $channel, $cku,$action, $object,$intersrc);
				}
			}else{
				if (stripos($url, '?') !== false) {
					$url.=sprintf('&cku=%s&action=%s&object=%s&intersrc=%s', $cku,$action, $object,$intersrc);
				} else {
					$url.=sprintf('?cku=%s&action=%s&object=%s&intersrc=%s', $cku,$action, $object,$intersrc);
				}
			}
			return sprintf('%s&type=%s&_url=%s',$url, $type,urlencode($redirect));
		}
	}
	
	
	/**
	 *  弹出信息
	 * @param unknown_type $msg
	 * @param unknown_type $url
	 */
	static public  function alertMsg($msg, $url='' ){
		if ($url){
			echo '<meta charset="utf-8" /><script>alert("'.$msg.'");location.href="'.$url.'";</script>';
		}else{
			echo '<meta charset="utf-8" /><script>alert("'.$msg.'");history.go(-1);</script>';
		}
		exit;
	}
	
	
	static public  function filter($sensitives, $title, $type=1){
		foreach($sensitives as $k=>$v){
			if($type == 1 && $v){
				$title = str_replace($v, "<font color=red>".$v."</font>", $title);
			} else if($type == 2 && $v){
				$title = str_replace($v, "****", $title);
			}
		}
		return $title;
	}
	
	/**
	 * 引入SEO信息
	 */
	static public function addSEO(&$seo_object, $title='', $keyworks='', $description='') {
		if ( $title != '') {
			$seo_object->assign('title',$title);
		}
		if ($keyworks != '') {
			$seo_object->assign('keyworks',$keyworks);
		}
		if ($description != '') {
			$seo_object->assign('description',$description);
		}
	}
	
	/**
	 * 中秋活动跳转
	 * @param unknown_type $url
	 * @param unknown_type $tj_type
	 * @return string
	 */
	static public function monTjurl($url,  $redirect, $tj_type = '') {
		$redirect = html_entity_decode($redirect);
		if ($tj_type) {
			if (strpos($redirect, '?') !== false) {
				$redirect.=sprintf('&intersrc=%s', $tj_type);
			} else {
				$redirect.=sprintf('?intersrc=%s', $tj_type);
			}
		}
		return sprintf('%s?_url=%s',$url, urlencode($redirect));
	}
	
}