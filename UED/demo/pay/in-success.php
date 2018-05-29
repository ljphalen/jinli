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
		<img class="pic_tip" src="<?php echo $appPic;?>/success.png" alt="" />
		<p class="tip">
			<span>10金币充值成功!</span>
			<span>账户余额：100金币</span>
		</p>
		</div>
		<div class="back">
			<a href="index.php">返回继续充值</a>
		</div>
	</article>
</div>
</body>
</html>