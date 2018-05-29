<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection" content="telephone=no" />
<title>网址导航</title>
<?php include "../_inc.php"; ?>
<link id="linkMain" rel="stylesheet" type="text/css" href="<?php echo $staticPath;?>/apps/3g/assets/css/3g.navigator.source.css">
<script src="<?php echo $staticPath;?>/sys/lib/zepto/zepto.js"></script>
<script src="<?php echo $staticPath;?>/apps/3g/assets/js/navigator.source.js"></script>
<style type="text/css">
	.nav-recommend img{
		border:none; border-radius: 4px; 
		/*background: url(<?php echo $staticPath;?>/apps/3g/nav/pic/03.png) no-repeat 0 0;
		background-size: 25px 25px;*/
	}
</style>
</head>

<body>
<input type="hidden" value="<?php echo $webroot.'/api/baidu/nav';?>" id="api-nav-hotwords">
<input type="hidden" value="<?php echo $webroot.'/nav/ad';?>" id="api-nav-ads">
<section id="wrapper">
	<div id="J_nav_top_ads" class="gn-ad">
		<!-- <img data-src="<?php echo $staticPath;?>/apps/3g/nav/pic/5301d9061cb4d.jpg" />
		<span class="close J_close">X</span> -->
	</div>

	<section class="module nav-search-wrap">
		<form name="search-form" method="get" action="http://m.baidu.com/ssid=0/from=1670b/bd_page_type=1/uid=EED33CCA81FCF5178750360E15F8025D/s">
			<div class="nav-search border1px-sh">
				<!--<div class="form-button-drapdown">
					<span><em class="form-button-drapdown-text">百度</em><i class="iconfont"></i></span>
					<div class="ui-poptip ishide">
						<div class="ui-poptip-box">
							<div class="ui-poptip-arrow ui-poptip-arrow-11"><em></em><span></span></div>
							<div class="ui-poptip-content">
								<ul class="form-button-drapdown-ul">
									<li data-engine="baidu">百度</li>
									<li data-engine="taobao">淘宝</li>
									<li data-engine="youku">优酷</li>
								</ul>
							</div>
						</div>
					</div>
				</div>-->
				<!-- 默认 -->
				<input type="hidden" id="api-baidu-hots" value="<?php echo $webroot;?>/3g/api/api-baidu-hots.json" />
				<input type="text" name="word" class="form-input form-input-default" autocomplete="off" value="输入关键词" />
				<!-- <div class="form-engine ishide">
					<span data-engine="baidu" data-action="http://m.baidu.com/ssid=0/from=1670b/bd_page_type=1/uid=EED33CCA81FCF5178750360E15F8025D/s" data-name="word">无编制公务员惹事</span>
					<span data-engine="taobao" data-action="http://r.m.taobao.com/s?p=mm_31749056_3153305_10442728" data-name="q">无编制公务员惹事2</span>
					<span data-engine="youku" data-action="http://m.youku.com/smartphone/searchlist?copid=5tjapiiqf21f6612tfrlb71c2" data-name="keyword">无编制公务员惹事3</span>
					<span data-engine="weibo" data-action="" data-name="key">无编制公务员惹事4</span>
				</div>
				 --><!-- font code &#337 from ux.etao.com/fonts -->
				<input type="submit" name="channel_11" class="form-button" value="搜索"/>
			</div>
		</form>
		<div class="hot-words-box">
			<div class="baidu-hot-words"></div>
			<span id="J_convert" class="btn-convert" onclick="_hmt.push(['_trackEvent','GNnav数据','导航搜索-切换','导航搜索-换一换'])">换一换</span>
		</div>
		</div>
	</section>

	<section class="module nav-recommend-wrap">
		<div class="nav-recommend-box border1px-sh">
			<ul class="nav-recommend">
				<li class="border1px-rb"><a href="###" onclick="_hmt.push(['_trackEvent','GNnav数据','热门推荐点击','百度'])"><i><img src="<?php echo $staticPath;?>/apps/3g/nav/pic/01.png" width="22" height="22" alt=""></i><span>百度</span></a></li>
				<li class="border1px-rb"><a href="###"><i><img src="<?php echo $staticPath;?>/apps/3g/nav/pic/02.png" width="22" height="22" alt=""></i><span>新浪</span></a></li>
				<li class="border1px-rb"><a href="###"><i><img src="<?php echo $staticPath;?>/apps/3g/nav/pic/03.png" width="22" height="22" alt=""></i><span>搜狐</span></a></li>
				<li class="border1px-rb"><a href="###"><i><img src="<?php echo $staticPath;?>/apps/3g/nav/pic/04.png" width="22" height="22" alt=""></i><span>凤凰</span></a></li>
				<li class="border1px-rb"><a href="###"><i><img src="<?php echo $staticPath;?>/apps/3g/nav/pic/05.png" width="22" height="22" alt=""></i><span>腾讯</span></a></li>
				<li class="border1px-rb"><a href="###"><i><img src="<?php echo $staticPath;?>/apps/3g/nav/pic/06.png" width="22" height="22" alt=""></i><span>淘宝</span></a></li>
				<li class="border1px-rb"><a href="###"><i><img src="<?php echo $staticPath;?>/apps/3g/nav/pic/07.png" width="22" height="22" alt=""></i><span>优酷</span></a></li>
				<li class="border1px-rb"><a href="###"><i><img src="<?php echo $staticPath;?>/apps/3g/nav/pic/08.png" width="22" height="22" alt=""></i><span>游戏</span></a></li>
				<li class="border1px-rb"><a href="###"><i><img src="<?php echo $staticPath;?>/apps/3g/nav/pic/09.png" width="22" height="22" alt=""></i><span>购物</span></a></li>
				<li class="border1px-rb"><a href="###"><i><img src="<?php echo $staticPath;?>/apps/3g/nav/pic/10.png" width="22" height="22" alt=""></i><span>小说</span></a></li>
				<li class="border1px-rb"><a href="###"><i><img src="<?php echo $staticPath;?>/apps/3g/nav/pic/11.png" width="22" height="22" alt=""></i><span>软件</span></a></li>
				<li class="border1px-rb"><a href="###"><i><img src="<?php echo $staticPath;?>/apps/3g/nav/pic/12.png" width="22" height="22" alt=""></i><span>美图</span></a></li>
				<li class="border1px-rb"><a href="###"><i><img src="<?php echo $staticPath;?>/apps/3g/nav/pic/08.png" width="22" height="22" alt=""></i><span>游戏</span></a></li>
				<li class="border1px-rb"><a href="###"><i><img src="<?php echo $staticPath;?>/apps/3g/nav/pic/09.png" width="22" height="22" alt=""></i><span>购物</span></a></li>
				<li class="border1px-rb"><a href="###"><i><img src="<?php echo $staticPath;?>/apps/3g/nav/pic/10.png" width="22" height="22" alt=""></i><span>小说</span></a></li>
				<li class="border1px-rb"><a href="###"><i><img src="<?php echo $staticPath;?>/apps/3g/nav/pic/11.png" width="22" height="22" alt=""></i><span>软件</span></a></li>
			</ul>
		</div>
	</section>

	<section class="module nav-drapdown-wrap">
		<section class="nav-drapdown border1px-sh">
			<div class="nav-drapdown-box">
				<div class="nav-drapdown-title btn-touch" onclick="if (this.className == 'nav-drapdown-title btn-touch') {_hmt.push(['_trackEvent','GNnav数据','一级栏目点击','新闻'])} ">
					<div class="inner border1px-b"><i><img src="<?php echo $staticPath;?>/apps/3g/nav/pic/96/001.png" width="16" height="16" /></i><h2>新闻资讯</h2><span>音乐频道新上线</span></div>
				</div>
				<div class="nav-drapdown-content nav-drapdown-news">
					<div class="gn-ad-link nav-ad-link border1px-b">
						<!-- <a href="###">音乐频道新上线音乐频道新上线！！</a> -->
						<span><a href="http://3g.3gtest.gionee.com/index/tj?id=6&type=NAV&_url=http%3A%2F%2Fwww.baidu.com%2F&t_bi=_1311197025">[百度谁谁谁]</a></span>
						<span><a href="http://3g.3gtest.gionee.com/index/tj?id=6&type=NAV&_url=http%3A%2F%2Fwww.163.com&t_bi=_1311197025">[广告02]</a></span>
						<span><a href="http://3g.3gtest.gionee.com/index/tj?id=6&type=NAV&_url=http%3A%2F%2Fwww.qq.com&t_bi=_1311197025">[广告03]</a></span>
					</div>
					<ul class="border1px-b">
						<li><a href="###" onclick="_hmt.push(['_trackEvent','GNnav数据','二级栏目点击','新闻'])">[新闻]</a></li>
						<li><a href="###" onclick="_hmt.push(['_trackEvent','GNnav数据','三级栏目点击','新浪'])">新浪</a></li>
						<li><a href="###">网易</a></li>
						<li><a href="###" style="color:#fd7801;">中新</a></li>
						<li><a href="###">腾讯</a></li>
					</ul>
					<ul class="border1px-b">
						<li><a href="###">[新闻]</a></li>
						<li><a href="###">新浪</a></li>
						<li><a href="###">网易</a></li>
						<li><a href="###">中新</a></li>
						<li><a href="###">腾讯</a></li>
					</ul>
				<?php for($i = 0; $i < 5; $i++): ?>
					<ul class="border1px-b">
						<li><a href="###">[新闻]</a></li>
						<li><a href="###">新浪</a></li>
						<li><a href="###">网易</a></li>
						<li><a href="###">中新</a></li>
						<li><a href="###">腾讯</a></li>
					</ul>
				<?php endfor; ?>
				</div>
			</div>
			<div class="nav-drapdown-box">
				<div class="nav-drapdown-title btn-touch">
					<div class="inner border1px-b">
						<i><img src="<?php echo $staticPath;?>/apps/3g/nav/pic/96/002.png" width="16" height="16" /></i><h2>休闲娱乐</h2><span><!-- 音乐频道新上线 --></span>
					</div>
				</div>
				<div class="nav-drapdown-content nav-drapdown-news">
					<div class="gn-ad-link nav-ad-link border1px-b">
						<!-- <a href="###">音乐频道新上线音乐频道新上线！！</a> -->
						<span><a href="http://3g.3gtest.gionee.com/index/tj?id=6&type=NAV&_url=http%3A%2F%2Fwww.baidu.com%2F&t_bi=_1311197025">[百度谁谁谁]</a></span>
						<span><a href="http://3g.3gtest.gionee.com/index/tj?id=6&type=NAV&_url=http%3A%2F%2Fwww.163.com&t_bi=_1311197025">[广告02]</a></span>
						<span><a href="http://3g.3gtest.gionee.com/index/tj?id=6&type=NAV&_url=http%3A%2F%2Fwww.qq.com&t_bi=_1311197025">[广告03]</a></span>
					</div>
					<ul class="border1px-b">
						<li><a href="###">[新闻]</a></li>
						<li><a href="###">新浪</a></li>
						<li><a href="###">网易</a></li>
						<li><a href="###" style="color:#fd7801;">中新</a></li>
						<li><a href="###">腾讯</a></li>
					</ul>
					<ul class="border1px-b">
						<li><a href="###">[新闻]</a></li>
						<li><a href="###">新浪</a></li>
						<li><a href="###">网易</a></li>
						<li><a href="###">中新</a></li>
						<li><a href="###">腾讯</a></li>
					</ul>
				<?php for($i = 0; $i < 5; $i++): ?>
					<ul class="border1px-b">
						<li><a href="###">[新闻]</a></li>
						<li><a href="###">新浪</a></li>
						<li><a href="###">网易</a></li>
						<li><a href="###">中新</a></li>
						<li><a href="###">腾讯</a></li>
					</ul>
				<?php endfor; ?>
				</div>
			</div>
			<div class="nav-drapdown-box">
				<div class="nav-drapdown-title btn-touch">
					<div class="inner border1px-b">
						<i><img src="<?php echo $staticPath;?>/apps/3g/nav/pic/96/003.png" width="16" height="16" /></i><h2>小说阅读</h2><span><!-- 音乐频道新上线 --></span>
					</div>
				</div>
				<div class="nav-drapdown-content nav-drapdown-news">
					<div class="gn-ad-link nav-ad-link border1px-b">
						<!-- <a href="###">音乐频道新上线音乐频道新上线！！</a> -->
						<span><a href="http://3g.3gtest.gionee.com/index/tj?id=6&type=NAV&_url=http%3A%2F%2Fwww.baidu.com%2F&t_bi=_1311197025">[百度谁谁谁]</a></span>
						<span><a href="http://3g.3gtest.gionee.com/index/tj?id=6&type=NAV&_url=http%3A%2F%2Fwww.163.com&t_bi=_1311197025">[广告02]</a></span>
						<span><a href="http://3g.3gtest.gionee.com/index/tj?id=6&type=NAV&_url=http%3A%2F%2Fwww.qq.com&t_bi=_1311197025">[广告03]</a></span>
					</div>
					<ul class="border1px-b">
						<li><a href="###">[新闻]</a></li>
						<li><a href="###">新浪</a></li>
						<li><a href="###">网易</a></li>
						<li><a href="###" style="color:#fd7801;">中新</a></li>
						<li><a href="###">腾讯</a></li>
					</ul>
					<ul class="border1px-b">
						<li><a href="###">[新闻]</a></li>
						<li><a href="###">新浪</a></li>
						<li><a href="###">网易</a></li>
						<li><a href="###">中新</a></li>
						<li><a href="###">腾讯</a></li>
					</ul>
				<?php for($i = 0; $i < 6; $i++): ?>
					<ul class="border1px-b">
						<li><a href="###">[新闻]</a></li>
						<li><a href="###">新浪</a></li>
						<li><a href="###">网易</a></li>
						<li><a href="###">中新</a></li>
						<li><a href="###">腾讯</a></li>
					</ul>
				<?php endfor; ?>
				</div>
			</div>
			<div class="nav-drapdown-box">
				<div class="nav-drapdown-title btn-touch">
					<div class="inner border1px-b">
						<i><img src="<?php echo $staticPath;?>/apps/3g/nav/pic/96/004.png" width="16" height="16" /></i><h2>生活购物</h2><span><!-- 音乐频道新上线 --></span>
					</div>
				</div>
				<div class="nav-drapdown-content nav-drapdown-news">
					<div class="gn-ad-link nav-ad-link border1px-b">
						<!-- <a href="###">音乐频道新上线音乐频道新上线！！</a> -->
						<span><a href="http://3g.3gtest.gionee.com/index/tj?id=6&type=NAV&_url=http%3A%2F%2Fwww.baidu.com%2F&t_bi=_1311197025">[百度谁谁谁]</a></span>
						<span><a href="http://3g.3gtest.gionee.com/index/tj?id=6&type=NAV&_url=http%3A%2F%2Fwww.163.com&t_bi=_1311197025">[广告02]</a></span>
						<span><a href="http://3g.3gtest.gionee.com/index/tj?id=6&type=NAV&_url=http%3A%2F%2Fwww.qq.com&t_bi=_1311197025">[广告03]</a></span>
					</div>
					<ul class="border1px-b">
						<li><a href="###">[新闻]</a></li>
						<li><a href="###">新浪</a></li>
						<li><a href="###">网易</a></li>
						<li><a href="###" style="color:#fd7801;">中新</a></li>
						<li><a href="###">腾讯</a></li>
					</ul>
					<ul class="border1px-b">
						<li><a href="###">[新闻]</a></li>
						<li><a href="###">新浪</a></li>
						<li><a href="###">网易</a></li>
						<li><a href="###">中新</a></li>
						<li><a href="###">腾讯</a></li>
					</ul>
				<?php for($i = 0; $i < 6; $i++): ?>
					<ul class="border1px-b">
						<li><a href="###">[新闻]</a></li>
						<li><a href="###">新浪</a></li>
						<li><a href="###">网易</a></li>
						<li><a href="###">中新</a></li>
						<li><a href="###">腾讯</a></li>
					</ul>
				<?php endfor; ?>
				</div>
			</div>
			<div class="nav-drapdown-box">
				<div class="nav-drapdown-title btn-touch">
					<div class="inner border1px-b">
						<i><img src="<?php echo $staticPath;?>/apps/3g/nav/pic/96/005.png" width="16" height="16" /></i><h2>游戏软件</h2><span><!-- 音乐频道新上线 --></span>
					</div>
				</div>
				<div class="nav-drapdown-content nav-drapdown-news">
					<div class="gn-ad-link nav-ad-link border1px-b">
						<!-- <a href="###">音乐频道新上线音乐频道新上线！！</a> -->
						<span><a href="http://3g.3gtest.gionee.com/index/tj?id=6&type=NAV&_url=http%3A%2F%2Fwww.baidu.com%2F&t_bi=_1311197025">[百度谁谁谁]</a></span>
						<span><a href="http://3g.3gtest.gionee.com/index/tj?id=6&type=NAV&_url=http%3A%2F%2Fwww.163.com&t_bi=_1311197025">[广告02]</a></span>
						<span><a href="http://3g.3gtest.gionee.com/index/tj?id=6&type=NAV&_url=http%3A%2F%2Fwww.qq.com&t_bi=_1311197025">[广告03]</a></span>
					</div>
					<ul class="border1px-b">
						<li><a href="###">[新闻]</a></li>
						<li><a href="###">新浪</a></li>
						<li><a href="###">网易</a></li>
						<li><a href="###" style="color:#fd7801;">中新</a></li>
						<li><a href="###">腾讯</a></li>
					</ul>
					<ul class="border1px-b">
						<li><a href="###">[新闻]</a></li>
						<li><a href="###">新浪</a></li>
						<li><a href="###">网易</a></li>
						<li><a href="###">中新</a></li>
						<li><a href="###">腾讯</a></li>
					</ul>
				<?php for($i = 0; $i < 5; $i++): ?>
					<ul class="border1px-b">
						<li><a href="###">[新闻]</a></li>
						<li><a href="###">新浪</a></li>
						<li><a href="###">网易</a></li>
						<li><a href="###">中新</a></li>
						<li><a href="###">腾讯</a></li>
					</ul>
				<?php endfor; ?>
				</div>
			</div>
		</section>
	</section>
	
	
	<!-- 宜搜搜索 START-->
	<!-- <div class="module">
		<section class="ct-form-search">
		</section>
	</div> -->

	<!-- bottom link ads START -->
	<div id="J_nav_btm_link" class="gn-ad-link center"></div>
	<!-- bottom link ads END -->
</section>
<footer id="footer">
	<div class="copyright">
		<!-- <span><a href="feedback.php">提建议>></a></span>			 -->
<!-- 		<br>
		<span>粤文市审[2012]196号</span>
		<br>
		<span>增值电信许可证：<a href="<?php echo $staticPath;?>/apps/3g/nav/pic/1.jpg" target="_blank">粤B2-20120350</a></span>
		<br>
		<span>网络文化经营许可证：<a href="<?php echo $staticPath;?>/apps/3g/nav/pic/2.jpg" target="_blank">粤网文[2013]029-029号</a></span>
		<br> -->
		<span style="font-family:Arial;">Copyright © 2012 - <script>document.write(new Date().getFullYear());</script> <a target="_blank" href="http://www.miitbeian.gov.cn/">粤ICP备05087105号</a></span>
	</div>
</footer>
<script type="text/javascript">
	//检查浏览器是否支持SVG
	//console.log( document.implementation && document.implementation.hasFeature("http://www.w3.org/TR/SVG11/feature#BasicStructure", "1.1"));
</script>
<script type="text/javascript">
	var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
	document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3F9a55e0c040a4ea7e63ff3bbadf8db6c9' type='text/javascript'%3E%3C/script%3E"));
</script>
</body>
</html>