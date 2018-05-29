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
			<h1><div class="omit">新闻中心</div></h1>
			<div class="back-home"><a href="index.php">&lt;</a></div>
		</header>

		<article class="ac">
			<div class="center-list J_centerItem">
				<ul>
					<?php for($i=0; $i<6; $i++){?>
					<li>
						<a class="detail" href="evaluate_detail.php">
							<div class="photo"><img data-src="<?php echo $appPic;?>/ev.png" src="<?php echo $appPic;?>/blank.gif"></div>
							<div class="desc">
								<h3>愤怒的小鸡新版本即将发布</h3>
								<p>
									愤怒的小鸡新版本即将发布愤怒的小鸡新版本即将发布愤怒的小鸡新版本即将发布愤怒的小鸡新版本即将发布
									<!-- <span class="desc"></span> -->
								</p>
							</div>
						</a>
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
		<div class="suggest_field">
				<h1>最新游戏</h1>
				<ul class="list">
					<?php for($i=0;$i<4;$i++){?>
					<li><a href=""><img src="<?php echo $appPic;?>/blank.gif" data-src="<?php echo $appPic;?>/pic_icon.jpg" alt=""></a></li>
					<?php }?>
				</ul>
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