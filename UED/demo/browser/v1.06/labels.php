<?php
$sysRef = 'http://a.tongtianjie.com/sys';

$appRef = 'http://a.tongtianjie.com/apps/browser/v1.06';
$appAssets = $appRef.'/assets';
$appPic = $appRef.'/pic';
?>
<!DOCTYPE HTML>
<html style="background-color:#f9f9f9;">
<head>
	<meta charset="utf-8">
	<title></title>
	<meta name="viewport" content="width=device-width,target-densitydpi=device-dpi, initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" />
	<meta name="appRef" content="<?php echo $appRef;?>" />
	<link rel="stylesheet" href="<?php echo $sysRef;?>/mcore.css" />
	<link rel="stylesheet" href="<?php echo $appAssets;?>/css/labels.css" />
	<script type="text/javascript" src="<?php echo $sysRef;?>/icat/1.1.2.a/icat.js" corelib="//jquery.js" main="../labels.js"></script>
</head>
<body>
	<div id="page" data-role="page" data-theme="no">
		<article id="J_labelBox" class="label-box">
			<ul class="clearfix">
				<!-- <li class="delDisabled">
					<a href="http://m.baidu.com" dt-href="http://m.baidu.com">
						<div class="pic"><img src="" alt="搜索" /></div>
						<div class="title"><span>搜索</span></div>
					</a>
				</li>
				<li class="swing">
					<a href="http://m.tmall.com" dt-href="http://m.tmall.com">
						<div class="del-icon J_delIcon"><s>×</s></div>
						<div class="pic"><img src="" alt="搜索" /></div>
						<div class="title"><span>搜索</span></div>
					</a>
				</li> -->
				<li id="J_addLabel" class="add-label">
					<a href="">
						<span>＋</span>
					</a>
				</li>
			</ul>
		</article>
	</div>
</body>
</html>