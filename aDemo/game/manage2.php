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
				<h2>管理</h2>
				<div class="back-home">
					<a href="index.php"></a>
				</div>
			</div>
		</header>
		
		<article class="ac manage">
			<div class="tab-box">
				<ul>
					<li><a href="manage.php">下载</a></li>
					<li class="selected"><a>已安装</a></li>
				</ul>
			</div>
			
			<div class="download">
				<div class="title">
					<div class="btn"><a class="cyan"><i>全部更新(4)</i></a></div>
				</div>
				<div class="item-list">
					<ul>
						<li>
							<a href="">
								<figure class="clearfix">
									<div class="pic"><img src="<?php echo $appPic;?>/pic_appImg.png" alt="" /></div>
									<div class="desc">
										<h3>Wind资讯(10M)</h3>
										<p>版本：1.0</p>
									</div>
								</figure>
							</a>
							<div class="btn">
								<a href="" class="cyan"><i>更新</i></a>
							</div>
						</li>
						<li>
							<a href="">
								<figure class="clearfix">
									<div class="pic"><img src="<?php echo $appPic;?>/pic_appImg.png" alt="" /></div>
									<div class="desc">
										<h3>Wind资讯(5M/10.5M)</h3>
										<p>版本：2.0.3</p>
									</div>
								</figure>
							</a>
							<div class="btn">
								<a href="" class="gray"><i>下载中</i></a>
							</div>
						</li>
						<li>
							<a href="">
								<figure class="clearfix">
									<div class="pic"><img src="<?php echo $appPic;?>/pic_appImg.png" alt="" /></div>
									<div class="desc">
										<h3>三国时代(10.5M)</h3>
										<p>版本：1.0</p>
									</div>
								</figure>
							</a>
							<div class="btn">
								<a href="" class="green"><i>安装</i></a>
							</div>
						</li>
						<li>
							<a href="">
								<figure class="clearfix">
									<div class="pic"><img src="<?php echo $appPic;?>/pic_appImg.png" alt="" /></div>
									<div class="desc">
										<h3>水果忍者(10.5M)</h3>
										<p>版本：2.0.3</p>
									</div>
								</figure>
							</a>
							<div class="btn">
								<a href="" class="orange"><i>卸载</i></a>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</article>
	</div>
	</body>
</html>