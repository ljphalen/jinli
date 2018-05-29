<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>金立门户</title>
	<?include '_inc.php'?>
</head>
<body>
	<div data-role="page" id="page" class="pros" data-theme="no">
		<header class="hd">
			<div class="logo">
				<h1 class="inner-title"><strong>产品大全</strong></h1>
			</div>
			<nav>
				<ul class="oz">
					<li><a href="proCla.php" data-ajax="false"><span>e-life<br />系列</span></a></li>
					<li><a href="" class="selected"><span>天鉴<br />系列</span></a></li>
					<li><a href=""><span>语音王<br />系列</span></a></li>
					<li><a href=""><span>荷塘<br />系列</span></a></li>
				</ul>
			</nav>
		</header>
		<div class="ct">
			<div class="scroll-pro isTouch">
				<span class="handle ui-slide-prev"><img src="<?=$appPic?>/jt_left.png" alt="" /></span>
				<span class="handle ui-slide-next"><img src="<?=$appPic?>/jt.png" alt="" /></span>
				<div class="box ui-slide-scrollbox">
					<ul class="ui-slide-scroll oz">
						<?for($i=0; $i<3; $i++){?>
						<li class="ui-slide-item">
							<div class="mask"></div>
							<div class="title"><span>GN10<?=$i+1?></span></div>
							<a href="proDetail.php" data-ajax="false"><img src="<?=$appPic?>/pic_mobile<?=$i+1?>.jpg" alt="" /></a>
						</li>
						<?}?>
					</ul>
				</div>
				<div class="ui-slide-tabs num">
					<span class="ui-slide-tab ui-slide-tabcur">1</span>
					<span class="ui-slide-tab">2</span>
					<span class="ui-slide-tab">3</span>
					<!-- <s>...</s>
					<span>12</span> -->
				</div>
			</div>
		</div>
	</div>
</body>
</html>