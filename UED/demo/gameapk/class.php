<!DOCTYPE HTML>
<?php include '_cfg.php';?>
<html class="<?php echo $ucClass;?>">
<head>
	<meta charset="UTF-8">
	<title>游戏大厅客户端</title>
	<?php include '_inc.php';?>
</head>
<!-- <body>
		<div  id="page" class="autoHeight">
			<section class="wrap first-grade">
				<div class="pic-list clearfix">
					<ul>
						<?php for($i=0; $i<10; $i++){?>
						<li>
							<a href="claDetail.php">
								<span class="pic"><img src="<?php echo $appPic;?>/pic_class.jpg" alt=""></span>
							</a>
						</li>
						<?php }?>
					</ul>
				</div>
			</section>
					<div class="load-more J_loadMore" data-ajaxurl="json.php" data-hasnext="true" data-curpage="1"><span>加载更多</span></div>
		</div>
</body> -->
<body id="category" data-pagerole="body"></body>
</html>