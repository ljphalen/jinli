<?php
$isDebug   = preg_match('/debug/i', $_SERVER['QUERY_STRING']);

$isLocal   = preg_match('/localhost|dev\.|\d+(\.\d+){3}/i', $_SERVER['HTTP_HOST']);
$isLocalIP = preg_match('/\d+(\.\d+){3}/', $_SERVER['HTTP_HOST']);
$ucweb     = preg_match('/UC/i', $_SERVER['HTTP_USER_AGENT']);

$ip        = GetHostByName($_SERVER['SERVER_NAME']);
$source    = $isDebug? '.source' : '';
$ucClass   = $ucweb? 'uc-hack' : '';
$root = $isLocalIP? 'http://'.$ip.':8899' : 'http://dev.assets.gionee.com';
$webroot   = $isLocal? $root : 'http://assets.3gtest.gionee.com';

$sysRef    = 'sys';
$appRef    = 'apps/game/event';
$appPic    = $webroot.'/'.$appRef.'/pic';
$timestamp = '?t=176';
?>

<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection" content="telephone=no" />
<meta name="format-detection" content="email=no" />


<link rel="stylesheet" href="<?php echo "$webroot/$sysRef/reset/mpcore$source.css$timestamp";?>" />
<link rel="stylesheet" href="<?php echo "$webroot/$appRef/assets/css/game$source.css$timestamp";?>" />