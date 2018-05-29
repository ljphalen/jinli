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
				<div class="ui-toolbar-title"><h1>支付结果</h1></div>
			</div>
		</div>
	</header>

	<article class="recharge">
		<div class="charge-info">
			<img class="pic_tip" src="<?php echo $appPic;?>/success.png" alt="" />
			<p class="tip">支付成功!</p>
		</div>
		<div class="back">
			<a href="index.php">返回</a>
		</div>
	</article>
</div>
</body>
</html>