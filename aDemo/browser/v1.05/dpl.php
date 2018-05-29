<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>DPL</title>
	<?include '_inc.php'?>
</head>
<body>
	<div data-role="page" id="page">
		<header class="hd">
			<div class="logo">
				<h1><a href=""><img src="<?=$appPic?>/logo.png" alt="" /></a></h1>
			</div>
		</header>
		
		<br /><!-- 华丽丽的分割线 -->
		
		<header class="hd">
			<div class="logo">
				<h1 class="inner-title">新品上市</h1>
			</div>
		</header>
		
		<br /><!-- 华丽丽的分割线 -->
		
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
						<span>xxx</span>
						<span style="display:none;">yyy</span>
						<span style="display:none;">zzz</span>
					</div>
					<span class="ui-slide-prev"></span>
					<span class="ui-slide-next ui-slide-nextdis"></span>
				</div>
				<!--end slide-->
			</div>
		</div>
	</div>
</body>
</html>