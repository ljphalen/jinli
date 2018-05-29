<?php
/**
 * 过滤数组中元素的空白符号
 * @return string
 */
function array_trim($arr) {
	if (is_array($arr))
		$arr = array_map("array_trim", $arr);
	else
		$arr = trim($arr);
	return $arr;
}

function activeCode() {
	return sha1 ( uniqid ( microtime ( true ) . rand_string ( 2 ) ) );
}

/**
 * 生成唯一码
 * @return string
 */
function generateKey() {
	$randCode = rand_string(16);
	$uniqid = uniqid(microtime(true) . $randCode);
	return md5($uniqid);
}

//截取字符
function utf_substr($str, $len = 10, $suffix = '...') {
	$nstr = $str;
	for ($i = 0; $i < $len; $i++) {
		$temp_str = substr($nstr, 0, 1);
		if (ord($temp_str) > 127) {
			$i++;
			if ($i < $len) {
				$new_str[] = substr($nstr, 0, 3);
				$nstr = substr($nstr, 3);
			}
		} else {
			$new_str[] = substr($nstr, 0, 1);
			$nstr = substr($nstr, 1);
		}
	}
	if (strlen($str) > $len)
		$new_str[] = $suffix;
	return join($new_str);
}

/**
 * 创建文件目录
 */
function mkdirs($path, $lastoneisfile = false, $rights = 0777) {
	if (trim($path) == '')
		return;
	if (!$lastoneisfile && substr($path, -1) != '/') {
		$path = $path . "/";
	}
	if (is_dir($path)) {
		return true;
	} //found it!
	$path = str_replace("\\", "/", $path);
	$path = preg_replace('/\/+/i', '/', $path);
	$pathes = explode('/', $path);
	$cnt = count($pathes) - 1;
	$dir = current($pathes) . "/";
	if (!is_dir($dir . "/")) {
		mkdir($dir);
	}
	for ($i = 0; $i < $cnt; $i++) {
		if (!is_dir($dir . "/")) {
			@mkdir($dir);
			@chmod($dir, $rights);
		}
		$dir .= next($pathes) . "/";
	}
}

function curl_get($url,$params=array())
{
	   $ch = curl_init();
        // 设置 curl 相应属性
        curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($params));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $returnTransfer = curl_exec($ch);
        curl_close($ch);
        return $returnTransfer; 
}

function curl_post($url, array $post = NULL, array $options = array()) {
	$defaults = array(CURLOPT_POST => 1, CURLOPT_HEADER => 0,
			CURLOPT_URL => $url, CURLOPT_FRESH_CONNECT => 1,
			CURLOPT_RETURNTRANSFER => 1, CURLOPT_FORBID_REUSE => 1,
			CURLOPT_TIMEOUT => 5,
			CURLOPT_POSTFIELDS => http_build_query($post));
	$ch = curl_init();
	curl_setopt_array($ch, ($options + $defaults));
	$result = curl_exec($ch);
	// 	$info = curl_getinfo($ch);
	// 	Log::write(print_r($info,true),'info');
	curl_close($ch);
	return $result;
}

//当前时间
function currentDate() {
	return date('Y-m-d H:i:s');
}
//格式化时间
function formatDate($dateInt){
	if (empty($dateInt))
		return '';
	return date("Y-m-d H:i:s", $dateInt);
}
/**
 * 计算两时间之间的差
 * @param string $end 指定时间
 * @param string $out_in_array 返回是否为数组
 * @return unknown|multitype:Ambigous <>
 */ 
function diff_date($datetime, $out_in_array = true) {
	$intervalo = date_diff(date_create(), date_create($datetime));

	$out = $intervalo
			->format(
					"Years:%Y,Months:%M,Days:%d,Hours:%H,Minutes:%i,Seconds:%s");

	if (!$out_in_array)
		return $out;
	$a_out = array();

	$a_tmp = explode(',', $out);
	foreach ($a_tmp as $v) {
		$_v = explode(':', $v);
		$a_out[$_v[0]] = $_v[1];
	}

	return $a_out;
}
/**
 * 反序列化
 * @param unknown $data
 * @return mixed
 */
function dunserialize($data) {
	if (($ret = unserialize($data)) === false) {
		$ret = unserialize(stripslashes($data));
	}
	return $ret;
}
/**
 * 
 * @param unknown $string
 * @param unknown $length
 * @param number $in_slashes
 * @param number $out_slashes
 * @param number $bbcode
 * @param number $html
 * @return string
 */
