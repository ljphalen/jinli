<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<title></title>
	<?php include '_inc.php'; ?>
</head>
<body>
	<div data-role="page" data-theme="no" id="page" class="home">
		<header class="hd">
			<h1><strong>金立购</strong></h1>
			<p><span>手机购物这儿最省</span></p>
		</header>
		
		<div class="ct">
			<!-- <div class="b-banner"><a href=""><img src="<?php echo $appPic; ?>/pic_bbanner.jpg" alt="" /></a></div> -->
			<div class="mainfocus isTouch" id="mainfocus">
				<div class="ui-slide-scrollbox">
					<ul class="ui-slide-scroll clearfix">
						<li class="ui-slide-item"><a href="products.asp"><img src="<?php echo $appPic; ?>/pic_bbanner1.jpg" alt=""></a></li>
						<li class="ui-slide-item"><a href="products.asp"><img src="<?php echo $appPic; ?>/pic_bbanner2.jpg" alt=""></a></li>
						<li class="ui-slide-item"><a href="products.asp"><img src="<?php echo $appPic; ?>/pic_bbanner3.jpg" alt=""></a></li>
					</ul>
				</div>
				<div class="ui-slide-tabs">
					<span class="ui-slide-tab ui-slide-tabcur"></span>
					<span class="ui-slide-tab"></span>
					<span class="ui-slide-tab"></span>
				</div>
				<span class="ui-slide-prev"></span>
				<span class="ui-slide-next"></span>
			</div>
			
			<div id="J_tearPaper" class="s-banner cor1">
				<a href="showOrder.php"><em>晒单拿话费</em><img src="<?php echo $appPic; ?>/pic_sbanner.gif" alt="" /></a>
			</div>
			
			<section>
				<article class="pic-text">
					<span class="handle disable"></span>
					<span class="handle"></span>
					<div id="J_textPic" class="scroll-pic" dt-ajaxUrl="ajaxData.php">
						<div class="wrap">
							<ul>
								<li class="cor2">
									<a href="">
										<img src="<?php echo $appPic; ?>/pic_baobei1.jpg" alt="" /><em><span>茅台王子，新品特价，买到赚到</span></em>
									</a>
								</li>
								<li class="cor3">
									<a href="">
										<img src="<?php echo $appPic; ?>/pic_baobei2.jpg" alt="" /><em><span>精选百货，超值商品送到家</span></em>
									</a>
								</li>
							</ul>
							<ul>
								<li class="cor3">
									<a href="">
										<img src="<?php echo $appPic; ?>/pic_baobei3.jpg" alt="" /><em><span>先收货付款，绝对的安全购物体验</span></em>
									</a>
								</li>
								<li class="cor4">
									<a href="">
										<img src="<?php echo $appPic; ?>/pic_baobei4.jpg" alt="" /><em><span>先收货付款，绝对的安全购物体验</span></em>
									</a>
								</li>
							</ul>
						</div>
					</div>
				</article>
				
				<div class="item cor1">
					<h2><strong>百宝箱</strong></h2>
					<div class="b-box clearfix">
						<a href=""><em>话费快充</em><s class="hot"><img src="<?php echo $appPic; ?>/ico_hot.png" alt="" /></s></a>
						<a href=""><em>彩票</em><s class="hot"><img src="<?php echo $appPic; ?>/ico_hot.png" alt="" /></s></a>
						<a href=""><em>酒店预订</em><s class="hot"><img src="<?php echo $appPic; ?>/ico_hot.png" alt="" /></s></a>
					</div>
				</div>
				<div class="item cor3">
					<h2><strong>流行趋势风向标</strong></h2>
					<figure>
						<div class="pic"><img src="<?php echo $appPic; ?>/pic_fashion.jpg" alt="" /></div>
						<div class="desc">
							<ul>
								<?php for($i=0; $i<4; $i++){?>
								<li><a href="">静佳JMIXP,“唇唇”欲动的诱惑唇蜜</a></li>
								<?php }?>
							</ul>
						</div>
					</figure>
				</div>
				<div class="item cor1">
					<h2><strong>女人看这里</strong></h2>
					<div class="icon">
						<i><img src="<?php echo $appPic; ?>/ico_woman.png" alt="" /></i>
						<?php for($i=0; $i<6; $i++){?>
						<a href=""><em>面护理</em></a>
						<?php }?>
					</div>
				</div>
				<div class="item cor2">
					<h2><strong>男人看这里</strong></h2>
					<div class="icon">
						<i><img src="<?php echo $appPic; ?>/ico_man.png" alt="" /></i>
						<?php for($i=0; $i<6; $i++){?>
						<a href=""><em>面护理</em></a>
						<?php }?>
					</div>
				</div>
				<div class="item cor3">
					<h2><strong>更多精彩</strong></h2>
					<div class="normal">
						<?php for($i=0; $i<6; $i++){?>
						<a href=""><em>面部护理</em></a>
						<?php }?>
					</div>
				</div>
				<div class="feed"><a href="feed.php"><span>我有话要说...</span></a></div>
			</section>
		</div>
	</div>
</body>
</html>