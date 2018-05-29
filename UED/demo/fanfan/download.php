<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection" content="telephone=no" />
<title>应用下载</title>
<?php include "_inc.php"; ?>
<link rel="stylesheet" href="<?php echo $staticPath;?>/sys/reset/phonecore.css" />
<link id="linkMain" rel="stylesheet" type="text/css" href="<?php echo $staticResPath;?>/fanfan/assets/css/fanfan.source.css">
<style type="text/css">


</style>
<script src="<?php echo $staticSysPath;?>/lib/zepto/zepto.js"></script>
<script src="<?php echo $staticSysPath;?>/icat/1.1.6/icat.js" data-main="<?php echo $staticResPath;?>/fanfan/assets/js/fanfan.source.js"></script>
</head>

<body>
<div id="page">
	<!-- <header id="header">
		<div class="nav">
			<span class="back"><a href="http://www.baidu.com/">&lt;</a></span>
			<span>资源详情</span>
			<span></span>
		</div>
	</header> -->
	
	<section class="download">
		<div class="inform">
			<div class="pic"><img src="<?php echo $staticResPath;?>/fanfan/pic/163.jpg" alt="" /></div>
			<div class="desc">
				<p class="note-title">温馨提示：<p>
				<p class="note-info">您所查看的咨询来自搜狐新闻，下载安装客户端后就能浏览更多精彩。</p>
			</div>

			<div class="extra"><a href="" class="btn">立即下载</a></div>
		</div>
		<div class="app-info">
			<p><span>名称：</span>搜狐新闻</p>
			<p><span>公司：</span>搜狐</p>
			<p><span>大小：</span>3.6MB</p>
		</div>
		
		<div class="intro">
			<h3>应用简介：</h3>
			<div class="desc">
				<p id="J_box_collapse">《迷失》是一款极为线性化的游戏，不让玩家在设定的两个关卡中做任何停留，由于你重温的是电视剧中的高潮部分!...</p>
				<p id="J_box_expand">《迷失》是一款极为线性化的游戏，不让玩家在设定的两个关卡中做任何停留，由于你重温的是电视剧中的高潮部分，所以这种设计也非常合理。同时《迷失》也是出于蓝而高于蓝，增加了大量的角色，漂亮的场景，以及神秘的色彩。</p>
				<div id="J_load_more" class="open-more"><span>展开<i>&gt;&gt;</i></span></div>
			</div>
		</div>
		
		<div class="pic-show">
			<div class="wrap clearfix border1px-t">
				<ul class="list">
					<?php for($i=0; $i<4; $i++){?>
					<li><img src="<?php echo $staticResPath;?>/fanfan/pic/screenshots.jpg" alt="" /></li>
					<?php }?>
				</ul>
			</div>
		</div>
	</section>
</div>
</body>
</html>