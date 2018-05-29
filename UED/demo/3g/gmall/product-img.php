<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection" content="telephone=no" />
<meta name="appRef" content="http://dev.assets.gionee.com/apps/3g">
<title>金立产品--产品列表</title>
<?php include "../_inc.php"; ?>
<link rel="stylesheet" href="<?php echo $staticPath;?>/sys/reset/mpcore.css" />
<link id="linkMain" rel="stylesheet" type="text/css" href="<?php echo $staticPath;?>/apps/3g/assets/css/3g.browser.source.css">
<script src="<?php echo $staticPath;?>/sys/lib/zepto/zepto.js"></script>
<script src="<?php echo $staticPath;?>/sys/icat/1.1.3/icat.js"></script>
<script src="<?php echo $staticPath;?>/apps/3g/assets/js/3g.browser.source.js"></script>
</head>

<body>
	<div id="page">
		<header id="header">
			<div class="m-nav">
				<section><a class="back" href="product-intro.php"><span>返回</span></a></section>
				<section><span>查看大图</span></section>
				<section></section>
			</div>
		</header>
		<div id="content">
			<!--图片滑动模块-->
			<div class="product-img">
				<div class="in-slider pro-slider">
					<div class="in-slider-cont">
						<ul>
							<?php for($i = 0; $i < 2; $i++): ?>
								<li><span href="###"><img src="<?php echo $staticPath;?>/apps/3g/pic/093029.jpg" alt=""></span></li>
								<li><span href="###"><img src="<?php echo $staticPath;?>/apps/3g/pic/092950.jpg" alt=""></span></li>
							<?php endfor; ?>
						</ul>
					</div>
					<div class="in-slider-status">
						<?php for($a=0; $a < 4; $a++){?><span <?php echo ($a==0? 'class=on':'');?>></span><?php }?>
					</div>
					<div class="in-slider-prev"></div>
					<div class="in-slider-next"></div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>