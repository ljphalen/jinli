<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title></title>
	<?php include '_inc.php'; ?>
</head>
<body>
	<div id="page" class="series">
		<?php include 'header.php'; ?>
		
		<article class="ct">
			<nav class="sub-nav">
				<div class="J_navList hidden">
					<ul>
						<li class="selected"><a href="products.php"><span>e-life<br/>系列</span></a></li>
						<li><a href="products.php"><span>天鉴<br/>系列</span></a></li>
						<li><a href="products.php"><span>语音王<br/>系列</span></a></li>
						<li><a href="products.php"><span>普及<br/>系列</span></a></li>
					</ul>
				</div>
				<div class="handle open"><span>分类<s></s></span></div>
			</nav>
			
			<div class="scroll-pro J_scrollPro isTouch">
				<span class="handle ui-slide-prev ui-slide-prevdis"><s>&lt;</s></span>
				<span class="handle ui-slide-next"><s>&gt;</s></span>
				<div class="box ui-slide-scrollbox">
					<ul class="ui-slide-scroll oz">
						<?php for($i=0; $i<3; $i++){?>
						<li class="ui-slide-item">
							<div class="title"><span>GN10<?php echo $i+1;?></span></div>
							<a href="proDetail.php"><img src="<?php echo $appPic;?>/pic_mobile<?php echo $i+1;?>.jpg" alt="" /></a>
						</li>
						<?php }?>
					</ul>
				</div>
				<div class="ui-slide-tabs num">
					<span class="ui-slide-tab ui-slide-tabcur">1</span>
					<span class="ui-slide-tab">2</span>
					<span class="ui-slide-tab">3</span>
				</div>
			</div>
		</article>
	</div>
</body>
</html>