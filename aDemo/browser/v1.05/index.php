<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>金立门户</title>
	<?include '_inc.php'?>
</head>
<body dt-cr="test">
	<div data-role="page" id="page" class="home" data-theme="no">
		<header class="hd">
			<div class="logo">
				<h1><a href=""><img src="<?=$appPic?>/logo.png" alt="" /></a></h1>
			</div>
			<nav>
				<ul class="oz">
					<li><a href="intro.php"><img src="<?=$appPic?>/nav01.png" alt="公司动态"><span>公司介绍</span></a></li>
					<li><a href="pros.php" class="selected"><img src="<?=$appPic?>/nav02.png" alt="产品世界"><span>产品世界</span></a></li><!--newPro.php-->
					<li><a href="service.php"><img src="<?=$appPic?>/nav03.png" alt="客户服务"><span>客户服务</span></a></li>
				</ul>
			</nav>
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
					<span class="ui-slide-prev"></span>
					<span class="ui-slide-next ui-slide-nextdis"></span>
				</div>
				<!--end slide-->
			</div>
			
			<div class="list sub-nav">
				<ul>
					<li>
						<a href="http://m.sohu.com/c/2/?v=3"><!--news.php-->
							<span class="ico"><img src="<?=$appPic?>/ico_subnav1.png" alt="" /></span>
							<em>新闻资讯</em>
							<s><img src="<?=$appPic?>/jt.png" alt="" /></s>
						</a>
					</li>
					<li>
						<a href="" data-ajax="false">
							<span class="ico"><img src="<?=$appPic?>/ico_subnav2.png" alt="" /></span>
							<em>手机购物</em>
							<s><img src="<?=$appPic?>/jt.png" alt="" /></s>
						</a>
					</li>
					<li>
						<a href="http://m.anzhuoapk.com/index.jsp?fr=jinli&i=browser&m=0&s=0">
							<span class="ico"><img src="<?=$appPic?>/ico_subnav3.png" alt="" /></span>
							<em>软件下载</em>
							<s><img src="<?=$appPic?>/jt.png" alt="" /></s>
						</a>
					</li>
					<li>
						<a href="daohang.php">
							<span class="ico"><img src="<?=$appPic?>/ico_subnav4.png" alt="" /></span>
							<em>网站导航</em>
							<s><img src="<?=$appPic?>/jt.png" alt="" /></s>
						</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
</body>
</html>