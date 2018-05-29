<?php
/**
 * 变量过滤回调函数
 */
function _call_var_filters(&$value)
{
	$value = dhtmlspecialchars($value);
}

/**
 * 对内容进行安全处理
 * @param string|array $string 要处理的字符串或者数组
 * @param $string $flags 指定标记
 */
function dhtmlspecialchars($string, $flags = null)
{
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = dhtmlspecialchars($val, $flags);
		}
	} else {
		if($flags === null) {
			$string = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string);
			if(strpos($string, '&amp;#') !== false) {
				$string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', $string);
			}
		} else {
			if(PHP_VERSION < '5.4.0') {
				$string = htmlspecialchars($string, $flags);
			} else {
				if(strtolower(CHARSET) == 'utf-8') {
					$charset = 'UTF-8';
				} else {
					$charset = 'ISO-8859-1';
				}
				$string = htmlspecialchars($string, $flags, $charset);
			}
		}
	}
	return $string;
}

/**
 * 对前台输出的内容进行安全过滤
 * encode:输出dhtmlspecialchars编码后的文字(默认)
 * text:输出带纯文本，即过滤掉所有代码
 * safe:输出安全的html代码，如保留br，p等无安全风险的html代码
 * @param mixed $content
 * @param string $output
 */
function safe($content, $output = 'encode')
{
	switch ($output)
	{
		case 'encode':
			return dhtmlspecialchars($content, ENT_QUOTES);
		case 'text':
			return dhtmlspecialchars(strip_tags($content));
		case 'safe':
			return h($content);
		default:
			return dhtmlspecialchars($content);
	}
}

/**
 * 显示友好的时间
 * @param String $time 预处理内容
 */
function time_ago($time) {
	if(!ctype_digit((string)$time))
		$time = strtotime($time);
	
    $t = time() - $time;
   
    if ($t == 0)
        $text = '刚刚';
    elseif ($t < 60)
        $text = $t . '秒前';
    elseif ($t < 60 * 60)
        $text = floor($t / 60) . '分钟前';
    elseif ($t < 60 * 60 * 24)
        $text = floor($t / (60 * 60)) . '小时前';
    elseif ($t < 60 * 60 * 24 * 2)
        $text = '昨天 ' . date('H:i', $time);
    elseif ($t < 60 * 60 * 24 * 3)
        $text = '前天 ' . date('H:i', $time);
    elseif ($t < 60 * 60 * 24 * 30)
        $text = date('m月d日 H:i', $time);
    elseif ($t < 60 * 60 * 24 * 365)
        $text = date('m月d日1', $time);
    else
        $text = date('Y年m月d日2', $time);
    return $text;
}

function changeTimeType($seconds){
	if ($seconds>3600){
		$hours = intval($seconds/3600);
		$minutes = $seconds-($hours*3600);
		$time = ($hours<10?str_pad ( $hours % 10, 2, '0', STR_PAD_LEFT ):$hours)." : ".gmstrftime('%M : %S', $minutes);
	}else{
		$time = gmstrftime('%H : %M : %S', $seconds);
	}
	return $time;
}

/**
 * 内容主体截取，自动生成more链接标记
 * @param String $content 预处理内容
 * @param int    $length 长度，中／英文处理长度不同
 * @param String $like   more标记链接地址
 */
function createMore($content, $length = 140, $like = false)
{
	if(!$content || !$like)
		return false;
	
}

/**
 * 静态文件CDNURL生成
 * @param string $path
 */
function cdn($path)
{
	$url = C('CDN_URL');

	$path = strtoupper($path);
	$link = isset($url[$path]) ? $url[$path] : '/'.strtolower($path);
	
	if($link[0] == '/')
		$link = sprintf('http://%s%s', $_SERVER['HTTP_HOST'], $link); 
	
	if($path == 'PUBLIC')
		$link = sprintf('%s/%s', rtrim($link, '/'), C('DEFAULT_THEME')); 
	
	return $link;
}

/**
 * 加载助手类
 * @param mixed $helper
 * @param bool $init 是否实例化
 */
function helper($helper, $init=true)
{
	$layer = 'Helper';
	$class = $helper.$layer;
	import("{$layer}/{$class}", LIB_PATH);
	
	return $init ? new $class() : true;
}

function loadClient($clients){
	$clientsArr = array();
	if (is_array($clients)) {
		$clientsArr = $clients;
	}
	if (is_string($clients)) {
		$clientsArr = explode(",", $clients);
	}
	if (empty($clientsArr)) {
		return ;
	}
	foreach ($clients as $client){
		$clientName = ucwords($client) . "Client";
		import("@.Client.$clientName");
	}
}

/**
 * 编辑器助手类
 * @param string $html 文本内容
 */
function editor_format($html)
{
	$html = preg_replace("@@is", "", $html);
	$html = preg_replace("@<br[^s]+/?>\s+@is", "<br />", $html);
	$html = str_replace(array("<br /><br /><br />", "<p><br /></p>", "<p></p><br /><br />"), "<p></p>", $html);
	return $html;
}

/**
 * 根据状态取得合同状态
 * @param $state
 * @return string
 */
function getContractStatusById($status)
{
    $id = intval($status);
    switch ($id) {
        case 0:
            return "未申请";
        case 1:
            return "申请中";
        case -1:
            return "申请不通过";
        case 2:
            return "申请成功";
        case 3:
            return "审核中";
        case -2:
            return "审核不通过";
        case 4:
            return "审核通过";
        case 5:
            return "未回传";
        case -3:
            return "已过期";
        case 6:
            return "即将到期";
    }
}

load("extend");
load("extend", APP_PATH.'Common/');