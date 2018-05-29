<?php
$isLocal   = preg_match("/dev\./i", $_SERVER['HTTP_HOST']);
$webroot   = $isLocal >= 1 ? "http://dev.assets.gionee.com" : 'http://3g.3gtest.gionee.com';
$sysroot   = "http://3gtest.gionee.com";

$sysRef    = $sysroot."/sys";
$appRef    = $isLocal >= 1 ? $webroot."/apps/3g/v1.1" : $webroot."/apps/3g/v1.1";
$appStyle  = $isLocal >= 1 ? "browser.source.css" : "browser.css";
$mainJs = $isLocal >= 1 ? "../browser.source.js" : "../browser.js";
$appAssets = $appRef."/assets";
$appPic    = $appRef."/pic";
?>
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection" content="telephone=no" />
<meta name="appRef" content="<?php echo $appRef;?>" />
<link rel="stylesheet" href="<?php echo $sysRef;?>/reset/mpcore.css" />
<link rel="stylesheet" href="<?php echo $appAssets;?>/css/browser.source.css" />
<script type="text/javascript" src="<?php echo $sysRef;?>/icat/1.1.3/icat.js" corelib="//zepto.js" main="<?php echo $mainJs;?>"></script>
<script type="text/javascript">var token = '00afsd0fsda0';</script>
<script type="text/javascript" src="<?php echo $webroot;?>/sys/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $webroot;?>/sys/protoFluid3.02.js"></script>