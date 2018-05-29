<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>金立门户</title>
	<?php include '_inc.php'?>
</head>
<body>
	<div data-role="page" id="page" class="news" data-theme="no">
		<?php include 'header.php'; ?>
		<h1 class="inner-title"><strong>新闻列表</strong></h1>
		<div class="ct">
			<div class="list news-title">
				<ul>
					<?php for($i=0; $i<5; $i++){?>
					<li>
						<a href="newsDetail.php">
							<i class="ico"><img src="<?=$appPic?>/pic_newsImg.jpg" alt="" /></i>
							<em>2012金立智能手机杯...<br /><span>2012年3月23日</span></em>
							<s><img src="<?=$appPic?>/jt.png" alt="" /></s>
						</a>
					</li>
					<?php }?>
				</ul>
			</div>
		</div>
	</div>
</body>
</html>