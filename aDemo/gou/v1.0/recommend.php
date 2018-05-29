<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>金立购—触屏版-搜索查询</title>
<?php include '_inc.php';?>
</head>

<body>
<section id="page">
	<?php include '_header.php';?>
	<!-- 主体内容 -->
	<section id="content">
		<section class="goods">
			<section class="recommend">
				<div class="top-info">
					<div class="pic"><img src="<?php echo $appRef;?>/pic/precommend-banner.jpg" alt="" /></div>
					<div class="txt mod-ws-box">
						<p><h3>小编寄语：</h3></p>
						<p>blablablablablablablablablabla
						blablablablablablablablablabla
						blablablablablablablablablabla</p>
						<p>让我们一起出发吧！</p>
					</div>
				</div>
				<section class="main">
					<header><h1>推荐商品</h1></header>
					<section class="mod-show-list" id="J_publWrap" data-ajaxUrl='json.php'>
						<ul class="mod-item-list">
							<li>
								<a href="product-detail.php">
									<div class="pic"><img src="<?php echo $appRef;?>/pic/search-list-pic01.jpg" alt="" /></div>
									<div class="txt">32人想要     ￥78.00</div>
								</a>
							</li>
							<li>
								<a href="product-detail.php">
									<div class="pic"><img src="<?php echo $appRef;?>/pic/search-list-pic02.jpg" alt="" /></div>
									<div class="txt">32人想要     ￥78.00</div>
								</a>
							</li>
							<li>
								<a href="product-detail.php">
									<div class="pic"><img src="<?php echo $appRef;?>/pic/search-list-pic01.jpg" alt="" /></div>
									<div class="txt">32人想要     ￥78.00</div>
								</a>
							</li>
							<li>
								<a href="product-detail.php">
									<div class="pic"><img src="<?php echo $appRef;?>/pic/search-list-pic01.jpg" alt="" /></div>
									<div class="txt">32人想要     ￥78.00</div>
								</a>
							</li>
							<li>
								<a href="product-detail.php">
									<div class="pic"><img src="<?php echo $appRef;?>/pic/search-list-pic02.jpg" alt="" /></div>
									<div class="txt">32人想要     ￥78.00</div>
								</a>
							</li>
							<li>
								<a href="product-detail.php">
									<div class="pic"><img src="<?php echo $appRef;?>/pic/search-list-pic01.jpg" alt="" /></div>
									<div class="txt">32人想要     ￥78.00</div>
								</a>
							</li>
							<li>
								<a href="product-detail.php">
									<div class="pic"><img src="<?php echo $appRef;?>/pic/search-list-pic01.jpg" alt="" /></div>
									<div class="txt">32人想要     ￥78.00</div>
								</a>
							</li>
						</ul>
					</section>
				</section>
			</section>
		</section>
	</section>
	<!--  /主体内容 -->
	<?php include '_footer.php';?>
</section>
<script type="template" id="J_itemView">
	<li>
		<a href="{list.click_href}">
			<div class="pic"><img src="{list.img}" alt="" /></div>
			<div class="txt">￥{list.price}</div>
		</a>
	</li>
</script>
</body>
</html>