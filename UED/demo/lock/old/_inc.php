<?php
$isLocal = preg_match('/localhost|dev\./i', $_SERVER['HTTP_HOST']);
$webRoot = $isLocal>=1 ? 'http://dev.assets.gionee.com' : 'http://assets.3gtest.gionee.com';
$sysroot = $webRoot;//"http://3gtest.gionee.com:100";

$sysRef = $sysroot.'/sys';
$appRef = $webRoot.'/apps/lock';
$appAssets = $appRef.'/assets';
$appPic = $appRef.'/pic';
?>
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection" content="telephone=no" />

<meta name="appRef" content="<?php echo $appRef;?>" />
<link rel="stylesheet" href="<?php echo $sysRef;?>/reset/mpcore.css" />
<link rel="stylesheet" href="<?php echo $appAssets;?>/css/lock.source.css" />
<script type="text/javascript">var token = 'fafsad', FileData = {"id":"9","title":"sadsad","author":"qqtf","description":"wqeqrwqrqwetweargfeq","thumbUrl ":"http:\/\/3gtest.gionee.com:89\/attachs\/file\/201211\/aqn154125\/icon_aqn.png","gifUrl ":"http:\/\/3gtest.gionee.com:89\/attachs\/file\/201211\/aqn154125\/pre_web_aqn.gif","uxUrl ":"http:\/\/3gtest.gionee.com:89\/down\/201211\/aqn154125\/aqn.ux"};</script>