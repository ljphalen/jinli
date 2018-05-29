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
			<h1><strong>一起购</strong></h1>
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
			
			<div class="s-banner">
				<a href="freeCharge.php"><em>免费拿话费，只需动动手指</em><img src="<?php echo $appPic; ?>/pic_sbanner.gif" alt="" /></a>
			</div>
			
			<section>
				<article class="pic-text">
					<span class="handle disable"></span>
					<span class="handle"></span>
					<div id="J_textPic" class="scroll-pic" dt-ajaxUrl="ajaxData.php">
						<div class="wrap">
							<!-- <ul>
							<?php for($i=0; $i<4; $i++){?>
								<li class="cor-blue">
									<a href="">
										<img src="<?php echo $appPic; ?>/pic_baobei1.jpg" alt="" /><em><span>茅台王子，新品特价，买到赚到</span></em>
									</a>
								</li>
								<?php if($i==1){?></ul><ul><?php }?>
							<?php }?>
							</ul> -->
							<ul>
								<li class="cor-blue">
									<a href="">
										<img src="<?php echo $appPic; ?>/pic_baobei1.jpg" alt="" /><em><span>茅台王子，新品特价，买到赚到</span></em>
									</a>
								</li>
								<li class="cor-orange">
									<a href="">
										<img src="<?php echo $appPic; ?>/pic_baobei2.jpg" alt="" /><em><span>精选百货，超值商品送到家</span></em>
									</a>
								</li>
							</ul>
							<ul>
								<li class="cor-red">
									<a href="">
										<img src="<?php echo $appPic; ?>/pic_baobei3.jpg" alt="" /><em><span>先收货付款，绝对的安全购物体验</span></em>
									</a>
								</li>
								<li class="cor-purple">
									<a href="">
										<img src="<?php echo $appPic; ?>/pic_baobei4.jpg" alt="" /><em><span>先收货付款，绝对的安全购物体验</span></em>
									</a>
								</li>
							</ul>
						</div>
					</div>
				</article>
				
				<div class="item cor-blue">
					<h2><strong>百宝箱</strong></h2>
					<div class="b-box clearfix">
						<a href=""><em>话费快充</em></a>
						<a href=""><em>彩票</em></a>
						<a href=""><em>图书馆</em></a>
					</div>
				</div>
				<div class="item cor-red">
					<h2><strong>销量排行榜</strong></h2>
					<div class="normal">
						<a href=""><em>TOP 1&nbsp;&nbsp;&nbsp;连衣裙</em></a>
						<a href=""><em>TOP 2&nbsp;&nbsp;&nbsp;女T恤</em></a>
						<a href=""><em>TOP 3&nbsp;&nbsp;&nbsp;男T恤</em></a>
						<a href=""><em>TOP 4&nbsp;&nbsp;&nbsp;男衬衫</em></a>
						<a href=""><em>TOP 5&nbsp;&nbsp;&nbsp;女凉鞋</em></a>
						<a href=""><em>TOP 6&nbsp;&nbsp;&nbsp;男板鞋</em></a>
					</div>
				</div>
				<div class="item cor-purple">
					<h2><strong>女人看这里</strong></h2>
					<div class="icon">
						<i><img src="<?php echo $appPic; ?>/ico_woman.png" alt="" /></i>
						<?php for($i=0; $i<6; $i++){?>
						<a href=""><em>面护理</em></a>
						<?php }?>
					</div>
				</div>
				<div class="item cor-blue">
					<h2><strong>男人看这里</strong></h2>
					<div class="icon">
						<i><img src="<?php echo $appPic; ?>/ico_man.png" alt="" /></i>
						<?php for($i=0; $i<6; $i++){?>
						<a href=""><em>面护理</em></a>
						<?php }?>
					</div>
				</div>
				<div class="item cor-orange">
					<h2><strong>猜你想要</strong></h2>
					<div class="normal">
						<?php for($i=0; $i<6; $i++){?>
						<a href=""><em>面部护理</em></a>
						<?php }?>
					</div>
				</div>
			</section>
		</div>
	</div>
</body>
</html>