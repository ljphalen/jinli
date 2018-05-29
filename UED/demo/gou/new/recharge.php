<!DOCTYPE HTML>
<?php include '_cfg.php';?>
<html<?php echo ($ucClass? ' class="'.$ucClass.'"' : '');?>>
<head>
	<meta charset="UTF-8">
	<title>话费充值</title>
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
					<h1>话费充值</h1>
					<span class="voice"></span>
				</div>
			</div>
		</header>
		<section id="iScroll">
			<form name="rechargeForm" id="recharge-form" channel-id="" class="recharge-form" action="">
				<div class="txt">填写充值号码：</div>
				<input name="phone"
					class="j-phone"
					id="j-phone"
					type="tel"
					placeholder="支持移动、联通、电信">
				<div id="j-phone-clone" class="j-phone-clone" style="display:none;">13632835121 中国移动</div>
				<div id="j-error-msg" class="error-msg" style="display:none;">请输入正确的手机号码</div>
				<div class="txt mt">选择充值金额：</div>
				<div class="num-list-wrap">
					<ul class="num-list">
						<li class="active" data-num="30">30元</li>
						<li data-num="50">50元</li>
						<li data-num="100">100元</li>
					</ul>
				</div>
				<div class="num">
					价格：<span id="j-price">￥29-29.5元</span>
				</div>
				<div class="web-btn" id="j-recharge-submit"><a href="javascript:;">立即充值</a></div>
				<input id="j-cardnum" name="cardnum" value="30" type="hidden" >
				<p class="remind">温馨提示：您所充值的手机号码将会在十分钟内自动到账，用户可以拨打当地运营商电话查询到账情况，如24小时未到账，请联系客服热线0755122312</p>
			</form>		
		</section>
	</div>
</body>
</html>