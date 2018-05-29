<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<title></title>
	<?php include '_inc.php';?>
</head>
<body>
	<div id="page">
		<a name="top"></a>
		<header class="hd">
			<div class="top-banner">
				<!-- <a href=""> -->
				<!-- <img src="<?php echo $appPic;?>/pic_topbanner.png" alt=""  > -->
				<a class="search" href="search.php"  >	
				</a>
			</a>
		</div>
			<nav>
				<ul>
					<li><a href="index.php">首页</a></li>
					<li><a href="class.php">分类</a></li>
					<li ><a href="topic.php">专题</a></li>
					<li class="selected"><a>排行</a></li>
				</ul>
			</nav>
		</header>

		<article class="ac">
			<div class="new J_newItem">
				<ul>
					<li class='<?php echo !isset($_GET["flag"]) ||$_GET["flag"]==1 ? "selected":"";?>'>
						<a <?php echo !isset($_GET["flag"])||$_GET["flag"]==1 ? "":"href=rank.php?flag=1";?>>最新发布</a></li>
					<li class='<?php echo (isset($_GET["flag"])&&$_GET["flag"]==2) ? "selected":""?>'>
						<a <?php echo (isset($_GET["flag"])&&$_GET["flag"]==2)?"":"href=rank.php?flag=2"?>>最新下载</a></li>
				</ul>
			</div>
			<div class="item-list J_gameItem spec">
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
</body>
</html>