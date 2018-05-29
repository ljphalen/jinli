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
				<h2>网络游戏专题</h2>
				<div class="back-home">
					<a href="index.php"></a>
				</div>
			</div>
		</header>
		
		<article class="ac">
			<div class="game-intro">
				<div class="pic"><a href=""><img src="<?php echo $appPic;?>/pic_topic.jpg" alt="" /></a></div>
				<div class="desc">
					<p>《迷失》是一款极为线性化的游戏，不让玩家在设定的两个关卡中做任何停留，由于你重温的是电视剧中的高潮部分，所以这种设计也非常合理。同时《迷失》也是出于蓝而高于蓝，增加了大量的角色，漂亮的场景，以及神秘的色彩。</p>
				</div>
			</div>
			<div class="download">
				<div class="title">
					<strong>共12款游戏</strong>
					<div class="btn"><a class="cyan"><i>全部下载</i></a></div>
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
						<?php }?>
					</ul>
				</div>
			</div>
		</article>
	</div>
	</body>
</html>