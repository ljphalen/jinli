<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<title></title>
	<?php include '_inc.php';?>
</head>
<body>
<div id="page">
	<header>
		<div class="ui-toolbar">
			<div class="ui-toolbar-wrap">
				<div class="ui-toolbar-left">
					<a class="ui-toolbar-backbtn" href="step1.php">返回</a>
				</div>
				<div class="ui-toolbar-title"><h1>充值方式</h1></div>
			</div>
		</div>
	</header>

	<section class="content">
		<div class="order-info has-border-bottom">
			<ul>
				<li>金币余额不足，请先充值！</li>
			</ul>
		</div>
		<div class="pay-title"><h2>请选择支付方式</h2></div>
		<div class="pay-list">
			<ul class="pay-ways">
				<li><a href="step3.php">支付宝</a></li>
				<li><a href="step3.php">银联</a></li>
				<li><a href="step3.php">财付通</a></li>
			</ul>

			<ul class="pay-ways">
				<li><a href="step3.php">充值卡</a></li>
				<li><a href="step3.php">点卡</a></li>
			</ul>
		</div>
	</section>

	<footer class="ft-smallpage"><p><a href="tel:400-779-6666">客服热线：400-779-6666</a></p></footer>
</div>
</body>
</html>