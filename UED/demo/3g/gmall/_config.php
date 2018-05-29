<?php
$isDebug   = preg_match("/debug/i", $_SERVER['QUERY_STRING']) >= 1;
$isLocal   = preg_match("/localhost|dev|[\d\.]{4}\./i", $_SERVER['HTTP_HOST']) >= 1;
$isLocalIp = preg_match("/[\d\.]{4}\./i", $_SERVER['HTTP_HOST']) >= 1;
$ucweb     = @preg_match("/UC/i", $_SERVER['HTTP_USER_AGENT']) >= 1;
$source    = $isDebug || $isLocal ? '.source' : '';
$ucClass   = $ucweb? 'uc-hack' : '';
//$assets    = $isLocal ? $isLocalIp ? "http://".$_SERVER['HTTP_HOST'].":8899": "http://dev.assets.gionee.com" : 'http://assets.3gtest.gionee.com';
$assets    = $isLocal ? $isLocalIp ? "//".$_SERVER['HTTP_HOST'].":8899": "//dev.assets.gionee.com" : '//assets.3gtest.gionee.com';
$webroot = $isLocal ? $isLocalIp ? "//".$_SERVER['HTTP_HOST'] : "//dev.demo.gionee.com" : '//demo.3gtest.gionee.com';
$sysRef    = "sys";

switch($moduleName){
	case "browser":
		//移动官网(浏览器内置版)
		$appStyle  = $isLocal >= 1 ? "3g.browser.source.css" : "3g.browser.css";
		$mainJs = $isLocal >= 1 ? "../3g.browser.source.js" : "../3g.browser.js";
		$appRef = "apps/3g/browser";
		$icat = '1.1.3';
		break;
	case "app": 
		//在线应用(浏览器内置版)
		$appStyle  = $isLocal >= 1 ? "3g.app.source.css" : "3g.app.css";
		$mainJs = $isLocal >= 1 ? "../3g.app.source.js" : "../3g.app.js";
		$appRef = "apps/3g/app";
		$icat = '1.1.3';
		break;
	case "gn_app":
		//在线应用单页面应用测试版
		$appRef = "apps/3g/app"; $mainCss = "gnapp"; $mainJs = "gnapp"; break;
		$icat = '1.1.4';
		$nocore = true;
		break;
	case "app-wap": 
		//在线应用（在线wap版）
		$appStyle  = $isLocal >= 1 ? "3g.appol.source.css" : "3g.appol.css";
		$mainJs = $isLocal >= 1 ? "../3g.appol.source.js" : "../3g.appol.js";
		$appRef = "apps/3g/app/wap";
		$icat = '1.1.3';
		break;
	case "nav":
		//网址导航(浏览器内置版)
		$appRef = "apps/3g"; $mainCss = "3g.navigator"; $mainJs = "navigator"; $useICAT = false; //新闻栏目
		$icat = '1.1.3';
		break;
	case "nav-novel":
		//网址导航小说
		$appRef = "apps/3g"; $mainCss = "3g.novel"; $mainJs = ""; //栏目
		break;
	case "nav2":
		//精品导航
		$appRef = "apps/3g/nav"; $mainCss = "3g.nav"; $mainJs = "main"; 
		break;
	case "pcenter":
		//个人中心
		$appStyle  = $isLocal >= 1 ? "3g.pcenter.source.css" : "3g.pcenter.css";
		$mainJs = $isLocal >= 1 ? "../3g.pcenter.source.js" : "../3g.pcenter.js";
		$appRef = "apps/3g/pcenter";
		$icat = '1.1.3';
		break;
	case "news": $appRef = "apps/3g/news"; $mainCss = "3g.news"; $mainJs = "news"; break; //新闻栏目
	case "E3": $appRef = "apps/3g/elife"; $mainCss = "e3.nav"; $mainJs = "e3.nav"; break;
	case "elife": $appRef = "apps/3g/elife"; $mainCss = "elife.3g"; $mainJs="elife_3g"; break;
	case "events": $appRef = "apps/3g/events"; $mainCss = "events_20130501"; $mainJs="events_20130501"; break;
	case "events_0531": $appRef = "apps/3g/events"; $mainCss = "events_20130531"; $mainJs="events_20130531"; break;
	default:
		die("请在页面中检查是否设置变量moduleName所属应用模块[browser,app,nav,pcenter]");
}

$appPic    = $assets.'/'.$appRef."/pic";
?>