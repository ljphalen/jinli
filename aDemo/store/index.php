<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>金立游戏</title>
	<?php include '_inc.php'?>
</head>
<body>
	<div id="page" class="home">
		<!-- <div class="banner scrollpic">
			<div class="pic-wrap">
				<ul>
					<li><a href=""><img src="" alt="" /></a></li>
				</ul>
			</div>
			<span class="handle"></span>
			<div class="mask"></div>
		</div> -->
		<div class="mainfocus isTouch" id="mainfocus">
			<div class="ui-slide-scrollbox">
				<ul class="ui-slide-scroll clearfix">
					<li class="ui-slide-item"><a href=""><img src="http://gou.gionee.com/attachs/ad/201208/143912.jpg" alt="乐淘商务皮鞋"></a></li>
					<li class="ui-slide-item"><a href=""><img src="http://gou.gionee.com/attachs/ad/201208/135435.jpg" alt="欢购七夕活动"></a></li>
					<li class="ui-slide-item"><a href=""><img src="http://gou.gionee.com/attachs/ad/201208/160729.jpg" alt="晒单拿话费"></a></li>
					<li class="ui-slide-item"><a href=""><img src="http://gou.gionee.com/attachs/ad/201208/093400.jpg" alt="欢购优惠券"></a></li>
					<li class="ui-slide-item"><a href=""><img src="http://gou.gionee.com/attachs/ad/201208/152022.jpg" alt="中彩汇"></a></li>
				</ul>
			</div>
			<div class="mask"></div>
			<div class="ui-slide-tabs">
				<span class="ui-slide-tab ui-slide-tabcur"></span>
				<span class="ui-slide-tab"></span>
				<span class="ui-slide-tab"></span>
				<span class="ui-slide-tab"></span>
				<span class="ui-slide-tab"></span>
			</div>
			<span class="ui-slide-prev"></span>
			<span class="ui-slide-next"></span>
		</div>
		
		<div class="nav-list">
			<ul>
				<?php for($i=0; $i<6; $i++){?>
				<li>
					<a href="detail.php" class="intro clearfix">
						<div class="pic"><img src="<?php echo $appPic?>/pic_storeApp.jpg" alt="" /></div>
						<div class="desc">
							<h2><strong>Wind 资讯</strong></h2>
							<p><span>Wind 资讯股票专家...</span></p>
						</div>
					</a>
					<a href="" class="download"><span>下载</span></a>
				</li>
				<?php }?>
			</ul>
			<div id="J_loadMore" class="more"><em dt-ajaxUrl=""><span>点击加载更多</span></em></div>
		</div>
		
	</div>
</body>
</html>