function getstr($string, $length, $in_slashes = 0, $out_slashes = 0,
		$bbcode = 0, $html = 0) {
	global $_G;

	$string = trim($string);
	$sppos = strpos($string, chr(0) . chr(0) . chr(0));
	if ($sppos !== false) {
		$string = substr($string, 0, $sppos);
	}
	if ($in_slashes) {
		$string = dstripslashes($string);
	}
	$string = preg_replace('/\[hide=?\d*\](.*?)\[\/hide\]/is', '', $string);
	if ($html < 0) {
		$string = preg_replace('/(\<[^\<]*\>|\r|\n|\s|\[.+?\])/is', ' ',
				$string);
	} elseif ($html == 0) {
		$string = dhtmlspecialchars($string);
	}

	if ($length) {
		$string = cutstr($string, $length);
	}
	/*
	if($bbcode) {
	    require_once DISCUZ_ROOT.'./source/class/class_bbcode.php';
	    $bb = & bbcode::instance();
	    $string = $bb->bbcode2html($string, $bbcode);
	}
	 */
	if ($out_slashes) {
		$string = daddslashes($string);
	}
	return trim($string);
}
/**
 * 
 * @param unknown $string
 * @return unknown|string
 */
function dstripslashes($string) {
	if (empty($string))
		return $string;
	if (is_array($string)) {
		foreach ($string as $key => $val) {
			$string[$key] = dstripslashes($val);
		}
	} else {
		$string = stripslashes($string);
	}
	return $string;
}
/**
 * 
 * @param unknown $string
 * @param unknown $length
 * @param string $dot
 * @return unknown|string
 */
function cutstr($string, $length, $dot = ' ...') {
	if (strlen($string) <= $length) {
		return $string;
	}

	$pre = chr(1);
	$end = chr(1);
	$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'),
			array($pre . '&' . $end, $pre . '"' . $end, $pre . '<' . $end,
					$pre . '>' . $end), $string);

	$strcut = '';
	if (strtolower(CHARSET) == 'utf-8') {

		$n = $tn = $noc = 0;
		while ($n < strlen($string)) {

			$t = ord($string[$n]);
			if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
				$tn = 1;
				$n++;
				$noc++;
			} elseif (194 <= $t && $t <= 223) {
				$tn = 2;
				$n += 2;
				$noc += 2;
			} elseif (224 <= $t && $t <= 239) {
				$tn = 3;
				$n += 3;
				$noc += 2;
			} elseif (240 <= $t && $t <= 247) {
				$tn = 4;
				$n += 4;
				$noc += 2;
			} elseif (248 <= $t && $t <= 251) {
				$tn = 5;
				$n += 5;
				$noc += 2;
			} elseif ($t == 252 || $t == 253) {
				$tn = 6;
				$n += 6;
				$noc += 2;
			} else {
				$n++;
			}

			if ($noc >= $length) {
				break;
			}

		}
		if ($noc > $length) {
			$n -= $tn;
		}

		$strcut = substr($string, 0, $n);

	} else {
		for ($i = 0; $i < $length; $i++) {
			$strcut .= ord($string[$i]) > 127 ? $string[$i] . $string[++$i]
					: $string[$i];
		}
	}

	$strcut = str_replace(
			array($pre . '&' . $end, $pre . '"' . $end, $pre . '<' . $end,
					$pre . '>' . $end),
			array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

	$pos = strrpos($strcut, chr(1));
	if ($pos !== false) {
		$strcut = substr($strcut, 0, $pos);
	}
	return $strcut . $dot;
}

