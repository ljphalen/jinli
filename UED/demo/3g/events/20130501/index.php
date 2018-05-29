<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>五一活动</title>
<!--指定当前应用模块名称 START-->
	<?php $moduleName = "events";?>
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
			<li>1. 活动主题：劳动光荣  中奖有礼</li>
			<li>2. 活动时间：4月29日——5月6日</li>
			<li>3. 活动奖品：小说券、购物优惠券、游戏礼包</li>
			<li>4. 活动规则：</li>
			<li>① 每个用户一天最多抽奖三次</li>
			<li>② 每天送出小说券XX张、购物优惠券XX张，游戏礼包XX个，送完即止</li>
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
