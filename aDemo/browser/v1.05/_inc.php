<?php
$sysRef = 'http://a.gionee.com/sys';

$appRef = 'http://a.gionee.com/apps/browser/v1.05';
$appAssets = $appRef.'/assets';
$appPic = $appRef.'/pic';
?>
<meta name="viewport" content="width=device-width,target-densitydpi=device-dpi, initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="appRef" content="<?=$appRef?>" />
<link rel="stylesheet" href="<?=$sysRef?>/core.css" />
<link rel="stylesheet" href="<?=$appAssets?>/css/portals.source.css" />
<script type="text/javascript" src="<?php echo $sysRef; ?>/icat/1.1.1/icat.source.js?t=201208090950" main="portals.source.js" corelib="jquery/jquery.js"></script>