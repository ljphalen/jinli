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
		<?php include '_header.php';?>
		<div id="content" class="pintro">
			<div class="module">
				<div class="product-banner"><a href="product-img.php"><img src="<?php echo $staticPath;?>/apps/3g/pic/t06.png" alt="" /></a></div>
			</div>
			<div class="module">
				<ul class="product-intro clearfix">
					<?php for($i = 0; $i < 5; $i++):?>
					<li class="item">
						<section class="item-pic"><img src="<?php echo $staticPath;?>/apps/3g/pic/171055.png" alt="" /></section>
						<section class="item-txt">双摄像头设计</section>
					</li>
					<li class="item">
						<section class="item-pic"><img src="<?php echo $staticPath;?>/apps/3g/pic/171055.png" alt="" /></section>
						<section class="item-txt">智能语音</section>
					</li>
					<?php endfor;?>
				</ul>
				<div class="button">
					<a href="product-detail.php" class="btn-gray">技术规格</a>
					<span class="btn-disable">敬请期待</span>
					<a href="tel:400-779-6666" class="btn-gray">电话咨询</a>
				</div>
			</div>
			<div class="module">
				<!-- 底部链接模块-->
				<ul class="bottom-link">
					<li class="title">其他系列</li>
					<?php for($i = 0; $i < 1; $i++):?>
					<li><a href="###">普及系列</a></li>
					<li><a href="###">天鉴系列 </a></li>
					<li><a href="###">E-LIFE系列</a></li>
					<?php endfor;?>
				</ul>
			</div>
		</div>
	</div>
</body>
</html>