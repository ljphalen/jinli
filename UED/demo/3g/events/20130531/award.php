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
	<section class="module">
		<div class="award-center-top ui-toolbar">
			<div class="ui-toolbar-wrap">
				<div class="ui-toolbar-left">
					<a class="ui-toolbar-backbtn" href="index.php">返回</a>
				</div>
				<div class="ui-toolbar-title award-center-title"><span>奖励中心</span></div>
				<div class="ui-toolbar-right"></div>
			</div>
		</div>
		<div class="award-center-content">
			<ul class="award-center-list">
			<?php for($i = 0; $i < 10; $i++):?>
				<li>
					<div class="pic"><a href="detail.php"><img src="<?php echo $appPic;?>/events-award-img01.jpg" /></a></div>
					<div class="txt">
						<!--<span>读书券</span>-->
						<!--<span>有效时间：2013-12-21</span>-->
						<!--<span>使用代码：XXXXXXXXXXXX</span>-->
						<!--<span><span><a href="###" class="btn">马上使用</a></span>-->
					</div>
				</li>
			<?php endfor;?>
			</ul>
		</div>
	</section>
</div>
</body>
</html>
