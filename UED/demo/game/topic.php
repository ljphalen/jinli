<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<title></title>
	<?php include '_inc.php';?>
</head>
<body>
	<div id="page" >
		<header class="hd">
			<div class="top-banner">
				<a class="search" href="search.php"></a>
			</div>
			<nav>
				<ul>
					<li><a href="index.php">首页</a></li>
					<li class="selected"><a>分类</a></li>
					<li><a href="topic.php">专题</a></li>
					<li><a href="rank.php">排行</a></li>
				</ul>
			</nav>
		</header>
		<section class="wrap">
			<div class="subject-list J_topicItem">
				<ul>
					<?php for($i=0; $i<5; $i++){?>
					<li>
						<a href="http://game.3gtest.gionee.com/client/subject/detail/?id=11&amp;intersrc=SUBJECT11&amp;t_bi=_1177612710">
							<div class="intro">
								<span class="pic">
									<img data-src="http://game.3gtest.gionee.com/attachs/game/subject/201303/195551.jpg" src="<?php echo $appPic;?>/blank.gif" alt="">
								</span>
								<span class="title">
									<b>中国的风</b>
									<em>2013-02-26</em>
								</span>
							</div>
							<div class="content">
								<span class="desc">
									南都讯 惠州作为全省的试点市，开始逐步推广使用预付费电表。以前是用电后按月缴费，此次更换新电表则要先往卡内充值买电后再用电。
								</span>
							</div>
						</a>
					</li>
					<?php }?>
				</ul>
				<div class="load-more J_loadMore" data-ajaxurl="json.php" data-hasnext="1" data-curpage="1">
					<span>加载更多</span>
				</div>
			</div>
		</section>
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
