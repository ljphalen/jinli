<!DOCTYPE HTML>
<?php include '_cfg.php';?>
<html<?php echo ($ucClass? ' class="'.$ucClass.'"' : '');?>>
<head>
	<meta charset="UTF-8">
	<title>提交订单</title>
	<script>var webPage = true;</script>
	<?php include '_inc.php';?>
	<link rel="stylesheet" href="<?php echo "$webroot/$appRef/assets/css/web$source.css$timestamp";?>">
</head>

<body data-pagerole="body">
	<div class="module">
		<header id="iHeader" class="hd">
			<div class="top-wrap">
				<div class="title">
					<h1>提交表单</h1>
					<span class="voice"></span>
				</div>
			</div>
		</header>

		<section id="iScroll">
			<div class="success-box">
				<div class="tip">
					<div class="ico"><span></span></div>
					<p>订单已提交</p>
					<p>订单号：35345455544</p>
				</div>
				<div class="web-btn"><a href="">随便逛逛</a></div>
				<p class="warning">温馨提示：选择在线支付的确认付款后2天内发货，选择货到付款的经客服人员确认后3天内发货;快递预计在一周内到达，请您保持手机畅通。</p>
			</div>
		</section>
	</div>
</body>
</html>