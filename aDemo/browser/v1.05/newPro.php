<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>金立门户</title>
	<?include '_inc.php'?>
</head>
<body>
	<div data-role="page" id="page" class="new-pro" data-theme="no">
		<header class="hd">
			<div class="logo">
				<h1 class="inner-title"><strong>新品上市</strong></h1>
			</div>
		</header>
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
						<span class="ui-slide-tab ui-slide-tabcur">1</span>
						<span class="ui-slide-tab">2</span>
						<span class="ui-slide-tab">3</span>
					</div>
					<div class="ui-slide-title">
						<div class="mask"></div>
						<span>GN205 买手机即送8G内存U盘一个</span>
						<span style="display:none;">GN205 买手机即送8G内存U盘二个</span>
						<span style="display:none;">GN205 买手机即送8G内存U盘三个</span>
					</div>
					<span class="ui-slide-prev"></span>
					<span class="ui-slide-next ui-slide-nextdis"></span>
				</div>
				<!--end slide-->
			</div>
			
			<div class="list pro-title">
				<ul>
					<?for($i=0; $i<5; $i++){?>
					<li>
						<a href="news.php">
							<span class="ico"><img src="<?=$appPic?>/pic_newPro.jpg" alt="" /></span>
							<em>买手机即送8G内存U盘一个</em>
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