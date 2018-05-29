<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>金立购—触屏版</title>
<?php include '_inc.php';?>
</head>

<body>
<?php
$data = array(
	array('img' => $appRef.'/pic/index-slide-pic.jpg','url' => '###'),
	array('img' => $appRef.'/pic/index-slide-pic.jpg','url' => '###'),
	array('img' => $appRef.'/pic/index-slide-pic.jpg','url' => '###'),
	array('img' => $appRef.'/pic/index-slide-pic.jpg','url' => '###'),
	array('img' => $appRef.'/pic/index-slide-pic.jpg','url' => '###')
);
?>
<div id="">
	<?php include '_header.php';?>
	<!-- 主体内容 -->
	<section id="content">
		<!-- 图片滚动展示模块 -->
		<div class="mod-slide mindex">
			<div class="wrap">
				<div class="pic">
					<?php foreach($data as $val):?>
					<a href="###"><img src="<?php echo $val['img'];?>" alt="" /></a>
					<?php endforeach;?>
				</div>
			</div>
			<!--<div class="mask"></div>-->
			<div class="handle">
				<span class="on"></span>
				<span></span>
				<span></span>
				<span></span>
				<span></span>
			</div>

			<div class="mod-search">
				<div class="short">
					<a href="search.php"><img src="<?php echo $appRef;?>/pic/search-index.png" /></a>
				</div>
			</div>
		</div>
		<!-- /图片滚动展示模块 -->

		<!-- 倒计时模块 -->
		<div class="mod-countdown">
			<div class="wrap">
				<div class="line"></div>
				<div class="title J_countDown" data-endTime="1351662800" data-currentTime="1351612800">剩余时间：<span class="hours">23</span>小时<span class="minutes">56</span>分钟<span class="seconds">16</span>秒<span class="over">结束</span></div>
				<div class="main">
					<div class="pic"><img src="<?php echo $appRef?>/pic/goods-list-pic.jpg" alt="" /></div>
					<div class="txt">
						<p>正品美尔挺 塑型美腿袜 瘦腿袜680D连裤袜丝袜子</p>
						<p><span class="number red">18.00</span>元 可用<span class="number red">17</span>银币抵现</p>
						<p class="">
							已有76人购买
							<span class="button"><a href="product-detail.php" class="btn orange-arrow"><em>查看详情</em></a></span>
						</p>

						
					</div>
				</div>
			</div>
		</div>
		<!-- /倒计时模块 -->

		<!-- 首页主题模块 -->
		<div class="showcase">
			<div class="line"></div>
			<ul class="mod-item-list">
				<li>
					<a href="theme.php">
						<div class="pic"><img src="<?php echo $appRef;?>/pic/theme-pic-01.jpg" alt="" /></div>
						<div class="txt">主题汇</div>
					</a>
				</li>
				<li>
					<a href="product-category-detail.php">
						<div class="pic"><img src="<?php echo $appRef;?>/pic/theme-pic-02.jpg" alt="" /></div>
						<div class="txt">女人街</div>
					</a>
				</li>
				<li>
					<a href="product-category-detail.php">
						<div class="pic"><img src="<?php echo $appRef;?>/pic/theme-pic-03.jpg" alt="" /></div>
						<div class="txt">男人邦</div>
					</a>
				</li>
				<li>
					<a href="product-category-detail.php">
						<div class="pic"><img src="<?php echo $appRef;?>/pic/theme-pic-04.jpg" alt="" /></div>
						<div class="txt">数码</div>
					</a>
				</li>
				<li>
					<a href="product-category-detail.php">
						<div class="pic"><img src="<?php echo $appRef;?>/pic/theme-pic-05.jpg" alt="" /></div>
						<div class="txt">家居生活</div>
					</a>
				</li>
				<li>
					<a href="product-score.php">
						<div class="pic"><img src="<?php echo $appRef;?>/pic/theme-pic-06.jpg" alt="" /></div>
						<div class="txt">积分换购</div>
					</a>
				</li>
			</ul>
		</div>
		<!-- /首页主题模块 -->
	</section>
	<!--  /主体内容 -->
	<?php include '_footer.php';?>
</div>
</body>
</html>