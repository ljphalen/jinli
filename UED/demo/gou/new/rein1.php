<!DOCTYPE HTML>
<?php include '_cfg.php';?>
<html<?php echo ($ucClass? 'class="'.$ucClass.'"' : '');?>>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<meta name="format-detection" content="telephone=no" />
	<meta name="format-detection" content="email=no" />
	<title>Amigo蓝牙游戏手柄</title>
	<link rel="stylesheet" href="<?php echo "$webroot/$sysRef/reset/phonecore$source.css$timestamp";?>" />
	<style>
		.pic-list{width:16rem; margin:0 auto; background:#1f253d; overflow:hidden;}
		.pic-list li{margin-top:3.5rem;}
		.pic-list li:last-child{margin:4.9rem 0;}
	</style>
</head>

<body id="rein" data-pagerole="body">
	<div class="module">
		<div id="iScroll">
			<ol class="pic-list">
				<li><img src="<?php echo "$webroot/$appPic/rein_picture1.jpg";?>" /></li>
				<li><img src="<?php echo "$webroot/$appPic/rein_picture2.jpg";?>" /></li>
				<li><img src="<?php echo "$webroot/$appPic/rein_picture3.jpg";?>" /></li>
				<li><img src="<?php echo "$webroot/$appPic/rein_picture4.jpg";?>" /></li>
				<li><img src="<?php echo "$webroot/$appPic/rein_picture5.jpg";?>" /></li>
				<li><img src="<?php echo "$webroot/$appPic/rein_picture6.jpg";?>" /></li>
				<li><img src="<?php echo "$webroot/$appPic/rein_picture7.jpg";?>" /></li>
			</ol>
		</div>
	</div>
</body>
</html>