<!DOCTYPE HTML>
<?php include '_cfg.php';?>
<html class="<?php echo $ucClass;?>">
<head>
	<meta charset="UTF-8">
	<title>游戏大厅客户端</title>
	<?php include '_inc.php';?>
</head>
<!-- <body>
	<div id="page" >
		<section class="wrap first-grade">
			<div class="pic-list clearfix special">
				<ul>
					<?php for($i=0; $i<10; $i++){?>
					<li>
						<a href="topicDetail.php">
							<span class="pic"><img src="<?php echo $appPic;?>/pic_topic.jpg" alt=""></span>
							<span class="desc">网络游戏合辑</span>
							<span class="mask"></span>
						</a>
					</li>
					<?php }?>
				</ul>
			</div>
		</section>
		<div class="load-more J_loadMore" data-ajaxurl="json.php" data-hasnext="1" data-curpage="1">
			<span>加载更多</span>
		</div>
	</div>
</body> -->
<body id="subject" data-pagerole="body"></body>
</html>