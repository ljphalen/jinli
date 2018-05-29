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
				<div class="ui-toolbar-title"><h1>充值已受理</h1></div>
			</div>
		</div>
	</header>

	<article class="recharge">
		<div class="charge-info">
		<img class="pic_tip" src="<?php echo $appPic;?>/fail.png" alt="" />
		<p class="tip">充值失败!</p>
		</div>
		<div class="back">
			<a href="index.php">返回继续支付</a>
		</div>
	</article>
</div>
</body>
</html>