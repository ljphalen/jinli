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
			<nav >
				<ul>
					<li class="selected"><a>首页</a></li>
					<li><a href="class.php">分类</a></li>
					<li><a href="topic.php">专题</a></li>
					<li><a href="rank.php">排行</a></li>
				</ul>
			</nav>
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
			<div class="item-box list">
				<h2>最新游戏</h2>
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
			<div class="line"><span></span></div>
			<div class="item-box list">
				<h2>推荐游戏</h2>
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
			<div class="line"><span></span></div>
			<div class="item-list dynamicinfo">
				<h2>资讯动态</h2>
				<ul>
					<?php for($i=0; $i<3; $i++){?>
					<li>
						<a class="detail" href="detail.php">
							<div class="desc">
							<span class="title">
								<?php if($i==0){?>
								<?php echo  "活动";?>
							<?php }else{?>
							<?php echo  "资讯";?>
							<?php }?>
								<span class="split">|</span></span><h3>愤怒的小鸡新版即将发布愤怒的小鸡新版即将发布愤怒的小鸡新版即将发布</h3>
							</div>
						</a>
					</li>
					<?php }?>
				</ul>
				<a href="newCenter.php">
					<div class="load-more">
						<span>进入新闻中心</span>
					</div>
				</a>
			</div>
			<div class="line"><span></span></div>
			<div class="item-list evaluate">
				<h2>热门点评</h2>
				<ul>
					<?php for($i=0; $i<3; $i++){?>
					<li>
						<a class="detail" href="detail.php">
							<div class="pic">
								<img data-src="<?php echo $appPic;?>/ev.png" src="<?php echo $appPic;?>/blank.gif" alt="">
							</div>
							<div class="desc">
								<h3>愤怒的小鸡新版即将发布</h3>
								<p>愤怒的小鸡新版即将发布愤怒的小鸡新版即将发布愤怒的小鸡新版即将发布</p>
							</div>
						</a>
					</li>
					<?php }?>
				</ul>
				<a href="evaluate.php">
					<div class="load-more">
						<span>进入评测中心</span>
					</div>
				</a>
			</div>
			<div class="line"><span></span></div>
			<div class="item-box topic">
				<h2>推荐专题</h2>
				<ul>
					<?php for($i=0; $i<4; $i++){?>
					<li>
						<a href="detail.php">
							<div class="pic">
								<img src="<?php echo $appPic;?>/pic_topic.jpg" alt="">
							</div>
							<div class="desc">捕鱼达人捕鱼达人捕鱼达人捕鱼达人捕鱼达人捕鱼达人</div>
							<div class="mask"></div>
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
			<div class="wchat" >
				<img src="<?php echo $appPic;?>/wechat.png" alt="" />
				<div>
					<p>欢迎关注我们的微信公共账户！</p>
					<p>帐号：JXH-20121220</p>

				</div>

			</div>
			<div class="quick-links">
				<a href="contact.php">联系我们</a>
				<a href="feed.php">意见反馈</a>
				<a href="">客户端下载</a>
			</div>
			
		</footer>
	</div>
	<?php include '_icat.php';?>
	<div class="copyright">
		<p>增值电信许可证:<a href="<?php echo $appPic;?>/1.jpg" target="_blank">粤B2-20120350</a></p>
		<p>Copyright © 2012 <a target="_blank" href="http://www.miitbeian.gov.cn/">粤ICP备05087105号</a></p>
		<p>网络文化经营许可证:<a href="<?php echo $appPic;?>/2.jpg" target="_blank">粤网文[2013]029-029号</a></p>
	</div>
</body>
</html>