function formWidget($fieldId, $fieldName, $field = array(), $param = array()) {
	$class = implode(" ",
			array_merge($field['class'], array('textInput', 'valid')));
	$fieldName = empty($fieldName) ? $fieldId : $fieldName;
	if ($field['formtype'] == 'textarea') {
		$html = "<textarea name=\"$fieldName\" id=\"$fieldId\" class=\"$class\" rows=\"3\" cols=\"40\" tabindex=\"1\">$param[$fieldId]</textarea>";
	} elseif ($field['formtype'] == 'select') {
		$field['choices'] = explode("\n", $field['choices']);
		$html = "<select name=\"$fieldName\" id=\"$fieldId\" class=\"$class\" tabindex=\"1\">";
		foreach ($field['choices'] as $op) {
			$html .= "<option value=\"$op\""
					. ($op == $param[$fieldId] ? 'selected="selected"' : '')
					. ">$op</option>";
		}
		$html .= '</select>';
	} elseif ($field['formtype'] == 'list') {
		$field['choices'] = explode("\n", $field['choices']);
		$html = "<select name=\"{$fieldName}[]\" id=\"$fieldId\" class=\"$class\" multiple=\"multiplue\" tabindex=\"1\">";
		$param[$fieldId] = explode("\n", $param[$fieldId]);
		foreach ($field['choices'] as $op) {
			$html .= "<option value=\"$op\""
					. (in_array($op, $param[$fieldId]) ? 'selected="selected"'
							: '') . ">$op</option>";
		}
		$html .= '</select>';
	} elseif ($field['formtype'] == 'checkbox') {
		$field['choices'] = explode("\n", $field['choices']);
		$param[$fieldId] = explode("\n", $param[$fieldId]);
		foreach ($field['choices'] as $op) {
			$html .= "<input type=\"checkbox\" name=\"{$fieldName}[]\" id=\"$fieldId\" class=\"$class\" value=\"$op\" tabindex=\"1\""
					. (in_array($op, $param[$fieldId]) ? ' checked="checked"'
							: '') . " />" . "$op";
		}
	} elseif ($field['formtype'] == 'radio') {
		$field['choices'] = explode("\n", $field['choices']);
		foreach ($field['choices'] as $op) {
			$html .= "<label class=\"lb\"><input type=\"radio\" name=\"{$fieldName}\" class=\"$class\" value=\"$op\" tabindex=\"1\""
					. ($op == $param[$fieldId] ? ' checked="checked"' : '')
					. " />" . "$op</label>";
		}
	} else {
		$html = "<input type=\"text\" name=\"$fieldName\" id=\"$fieldId\" class=\"$class\" value=\"$param[$fieldId]\" tabindex=\"1\" />";
	}

	return $html;
}

function checkphone($phone){
	return true;
}


////获得访客真实ip
function Getip() {
	if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
		$ip = $_SERVER["HTTP_CLIENT_IP"];
	}
	//获取代理ip
	if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
	}
	if ($ip) {
		$ips = array_unshift($ips, $ip);
	}

	$count = count($ips);
	for ($i = 0; $i < $count; $i++) {
		//排除局域网ip
		if (!preg_match("/^(10|172\.16|192\.168)\./i", $ips[$i])) {
			$ip = $ips[$i];
			break;
		}
	}
	$tip = empty($_SERVER['REMOTE_ADDR']) ? $ip : $_SERVER['REMOTE_ADDR'];
	if ($tip == "127.0.0.1") {
		//从网络获取IP
		return get_onlineip();
	} else {
		return $tip;
	}
}

////获得本地真实IP
function get_onlineip() {
	$mip = file_get_contents("http://iframe.ip138.com/ic.asp");
	if ($mip) {
		preg_match("/\[.*\]/", $mip, $sip);
		$p = array("/\[/", "/\]/");
		return preg_replace($p, "", $sip[0]);
	} else {
		return "failure";
	}
}

////根据ip获得访客所在地地名
function Getaddress($ip = '') {
	if (empty($ip)) {
		$ip = $this->Getip();
	}
	$ipadd = file_get_contents(
			"http://int.dpool.sina.com.cn/iplookup/iplookup.php?ip=" . $ip);//根据新浪api接口获取
	if ($ipadd) {
		$charset = iconv("gbk", "utf-8", $ipadd);
		preg_match_all("/[\x{4e00}-\x{9fa5}]+/u", $charset, $ipadds);

		return $ipadds; //返回一个二维数组
	} else {
		return "Nan";
	}
}

function PostSubmit($post_data,$url)
{		
	if(is_array($post_data)){	
		$o="";
		foreach ($post_data as $k=>$v)
		{
			$o.= "$k=".urlencode($v)."&";
		}
		$post_data=substr($o,0,-1);
	}	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, "Jimmy's CURL Example beta");
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;	
}

//对象转换为数组
function objectToArray( $object ) {
	if( !is_object( $object ) && !is_array( $object ) ) {
		return $object;
	}
	if( is_object( $object ) ) {
		$object = (array) $object;
	}
	return array_map( 'objectToArray', $object );
}

//保持key不变对原数组的重新洗牌
function shuffle_assoc($array)
{
	$keys = array_keys($array);
	shuffle($keys);
	return array_merge(array_flip($keys), $array);
}

if (!function_exists("bcsub")) {
	function bcsub($m1,$m2,$f){
		return round($m1-$m2, $f);
	}
}

