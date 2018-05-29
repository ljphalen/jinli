<?php
$sysRef = 'http://vkassets.sinaapp.com/sys';

$appRef = 'http://vkassets.sinaapp.com/apps/gou/v0.8';
$appAssets = $appRef.'/assets';
$appPic = $appRef.'/pic';
?>
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection" content="telephone=no" />
<meta name="appRef" content="<?php echo $appRef;?>" />
<link rel="stylesheet" href="<?php echo $sysRef;?>/reset/mpcore.css" />
<link rel="stylesheet" href="<?php echo $appAssets;?>/css/gngou.source.css" />
<script type="text/javascript" src="<?php echo $sysRef;?>/icat/1.1.3/icat.js" corelib="//1.8.0/jquery.js" main="../gngou.source.js"></script>
<script type="text/javascript">var token = '132465564654';</script>