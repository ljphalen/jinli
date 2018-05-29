<?php
$app = 'assets';
$appPic = $app.'/pic';
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title></title>
	<meta name="viewport" content="width=device-width,target-densitydpi=device-dpi, initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=no" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<link rel="stylesheet" href="<?=$app?>/css/core.css" />
	<link rel="stylesheet" href="<?=$app?>/css/pay.css" />
</head>
<body onload="var href = location.href; location.href = href;">
	<div id="page" class="has-tip"><!-- 加has-tip这个class表示有弹窗口出现，无则不出现 -->
		<div class="mask"></div>
		<div class="dialog">
			<div class="main">
				<p><span>抱歉通知您，<br />本次支付失败！</span></p><!-- 恭喜您，支付成功！ -->
				<div class="btn"><a href=""><span>查看买到的商品</span></a></div>
			</div>
		</div>
		
		<header class="hd">
			<h1><strong>金立收银台</strong></h1>
		</header>
		<article class="ct">
			<section>
				<form name="order" action="" method="post">
					<ul>
						<li><span><em>订单号：</em>JLG123456789</span><input type="hidden" name="id" value="JLG123456789" /></li>
						<li><span><em>商品名称：</em>富安娜家纺 单人清凉印花夏被152*210cm</span><input type="hidden" name="name" value="富安娜家纺 单人清凉印花夏被152*210cm" /></li>
						<li><span><em>应付金额：</em>￥198.00</span><input type="hidden" name="price" value="￥198.00" /></li>
					</ul>
				</form>
			</section>
		</article>
		<script type="text/javascript">
			function submitForm(){
				var form = document.forms['order'];
				form.submit();
			}
		</script>
		<footer class="ft">
			<div class="pay-ways">
				<ul>
					<li><a href="javascript:submitForm();"><i class="icon"><img src="<?=$appPic?>/pic_alipay.jpg" alt="" /></i><em>使用支付宝支付</em><s></s></a></li>
					<li><span><i class="icon"><img src="<?=$appPic?>/pic_yinlian.jpg" alt="" /></i><em>使用银联支付</em></span></li>
				</ul>
			</div>
		</footer>
	</div>
</body>
</html>