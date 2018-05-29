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
			<p>充值将在2分钟内完成</p>
			<p class="zt-orange J_wait">正在充值，请您耐心等待...</p>
			<p><a class="refresh" href="index.php" onclick="document.getElementsByClassName('J_wait')[0].style.display='block';return false;"><img src="<?php echo $appPic;?>/loading.gif" alt=""  >点击刷新结果</a></p>
		</div>
		<div class="back">
			<a href="index.php">返回继续支付</a>
		</div>
	</article>
</div>
</body>
</html>