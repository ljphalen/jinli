<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title></title>
	<?php include '_inc.php';?>
</head>
<body>
	<div id="page" class="home">
		<header class="hd">
			<div class="wrap">
				<h1>在线锁屏</h1>
			</div>
		</header>
		
		<article class="ac scrollWrap" id="iScroll">
			<div class="scrollPanel">
				<div class="pic-text-list">
					<ul>
						<?php for($i=0; $i<3; $i++){?>
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
				
				<div class="pic-showcase">
					<ul>
						<?php for($i=0; $i<8; $i++){?>
						<li class="pic"><a href=""><img src="<?php echo $appPic?>/pic_testIcon.jpg" /></a></li>
						<?php }?>
					</ul>
				</div>

				<footer class="ft">
					<p>
						<span>&copy; 千机解锁</span>
						<span>copyright2012</span>
						<span>苏州天平</span>
					</p>
				</footer>
			</div>
		</article>
	</div>
	<?php include '_icat.php';?>
</body>
</html>