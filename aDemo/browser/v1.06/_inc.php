<?php
$sysRef = 'http://a.tongtianjie.com/sys';

$appRef = 'http://a.tongtianjie.com/apps/browser/v1.06';
$appAssets = $appRef.'/assets';
$appPic = $appRef.'/pic';
?>
<meta name="viewport" content="width=device-width,target-densitydpi=device-dpi, initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" />
<meta name="format-detection" content="telephone=no" />
<meta name="appRef" content="<?php echo $appRef;?>" />
<link rel="stylesheet" href="<?php echo $sysRef;?>/reset/core.css" />
<link rel="stylesheet" href="<?php echo $appAssets;?>/css/browser.source.css" />
<script type="text/javascript" src="<?php echo $sysRef;?>/icat/1.1.2/icat.js" main="browser.source.js" corelib="jquery/jquery.js"></script>