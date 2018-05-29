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
			<h1>游戏专题</h1>
		</header>
		<div class="goTop J_gotoTop">
			<a ></a>
		</div>
		<article class="ac">
			<div class="item-box topic J_topicItem">
				<ul>
					<?php for($i=0; $i<8; $i++){?>
					<li>
						<a href="album.php">
							<div class="pic">
								<img src="<?php echo $appPic;?>/pic_topic.jpg" alt="">
							</div>
							<div class="desc">一周精品回顾</div>
							<div class="mask"></div>
						</a>
					</li>
					<?php }?>
				</ul>
			</div>
			<div class="load-more J_loadMore" data-ajaxUrl="json.php" data-hasnext="true" data-curpage="1">
				<span>加载更多</span>
			</div>
		</article>

		<footer class="ft">
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