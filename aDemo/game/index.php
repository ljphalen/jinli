<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>金立游戏—客户端</title>
	<?php include '_inc.php';?>
</head>
<body>
	<div id="page">
		<header class="hd">
			<div class="wrap no-shadow">
				<h1><img src="<?php echo $appPic;?>/logo.png" alt="" /></h1>
				<div class="manage-links">
					<a href="allgames.php">
						<img src="<?php echo $appPic;?>/ico_head1.png" alt="" />
						<span>全部游戏</span>
					</a>
					<a href="manage.php">
						<img src="<?php echo $appPic;?>/ico_head2.png" alt="" />
						<span>管理</span>
					</a>
				</div>
			</div>
		</header>
		
		<article class="ac home-wrap">
			<div class="games-desk">
				<div class="clearfix">
					<ul>
						<?php for($i=0; $i<4; $i++){?>
						<li>
							<a href="">
								<div class="pic">
									<span><img src="<?php echo $appPic;?>/pic_appImg.png" alt="" /></span>
								</div>
								<div class="title"><span>愤怒的小鸟</span></div>
							</a>
						</li>
						<?php }?>
					</ul>
				</div>
				<div class="clearfix">
					<ul>
						<?php for($i=0; $i<3; $i++){?>
						<li>
							<a href="">
								<div class="pic">
									<span><img src="<?php echo $appPic;?>/pic_appImg.png" alt="" /></span>
								</div>
								<div class="title"><span>愤怒的小鸟</span></div>
							</a>
						</li>
						<?php }?>
						<li>
							<a href="addgame.php">
								<div class="pic">
									<span><img src="<?php echo $appPic;?>/ico_addGame.png" alt="" /></span>
								</div>
								<div class="title"><span>添加游戏</span></div>
							</a>
						</li>
					</ul>
				</div>
			</div>
			
			<section class="games-link">
				<div class="banner">
					<div class="slide-pic">
						<a href=""><img src="<?php echo $appPic;?>/pic_banner1.jpg" alt="" /></a>
						<a href=""><img src="<?php echo $appPic;?>/pic_banner2.jpg" alt="" /></a>
						<a href=""><img src="<?php echo $appPic;?>/pic_banner3.jpg" alt="" /></a>
					</div>
					<div class="handle">
						<span class="on"></span>
						<span></span>
						<span></span>
					</div>
				</div>
				
				<div class="icon-ad clearfix">
					<div class="icons">
						<ul>
							<?php for($i=0; $i<6; $i++){?>
							<li><a href=""><img src="<?php echo $appPic;?>/pic_hmAppImg.png" alt="" /></a></li>
							<?php }?>
						</ul>
					</div>
					<div class="ads">
						<?php for($i=0; $i<3; $i++){?>
						<a href="gametopic.php"><img src="<?php echo $appPic;?>/pic_adImg.jpg" alt="" /></a>
						<?php }?>
					</div>
				</div>
				
				<div class="item-list clearfix">
					<ul>
						<?php for($i=0; $i<4; $i++){?>
						<li>
							<a href="gamedetail.php">
								<figure class="clearfix">
									<div class="pic"><img src="<?php echo $appPic;?>/pic_appImg.png" alt="" /></div>
									<div class="desc">
										<h3>水果忍者</h3>
										<p>乐逗游戏</p>
									</div>
								</figure>
								<div class="mask"></div>
							</a>
						</li>
						<?php }?>
					</ul>
				</div>
			</section>
		</article>
		
		<footer class="ft"></footer>
	</div>
</body>
</html>