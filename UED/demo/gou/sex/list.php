<!DOCTYPE HTML>
<?php include '_cfg.php';?>
<html class="<?php echo $ucClass;?>">
<head>
	<meta charset="UTF-8">
	<title>demo</title>
	<?php include '_inc.php';?>
</head>

<body id="list" data-pagerole="body">
	<div class="module">
		<section id="iScroll">
			<div class="tj-list J_tjList" data-ajaxUrl="http://mall.miigou.com/api/H5/index?kid=" data-hasnext="true">
				<ul>
					<?php for($i=0; $i<6; $i++){?>
					<li>
						<a href="detail.php">
							<div class="pic">
								<img src="<?php echo "$webroot/$appPic/pic_listimg.jpg";?>" alt="">
								<span class="price">￥30</span>
							</div>
							<div class="desc">凭借简约简约新奇亲和力的品理念树品理念树立起...</div>
						</a>
					</li>
					<?php }?>
				</ul>
				<div id="pullUp">
					<span class="pullUpIcon"></span><span class="pullUpLabel">滑动加载更多...</span>
				</div>
			</div>
		</section>
	</div>
</body>
</html>