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
					<h1>退/换货</h1>
					<span class="voice"></span>
				</div>
			</div>
		</header>

		<section id="iScroll">
			<div class="submit-success">
				<div class="success-text">
					<h3>尊敬的用户，您的申请已经提交.</h3>
					<p>请等待...我们会在24小时内与您沟通，确认后，支持退换货请先支付邮费，我们会需要注意以下几个问题。如果时因为商品本身质量原因，我们会在收到货之后退还给您。感谢您对官方商城的支持我们用心服务，期待您十分满意。</p>
				</div>
				<div class="web-btn"><a href="">逛逛其他商品</a></div>
			</div>
		</section>
	</div>
</body>
</html>