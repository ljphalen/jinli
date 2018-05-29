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
			<div class="wrap">
				<h2>全部游戏</h2>
				<div class="back-home">
					<a href="index.php"></a>
				</div>
			</div>
		</header>
		
		<article class="ac">
			<div class="tab-box">
				<ul>
					<li><a href="">全部</a></li>
					<li class="selected"><a href="">益智</a></li>
					<li><a href="">动作</a></li>
				</ul>
			</div>
			<div class="item-list">
				<ul>
					<?php for($i=0; $i<2; $i++){?>
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
							<a href="" class="gray"><i>下载中</i></a>
							<span>10.5M</span>
						</div>
					</li>
					<?php }?>
				</ul>
			</div>
		</article>
	</div>
	</body>
</html>