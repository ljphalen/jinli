<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>我的乐园-兑奖</title>
	<?php include '_inc.php';?>
</head>
<body>
	<div id="page">
		<?php $title = '兑奖'; include '_sheader.php';?>
		
		<article class="ac exc-prize">
			<div class="prize-list clearfix">
				<ul>
					<?php for($i=1; $i<=8; $i++){?>
					<li><span><img src="<?php echo $appPic;?>/pic_prizePic.jpg" alt="" /></span></li>
					<?php }?>
					<li><span></span></li>
				</ul>
			</div>
			
			<div class="btn-wrap">
				<a href="winprize3.php" class="btn" id="J_prizeBtn" data-ajaxUrl="json.php">兑奖</a>
			</div>
			<div class="sign-rule">
				<p>点击兑奖，系统将随机选中其中一款奖项，祝您好运。</p>
			</div>
		</article>
	</div>
</body>
</html>