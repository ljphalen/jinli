<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection" content="telephone=no" />
<meta name="appRef" content="http://dev.assets.gionee.com/apps/3g">
<title>金立产品--应用下载</title>
<?php include "../_inc.php"; ?>
<link rel="stylesheet" href="<?php echo $staticPath;?>/sys/reset/mpcore.css" />
<link id="linkMain" rel="stylesheet" type="text/css" href="<?php echo $staticPath;?>/apps/3g/assets/css/3g.browser.source.css">
<script src="<?php echo $staticPath;?>/sys/lib/zepto/zepto.js"></script>
<script src="<?php echo $staticPath;?>/sys/icat/1.1.3/icat.js"></script>
<script src="<?php echo $staticPath;?>/apps/3g/assets/js/3g.browser.source.js"></script>
</head>

<body>
	<div id="page">
		<?php $navIndex=2; include '_bheader.php';?>
		
		<article class="ac">
			<div class="slide-pic banner">
				<div class="pic">
					<a href=""><img src="<?php echo $staticPath;?>/apps/3g/pic/pic_banner.jpg" alt="" /></a>
				</div>
			</div>
			
			<div class="download">
				<section class="">
					<div id="app-cont">
						<div id="download-app-list" class="app-list" data-ajaxUrl="json.php">
							<!-- 列表 -->
							<?php for($i = 0; $i < 10; $i++):?>
								<div class="app-item-list">
									<div class="app-item-left">
										<a href="###">
											<div class="app-item-img">
												<img src="<?php echo $staticPath;?>/3g/apps/3g/pic/caipiao.png" />
											</div>
											<div class="app-item-info">
												<div class="app-item-title">彩票</div>
												<div class="star" data-star="5"></div>
												<div class="app-item-text">一款购物软件</div>
											</div>
										</a>
									</div>
									<div class="app-item-bton button"><a href="###" class="btn gray">下载</a></div>
								</div>
							<?php endfor;?>
						</div>
					</div>
				</section>
			</div>
			<footer>
				<!-- 友情链接模块 -->
				<ul class="footer-link">
					<li><a href="###">产品服务</a></li>
					<li><a href="###">网站导航</a></li>
					<li><a href="###">在线应用</a></li>
					<li><a href="###">个人中心</a></li>
					<li><a href="###">签到</a></li>
				</ul>
			</footer>
		</article>
	</div>
	
<!-- 分类应用模板 -->
<script id="J_itemView" type="text/template">
	{each data.list}
	<div class="app-item-list">
		<div class="app-item-left">
			<a href="###">
				<div class="app-item-img">
					<img src="{$value.img}" />
				</div>
				<div class="app-item-info">
					<div class="app-item-title">{$value.title}</div>
					<div class="star star{$value.star}"></div>
					<div class="app-item-text">{$value.desc}</div>
				</div>
			</a>
		</div>
		<div class="app-item-bton button"><a href="{$value.link}" class="btn gray">下载</a></div>
	</div>
	{/each}
</script>
</body>
</html>