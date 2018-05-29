<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>金立门户</title>
	<?include '_inc.php'?>
</head>
<body>
	<div data-role="page" id="page" class="pro-cla" data-theme="no">
		<header class="hd">
			<div class="logo">
				<h1 class="inner-title"><strong>机型：GN205</strong></h1>
			</div>
		</header>
		<div class="ct">
			<div class="scroll-pro isTouch">
				<span class="handle ui-slide-prev"><img src="<?=$appPic?>/jt_left.png" alt="" /></span>
				<span class="handle ui-slide-next"><img src="<?=$appPic?>/jt.png" alt="" /></span>
				<div class="box ui-slide-scrollbox">
					<ul class="ui-slide-scroll oz">
						<?for($i=0; $i<3; $i++){?>
						<li class="ui-slide-item"><a href="proDetail.php" data-ajax="false"><img src="<?=$appPic?>/pic_mobile<?=$i+1?>.jpg" alt="" /></a></li>
						<?}?>
					</ul>
				</div>
				<div class="ui-slide-tabs point">
					<span class="ui-slide-tab ui-slide-tabcur"></span>
					<span class="ui-slide-tab"></span>
					<span class="ui-slide-tab"></span>
				</div>
			</div>
		</div>
	</div>
</body>
</html>