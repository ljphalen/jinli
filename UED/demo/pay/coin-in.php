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
				<div class="ui-toolbar-title"><h1>金币充值</h1></div>
			</div>
		</div>
	</header>
	<section >
		<div class="coin-in">
			<div class="pay-account">帐号：138123456789</div>
			<div class="tip"><span>余额不足</span>，还缺X金币，请先充值!</div>
			<div class="pay-title"><h2>请选择支付方式：</h2></div>
		<div class="pay-list">
			<ul class="pay-ways">
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
					<a href="index.php">
					<span class="pic"><img src="<?php echo $appPic;?>/phone.png" alt="" /></span>
					<span class="desc">
						<em>手机充值卡</em>
						<b>支持联通、移动、电信手机充值</b>
					</span>
					</a>
				</li>
				<li>
					<a href="index.php">
					<span class="pic"><img src="<?php echo $appPic;?>/card.png" alt="" /></span>
					<span class="desc">
						<em>游戏点卡</em>
						<b>支持网易一卡通，盛大一卡通等</b>
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
		</div>
	</section>
</div>
</body>
</html>