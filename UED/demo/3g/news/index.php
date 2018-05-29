<!DOCTYPE HTML>
<html manifest="cache.appcache">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection" content="telephone=no" />
<title>新闻</title>
<?php include "../_inc.php"; ?>
<link id="linkMain" rel="stylesheet" type="text/css" href="<?php echo $staticPath;?>/apps/3g/news/assets/css/3g.news.source.css">
<script src="<?php echo $staticPath;?>/sys/icat/1.1.5/icat.js"></script>
<script src="<?php echo $staticPath;?>/apps/3g/news/assets/js/news.source.js"></script>
</head>
<body>
<div class="wrapper">
	<header>
		<div class="ui-toolbar">
			<div class="ui-toolbar-wrap">
				<div class="ui-toolbar-left"></div>
				<h2 class="ui-toolbar-title">新闻</h2>
				<div class="ui-toolbar-right news-home-weather">
					<?php if(!$isLocal){?>
						<script type="text/javascript" src="http://ext.weather.com.cn/23624.js" async="true"></script>
					<?php }?>
				</div>
			</div>
		</div>
		<!--<div class="ui-toolbar-refresh" id="J_toolbar_refresh"><em>刷新</em></div>-->
		<div class="news-menu-wrap">
			<nav class="news-menu">
				<ul class="news-menu-ul">
					<li class="item"><a href="###" onclick="_hmt.push(['_trackEvent','GNnews数据','GNnews-顶部菜单点击','GNnews顶部菜单-新浪'])">新浪</a></li>
					<li class="item"><a href="###" class="sel">网易</a></li>
					<li class="item"><a href="###">新浪</a></li>
					<li class="item"><a href="###">新浪</a></li>
					<li class="item"><a href="###">新浪</a></li>
					<li class="item"><a href="###">新浪</a></li>
					<li class="item"><a href="###">新浪</a></li>
					<li class="item"><a href="###">网易</a></li>
				</ul>
			</nav>
		</div>
	</header>
	<div class="module">
		<section class="news-hot">
			<div class="news-hot-title" data-remote="<?php echo $webroot.'/3g/api/news.json';?>"><a href="###">山东省副省长黄胜一被派无期</a></div>
			<div class="news-hot-topic">
				<a href="###">传有46名情妇</a>
				<i class="vhr">|</i>
				<a href="">反腐相关专题</a></div>
		</section>
	</div>
	<!-- Banner轮播区域 -->
	<div class="module">
		<div class="ui-slider showpic" id="J_full_slider" data-ajaxUrl="<?php echo $webroot.'/3g/api/ad.json';?>"></div>
	</div>
	<!-- 热门搜索区域 -->
	<div class="module">
		<section class="ui-search">
			<form name="easou" method="get" action="http://ad3.easou.com:8080/j10ad/ea2.jsp">
				<input type="hidden" name="cid" value="bkcn3510_47908_D_1" />
				<div class="ui-search-wrap">
					<input type="hidden" id="api-baidu-hots" value="<?php echo $webroot;?>/3g/api/api-baidu-hots.json" />
					<input type="search" name="key" class="inp-search" autocomplete="off" value="免费小说" />
					<input type="submit" name="channel_11" class="btn-search" value="搜索" />
				</div>
				<div class="in-hot-world">
					<a href="#"><i class="num">1</i>红会贪官</a>
					<a href="#"><i class="num">2</i>庐山地震 </a>
					<a href="#"><i class="num">3</i>中国大妈</a>
					<!-- <a href="#"><i class="num">4</i>中国最强音</a> -->
				</div>
			</form>
		</section>
	</div>

	<section class="module news-wrap">
	<input type="hidden" value="<?php echo $webroot.'/3g/api/news2.json?ids=1222312,2,3,3';?>" id="initNewsData" />
	<?php for($i = 0; $i < 3; $i++):?>
		<section class="block">
			<div class="news-nav">
				<ul class="news-tab" id="J_news_tab<?php echo $i;?>">
				<?php for($t = 0; $t < 3; $t++):?>
					<li <?php if ($t != 0) {?>data-remote="<?php echo $webroot.'/3g/api/news.json';?>" <?php } ?> data-part="<?php echo $i;?>" data-num="<?php echo $t;?>" class="<?php if($t == 0) echo 'sel';?>"><span>时事</span></li>
				<?php endfor;?>
				</ul>
			</div>
			<div class="news-content">
				<div class="news-tab-item" id="J_news_tab_item<?php echo $i;?>">
				<?php for($j = 0; $j < 3; $j++):?>
					<ul class="news-list <?php if($j != 0) echo 'ishide';?>">
						<li class="item"><a href="###">图片故事：胶囊公寓里的青春<?php echo $j;?></a></li>
						<li class="item"><a href="###">罗姆尼的中国账本</a></li>
						<li class="item"><a href="###">月光女神将放歌太空</a></li>
						<li class="item"><a href="###">我的获奖是文学的胜利</a></li>
						<li class="item"><a href="###">退伍军人杀微信女友   沿路抛尸退伍军人杀微信女友   沿路抛尸</a></li>
						<li class="item list-more">
							<a href="#">更多资讯</a>
						</li>
					</ul>
				<?php endfor;?>
				</div>
			</div>
		</section>
	<?php if($i == 0):?>
		<section class="gn-ad">
			<ul>
				<li><a onclick="_hmt.push(['_trackEvent','GNnews数据','GNnews-通栏广告点击','GNnews-通栏广告-购物(左1)'])" href="###"><img src="<?php echo $staticPath;?>/apps/3g/news/pic/t01.png" /></a></li>
				<li><a onclick="_hmt.push(['_trackEvent','GNnews数据','GNnews-通栏广告点击','GNnews-通栏广告-游戏(右1)'])" href="###"><img src="<?php echo $staticPath;?>/apps/3g/news/pic/t02.png" /></a></li>
			</ul>
		</section>
	<?php endif;?>
	<?php endfor;?>
		<section class="news-cate">
			<div class="news-cate-title">更多分类</div>
			<ul class="news-cate-link">
				<li><a onclick="_hmt.push(['_trackEvent','GNnews数据','GNnews-更多分类点击','GNnews更多分类-时尚'])" href="###">时尚</a></li>
				<li><a href="###">美图</a></li>
				<li><a href="###">XXX</a></li>
				<li><a href="###">时尚</a></li>
				<li><a href="###">美图</a></li>
				<li><a href="###">XXX</a></li>
				<li><a href="###">美图</a></li>
			</ul>
		</section>
	</div>
	<footer id="footer">
		<div class="copyright">
			<span>粤文市审[2012]196号</span>
			<br>
			<span>增值电信许可证：<a href="" target="_blank">粤B2-20120350</a></span>
			<br>
			<span>网络文化经营许可证：<a href="/attachs/SKMBT_C35107083006260.jpg" target="_blank">粤网文[2013]029-029号</a></span>
			<br>
			<span style="font-family:Arial;">Copyright © 2012 - 2013 <a target="_blank" href="http://www.miitbeian.gov.cn/">粤ICP备05087105号</a><!--粤文市审[2012]196号--></span>
		</div>
	</footer>
</div>
<!-- 新闻条目模块 START -->
<script id="J_itemView" type="text/template">
	{each data}
		<li><a href="{$value.url}"><span>{$value.title}</span><span>{$value.ontime}</span></a></li>
	{/each}
</script>
<script type="text/javascript">
	var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
	document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3F9a55e0c040a4ea7e63ff3bbadf8db6c9' type='text/javascript'%3E%3C/script%3E"));
</script>
</body>
</html>
