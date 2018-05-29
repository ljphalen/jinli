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
				<div class="ui-toolbar-title"><h1>支付宝充值</h1></div>
			</div>
		</div>
	</header>
	<section>
		<form action="###" method="post">	
		<div class="alipay-in">
			<div class="in-title">
				<h2>请选择充值金额</h2>
			</div>
			<div class="in-list">
				<ul>
					<li class="selected">10元</li>
					<li>20元</li>
					<li>50元</li>
					<li >100元</li>
					<li>200元</li>
					<li>500元</li>
				</ul>
			</div>
			<div class="input">
				<span>充值金额(元)</span>
				<input type="text" placeholder="单笔不超过500元" />
				
			</div>
			<div class="tip">充值<span >100元=100金币</span>(费率0%)</div>
			<div class="button">
				<div class="back"><input type="submit" name="submit" class="btn" value="立即充值"  /></div>	
				<div class="back"><a class="btn-light" href="alipay-in.php">选择其他方式</a></div>
			</div>
		</div>
	</form>
	</section>
	<div class="J_tipBox tip-box">
		<p>请输入正确的卡号或密码</p>
	</div>
</div>
</body>
</html>