<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>金立游戏DPL</title>
	<?php include '_inc.php';?>
</head>
<body>
	<div id="page">
		<header class="hd">
			<div class="wrap">
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
			<div class="wrap">
				<h2>添加游戏</h2>
				<div class="back-home">
					<a href="index.php"></a>
				</div>
			</div>
		</header>
		
		<article class="ac">
			<div class="speed-dial">
				<ul>
					<li>
						<a href="">
							<span class="pic"><img src="<?php echo $appPic;?>/pic_appImg.png" alt="" /></span>
							<em>会说话的Tom猫</em>
							<span class="icon"><img src="<?php echo $appPic;?>/ico_tick.png" alt="" /></span>
							<input type="hidden" name="" />
						</a>
					</li>
					<li class="checked">
						<a href="">
							<span class="pic"><img src="<?php echo $appPic;?>/pic_appImg.png" alt="" /></span>
							<em>Tom猫</em>
							<span class="icon"><img src="<?php echo $appPic;?>/ico_tick.png" alt="" /></span>
							<input type="hidden" name="" />
						</a>
					</li>
				</ul>
			</div>
			
			<div class="item-list">
				<ul>
					<li>
						<a href="">
							<figure class="clearfix">
								<div class="pic"><img src="<?php echo $appPic;?>/pic_appImg.png" alt="" /></div>
								<div class="desc">
									<h3>Wind资讯</h3>
									<p>Wind资讯股票专家...</p>
								</div>
							</figure>
						</a>
						<div class="btn">
							<a href="" class="green"><i>安装</i></a>
							<span>10.5M</span>
						</div>
					</li>
					<li>
						<a href="">
							<figure class="clearfix">
								<div class="pic"><img src="<?php echo $appPic;?>/pic_appImg.png" alt="" /></div>
								<div class="desc">
									<h3>Wind资讯</h3>
									<p>Wind资讯股票专家...</p>
								</div>
							</figure>
						</a>
						<div class="btn">
							<a href="" class="orange"><i>下载</i></a>
							<span>10.5M</span>
						</div>
					</li>
				</ul>
			</div>
			
			<style type="text/css">
				.btn-wrap{margin:10px 0; font-size:20px;}
				.btn-wrap .btn{margin:0 10px;}
				.btn-wrap div{float:left;}
				.btn-wrap span{display:inline-block;}
			</style>
			<div class="btn-wrap clearfix">
				<span class="btn" style="width:120px;"><a href="" class="cyan"><i>全部下载</i></a></span>
				<div class="btn" style="width:80px;"><a href="" class="gray"><i>等待中</i></a></div>
				<div class="btn" style="width:80px;"><a href="" class="croci"><i>暂停</i></a></div>
			</div>
			
			<div class="tab-box">
				<ul>
					<li class="selected"><a href="">下载</a></li>
					<li><a href="">已安装</a></li>
				</ul>
			</div>
			
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
		</article>
		
		<footer class="ft"></footer>
	</div>
</body>
</html>