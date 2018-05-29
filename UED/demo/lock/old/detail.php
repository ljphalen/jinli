<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>锁屏详情页</title>
	<?php include '_inc.php';?>
</head>
<body>
	<div id="page" class="detail">
		<header class="hd">
			<div class="wrap">
				<div class="back-home"><a href="index.php"></a></div>
				<h1>啤酒之夏</h1>
				<div class="extra-btn">
					<span class="tleft disabled"></span>
					<span class="tright"></span>
				</div>
			</div>
		</header>
		
		<article class="ac scrollWrap" id="iScroll">
			<div class="scrollPanel">
				<section class="theme-detail">
					<div class="pic"><span><img src="<?php echo $appPic?>/pic_testTheme.jpg" /></span></div>
					<!--<h2>清爽啤酒，畅快一夏！</h2>-->
					<!--<h3><span>大小：4.66M</span> 设计师：Zoe</h3>-->
					<div class="info">
						<div><span>大小：4.66M</span><span></span></div>
						<div>
							<span>下载量：1320人</span>
							<span>设计师：Zoe</span>
						</div>
					</div>
					<p>简介：<br />炎炎夏日，你最享受的是什么，当然莫过于一杯清凉的冰爽啤酒啦，从喉咙开始，凉遍你全身每个细胞，让你尽情享受生活的快乐，不要再控制你的食欲了，打开开关，尽情畅饮吧！</p>
				</section>
				
				<!--<div class="pic-showcase">
					<h3>游戏系列</h3>
					<ul>
						<?php for($i=0; $i<3; $i++){?>
						<li class="pic"><a href=""><img src="<?php echo $appPic?>/pic_testIcon.jpg" /></a></li>
						<?php }?>
						<li class="more"><a href="series.php">更多<br />...</a></li>
					</ul>
				</div>-->
			</div>
		</article>
		
		<footer class="ft">
			<div class="load-status J_loadStatus">
				<span class="download">下载</span>
			</div>
		</footer>
	</div>
	<?php include '_icat.php';?>
</body>
</html>