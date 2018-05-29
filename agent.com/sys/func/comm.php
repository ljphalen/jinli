<?php 
spl_autoload_register('sys_autoload');
function sys_autoload($classname) {
	if(!in_array($classname, array('Smarty'))){
		if(file_exists(SROOT.'/sys/libs/'.$classname.'.class.php')){
	    	require_once (SROOT.'/sys/libs/'.$classname.'.class.php');
		}
	}
} 

function get($key){
	if(!isset($_GET[$key]))return '';
	return empty($_GET[$key])&&!is_numeric($_GET[$key])?'':$_GET[$key];
}

function post($key){
	if(!isset($_POST[$key]))return '';
	return empty($_POST[$key])&&!is_numeric($_POST[$key])?'':$_POST[$key];
}

function getval($val){
	return !empty($val)?$val:'';
}

function getnum($val){
	return isset($val)?$val:0;
}

// 获取客户端IP地址
function get_client_ip(){
	if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
		$ip = getenv("HTTP_CLIENT_IP");
	else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
		$ip = getenv("HTTP_X_FORWARDED_FOR");
	else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
		$ip = getenv("REMOTE_ADDR");
	else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
		$ip = $_SERVER['REMOTE_ADDR'];
	else
		$ip = "unknown";
	return $ip;
}

function jump_url ($msg = '', $url = '', $type = 'js',$tagert='document')
{
	if ($type == 'js') {
		@header("content-type:text/html;charset=utf-8");
		if (is_array($url)) {
			$url = $url[0]['url'];
		}
		$html = "";
		if ($msg) {
			$html .= "alert('$msg');";
		}
		if ($url) {
			$html .= $tagert.".location.href='$url';";
		} else {
			$html .= "history.go(-1);";
		}
		$html = "<script>$html</script>";
		echo $html;
	} else {
		if (! is_array($url)) {
			if ($url == '') {
				$url = array(
						array('text' => '', 'url' => 'javascript:history.go(-1)'));
			} else {
				$url = array(array('text' => '返回列表', 'url' => $url));
			}
		}
		$smarty = load_smarty();
		$smarty->assign('msg', $msg);
		$smarty->assign('links', $url);
		$smarty->assign('defaultUrl', $url[0]['url']);
		$smarty->display('public/message.html');
	}
	exit();
}

/**
 * 分页显示函数
 * @author Haver.Guo
 * @param int $count 总数据条数
 * @param int $rows 每页显示数据
 * @param int $pages 当前页
 * @param string $basicUrl 起始URL（即参数前URL）
 * @param string $parm 参数（不带？号）
 * @param int $showMaxPageLink 最大显示页码数
 */
function show_pages($count ,$rows , $page ,$basicUrl ,$parm ,$showMaxPageLink=11){
	if($count<=$rows || $count<=0){
		return '共找到'.$count.'条数据';
	}

	//$showMaxPageLink = 11;			//最大显示页码数
	$pages = ceil($count/$rows);	//页数
	$parm = ('' != $parm) ? '?'.$parm.'&page=' : '?page=';
	$url = $basicUrl.$parm;

	//起始显示页
	$beginPage = $page - intval($showMaxPageLink/2);
	$beginPage = $beginPage>0 ? $beginPage : 1;

	//结束显示页
	$endpage = $beginPage + $showMaxPageLink -1;
	$endpage = $endpage<=$pages ? $endpage : $pages;

	//如果页尾不够跨度，起始页需要继续前行
	if(($endpage-$beginPage)<$showMaxPageLink && $beginPage>1){
		$beginPage = $endpage - $showMaxPageLink + 1; //加1表示减去自己的位置
		$beginPage = $beginPage>0 ? $beginPage : 1;
	}

	$html .= '第'.$page.'页/共'.$pages.'页 ';
	$html .= '<a href="'.$basicUrl.$parm.'1">首页</a>';

	//显示前一页
	if($page > 1) $html .= '<a href="'.$url.($page-1).'">上一页</a>';

	//显示...链接（表示在起始页前还有页面）
	if(($beginPage-1)>=1) $html .= '<a href="'.$url.($beginPage-1).'">...</a>';

	//显示页面列表
	for ($i = $beginPage; $i<=$endpage; $i++){
		if($i == $page)
			$html .= '<span>'.$i.'</span>';
		else
			$html .= '<a href="'.$url.$i.'">'.$i.'</a>';
	}

	//显示...链接（表示在结束页后还有页面）
	if($endpage+1<=$pages) $html .= '<a href="'.$url.($endpage+1).'">...</a>';

	//显示后一页
	if($page <= ($pages-1)) $html .= '<a href="'.$url.($page+1).'">下一页</a>';

	$html .= '<a href="'.$url.$pages.'">末页</a>';

	$html .= ' 共找到'.$count.'条数据';
	return $html;
}

/**
 * 把时间秒转成天时分秒格式
 */
