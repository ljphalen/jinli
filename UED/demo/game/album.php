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
			<h1>网络游戏合辑</h1>
			<div class="back-home"><a href="index.php">&lt;</a></div>
		</header>

		<article class="ac">
			<!-- <div class="slide-pic h J_slidePic">
				<div class="slideWrap">
					<div class="pic">
						<a><img src="<?php echo $appPic;?>/pic_banner1.jpg"></a>
					</div>
				</div>
			</div> -->
			<div class="ad-topic">
				<a >
					<img src="http://assets.3gtest.gionee.com/apps/game/channel/pic/blank.gif" alt="" data-src="http://game.3gtest.gionee.com/attachs/game/subject/201312/52bd1e25b56a7.jpg">
					<div class="content">
						<span>卖萌  。。。..hah45^南都讯   惠州作为全省的试点市，开始逐步推广使用预付费电表。以前是用电后按月缴费，此次更换新电表则要先往卡内充值买电后再用电。</span>
							<p>2013-01-26</p>
					</div>
				</a>
			</div>
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