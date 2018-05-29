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
	static public function getToken() {
		if (!isset($_COOKIE['_securityCode']) || '' == $_COOKIE['_securityCode']) {
			/*用户登录的会话ID*/
			$key = substr(md5('TOKEN:' . time() . ':' . $_SERVER['HTTP_USER_AGENT']), mt_rand(1, 8), 8);
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
	 * queue对象
	 */
	static public function getQueue() {
		$config = Common::getConfig('redisConfig');
		var_dump($config);
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
		$ext = strtolower(substr(strrchr($img['name'], '.'), 1));
		if($ext != 'gif') image2webp($attachPath.$url, $attachPath.$url.".webp");
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
	    $error = json_encode($error, JSON_UNESCAPED_UNICODE);
		error_log(date('Y-m-d H:i:s') .' ' . $error . "\n", 3, Common::getConfig('siteConfig', 'logPath') . $file);
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
		$cache = Cache_Factory::getCache ();
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
	
	/**
	 * 文件大小大于1000,由M转换为G
	 * @param 　float $numbers
	 * @return float
	 */
	static public  function numConvert($numbers){
		$numbers = $numbers.'M';
		if($numbers >= 1000){
			$numbers = sprintf("%.2f", $numbers /(1024*1024)).'G';;
		}
		return $numbers;
	}
	
    /**
     * 写入日志文件
     * @param unknown_type $path
     * @param unknown_type $file_name
     * @param unknown_type $data
     * @param unknown_type $method
     */
	static public  function WriteLogFile($path, $file_name, $data, $method = 'ab'){
		//日志开关
		$log_status = Game_Service_Config::getValue('log_status');
		//if(!$log_status) return false;
		if(!$path || !$file_name) return false;
		//创建目录
		if(!Util_Folder::isDir($path)){
			Util_Folder::mkRecur($path);
		}
		return Util_File::logFile($path.$file_name, $data, $method);
		
	}
	
	/**
	 * 获取客户段访问IP地址,成功返回客户段IP,失败返回空
	 */
	static public  function  getClientIP() {
		if (isset($_SERVER['HTTP_CLIENT_IP']) and !empty($_SERVER['HTTP_CLIENT_IP'])){
			return $_SERVER['HTTP_CLIENT_IP'];
		}
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) and !empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			return strtok($_SERVER['HTTP_X_FORWARDED_FOR'], ',');
		}
		if (isset($_SERVER['HTTP_PROXY_USER']) and !empty($_SERVER['HTTP_PROXY_USER'])){
			return $_SERVER['HTTP_PROXY_USER'];
		}
		if (isset($_SERVER['REMOTE_ADDR']) and !empty($_SERVER['REMOTE_ADDR'])){
			return $_SERVER['REMOTE_ADDR'];
		} else {
			return "0.0.0.0";
		}
	}
	
	
	
	/**
	 * 两个日期相差的天数,小时，分，秒
	 * @param string $start_time
	 * @param string $end_time
	 * @param string $unit d h i s
	 * @param boolean $isFlag 是否未自然日计算
	 * @return number|boolean
	 */
	static public function diffDate($startDate, $endDate, $unit = "d") { //时间比较函数，返回两个日期相差几秒、几分钟、几小时或几天
		switch ($unit) {
			case 's':
				$dividend = 1;
				break;
			case 'i':
				$dividend = 60; 
				break;
			case 'h':
				$dividend = 3600;
				break;
			case 'd':
				$dividend = 86400;
				break; 
			default:
				$dividend = 86400;
		}
		
		$startTime = strtotime($startDate);
		$endTime = strtotime($endDate);
		if ($startTime && $endTime){
			if($dividend == 86400){
				$startDay = date('Y-m-d 00:00:01', strtotime($startDate));
				$endDay = date('Y-m-d 00:00:01', strtotime($endDate));
				$startTime = strtotime($startDay);
				$endTime = strtotime($endDay);
				return round(($endTime - $startTime) / $dividend);
			}else{
				return round(($endTime - $startTime) / $dividend);
			}
			
		}
		return false;
	}

	
	/**
	 * sp 参数分析
	 * 完整 E3_1.4.9.e_4.2.1_Android4.2.1_720*1280_I01000_wifi_FD34645D0CF3A18C9FC4E2C49F11C510
	 * 机型_客户端版本_金立rom版本_android版本_分辨率（320*480）_渠道号_网络类型_加密imei
	 * @param string $sp
	 * @param string $key
	 */
	static public function parseSp($sp, $key = ''){
		$tmp = array();
		$data = explode('_',$sp);
		$tmp['sp'] = $sp ? $sp : '';
		$tmp['device'] = is_null($data[0]) ? '' : $data[0];
		$tmp['game_ver'] = is_null($data[1]) ? '' : $data[1];
		$tmp['rom_ver'] = is_null($data[2]) ? '' : $data[2];
		$tmp['android_ver'] = is_null($data[3]) ? '' : $data[3];
		$tmp['pixels'] = is_null($data[4]) ? '' : $data[4];
		$tmp['channel'] = is_null($data[5]) ? '' : $data[5];
		$tmp['network'] = is_null($data[6]) ? '' : $data[6];
		$tmp['imei'] = is_null($data[7]) ? '' : $data[7];
		return ($key) ? $tmp[$key] : $tmp;
	}
	
	static public function parseSdkSp($sp, $key = ''){
	    $tmp = array();
	    $data = explode('_',$sp);
	    $length = count($data);
	    $tmp['sp'] = $sp ? $sp : '';
	    $tmp['device'] = is_null($data[0]) ? '' : $data[0];
	    $tmp['game_ver'] = is_null($data[1]) ? '' : $data[1];
	    $tmp['rom_ver'] = is_null($data[2]) ? '' : $data[2];
	    $tmp['android_ver'] = is_null($data[3]) ? '' : $data[3];
	    $tmp['pixels'] = is_null($data[4]) ? '' : $data[4];
	    if (8 == $length) {
	    	$tmp['channel'] = is_null($data[5]) ? '' : $data[5];
    		$tmp['network'] = is_null($data[6]) ? '' : $data[6];
    		$tmp['imei'] = is_null($data[7]) ? '' : $data[7];
	    } else if (7 == $length) {
    	    $tmp['network'] = is_null($data[5]) ? '' : $data[5];
    	    $tmp['imei'] = is_null($data[6]) ? '' : $data[6];
	    }
	    return ($key) ? $tmp[$key] : $tmp;
	}
	
	/**
	 * 返回区间的开始日期与结束日期
	 * @param unknown_type $time
	 * @param unknown_type $section_start
	 * @param unknown_type $section_end
	 */
	static public function getSectionTime($time, $section_start = 1, $section_end = 1){
		
		//
		$time_arr = array();
		if($section_start == 1){
			$time_arr['start_time'] = $time;
		}else{
			$tmp = date('Y-m-d 00:00:00', $time);
			$time_arr['start_time'] = strtotime( $tmp." + ".($section_start-1)." day" );
		}
		
		if($section_end == 1){
			$tmp = date('Y-m-d 23:59:59', $time);
			$time_arr['end_time'] = strtotime($tmp);
		}else{
			$tmp = date('Y-m-d 23:59:59', $time);
			$time_arr['end_time'] = strtotime( $tmp." + ".($section_end-1)." day" );
		}
		
		return $time_arr;
		
	}
	
	
	/**
	 * 算出用户的登录日期显示
	 */
	static public function loginDate($days = 1 ,$cycle = 7 ){
		$tmp = array();
		$z = 0;
		for ($i=1; $i <$days+1; $i++){
			$tmp[$i] = date('Y-m-d',strtotime($z.' day'));
			$z--;
		}
		$j = 0;
		for($i = $days+1; $i <= $cycle; $i++ ){
			$j++;
			$tmp[$i] = date('Y-m-d',strtotime('+'.$j.' day'));
	
		}
		sort($tmp);
		return $tmp ;
	}
	
	/**
	 * 验证客户端加密数据
	 * @param unknown_type $uuid
	 * @param unknown_type $uname
	 * @param unknown_type $clientId
	 * @return boolean
	 */
	static public function verifyClientEncryptData($uuid, $uname, $clientId){
	
		if(!$uuid || !$uname || !$clientId){
			return false;
		}
		
		$replaceStr = Common::encryptClientData($uuid, $uname);
		if(strtolower(md5($replaceStr)) == strtolower($clientId)){
			return true;
		}else{
			return false;
		}
		
	
	
	}
	
	/**
	 * 加密客户端的用户名与密码
	 * @param unknown_type $uuid
	 * @param unknown_type $uname
	 * @return string|Ambigous <unknown, string>
	 */
	static public function encryptClientData($uuid, $uname){
	
		$replaceStr ='';
		if(!trim($uuid) || !trim($uname) ){
			return $replaceStr;
		}
		//位置
		$len = strlen($uname);
		$position = array();
		for($i=0; $i <= $len-1; $i++){
			$position[$i] = (ord($uname[$i])+$i)%32;
		}
		//替换字符
		$replaceStr = $uuid;
		for($i=0; $i <= $len-1; $i++){
			$replaceStr[$position[$i]] = $uname[$i];
		}
		return 	$replaceStr;
	}
	
    static public function isValidImei($imei) {
        if (!$imei) {
            return false;
        }

        return ($imei != 'FD34645D0CF3A18C9FC4E2C49F11C510') ? true : false;
    }
    
    public static function array2object($array) {
    
    	if (is_array($array)) {
    		$obj = new StdClass();
    		 
    		foreach ($array as $key => $val){
    			$obj->$key = $val;
    		}
    	}
    	else { $obj = $array; }
    
    	return $obj;
    }
    
    public static function object2array($object) {
    	if (is_object($object)) {
    		foreach ($object as $key => $value) {
    			$array[$key] = $value;
    		}
    	}
    	else {
    		$array = $object;
    	}
    	return $array;
    }
    
    static public function getSeasonTimeRange(){
    	$season = ceil((date('n'))/3);//当月是第几季度
    	$startTime =  date('Y-m-d H:i:s', mktime(0, 0, 0, $season*3-3+1,1,date('Y')));
    	$endTime =    date('Y-m-d H:i:s', mktime(23,59,59,$season*3,date('t',mktime(0, 0 , 0,$season*3,1,date("Y"))),date('Y')));
        return array('startTime'=>$startTime, 'endTime'=>$endTime);
    }
    
    static public function getClientVersion($clientVersion){
    	$originArr = explode('.', $clientVersion);
    	$version = sprintf("%s.%s.%s", $originArr[0], $originArr[1], $originArr[2]);
    	return $version;
    	 
    }
    
    static public function getSystemVersion($systemVersion){
    	if(!$systemVersion){
    		return false;
    	}
    	$systemVersion = substr($systemVersion, 7);
    	return $systemVersion;
    }
    
    static public function compareSysytemVersion($systemVersion){
    	if(!$systemVersion){
    		return false;
    	}
    	$result = strcmp($systemVersion, '4.2');
    	if($result >= 0){
    		return true;
    	}else{
    		return false;
    	}
    }
    
    
    
    static public function compareClientVersion($originalClientVersion, $destinationClientVersion){
    	$result = strcmp($originalClientVersion, $destinationClientVersion);
    	if($result >= 0){
    		return true;
    	}else{
    		return false;
    	}
    }
    
    /**
        0 - 如果两个版本相等
        <0 - 如果 $version1 小于 $version2
        >0 - 如果 $version1 大于 $version2
     * @return number
     */
    private static function compareVersion($version1, $version2) {
        if(! $version1) {
            return -1;
        }
        if(! $version2) {
            return 1;
        }
    	$result = 0;
    	if($version1 == $version2) {
    	    return $result;
    	}
    	$version1Arr = explode('.', $version1);
    	$version1Length = count($version1Arr);
    	$version2Arr= explode('.', $version2);
    	$version2Length = count($version2Arr);
        
    	$minLength = $version1Length;
    	if($version1Length > $version2Length) {
    	    $minLength = $version2Length;
    	    $result = -1;
    	}else if($version1Length < $version2Length) {
    	    $result = 1;
    	}
    	for ($i = 0; $i <= $minLength; $i ++) {
    	    if (is_numeric($version1Arr[$i]) && is_numeric($version2Arr[$i])) {
    	        if($version1Arr[$i] == $version2Arr[$i]) {
    	            continue;
    	        }
    	        $result = ($version1Arr[$i] > $version2Arr[$i]) ? 1 : -1;
    	        break;
    	    }
            $tmp = strncmp($version1Arr[$i], $version2Arr[$i]);
    	    if($tmp == 0) continue;
    	    $result = $tmp;
    	    break;
    	}
    	return $result;
    }
    
    public static function isAfterVersion($version, $afterVersion) {
        return self::compareVersion($version, $afterVersion) >= 0;
    }
    
    public static function isBeforeVersion($version, $beforeVersion) {
        return self::compareVersion($version, $beforeVersion) <=0;
    }
    
    /**
     * 取手机客户端两个版本号进行对比
     * 
     * @param string $originVersion 手机版本号 1.5.0
     * @param string $destVersion 手机客户端版本号 1.5.3
     * @return 如果$originVersion >= $destVersion 返回结果为 true
     * 		   如果$originVersion < $destVersion  返回结果为 false
     */
    public static function compareWithVersion($originVersion, $destVersion){
    	if($originVersion == $destVersion) {
    		return true;
    	}
    	
    	$originArr = explode('.', $originVersion);
    	$originLength = count($originArr);
    	$destArr= explode('.', $destVersion);
    	$destLength = count($destArr);
    	$maxLength = ($originLength >= $destLength) ? $originLength : $destLength;
    	
    	$flag = false;
    	for($i=0;$i<=$maxLength;$i++){
    		//originArr该段存在，$destArr该段不存在
    		if($originArr[$i] && (!$destArr[$i])){
    			$flag = true;
    			break;
    		}
    		//originArr该段不存在，$destArr该段存在
    		if((!$originArr[$i]) && $destArr[$i]){
    			$flag = false;
    			break;
    		}
    		//originArr and $destArr 该段相等比较下一段
    		if($originArr[$i] == $destArr[$i]) continue;
    		
    		if($i <= 3){
    			//数字比较大小
    			$flag = ($originArr[$i] > $destArr[$i]) ? true : false;
    			break;
    		} else{
    			//字母自然比较
    			$flag = (strncmp($originArr[$i], $destArr[$i]) > 0) ? true : false;
    			break;
    		}
    	}
    	return $flag;
    }
    
    
    static public function filterCommonApps($oldAppList) {
	    $filterList = self::getConfig('appFilterConfig');

    	$newAppList = array();
    	foreach($oldAppList as $key => $value) {
			if(!array_key_exists($value, $filterList)){
				$newAppList[] = $value;
			}
		}
    	return $newAppList;
    }
    
    static public function fillStyle($data){
    	if(!$data) return "";
    	$content = html_entity_decode($data, ENT_QUOTES);
    	//去除html空白处理
    	$subject = strip_tags($content, '<img><a>');
    	$pattern = array('/\s/','/&nbsp;/i');//去除空白跟空格
    	$text = preg_replace($pattern, '', $subject);
    	if(empty($text)) return "";
    
    	$html = <<<str
    	<style>
    		html,body,div,span,h1,h2,h3,h4,h5,h6,p,dl,dt,dd,ol,ul,li,a,em,img,small,strike,strong,form,label,canvas,footer,header,nav,output{
    			margin:0; padding:0;
    		}
    		.ui-editor {
  				word-break: break-all;
    			line-height: 1.2rem;
			}
			.ui-editor i, .ui-editor em {
  				font-style: italic !important;
			}
			.ui-editor b {
  				font-weight: bold !important;
			}
			.ui-editor u {
  				text-decoration: underline !important;
			}
			.ui-editor s {
  				text-decoration: line-through !important;
			}
			.ui-editor ul li {
  				list-style: initial;
  				margin-left: 1rem !important;
			}
			.ui-editor ol li {
  				list-style: decimal;
  				margin-left: 1rem !important;
			}
			.ui-editor span, .ui-editor p, .ui-editor h1, .ui-editor h2, .ui-editor h3, .ui-editor h4, .ui-editor h5 {
  				white-space: normal !important;
			}
			.ui-editor span, .ui-editor p {
  				line-height: 1.2rem;
			}
			.ui-editor img {
  				padding-top: 5px;
 				max-width: 100% !important;
  				width: auto;
  				height: auto;
  				display: block;
 				margin: 0 auto;
			}
			.ui-editor table {
  				margin: 4px 0;
  				max-width: 300px !important;
			}
			.ui-editor h1, .ui-editor h2, .ui-editor h3 {
  				font-size: 1.2rem !important;
  				line-height: 1.5rem;
			}
			.ui-editor h4, .ui-editor h5, .ui-editor h6 {
  				font-size: 1.2rem !important;
  				line-height: 1.3rem;
			}
    	</style>
    	<div class="ui-editor" style='font-size:13px; color:#777777;'>
str;
    	$html.= $content.'</div>';
    	return base64_encode($html);
    }
    
    static public function getOrderOutput($action, $param, $version) {
    	$data['ordertag'] = 'gamehallorder';
    	$data['action'] = $action;
    	$data['version'] = $version;
    	$data['param'] = $param;
    	
    	$output = array(
    			'success' => true,
    			'msg' => '',
    			'sign' => 'GioneeGameHall',
    			'data' => $data
    	);
    	
    	return $output;
    }
}
