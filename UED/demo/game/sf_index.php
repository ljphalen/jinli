<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<title></title>
	<?php include '_inc.php';?>
</head>
<body>
	<div id="page" class="superfile">
		<header class="hd">
			<h1>推荐游戏</h1>
		</header>

		<article class="ac">
			<div class="slide-pic J_slidePic">
				<div class="slideWrap">
					<div class="pic">
						<a href="#"><img src="<?php echo $appPic;?>/pic_banner1.jpg"></a>
						<a href="#"><img src="<?php echo $appPic;?>/pic_banner2.jpg"></a>
						<a href="#"><img src="<?php echo $appPic;?>/pic_contact.jpg"></a>
					</div>
				</div>
				<div class="handle">
					<span class="on"></span>
					<span></span>
					<span></span>
				</div>
			</div>
			<div class="item-box">
				<h2 class="sf-title">最新游戏</h2>
				<ul>
					<?php for($i=0; $i<8; $i++){?>
					<li>
						<a href="detail.php">
							<div class="pic">
								<img src="<?php echo $appPic;?>/icon_game.jpg" alt="">
							</div>
							<div class="desc">捕鱼达人</div>
						</a>
					</li>
					<?php }?>
				</ul>
			</div>
			<div class="item-box">
				<h2 class="sf-title">热门游戏</h2>
				<ul>
					<?php for($i=0; $i<8; $i++){?>
					<li>
						<a href="detail.php">
							<div class="pic">
								<img src="<?php echo $appPic;?>/icon_game.jpg" alt="">
							</div>
							<div class="desc">捕鱼达人</div>
						</a>
					</li>
					<?php }?>
				</ul>
			</div>
		</article>
		<div class="goTop J_gotoTop">
			<a ></a>
		</div>
		<footer class="ft">
			<div class="quick-links">
				<a href="contact.php">联系我们</a>
				<a href="feed.php">意见反馈</a>
				<a href="">客户端下载</a>
			</div>
		</footer>
	</div>
	<?php include '_icat.php';?>
</body>
</html>