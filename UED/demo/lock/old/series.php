<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>锁屏系列</title>
	<?php include '_inc.php';?>
</head>
<body>
	<div id="page" class="series">
		<header class="hd">
			<div class="wrap">
				<div class="back-home"><a href="index.php"></a></div>
				<nav>
					<ul>
						<li><a href="">唯美</a></li>
						<li class="selected"><a href="">卡通</a></li>
						<li><a href="">游戏</a></li>
					</ul>
				</nav>
			</div>
		</header>
		
		<article class="ac scrollWrap" id="iScroll">
			<div class="pic-text-list scrollPanel J_themeList">
				<ul>
					<?php for($i=0; $i<5; $i++){?>
					<li>
						<a href="detail.php">
							<div class="pic">
								<span><img src="<?php echo $appPic?>/pic_testIcon.jpg" /></span>
							</div>
							<div class="desc">
								<h3>打瞌睡</h3>
								<p>快快叫醒绿豆蛙！</p>
							</div>
						</a>
					</li>
					<?php }?>
				</ul>
			</div>
		</article>
		
		<footer class="ft">
			<p><span class="load-more J_loadMore" data-ajaxUrl="seriesJson.php" data-hasnext="true" data-curpage="1">更多 ...</span></p>
		</footer>
	</div>
	<?php include '_icat.php';?>
</body>
</html>