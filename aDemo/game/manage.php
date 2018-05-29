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
					<li class="selected"><a>下载</a></li>
					<li><a href="manage2.php">已安装</a></li>
				</ul>
			</div>
			
			<div class="download">
				<div class="title">
					<strong>下载中(1)</strong>
				</div>
				<div class="item-list">
					<ul>
						<li>
							<a href="">
								<figure class="clearfix">
									<div class="pic"><img src="<?php echo $appPic;?>/pic_appImg.png" alt="" /></div>
									<div class="desc">
										<h3>Wind资讯(10M)</h3>
										<p>54%</p>
									</div>
								</figure>
							</a>
							<div class="btn">
								<a href="" class="croci"><i>暂停</i></a>
							</div>
						</li>
						<li>
							<a href="">
								<figure class="clearfix">
									<div class="pic"><img src="<?php echo $appPic;?>/pic_appImg.png" alt="" /></div>
									<div class="desc">
										<h3>Wind资讯(5M/10.5M)</h3>
										<p>51%</p>
									</div>
								</figure>
							</a>
							<div class="btn">
								<a href="" class="orange"><i>继续</i></a>
							</div>
						</li>
					</ul>
				</div>
				
				<div class="title">
					<strong>已下载的安装包(2)</strong>
				</div>
				<div class="item-list">
					<ul>
						<?php for($i=0; $i<2; $i++){?>
						<li>
							<a href="">
								<figure class="clearfix">
									<div class="pic"><img src="<?php echo $appPic;?>/pic_appImg.png" alt="" /></div>
									<div class="desc">
										<h3>Wind资讯(10.5M)</h3>
										<p>100%</p>
									</div>
								</figure>
							</a>
							<div class="btn">
								<a href="" class="green"><i>安装</i></a>
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