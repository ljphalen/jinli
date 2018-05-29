<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<title></title>
	<?php include '_inc.php';?>
</head>
<body>
	<div id="page">
		<header class="hd">
			<h1><div class="omit">捕鱼达人是一款深海捕鱼的游戏</div></h1>
			<div class="back-home"><a href="index.php">&lt;</a></div>
		</header>

		<article class="ac">
			<div class="item-list J_gameItem">
				<ul>
					<?php for($i=0; $i<6; $i++){?>
					<li>
						<a class="detail" href="">
							<div class="pic"><img src="<?php echo $appPic;?>/icon_game.jpg" alt=""></div>
							<div class="desc">
								<h3>捕鱼达人</h3>
								<p>捕鱼达人是一款深海捕鱼的游戏，玩家在</p>
							</div>
						</a>
						<div class="download"><a class="btn" href="">安装</a></div>
					</li>
					<?php }?>
				</ul>
			</div>
			<div class="load-more J_loadMore" data-ajaxUrl="json.php" data-hasnext="true" data-curpage="1">
				<span>加载更多</span>
			</div>
		</article>
		<div class="goTop J_gotoTop">
			<a ></a>
		</div>
		<footer class="ft">
			<div class="wchat" >
				<img src="<?php echo $appPic;?>/wechat.png" alt="" />
				<div>
				<p>欢迎关注我们的微信公共账户！</p>
				<p>帐号：JXH-20121220</p>
			</div>
		</div>
			<div class="quick-links">
				<a href="contact.php">联系我们</a>
				<a href="">意见反馈</a>
				<a href="">客户端下载</a>
			</div>
		</footer>
	</div>
	<?php include '_icat.php';?>
</body>
</html>