function formatSecond($seconds, $show_seconds=false) {
	$seconds = intval($seconds);
	if ($seconds < 60) {
		return $seconds . "秒";
	} else {
		$_day_count    = intval($seconds / (24*60*60));
		$seconds       = $seconds % (24*60*60);
		$_hour_count   = intval($seconds / (60*60));
		$seconds       = $seconds % (60*60);
		$_minute_count = intval($seconds / 60);
		$seconds       = $seconds % (60);
		$_second_count = intval($seconds);
		$str = '';
		if ($_day_count > 0) { $str .= ($_day_count . '天'); }
		if ($_hour_count > 0) { $str .= ($_hour_count . '小时'); }
		if ($_minute_count > 0) { $str .= ($_minute_count . '分钟'); }
		if ($show_seconds && ($_second_count > 0)) { $str .= ($_second_count . '秒'); }
		return $str;
	}
}

/**
 * 用ffmpeg获取视频的长度
 * 
 * @param string $file	视频文件的绝对地址
 * @return string
 */
function getVidelLen($file, $isSecond=true){
	if (!file_exists($file)) {
		return array("file not exists===>".$file);
	}
	$vtime =  exec("ffmpeg -i ".$file." 2>&1 | grep 'Duration' | cut -d ' ' -f 4 | sed s/,//");
	if ($isSecond){
		list($hour, $minute, $sec) = explode(":", $vtime);
		return  intval($hour)*3600 + intval($minute)*60 + intval($sec);
	}else {
		return $vtime;
	}
}
function getOS ()
{
	$user_OSagent = $_SERVER['HTTP_USER_AGENT'];  
	if(eregi('windows nt 6.3', $user_OSagent) || eregi('windows nt 6.2', $user_OSagent)){  
		$visitor_os ="Windows 8";   
	}elseif(strpos($user_OSagent,"NT 6.1")){  
		$visitor_os ="Windows 7";   
	} elseif(strpos($user_OSagent,"NT 5.1")) {   
	   $visitor_os ="Windows XP (SP2)";   
	} elseif(strpos($user_OSagent,"NT 5.2") && strpos($user_OSagent,"WOW64")){   
	   $visitor_os ="Windows XP 64-bit Edition";   
   } elseif(strpos($user_OSagent,"NT 5.2")) {  
		$visitor_os ="Windows 2003";   
   } elseif(strpos($user_OSagent,"NT 6.0")) {  
		$visitor_os ="Windows Vista";   
	} elseif(strpos($user_OSagent,"NT 5.0")) {  
	  $visitor_os ="Windows 2000";   
	} elseif(strpos($user_OSagent,"4.9")) {  
	   $visitor_os ="Windows ME";  
	} elseif(strpos($user_OSagent,"NT 4")) {  
	   $visitor_os ="Windows NT 4.0";  
	} elseif(strpos($user_OSagent,"98")) {  
	  $visitor_os ="Windows 98";  
   } elseif(strpos($user_OSagent,"95")) {  
		$visitor_os ="Windows 95";  
	}elseif(strpos($user_OSagent,"NT")) {  
		$visitor_os ="Windows 较高版本";  
	}elseif(strpos($user_OSagent,"Mac")) {  
		$visitor_os ="Mac";  
	} elseif(strpos($user_OSagent,"Linux")) {   
		$visitor_os ="Linux";  
	} elseif(strpos($user_OSagent,"Unix")) {  
		$visitor_os ="Unix";  
	} elseif(strpos($user_OSagent,"FreeBSD")) {  
		$visitor_os ="FreeBSD";  
	} elseif(strpos($user_OSagent,"SunOS")) {  
		$visitor_os ="SunOS";   
	} elseif(strpos($user_OSagent,"BeOS")) {  
		$visitor_os ="BeOS";   
	} elseif(strpos($user_OSagent,"OS/2")) {  
		$visitor_os ="OS/2";  
	} elseif(strpos($user_OSagent,"PC")) {  
		$visitor_os ="Macintosh";  
	} elseif(strpos($user_OSagent,"AIX")) {  
		$visitor_os ="AIX";  
	} elseif(strpos($user_OSagent,"IBM OS/2")) {  
		$visitor_os ="IBM OS/2";  
	} elseif(strpos($user_OSagent,"BSD")) {  
		$visitor_os ="BSD";  
   } elseif(strpos($user_OSagent,"NetBSD")) {  
		$visitor_os ="NetBSD";  
	} else {  
	   $visitor_os ="其它操作系统";  
	}  
	return $visitor_os; 
}

