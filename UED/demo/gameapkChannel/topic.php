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
	<div id="page" >
		<section class="wrap">
			<div class="subject-list J_subjectItem">
				<ul>
					<?php for($i=0; $i<10; $i++){?>
					<li>
						<a data-infpage="中国的风,http://game.3gtest.gionee.com/client/subject/detail/?id=11&amp;intersrc=SUBJECT11&amp;t_bi=_1177612710">
							<div class="intro">
								<span class="pic">
									<img data-src="http://game.3gtest.gionee.com/attachs/game/subject/201303/195551.jpg" src="<?php echo $appPic;?>/blank.gif" alt="">
								</span>
								<span class="title">
									<b>中国的风</b>
									<em>2013-02-26</em>
								</span>
							</div>
							<div class="content">
								<span class="desc">
									南都讯 惠州作为全省的试点市，开始逐步推广使用预付费电表。以前是用电后按月缴费，此次更换新电表则要先往卡内充值买电后再用电。
								</span>
							</div>
						</a>
					</li>
					<?php }?>
				</ul>
			</div>
		</section>
		<div class="load-more J_loadMore" data-ajaxurl="json.php" data-hasnext="1" data-curpage="1">
				<span>加载更多</span>
		</div>
	</div>
</body>
</html>