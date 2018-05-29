<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>新闻列表</title>
<!--指定当前应用模块名称 START-->
	<?php $moduleName = "news";?>
<!--指定当前应用模块名称 END-->
<?php include '../__inc.php';?>
</head>

<body>
<section class="wrapper">
	<header>
		<div class="ui-toolbar">
			<div class="ui-toolbar-wrap">
				<div class="ui-toolbar-left"></div>
				<h2 class="ui-toolbar-title">娱乐</h2>
				<div class="ui-toolbar-right J_news_more">
					<span class="icon-label">更多</span>
					<span class="icon-arrow">
						<i class="up">▲</i>
						<i class="down">▼</i>
					</span>
				</div>
			</div>
		</div>
		<div class="news-menu-wrap news-menu-cate-wrap ishide">
			<nav class="news-menu">
				<ul class="news-menu-ul">
					<li class="item"><a href="###">国内</a></li>
					<li class="item"><a href="###">港台</a></li>
					<li class="item"><a href="###">美女</a></li>
					<li class="item"><a href="###">八卦</a></li>
					<li class="item"><a href="###">国内</a></li>
					<li class="item"><a href="###">港台</a></li>
					<li class="item"><a href="###">美女</a></li>
					<li class="item"><a href="###">八卦</a></li>
				</ul>
			</nav>
		</div>
	</header>
	<section id="content">
		<!-- your code in here -->
		<div class="news-list-box">
			<h3 class="news-list-head">明星八卦</h3>
			<ul class="ui-list">
				<li class="ui-list-img">
					<a href="#">
						<img src="<?php echo $appPic;?>/news-slide-01.jpg" />
						<span>湛江村民满月宴13娃食物中毒</span>
					</a>
				</li>
				<li class="ui-list-item"><a href="#">记者昨日从廉江市获悉，前日，廉江市唐鹏镇彭岸村委会大图村民参加一村民的满月</a></li>
				<li class="ui-list-item"><a href="#">列表内容二</a></li>
				<li class="ui-list-item"><a href="#">记者昨日从廉江市获悉，前日，廉江市唐鹏镇彭岸村委会大图村民参加一村民的满月</a></li>
				<li class="ui-list-item"><a href="#">列表内容四</a></li>
			</ul>
		</div>
		<div class="news-list-box">
			<h3 class="news-list-head">明星八卦</h3>
			<ul class="ui-list">
				<li class="ui-list-item"><a href="#">列表内容一</a></li>
				<li class="ui-list-item"><a href="#">列表内容二</a></li>
				<li class="ui-list-item"><a href="#">列表内容三</a></li>
				<li class="ui-list-item"><a href="#">列表内容四</a></li>
			</ul>
		</div>
		<div class="news-list-box">
			<h3 class="news-list-head">明星八卦</h3>
			<ul class="ui-list">
				<li class="ui-list-item"><a href="#">列表内容一</a></li>
				<li class="ui-list-item"><a href="#">列表内容二</a></li>
				<li class="ui-list-item"><a href="#">列表内容三</a></li>
				<li class="ui-list-item"><a href="#">列表内容四</a></li>
			</ul>
		</div>
	</section>
	<a href="javascript:window.scrollTo(0,1);" class="ui-gotop"></a>
</section>
</body>
</html>