function getBrowse()
{
	$user_OSagent = $_SERVER['HTTP_USER_AGENT']; 
		if(strpos($user_OSagent,"Maxthon") && strpos($user_OSagent,"MSIE")) {   
		   $visitor_browser ="Maxthon(Microsoft IE)";   
		}elseif(strpos($user_OSagent,"Maxthon 2.0")) {   
			$visitor_browser ="Maxthon 2.0";   
		}elseif(strpos($user_OSagent,"Maxthon")) {   
		   $visitor_browser ="Maxthon";   
		}elseif(strpos($user_OSagent,"MSIE 9.0")) {   
		   $visitor_browser ="MSIE 9.0";   
		}elseif(strpos($user_OSagent,"MSIE 8.0")) {   
			$visitor_browser ="MSIE 8.0";   
		}elseif(strpos($user_OSagent,"MSIE 7.0")) {   
			$visitor_browser ="MSIE 7.0";   
		}elseif(strpos($user_OSagent,"MSIE 6.0")) {   
		   $visitor_browser ="MSIE 6.0";   
		} elseif(strpos($user_OSagent,"MSIE 5.5")) {   
			$visitor_browser ="MSIE 5.5";   
		} elseif(strpos($user_OSagent,"MSIE 5.0")) {   
			$visitor_browser ="MSIE 5.0";   
		} elseif(strpos($user_OSagent,"MSIE 4.01")) {   
		  $visitor_browser ="MSIE 4.01";   
		}elseif(strpos($user_OSagent,"MSIE")) {   
		   $visitor_browser ="MSIE 较高版本";   
		}elseif(strpos($user_OSagent,"NetCaptor")) {   
			$visitor_browser ="NetCaptor";   
		} elseif(strpos($user_OSagent,"Netscape")) {   
		   $visitor_browser ="Netscape";   
		}elseif(strpos($user_OSagent,"Chrome")) {   
		   $visitor_browser ="Chrome";   
		} elseif(strpos($user_OSagent,"Lynx")) {   
		  $visitor_browser ="Lynx";   
		} elseif(strpos($user_OSagent,"Opera")) {   
		   $visitor_browser ="Opera";   
		} elseif(strpos($user_OSagent,"Konqueror")) {   
			$visitor_browser ="Konqueror";   
		} elseif(strpos($user_OSagent,"Mozilla/5.0")) {   
			$visitor_browser ="Mozilla";   
		} elseif(strpos($user_OSagent,"Firefox")) {   
		   $visitor_browser ="Firefox";   
		}elseif(strpos($user_OSagent,"U")) {   
			$visitor_browser ="Firefox";   
		} else {   
		   $visitor_browser ="其它";   
		}   
	   return $visitor_browser; 
}
function getSeoKeyword()
{
	$referer = isset($_SESSION['VISIT_REFERER']) ? $_SESSION['VISIT_REFERER'] : '';
	
	if($referer){
		
		return json_decode($referer);		
	}
	
	$url=isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'';//获取入站url。	
	if(!$url){
		$_SESSION['VISIT_REFERER'] = json_encode(array('',''));
		return array('','');					// 直接打开网站
	}
	$search = array(
			'谷歌'=>array("google.com",'q=','utf'),
			'百度'=>array('baidu.com','wd=','gbk'),
			'雅虎'=>array('yahoo.cn','q=','utf'),
			'搜狗'=>array('sogou.com','query=','gbk'),
			'搜搜'=>array('soso.com','w=','gbk'),
			'必应'=>array('bing.com','q=','utf'),
			'有道'=>array('youdao.com','q=','utf'),
	);
	
	foreach ($search as $_k=>$_s){
		preg_match('/\b'.$_s[0].'\b/',$url,$match);
		if(!empty($match))break;
	}	
	if(empty($match)){
		preg_match('@^(?:http://)?([^/]+)@i',$url,$matches);
		$_SESSION['VISIT_REFERER'] = json_encode(array($matches[1],''));
		
		return array($matches[1],'');
	}
	if($_k == '本站'){
		$_SESSION['VISIT_REFERER'] = json_encode(array($_k,''));
		
		return array($_k,'');				// 来自网站内部
	}
	
	$q = '';
	$start=stripos($url,$search[$_k][1]);
	$url=substr($url,$start + strlen($search[$_k][1]));
	$start=stripos($url,'&');
	if ($start>0)
	{
		$start=stripos($url,'&');
		$q=substr($url,0,$start);
	}
	else
	{
		$q=substr($url,0);
	}
	$q=urldecode($q);
	if($search[$_k][2] == 'gbk')
		$q=iconv("GBK","UTF-8",$q);//引擎为gbk	
	$_SESSION['VISIT_REFERER'] = json_encode(array($_k,$q));
	return array($_k,$q);
}