function formatdate($t)
{
	$t=intval($t);
	
	$d=floor($t/(60*60*24));
	$h=floor(($t-$d*60*60*24)/(60*60));
	$M=floor(($t-$d*(60*60*24)-$h*60*60)/60);
	$s=floor($t-$d*(60*60*24)-$h*60*60-$M*60);
	$str="";
	if($d>0){
		$str=$str.$d."天";
	}
	if($h>0){
		$str=$str.$h."小时";
	}
	if($M>0){
		$str=$str.$M."分";
	}
	if($s>0){
		$str=$str.$s."秒";
	}
	return $str;
}

/**
 * 取支付方式列表
 */
function get_configs($key){
	$config_file = SROOT.'/data/config/'.$key.'.php';

	if(!file_exists($config_file)){
		return array();
	}
	
	return include $config_file;
}


/**
 * 写日志
 * @param string $method    接口方法名
 * @param string $para		接收到的参数
 * @param string $returnStr 返回的字符串
 */
function writeLog($method,$para, $returnStr)
{
	$path = SYSLOG.$method;
	if (!file_exists($path))
	{
		mkdir($path,0777);
	}
	$fileName = $path.'/'.date('Y-m-d').'.log';

	$fp = fopen($fileName, 'a+');
	//判断创建或打开文件是否  创建或打开文件失败，请检查权限或者服务器忙;
	if($fp===false)
	{
		return false;
	}
	else
	{
		if(fwrite($fp,'[TIME:'. date("Y-m-d H:i:s").'] ---- [PARA:'. $para.'] ---- [RETURN: {'.$returnStr."}]\r\n"))
		{
			fclose($fp);
			return true;
		}else{
			return false;
		}
	}
}

/**
 * 弹出信息
 */
function alertMsg($msg,$url=''){
	if ($url){
		echo '<meta charset="utf-8" /><script>alert("'.$msg.'");location.href="'.$url.'";</script>';
	}else{
		echo '<meta charset="utf-8" /><script>alert("'.$msg.'");history.go(-1);</script>';
	}
	exit;
}


function arrayToXml($arr = array()){
	if (empty($arr)){
		return false;
	}
	$str = '';
	foreach ($arr as $key => $var){
		$str .= "<{$key}>{$var}</{$key}>";
	}
	return $str;
}


/*
 * 生成验证码,防止注册机
*/
function captcha ()
{
	$number = strval(rand(1000, 9999));
	//$this->session->set_userdata('captchStr', $number);
	$_SESSION['captchStr'] = $number;
	$number = strval($number);
	$number_len = strlen($number);
	//验证宽高
	$img_width = 80;
	$img_height = 40;
	$img = @imagecreatefrompng('public/img/back.png');
	if(!$img) { /* See if it failed */
		$img  = imagecreatetruecolor(60, 20); /* Create a blank image */
		$bgc = imagecolorallocate($img, 255, 255, 255);
		$tc  = imagecolorallocate($img, 0, 0, 0);
		imagefilledrectangle($img, 0, 0, 150, 30, $bgc);
		/* Output an errmsg */
		//imagestring($img, 1, 5, 5, "Error loading $imgname", $tc);
	}
	//imagecolorallocate($img, 0x6C, 0x74, 0x70);
	$white = imagecolorallocate($img, 0xff, 0x00, 0x00);
	//字符大小
	$ix = 5;
	$iy = 3;
	for ($i = 0; $i < $number_len; $i ++) {
		imagestring($img, 8, $ix, $iy, $number[$i], $white);
		$ix += 14;
	}
	ob_end_clean();
	//关闭浏览器缓存
	header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
	// HTTP/1.1
	header('Cache-Control: private, no-store, no-cache, must-revalidate');
	header('Cache-Control: post-check=0, pre-check=0, max-age=0', false);
	// HTTP/1.0
	header('Pragma: no-cache');
	//输出验证码
	header("Content-type: " . image_type_to_mime_type(IMAGETYPE_PNG));
	imagepng($img);
	imagedestroy($img);
}

function LoadJpeg($imgname)
{
	$im = @imagecreatefromjpeg($imgname); /* Attempt to open */
	if(!$im) { /* See if it failed */
		$im  = imagecreatetruecolor(150, 30); /* Create a blank image */
		$bgc = imagecolorallocate($im, 255, 255, 255);
		$tc  = imagecolorallocate($im, 0, 0, 0);
		imagefilledrectangle($im, 0, 0, 150, 30, $bgc);
		/* Output an errmsg */
		imagestring($im, 1, 5, 5, "Error loading $imgname", $tc);
	}
	return $im;
}
/**
 * 获取数据连接
 * @param string $dbkey
 * @return multitype:NULL string
 */
function get_db_config($dbkey = 'db1') {
	$config = array(
			'hostname'=>$GLOBALS['ini_db'][$dbkey]['host'],
			'username'=>$GLOBALS['ini_db'][$dbkey]['uid'],
			'password'=>$GLOBALS['ini_db'][$dbkey]['pwd'],
			'database'=>$GLOBALS['ini_db'][$dbkey]['db'],
			'charset'=>'utf8',
			'type' =>$GLOBALS['ini_db'][$dbkey]['type'],
			'port' =>$GLOBALS['ini_db'][$dbkey]['port'],
	);
	return $config;
}

