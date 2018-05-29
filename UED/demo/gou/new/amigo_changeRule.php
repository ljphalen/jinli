<!DOCTYPE HTML>
<?php include '_cfg.php';?>
<html<?php echo ($ucClass? ' class="'.$ucClass.'"' : '');?>>
<head>
	<meta charset="UTF-8">
	<title>退/换货</title>
	<script>var webPage = true;</script>
	<?php include '_inc.php';?>
	<link rel="stylesheet" href="<?php echo "$webroot/$appRef/assets/css/web$source.css$timestamp";?>">
</head>

<body data-pagerole="body">
	<div class="module">
		<header id="iHeader" class="hd">
			<div class="top-wrap">
				<div class="title">
					<a href="" class="back"></a>
					<h1>退/换货规则</h1>
					<span class="voice"></span>
				</div>
			</div>
		</header>

		<section id="iScroll">
			<div class="rule-text">
				<div class="title"><span>退换货流程</span></div>
				<h3>1. 如果您是在线支付购买用户：</h3>
				<p><img src="<?php echo "$webroot/$appPic/amigo_pic_ruleImg1.jpg";?>"></p>

				<h3>2. 如果您是货到付款购买用户：</h3>
				<p><img src="<?php echo "$webroot/$appPic/amigo_pic_ruleImg2.jpg";?>"></p>

				<div class="title"><span>退换货说明</span></div>
				<p>1. 购物大厅官方商城支持自货物签收后7天内无理由退换货，请保持商品出售时原包装及配件完好；<br>
					2. 如非产品本身质量问题，退换货运费由消费者承担；如是产品本身质量问题，退换货运费则由卖方承担；<br>
					3. 退款条件达成后，退款到账时间需约1-3个工作日；<br>
					4. 如参加买赠促销的订单，退货时须将赠品一起寄回。</p>
			</div>
		</section>
	</div>
</body>
</html>