function loadJs($fileName, $version="2.1.0"){
	if (APP_DEBUG) {
		return "{$fileName}.js";
	}
	return "dest/{$fileName}_v{$version}.min.js"; 
}

/**
 * session key 组合
 *
 * @param Integer $uid
 * @return Sring
 */
function getSessionKey($uid) {
	$rat = "gionee";
	$better_token = substr ( md5 ( uniqid ( mt_rand (), true ) ), 0, 16 ); // 16
	$uid_token = sprintf ( "%08s", base_convert ( $uid, 10, 16 ) ); // 8
	$time_token = substr ( md5 ( microtime ( true ) ), 0, 4 ); // 4
	$sig_token = substr ( md5 ( $better_token . $uid_token . $time_token . $rat ), 0, 4 ); // 4
	$session_key = $uid_token . $better_token . $time_token . $sig_token;
	return $session_key;
}

/**
 * 获取退出key 串
 *
 * @return String
 */
function getUniqId() {
	return md5 ( uniqid ( mt_rand (), true ) . "gionee is very well!" );
}

/**
 * 显示软件尺寸
 * @param int $size
 * @return array float,string
 */
function showsize($size,$count=0, $ul=999,$float=2)
{
	$_size = $size / 1024;
	if($_size > 1024 && ($count+1)<$ul){
		return showsize($_size ,$count+1);
	}
	switch($count)
	{
		case 0:$u = 'KB';break;
		case 1:$u = 'MB';break;
		case 2:$u = 'GB';break;
		case 3:$u = 'TB';break;
		default: $u = 'KB';
	}
	return array(number_format($_size,$float) ,$u);
}

/**
 * @param string $sendto_email 邮件发送地址
 * @param string $subject 邮件主题
 * @param string $body 邮件正文内容
 * @param string $sendto_name 邮件接受方的姓名，发送方起的名字。一般可省。
 * @return number
 */
function smtp_mail($sendto_email, $subject = null, $body = null, $sendto_name = null) {
	$smtp = C ( 'SMTP' ); //读取SMTP服务器配置
	import ( "Extend.phpmailer.phpmailer",LIB_PATH );
	//新建一个邮件发送类对象
	$mail = new PHPMailer (false);
	// send via SMTP
	$mail->IsSMTP ();
	// SMTP 邮件服务器地址，这里需要替换为发送邮件的邮箱所在的邮件服务器地址
	$mail->Host = $smtp ['SMTP_HOST'];
	//邮件服务器验证开
	$mail->SMTPAuth = true;
	// SMTP服务器上此邮箱的用户名，有的只需要@前面的部分，有的需要全名。请替换为正确的邮箱用户名
	$mail->Username = $smtp ['SMTP_USER'];
	// SMTP服务器上该邮箱的密码，请替换为正确的密码
	$mail->Password = $smtp ['SMTP_PASSWORD'];
	// SMTP服务器上发送此邮件的邮箱，请替换为正确的邮箱  ,与$mail->Username 的值是对应的。
	$mail->From = $smtp ['SMTP_MAIL'];
	// 真实发件人的姓名等信息，这里根据需要填写
	$mail->FromName = $smtp ['SMTP_NAME'];
	// 这里指定字符集！
	$mail->CharSet = "utf-8";
	$mail->Encoding = "base64";
	// 收件人邮箱和姓名
	$mail->AddAddress ( $sendto_email, $sendto_name );
	// 发送 HTML邮件
	$mail->IsHTML ( false );
	// 邮件主题
	$mail->Subject = $subject;
	// 邮件内容
	$mail->Body = $body;
	$mail->AltBody = "text/html";
	$mail->SMTPDebug = false;
	
	$status = 1;
	if (! $mail->Send ())
	{
		//失败一次以后，进行一次重试，并写入Log
		if (! $mail->Send ())
		{
			Log::write($mail->ErrorInfo, "SMTP:ERROR");
			$status = 0;
		}
	}
	
	$email = array("dateline"=>time());
	$email["status"] = $status;
	$email["to"] = $sendto_email;
	$email["title"] = $subject;
	$email["body"] = $body;
	$email["status"] = $status;

	M("smtp_logs")->add($email);
	return $status;
}