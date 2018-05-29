<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>小问卷大抽奖</title>
<!--指定当前应用模块名称 START-->
	<?php $moduleName = "events_0531";?>
<!--指定当前应用模块名称 END-->
<?php include '../../__inc.php';?>
</head>

<body>
<div class="wrapper">
	<section class="module events-detail">
		<div class="award-center-top ui-toolbar">
			<div class="ui-toolbar-wrap">
				<div class="ui-toolbar-left">
					<a class="ui-toolbar-backbtn" href="index.php">返回</a>
				</div>
				<div class="ui-toolbar-title events-detail-title"><span>奖励详情</span></div>
				<div class="ui-toolbar-right"></div>
			</div>
		</div>
		<div class="events-detail-content">
			<div class="events-detail-item">
				<div class="pic"><img src="<?php echo $appPic;?>/events-award-img01.jpg" /></div>
				<div class="txt">
					<!--<span>读书券</span>-->
					<!--<span>有效时间：2013年12月21日</span>-->
					<!--<span>使用代码：XXXXXX</span>-->
					<!--<span><a href="###" class="btn">马上使用</a></span>-->
				</div>
			</div>
			<div class="events-detail-tips">
				<p>使用方式：点击立即抽奖，转动轮盘，系统将随机选出幸运用户；未登陆的用户请用金立账号登陆后参与。</p>
			</div>
		</div>
	</section>
</div>
</body>
</html>
