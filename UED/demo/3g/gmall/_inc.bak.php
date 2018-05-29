<?php
$isDebug   = preg_match("/debug/i", $_SERVER['QUERY_STRING']) >= 1;
$isLocal   = preg_match("/localhost|dev|[\d\.]{4}\./i", $_SERVER['HTTP_HOST']) >= 1;
$isLocalIp = preg_match("/[\d\.]{4}\./i", $_SERVER['HTTP_HOST']) >= 1;
$ucweb     = @preg_match("/UC/i", $_SERVER['HTTP_USER_AGENT']) >= 1;
$source    = $isDebug || $isLocal ? '.source' : '';
$ucClass   = $ucweb? 'uc-hack' : '';
$webroot   = $isLocal ? $isLocalIp ? "http://".$_SERVER['HTTP_HOST'].":8899": "http://dev.assets.gionee.com" : 'http://assets.3gtest.gionee.com';
$sysRef    = $webroot."/sys";
$appRef    = $webroot."/apps/3g";

switch($moduleName){
	case "browser":
		//移动官网(浏览器内置版)
		$appStyle  = $isLocal >= 1 ? "3g.browser.source.css" : "3g.browser.css";
		$mainJs = $isLocal >= 1 ? "../3g.browser.source.js" : "../3g.browser.js";
		break;
	case "app": 
		//在线应用(浏览器内置版)
		$appStyle  = $isLocal >= 1 ? "3g.app.source.css" : "3g.app.css";
		$mainJs = $isLocal >= 1 ? "../3g.app.source.js" : "../3g.app.js";
		break;
	case "app-browser":
		//在线应用单页面应用测试版
		$appStyle  = $isLocal >= 1 ? "3g.gnapp.source.css" : "3g.gnapp.css";
		$mainJs = $isLocal >= 1 ? "../3g.gnapp.source.js" : "../3g.gnapp.js";
		$nocore = true;
		break;
	case "app-wap": 
		//在线应用（在线wap版）
		$appStyle  = $isLocal >= 1 ? "3g.appol.source.css" : "3g.appol.css";
		$mainJs = $isLocal >= 1 ? "../3g.appol.source.js" : "../3g.appol.js";
		break;
	case "nav":
		//网址导航(浏览器内置版)
		$appStyle  = $isLocal >= 1 ? "3g.navigator.source.css" : "3g.navigator.css";
		$mainJs = $isLocal >= 1 ? "../3g.navigator.source.js" : "../3g.navigator.js";
		break;
	case "nav2":
		//精品导航
		$appStyle  = $isLocal >= 1 ? "3g.nav.source.css" : "3g.nav.css";
		$mainJs = $isLocal >= 1 ? "../3g.nav.source.js" : "../3g.nav.js";
		break;
	case "pcenter":
		//个人中心
		$appStyle  = $isLocal >= 1 ? "3g.pcenter.source.css" : "3g.pcenter.css";
		$mainJs = $isLocal >= 1 ? "../3g.pcenter.source.js" : "../3g.pcenter.js";
		break;
	default:
		die("请在页面中检查是否设置变量moduleName所属应用模块!");
}

$appAssets = $appRef."/assets";
$appPic    = $appRef."/pic";
?>
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection" content="telephone=no" />
<meta name="appRef" content="<?php echo $appRef;?>" />
<?php if(!isset($nocore)):?>
<link rel="stylesheet" href="<?php echo $sysRef;?>/reset/mpcore.css" />
<?php endif;?>
<link rel="stylesheet" href="<?php echo $appAssets.'/css/'.$appStyle;?>" />
<script type="text/javascript" src="<?php echo $sysRef;?>/icat/1.1.3/icat.js" corelib="//zepto.js" main="<?php echo $mainJs;?>"></script>
<script type="text/javascript">var token = '132465564654';</script>
<!-- 多屏幕模拟测试专用 START -->
<!-- 使用方法：http://yourdomain/#?protoFluid=ready-->
<!-- <script type="text/javascript" src="<?php echo $webroot;?>/sys/jquery.min.js" async="true"></script> -->
<!-- <script type="text/javascript" src="<?php echo $webroot;?>/sys/protoFluid3.02.js" async="true"></script> -->
<!-- <script src="http://18.8.2.55:8080/target/target-script-min.js#anonymous"></script> -->
<!-- 多屏幕模拟测试专用 END -->