<!DOCTYPE HTML>
<?php include '_cfg.php';?>
<html class="<?php echo $ucClass;?>">
<head>
	<meta charset="UTF-8">
	<title>游戏大厅客户端</title>
	<?php include '_inc.php';?>
	<script>
	var token = '15b1d100';
	</script>
</head>
<body>
	<div  id="page">
		<section class="wrap">
			<div class="category-list J_categoryItem">
				<ul>
					<?php for($i=0; $i<10; $i++){?>
					<li>
						<a  data-infpage="飞行射击,http://game.3gtest.gionee.com/client/category/detail/?id=43&amp;intersrc=CATEGORY43&amp;t_bi=_1177612710&amp;sp=1_1.4.8">
							<span class="pic">
								<img data-src="http://game.3gtest.gionee.com/attachs/game/Attribute/201309/171351.png" src="<?php echo $appPic;?>/blank.gif" alt="">
							</span>
							<span class="desc">
								<b>飞行射击</b>
								<em>游戏大厅、王者之剑、神偷老爸</em>
							</span>
						</a>
					</li>
					<?php }?>
				</ul>
			</div>
		</section>
		<div class="load-more J_loadMore" data-ajaxurl="json.php" data-hasnext="true" data-curpage="1"><span>加载更多</span></div>
	</div>
</body>
</html>