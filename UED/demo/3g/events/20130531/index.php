<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>小问卷大抽奖活动</title>
<!--指定当前应用模块名称 START-->
	<?php $moduleName = "events_0531";?>
<!--指定当前应用模块名称 END-->
<?php include '../../__inc.php';?>
</head>

<body data-pagerole="body">
<div class="wrapper">
	<!-- 抽奖区域 START -->
	<section class="module">
		<div class="lottery-wrap">
			<div class="lottery-bg J_award_btn">立即抽奖</div>
			<div class="lottery-pointer"></div>
		</div>
	</section>
	<!-- 抽奖区域 END -->

	<!-- 中奖提示区域 START -->
	<section class="module J_winning_tips ishide">
		<div class="winning-tips-wrap"></div>
	</section>
	<!-- 中奖提示区域 END -->

	<!-- 活动信息区域 START -->
	<section class="module events-info">
		<ol>
			<li>1. 活动主题：小问卷 大抽奖</li>
			<li>2. 活动时间：用户每次做完问卷可进行抽奖</li>
			<li>3. 活动奖品：1Q币、2Q币、5Q币、20Q币</li>
			<li>4. 活动规则：</li>
			<li class="padd">① 做完一份问卷获得三次抽奖机会（该问卷推广期间只有这三次抽奖机会）</li>
			<li class="padd">② 点击立即抽奖，转动轮盘，系统将随机选出幸运用户；未登陆的用户请用金立账号登陆后参与</li>
			<li class="padd">③ 每天送出1000Q币，送完即止</li>
			<li>5. 活动交流：请加群 – 274289481</li>
		</ol>
	</section>
	<!-- 活动信息区域 END -->

	<!-- 奖励展示区域 START -->
	<section class="module events-show">
		<div class="events-show-title">
			<h2>奖品展示</h2>
			<a href="award.php">进入奖励中心&gt;&gt;</a>
		</div>
		<div class="events-show-pic">
			<a href="detail.php"><img src="<?php echo $appPic;?>/events-award-img01.jpg" /><!--<span>阅读券</span>--></a>
			<a href="detail.php"><img src="<?php echo $appPic;?>/events-award-img03.jpg" /><!--<span>游戏礼包</span>--></a>
			<a href="detail.php"><img src="<?php echo $appPic;?>/events-award-img02.jpg" /><!--<span>购物券</span>--></a>
		</div>
	</section>
	<!-- 奖励展示区域 END -->
</div>
</body>
</html>
