<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title></title>
	<?php include '_inc.php'; ?>
</head>
<body>
	<div id="page" class="home">
		<?php include 'header.php'; ?>
		
		<article class="ct">
			<div class="banner">
				<!--begin slide -->
				<div class="mainfocus isTouch" id="mainfocus">
					<div class="ui-slide-scrollbox">
						<ul class="ui-slide-scroll clearfix" >
							<li class="ui-slide-item"><a href=""><img src="<?php echo $appPic;?>/01.jpg" alt=""/></a></li>
							<li class="ui-slide-item"><a href=""><img src="<?php echo $appPic;?>/02.jpg" alt=""/></a></li>
							<li class="ui-slide-item"><a href=""><img src="<?php echo $appPic;?>/03.jpg" alt=""/></a></li>
						</ul>
					</div>
					<div class="mask"></div>
					<div class="ui-slide-tabs">
						<span class="ui-slide-tab ui-slide-tabcur"></span>
						<span class="ui-slide-tab"></span>
						<span class="ui-slide-tab"></span>
					</div>
					<span class="ui-slide-prev"></span>
					<span class="ui-slide-next ui-slide-nextdis"></span>
				</div>
				<!--end slide-->
			</div>
			
			<div class="channel">
				<div class="theSwiper">
					<div class="theList">
						<?php for($i=0; $i<3; $i++){?>
						<div class="theContent summ category">
							<a href="" class="title">
								<span class="icon"><img src="<?php echo $appPic;?>/ico_clShop.png" alt="" /></span>
								<span class="text"><em>购物</em></span>
							</a>
							<a href="" class="desc">
								<div class="box">
									<span>即日起雪佛兰品牌联手国内500多家经销商推出 驾享新价值...【详情<?php echo $i;?>】</span>
								</div>
							</a>
						</div>
						<?php }?>
					</div>
				</div>
				
				<div class="theSwiper">
					<div class="theList">
						<?php for($i=0; $i<3; $i++){?>
						<div class="theContent summ category">
							<a href="" class="title">
								<span class="icon"><img src="<?php echo $appPic;?>/ico_clShop.png" alt="" /></span>
								<span class="text"><em>新闻</em></span>
							</a>
							<a href="" class="desc">
								<div class="box">
									<span>即日起雪佛兰品牌联手国内500多家经销商推出 驾享新价值...【详情<?php echo $i;?>】</span>
								</div>
							</a>
						</div>
						<?php }?>
					</div>
				</div>
				
				<div class="theSwiper">
					<div class="theList">
						<?php for($i=0; $i<3; $i++){?>
						<div class="theContent summ category">
							<a href="" class="title">
								<span class="icon"><img src="<?php echo $appPic;?>/ico_clShop.png" alt="" /></span>
								<span class="text"><em>游戏</em></span>
							</a>
							<a href="" class="desc">
								<div class="box">
									<span>即日起雪佛兰品牌联手国内500多家经销商推出 驾享新价值...【详情<?php echo $i;?>】</span>
								</div>
							</a>
						</div>
						<?php }?>
					</div>
				</div>
				
				<div class="channel-box isTouch">
					<div class="ui-slide-scrollbox">
						<ul class="ui-slide-scroll clearfix" >
							<?php for($i=0; $i<3; $i++){?>
							<li class="ui-slide-item">
								<div class="wrap">
									<div class="title"><a href="">
										<span class="icon"><img src="<?php echo $appPic;?>/ico_clNews.png" alt="" /></span>
										<span class="text"><em>软件</em></span>
									</a></div>
									<div class="desc">
										<a href=""><span>即日起雪佛兰品牌联手国内500多家经销商推出 驾享新价值...【详情<?php echo $i;?>】</span></a>
									</div>
								</div>
							</li>
							<?php }?>
						</ul>
					</div>
					<div class="ui-slide-tabs">
						<span class="ui-slide-tab ui-slide-tabcur"></span>
						<span class="ui-slide-tab"></span>
						<span class="ui-slide-tab"></span>
					</div>
				</div>
				
				<div class="channel-box isTouch">
					<div class="ui-slide-scrollbox">
						<ul class="ui-slide-scroll clearfix" >
							<?php for($i=0; $i<3; $i++){?>
							<li class="ui-slide-item">
								<div class="wrap">
									<div class="title"><a href="">
										<span class="icon"><img src="<?php echo $appPic;?>/ico_clNews.png" alt="" /></span>
										<span class="text"><em>导航</em></span>
									</a></div>
									<div class="desc">
										<a href=""><span>即日起雪佛兰品牌联手国内500多家经销商推出 驾享新价值...【详情<?php echo $i;?>】</span></a>
									</div>
								</div>
							</li>
							<?php }?>
						</ul>
					</div>
					<div class="ui-slide-tabs">
						<span class="ui-slide-tab ui-slide-tabcur"></span>
						<span class="ui-slide-tab"></span>
						<span class="ui-slide-tab"></span>
					</div>
				</div>
			</div>
		</article>
	</div>
</body>
</html>