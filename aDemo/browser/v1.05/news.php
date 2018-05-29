<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>金立门户</title>
	<?include '_inc.php'?>
</head>
<body>
	<div data-role="page" id="page" class="news" data-theme="no">
		<header class="hd">
			<div class="logo">
				<h1 class="inner-title"><strong>公司头条</strong></h1>
				<p><a href="">2012金立智能手机杯中国围甲联赛杭州开幕</a></p>
			</div>
			<div class="top-line">
				<p>4月27日，2012“金立智能手机杯”中国围棋甲级联赛开幕式在中国棋院杭州分院举行。中国棋院杭州分院院长王国平，国家体育总局棋牌运动管理中心主任刘思明，深圳金立通信设备有限公司董事长刘立荣，中国围棋协会主席王汝南等领导莅临了开幕式晚宴。...<a href="">【详情】</a></p>
			</div>
		</header>
		<div class="ct">
			<div class="list news-title">
				<ul>
					<?for($i=0; $i<5; $i++){?>
					<li>
						<a href="newsDetail.php">
							<i class="ico"><img src="<?=$appPic?>/pic_newsImg.jpg" alt="" /></i>
							<em>2012金立智能手机杯...<br /><span>2012年3月23日</span></em>
							<s><img src="<?=$appPic?>/jt.png" alt="" /></s>
						</a>
					</li>
					<?}?>
				</ul>
			</div>
		</div>
	</div>
</body>
</html>