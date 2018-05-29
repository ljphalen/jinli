<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>金立世界-售后服务</title>
	<?php include '_inc.php';?>
</head>
<body>
	<div id="page">
		<?php $title = '资源详情'; include '_sheader.php';?>
		
		<article class="ac resource-detail">
			<div class="inform">
				<div class="pic"><img src="<?php echo $appPic;?>/ico_phone.png" alt="" /></div>
				<div class="desc">
					<h2>应用名称：QQ游戏2012</h2>
					<p>公司：腾讯<br />大小：4.5MB</p>
				</div>
				<div class="extra"><a href="" class="btn">下载</a></div>
			</div>
			
			<div class="intro">
				<h3>简介：</h3>
				<div class="desc">
					<p id="J_box_collapse">《迷失》是一款极为线性化的游戏，不让玩家在设定的两个关卡中做任何停留，由于你重温的是电视剧中的高潮部分!...</p>
					<p id="J_box_expand">《迷失》是一款极为线性化的游戏，不让玩家在设定的两个关卡中做任何停留，由于你重温的是电视剧中的高潮部分，所以这种设计也非常合理。同时《迷失》也是出于蓝而高于蓝，增加了大量的角色，漂亮的场景，以及神秘的色彩。</p>
					<div id="J_load_more" class="open-more"><span>展开<i>&gt;&gt;</i></span></div>
				</div>
			</div>
			
			<div class="pic-show">
				<div class="wrap clearfix">
					<ul class="list">
						<?php for($i=0; $i<4; $i++){?><li><img src="<?php echo $appPic;?>/pic_screenshots.jpg" alt="" /></li><?php }?>
					</ul>
				</div>
			</div>
		</article>
		
	</div>
</body>
</html>