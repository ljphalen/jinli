<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<title></title>
	<?php include '_inc.php';?>
</head>
<body>
<div class="full-mask"></div>
<div class="full-loading">
	<p><img src="<?php echo $appPic;?>/loading.gif" /></p>
	<p>正在加载...</p>
</div>
<div id="page">
	<header>
		<div class="ui-toolbar">
			<div class="ui-toolbar-wrap">
				<div class="ui-toolbar-left">
					<a class="ui-toolbar-backbtn" href="step2.php">返回</a>
				</div>
				<div class="ui-toolbar-title"><h1>支付宝充值</h1></div>
			</div>
		</div>
	</header>

	<section class="content">
		<form action="###" method="post">
			<section class="order-info has-border-bottom">
				<ul>
					<li>请输入充值金额<input type="text" name="num" class="inp-small inp-text" value="" autocomplete="off" maxLength="7" />元</li>
					<li>充值将获得：<em class="zt-red">10金币</em>（费率0%）</li>
				</ul>
			</section>
			<div class="ui-tips-box">
				<h2>温馨提示：</h2>
				<p>
					1.使用支付宝、财付通、银联充值，<em class="zt-red">1元=1金币</em><br />
					2.充值过程可能有1分钟左右，请您耐心等待。<br />
					3.如遇到充值不到账的情况，请您联系客服。
				</p>
			</div>
			<div class="button mrt20">
				<input type="submit" name="submit" class="btn gray" value="确定充值" />
			</div>
		</form>
	</section>
	
	<footer class="ft-smallpage"><p><a href="tel:400-779-6666">客服热线：400-779-6666</a></p></footer>
</div>
</body>
</html>