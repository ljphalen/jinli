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
					<a class="ui-toolbar-backbtn" href="index.php">返回</a>
				</div>
				<div class="ui-toolbar-title"><h1>收银台</h1></div>
			</div>
		</div>
	</header>
	<section class="content">
		<div class="order-info has-border-bottom">
			<ul>
				<li><span class="bold">商品名称：</span><em>富安娜家纺</em></li>
				<li><span class="bold">订单金额：</span><em>243</em>元</li>
				<li><span class="bold">使用金豆：</span><em>300</em>金豆&nbsp;(&nbsp;3元&nbsp;)</li>
				<li><span class="bold">您还需支付：</span><em class="zt-red">238</em>元</li>
			</ul>
		</div>
		<div class="pay-title"><h2>请选择支付方式：</h2></div>
		<div class="pay-list">
			<ul class="pay-ways">
				<li>
					<span class="icon-num"><em>100金币</em></span>
					<a href="index.php">
					<span class="pic"><img src="<?php echo $appPic;?>/gold_coin.png" alt="" /></span>
					<span class="desc">
						<em>金币卡</em>
						<b>支持兑换金立公司金币卡</b>
					</span>
					</a>
				</li>
				<li>
					<a href="index.php">
					<span class="pic"><img src="<?php echo $appPic;?>/zfb.png" alt="" /></span>
					<span class="desc">
						<em>支付宝</em>
						<b>支持消费支付宝账户余额</b>
					</span>
					</a>
				</li>
				<li>
					<a href="index.php">
						<span class="pic"><img src="<?php echo $appPic;?>/cft.png" alt="" /></span>
						<span class="desc">
						<em>财付通</em>
						<b>支持消费财付通账户余额</b>
					</span>
					</a>
				</li>
				<li>
					<a href="step3.php">
						<span class="pic"><img src="<?php echo $appPic;?>/pic-yhk.png" alt="" /></span>
						<span class="desc">
						<em>银行卡</em>
						<b>支持消费财付通账户余额</b>
					</span>
					</a>
				</li>
			</ul>
		</div>
	</section>
	<footer class="ft-smallpage"><p><a href="tel:400-779-6666">客服热线：400-779-6666</a></p></footer>
</div>
</body>
</html>