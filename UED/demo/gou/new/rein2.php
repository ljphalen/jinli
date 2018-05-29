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
		.pic-list, .desc{width:16rem; margin:0 auto; overflow:hidden;}
		.desc{background:#e5382b;}
		.desc .ad-text{margin-top:4.5rem;}
		.desc .date{margin:6.6rem 0 3.6rem;}
	</style>
</head>

<body id="rein" data-pagerole="body">
	<div class="module">
		<div id="iScroll">
			<ol class="pic-list">
				<li><img src="<?php echo "$webroot/$appPic/rein_image1.jpg";?>" /></li>
				<li><img src="<?php echo "$webroot/$appPic/rein_image2.jpg";?>" /></li>
				<li><img src="<?php echo "$webroot/$appPic/rein_image3.jpg";?>" /></li>
				<li><img src="<?php echo "$webroot/$appPic/rein_image4.jpg";?>" /></li>
			</ol>
			<section class="desc">
				<div class="ad-text"><img src="<?php echo "$webroot/$appPic/rein_image5.jpg";?>" /></div>
				<div class="date"><img src="<?php echo "$webroot/$appPic/rein_image6.jpg";?>" /></div>
			</section>
		</div>
	</div>
</body>
</html>