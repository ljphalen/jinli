<!DOCTYPE HTML>
<?php include '_cfg.php';?>
<html<?php echo ($ucClass? ' class="'.$ucClass.'"' : '');?>>
<head>
	<meta charset="UTF-8">
	<title>官方商城</title>
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
					<h1>官方商城</h1>
				</div>
			</div>
			<wrapsubnav>
				<nav>
					<ul>
						<li><a href="amigo_mall.php">商品</a></li>
						<li><a href="amigo_activities.php">活动</a></li>
						<li class="selected"><span>服务</span></li>
					</ul>
				</nav>
			</wrapsubnav>
		</header>

		<section id="iScroll">
			<div class="service-list">
				<ul>
					<li><a href="amigo_orderView.php">我要查询订单</a></li>
					<li><a href="amigo_orderChange1.php">我要退/换货</a></li>
					<li><a href="tel:0571">我要联系客服</a></li>
				</ul>
			</div>
		</section>
	</div>
</body>
</html>