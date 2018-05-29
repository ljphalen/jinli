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
		<section class="pnavigator">
			<section class="top-nav">
				<ul>
					<li>
						<a href="###">
							<div class="pic"><img src="<?php echo $appRef;?>/pic/icon-card.png" alt="" /></div>
							<div class="txt">话费快充</div>
						</a>
					</li>
					<li class="hot">
						<a href="###">
							<div class="pic"><img src="<?php echo $appRef;?>/pic/icon-caipiao.png" alt="" /></div>
							<div class="txt">手机购彩</div>
						</a>
					</li>
					<li>
						<a href="###">
							<div class="pic"><img src="<?php echo $appRef;?>/pic/icon-move.png" alt="" /></div>
							<div class="txt">电影票</div>
						</a>
					</li>
					<li>
						<a href="###">
							<div class="pic"><img src="<?php echo $appRef;?>/pic/icon-hotel.png" alt="" /></div>
							<div class="txt">酒店预订</div>
						</a>
					</li>
				</ul>
			</section>
			<!-- 图片滚动展示模块 -->
			<div class="mod-slide mindex">
				<div class="wrap">
					<div class="pic">
						<?php foreach($data as $val):?>
						<a href="###"><img src="<?php echo $val['img'];?>" alt="" /></a>
						<?php endforeach;?>
					</div>
				</div>
				<div class="mask"></div>
				<div class="handle">
					<span class="on"></span>
					<span></span>
					<span></span>
					<span></span>
					<span></span>
				</div>
			</div>
			<!-- /图片滚动展示模块 -->
			<!-- 首页主题模块 -->
			<section class="btm-list">
				<h3>衣服</h3>
				<ul class="mod-item-list">
					<li>
						<a href="###">
							<div class="pic"><img src="<?php echo $appRef;?>/pic/navigator-pic-01.jpg" alt="" /></div>
							<div class="txt">运动</div>
						</a>
					</li>
					<li>
						<a href="###">
							<div class="pic"><img src="<?php echo $appRef;?>/pic/navigator-pic-02.jpg" alt="" /></div>
							<div class="txt">休闲</div>
						</a>
					</li>
					<li>
						<a href="###">
							<div class="pic"><img src="<?php echo $appRef;?>/pic/navigator-pic-03.jpg" alt="" /></div>
							<div class="txt">裙子</div>
						</a>
					</li>
					<li>
						<a href="###">
							<div class="pic"><img src="<?php echo $appRef;?>/pic/navigator-pic-04.jpg" alt="" /></div>
							<div class="txt">秋装</div>
						</a>
					</li>
					<li>
						<a href="###">
							<div class="pic"><img src="<?php echo $appRef;?>/pic/navigator-pic-01.jpg" alt="" /></div>
							<div class="txt">生活</div>
						</a>
					</li>
					<li>
						<a href="###">
							<div class="pic"><img src="<?php echo $appRef;?>/pic/navigator-pic-03.jpg" alt="" /></div>
							<div class="txt">换购</div>
						</a>
					</li>
				</ul>
			</section>

<section class="btm-list">
				<h3>鞋子</h3>
				<ul class="mod-item-list">
					<li>
						<a href="###">
							<div class="pic"><img src="<?php echo $appRef;?>/pic/navigator-pic-01.jpg" alt="" /></div>
							<div class="txt">运动</div>
						</a>
					</li>
				</ul>
			</section>
			<!-- /首页主题模块 -->
		</section>
	</section>
	<!--  /主体内容 -->
	<?php include '_footer.php';?>
</section>
</body>
</html>