<!DOCTYPE HTML>
<?php include '_cfg.php';?>
<html class="<?php echo $ucClass;?>">
<head>
	<meta charset="UTF-8">
	<title>游戏大厅客户端</title>
	<?php include '_inc.php';?>
	<script type="text/javascript">var token = '00safd';</script>
</head>
<body >
	<div  id="list-page">
		<section class="wrap">
			<div class="subject-list J_stratgyItem">
				<ul>
					<?php for($i=0; $i<7; $i++){?>
					<li>
						<a href="strategyDetail.php">
							<div class="intro">
									<?php if($i<3){ ?>
								<span class="pic">
									<img src="http://game.3gtest.gionee.com/attachs/game/subject/201303/112916.jpg" data-src="http://game.3gtest.gionee.com/attachs/game/subject/201303/112916.jpg" alt="">
								</span>
								<?php }?>
								<span class="title">
									<b>《新手篇-邀请仙友 轻松得珍贵羊宝宝》</b>
									<em>2013-12-04</em>
								</span>
							</div>
							<div class="content">
								<span class="desc">礼包非常丰富哦！我们为你准备了万圣节大餐，让你们在游戏的同时，也能感受到浓浓的万圣节日气氛！Come on，come on,Come on，come on,Come on，come on……</span>
							</div>
						</a>
					</li>
					<?php }?>
				</ul>
			</div>
		</section>
		<div class="loading J_loadMore"  style="display: none;" data-ajaxurl="json.php" data-hasnext="true" data-curpage="1">
			<!-- <span>加载更多</span> -->
			<span class="load"></span>
			<!-- <span class="bottom">到底了，去其它页面看看吧</span> -->
		</div>
	</div> 
</body>
</html>