/**
 * 异步POST请求（不返回结果）
 * @param  $url 	URL串
 * @param  $timeout 超时时间
 */
function do_post_async($url,$timeout=30,$post_data=array())
{
	$urls = parse_url($url);
	$urls["path"] = (empty($urls["path"]) ? "/" : $urls["path"]);
	$urls["port"] = (empty($urls["port"]) ? 80 : $urls["port"]);
	$host_ip = @gethostbyname($urls["host"]);
	$fsock_timeout = $timeout; //超时时间
	if(($fsock = fsockopen($host_ip, $urls['port'], $errno, $errstr, $fsock_timeout)) < 0){
		return false;
	}
	$request =  $urls["path"].(!empty($urls["query"]) ? "?" . $urls["query"] : "");
	$post_data2 = http_build_query($post_data);
	$in  = "POST " . $request . " HTTP/1.1\r\n";
	$in .= "Accept: */*\r\n";
	$in .= "Host: " . $urls["host"] . "\r\n";
	//$in .= "User-Agent: Lowell-Agent\r\n";
	$in .= "Content-type: application/x-www-form-urlencoded\r\n";
	$in .= "Content-Length: " . strlen($post_data2) . "\r\n";
	$in .= "Connection: Close\r\n\r\n";
	$in .= $post_data2 . "\r\n\r\n";
	unset($post_data2);
	if(!@fwrite($fsock, $in, strlen($in))){
		@fclose($fsock);
		return false;
	}
	//@fclose($fsock);
	//return GetHttpContent($fsock);
}

/**
 * 取当前站点地址（不带参数）
 * @return string
 */
function get_hosturl()
{
	$hosturl = 'http';

	if ($_SERVER["HTTPS"] == "on"){
		$hosturl .= "s";
	}
	$hosturl .= "://";

	if ($_SERVER["SERVER_PORT"] != "80"){
		$hosturl .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"];
	}else{
		$hosturl .= $_SERVER["SERVER_NAME"];
	}
	return $hosturl;
}

/**
 * 检名检查
 */
function check_sig($ac) {
	$cfg_params = $GLOBALS['ini_api_params'];
	if(!isset($cfg_params[$ac]))return false;
	
	if(empty($GLOBALS['ini_api_check'][get('clientid')]['key']))return false;
	
	$arr = $cfg_params[$ac];
	sort($arr);
	$params = array();
	$signs = array();
	foreach ($arr as $val) {
		$signs[$val] = isset($_GET[$val])?$_GET[$val]:'';
	}

	// 校验，先对数组转为字符串，然后加上密钥，再与传递过来的Sig比对
	$sig = arrtostr($signs, '').'secret='.$GLOBALS['ini_api_check'][get('clientid')]['key'];
	//echo $sig;echo "<br>";echo md5($sig);exit;
	return get('sig') == md5($sig);
}

/**
 * 数组转字符串
 *
 * @param Array $arr 数组
 * @param string $separator分隔符
 */
function arrtostr($arr = array(), $separator) {
	$return = '';
	if (is_array($arr)) {
		foreach ($arr as $key => $val) {
			$return .= $return ? $separator : '';
			$return .= $key . '=' . $val;
		}
	}
	return $return;
}


/**
 * 输出报文
 * @author Haver.Guo
 * @param array $arr
 * @param string $format 值为json,xml或空
 */
function output_message($arr = array(), $format = ''){
	$rtn = '';
	if(is_array($arr)){
		if('' == $format || 'xml' == $format){
			$XmlUtil = new xmlutil();
			$rtn = $XmlUtil-> outputXML($arr);
			$XmlUtil = null;
		}elseif('json' == $format){
			$rtn = json_encode($arr);
		}
	}

	$method = get('ac');
	if(!$method) $method = 'err_method';
	//日志文件，[return:返回数据]-[运行时间及内存]-[url:请求地址;from:来源地址]
	$log = 'return:'.$rtn.';url:'.get_url().';from:'.get_refer();

	//写日志
	if(API_ERRLOG){
		writelog($method,'',$log);
	}

	echo $rtn;
	exit;
}

/**
 * 读取配置状态
 * @author Haver.Guo
 * @param int $codeid
 */
function read_status($codeid){
	$errCodeArr = $GLOBALS['ini_api_errcode'];
	if(isset($errCodeArr[$codeid])){
		$arr = array('status' => $codeid,'statusnote' => $errCodeArr[$codeid]);
	}else{
		$arr = array('status' => $codeid,'statusnote' => '');
	}
	return $arr;
}

/**
 * 获取当前网页URL
 * @return string
 */
function get_url($urlencode=false) {
	return $urlencode ? urlencode($_SERVER['REQUEST_URI']) : $_SERVER['REQUEST_URI'];
}

/**
 * 获取网页来源地址
 * @return string
 */
function get_refer() {
	return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
}