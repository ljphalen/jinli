<!DOCTYPE HTML>
<?php include '_cfg.php';?>
<html<?php echo ($ucClass? ' class="'.$ucClass.'"' : '');?>>
<head>
	<meta charset="UTF-8">
	<title>创建订单</title>
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
					<h1>查询结果</h1>
					<span class="voice"></span>
				</div>
			</div>
		</header>

		<section id="iScroll">
			<div class="result-show">
				<div class="main">
					<span class="state">订单状态：订单已创建成功。您还未付款，请立即支付。</span>
					<span class="detail">
						<em>详细信息</em>
						收货人：xxx<br>
						手机号：xxx<br>
						创建时间：2014-01-05 20:04:13<br>
						商品名称：罗马仕移动电源<br>
						收货地址：广东省深圳市深南大道1234号
					</span>
				</div>
				<div class="web-btn"><a href="" class="gray">修改订单信息</a><a href="">立即支付</a></div>
			</div>
			<div class="result-show">
				<p>尊敬的用户, 您于2014年1月16日09:00支付成功. 请等待收货.</p>
			</div>
		</section>
	</div>
</body>
</html>