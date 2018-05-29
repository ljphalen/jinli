<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>金立门户</title>
	<?include '_inc.php'?>
</head>
<body>
	<div data-role="page" id="page" class="intro" data-theme="no">
		<?php include 'header.php'; ?>
		<div class="ct">
			<div class="banner">
				<!--begin slide -->
				<div class="mainfocus isTouch" id="mainfocus">
					<div class="ui-slide-scrollbox">
						<ul class="ui-slide-scroll clearfix" >
							<li class="ui-slide-item"><a href=""><img src="<?=$appPic?>/01.jpg" alt=""/></a></li>
							<li class="ui-slide-item"><a href=""><img src="<?=$appPic?>/02.jpg" alt=""/></a></li>
							<li class="ui-slide-item"><a href=""><img src="<?=$appPic?>/03.jpg" alt=""/></a></li>
						</ul>
					</div>
					<div class="ui-slide-tabs">
						<span class="ui-slide-tab ui-slide-tabcur"></span>
						<span class="ui-slide-tab"></span>
						<span class="ui-slide-tab"></span>
					</div>
					<span class="ui-slide-prev"></span>
					<span class="ui-slide-next ui-slide-nextdis"></span>
				</div>
				<!--end slide-->
			</div>
			
			<div class="list sub-link">
				<ul>
					<li>
						<a href="intro_about.php">
							<span class="ico"><img src="<?=$appPic?>/pic_intro1.png" alt="" /></span>
							<em>关于我们</em>
							<s><img src="<?=$appPic?>/jt.png" alt="" /></s>
						</a>
					</li>
					<li>
						<a href="intro_culture.php" data-ajax="false">
							<span class="ico"><img src="<?=$appPic?>/pic_intro2.png" alt="" /></span>
							<em>企业文化</em>
							<s><img src="<?=$appPic?>/jt.png" alt="" /></s>
						</a>
					</li>
					<li>
						<a href="intro_honor.php">
							<span class="ico"><img src="<?=$appPic?>/pic_intro3.png" alt="" /></span>
							<em>企业荣誉</em>
							<s><img src="<?=$appPic?>/jt.png" alt="" /></s>
						</a>
					</li>
					<li>
						<a href="intro_event.php">
							<span class="ico"><img src="<?=$appPic?>/pic_intro4.png" alt="" /></span>
							<em>金立大事</em>
							<s><img src="<?=$appPic?>/jt.png" alt="" /></s>
						</a>
					</li>
					<li>
						<a href="news.php">
							<span class="ico"><img src="<?=$appPic?>/pic_intro5.png" alt="" /></span>
							<em>新闻活动</em>
							<s><img src="<?=$appPic?>/jt.png" alt="" /></s>
						</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
</body>
</html>