<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<title></title>
	<?php include '_inc.php';?>
</head>
<body>
	<div id="page"><!--  class="has-tip"加has-tip这个class表示有弹窗口出现，无则不出现 -->
		<div class="mask"></div>
		<div class="dialog">
			<div class="main">
				<p><span>抱歉通知您，<br />本次支付失败！</span></p><!-- 恭喜您，支付成功！ -->
				<div class="btn"><a href=""><span>查看买到的商品</span></a></div>
			</div>
		</div>

		<header class="hd-smallpage">
			<h1>收银台</h1>
			<div class="back-prev"><a href=""></a></div>
		</header>
		<article class="ct">
			<section>
				<ul>
					<li><span><em>订单号：</em>JLG123456789</span></li>
					<li><span><em>商品名称：</em>富安娜家纺 单人清凉印花夏被152*210cm</span></li>
					<li><span><em>应付金额：</em>￥198.00</span></li>
				</ul>
			</section>
		</article>
		<div class="pay-ways">
			<ul>
				<li><a href="">金币支付（余额300金币）</a></li>
				<li><a href="">支付宝支付</a></li>
				<li><a href="">银联支付</a></li>
				<li><a href="">财付通支付</a></li>
			</ul>
		</div>
		
	</div>
</body>
</html>