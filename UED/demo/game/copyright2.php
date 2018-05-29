<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<title></title>
	<?php
$isLocal   = preg_match('/localhost|dev\.|\d+(\.\d+){3}/i', $_SERVER['HTTP_HOST']);
$isLocalIP = preg_match('/\d+(\.\d+){3}/', $_SERVER['HTTP_HOST']);
$ip        = GetHostByName($_SERVER['SERVER_NAME']);
$root = $isLocalIP? 'http://'.$ip.':8899' : 'http://dev.assets.gionee.com';
$webroot   = $isLocal? $root : 'http://assets.3gtest.gionee.com';

$sysRef    = 'sys';
$appRef    = 'apps/game/3g';
$appPic    = $webroot.'/'.$appRef.'/pic';
?>
<meta name="viewport" content="width=device-width">
</head>
<body>
	<div style="margin:0 auto">
		<img alt="" style="width:300px"  src="<?php echo $appPic;?>/2.jpg">
	</div>
</body>
</html>