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

	<article class="ac-smallpage">
		<div class="tips"><h2>支付超时！</h2></div>
		<div class="auto-skip">
			<p class="mrb20">我们还没有收到第三方支付的响应，<br><b class="zt-red">请您稍后查询账户余额。</b></p>
			<p id="J_autoSkip" data-href="http://gou.gionee.com"><em>30</em>秒后自动转向支付页</p>
			<p>如果没有反应，请点击<a href="http://gou.gionee.com">这里</a></p>
		</div>
		<div class="order">
			<dl>
				<dd>充值方式：支付宝</dd>
				<dd>充值金额：100元</dd>
				<dd>充值结果：失败</dd>
				<dd>失败原因：充值卡错误</dd>
			</dl>
		</div>
	</article>
	<footer class="ft-smallpage"><p><a href="tel:400-779-6666">任何问题：欢迎联系400-779-6666</a></p></footer>
</div>
</body>
</html>