<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title></title>
	<?php include '_inc.php'; ?>
</head>
<body>
	<div id="page" class="products">
		<?php include 'header.php'; ?>
		
		<article class="ct">
			<div class="scroll-pro J_scrollPro isTouch">
				<span class="handle ui-slide-prev"><s>&lt;</s></span>
				<span class="handle ui-slide-next"><s>&gt;</s></span>
				<div class="box ui-slide-scrollbox">
					<ul class="ui-slide-scroll oz">
						<?php for($i=0; $i<3; $i++){?>
						<li class="ui-slide-item">
							<i><img src="<?php echo $appPic;?>/pic_mobile<?php echo $i+1;?>.jpg" alt="" /></i>
						</li>
						<?php }?>
					</ul>
				</div>
				<div class="ui-slide-tabs point">
					<span class="ui-slide-tab ui-slide-tabcur"></span>
					<span class="ui-slide-tab"></span>
					<span class="ui-slide-tab"></span>
				</div>
			</div>
		</article>
	</div>
</body>
</html>