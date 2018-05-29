<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>网址导航</title>
<!--指定当前应用模块名称 START-->
	<?php $moduleName = "nav"; $webTitle = "网址导航"; $hasFooter = true; ?>
<!--指定当前应用模块名称 END-->
<?php include '../_inc.php';?>
</head>
<body>
<div class="wrapper">
	<!-- 内容区域 START-->
	<!-- 头部搜索模块暂时隐藏
	<div class="module none">
		<div class="gn-ad">
			<div class="pic"><img src="<?php echo $appPic;?>/nav-top-ad-01.jpg" /></div>
			<div class="ui-search">
				<form name="easou" method="get" action="http://ad3.easou.com:8080/j10ad/ea2.jsp">
					<input type="hidden" name="cid" value="bkcn3510_47908_D_1" />
					<div class="ui-search-wrap">
						<input type="submit" name="channel_11" class="btn-search" value="" />
						<input type="text" name="key" class="inp-search" autocomplete="off" value="免费小说" />
					</div>
				</form>
			</div>
		</div>
	</div>
	-->
	<div class="module">
		<section class="tag-box mainTag">
			<div class="navTag">
				<ul id="tabs01">
					<li class="actived">热门站点</li>
					<li data-ajaxUrl= "../api/data.json">在线应用</li>
					<li data-ajaxUrl= "../api/data.json">软件下载</li>
				</ul>
			</div>
			<div id="tabs-cont01">
				<div class="tabLink1 box">
					<div class="cate">
						<div class="cate-cont">
							<dl>
								<dd><a href="http://m.baidu.com">百度</a></dd>
								<dd><a href="http://sina.cn/?nav=top&pos=3&vt=4">新浪</a></dd>
								<dd><a href="http://m.sohu.com/">搜狐</a></dd>
								<dd><a href="http://3g.163.com/touch/">网易</a></dd>
								<dd><a href="3g.ifeng.com">凤凰</a></dd>
							</dl>
							<dl>
								<dd><a href="http://qidian.cn/wap2/index.do?">起点</a></dd>
								<dd><a href="http://weibo.cn/">微博</a></dd>
								<dd><a href="http://mt.renren.com/">人人</a></dd>
								<dd><a href="http://m.douban.com">豆瓣</a></dd>
								<dd><a href="http://wapp.baidu.com/">贴吧</a></dd>
							</dl>
							<dl>
								<dd><a href="http://wap.ganji.com/">赶集</a></dd>
								<dd><a href="http://m.58.com/">58</a></dd>
								<dd><a href="http://wapmap.baidu.com/">地图</a></dd>
								<dd><a href="http://m.kugou.com/">酷狗</a></dd>
								<dd><a href="http://gou.gionee.com/?source=gionee%20brower1.2">金立购</a></dd>
							</dl>
							<dl>
								<dd><a href="http://r.m.taobao.com/m2?e=%2BH8j1GjPyOOMyJxKLIWDYm6soqqFE9nYGshDO7CxrtWh">淘热卖</a></dd>
								<dd><a href="http://m.dangdang.com/touch/index.php">当当</a></dd>
								<dd><a href="http://wap.9game.cn/">九游</a></dd>
								<dd><a href="http://sj.lexun.com">乐讯</a></dd>
								<dd><a href="http://game.gionee.com/?source=gioneebrowser">游戏</a></dd>
							</dl>
						</div>
					</div>
				</div>
				<div class="tabLink2 box ishide">
					<section class="in-entry">
						<div class="in-entry-cont">
							<ul></ul>
						</div>
					</section>
				</div>
				<div class="tabLink3 box ishide">
					<section class="in-entry">
						<div class="in-entry-cont">
							<ul></ul>
						</div>
					</section>
				</div>
				<!--<div data-tab="tabLink2"  class="ishide">
					<section class="in-entry">
					<div class="in-entry-cont">
						<ul>
							<li>
								<a href="http://caipiao.wap.taobao.com/?ttid=51sjl001"><img src="<?php echo $appPic;?>/caipiao.png" alt="彩票" /></a>
								<a href="http://caipiao.wap.taobao.com/?ttid=51sjl001"><span>彩票</span></a>
							</li>
							<li>
								<a href="http://r.m.taobao.com/zc?p=mm_31749056_3417338_11044550"><img src="<?php echo $appPic;?>/huafei.png" alt="话费" /></a>
								<a href="http://r.m.taobao.com/zc?p=mm_31749056_3417338_11044550"><span>话费</span></a>
							</li>
							<li>
								<a href="http://m.dangdang.com/pub_search.php?key=%E5%B0%8F%E8%AF%B4&sort_type=sales_1&unionid=P-308411m"><img src="<?php echo $appPic;?>/goushu.png" alt="购书" /></a>
								<a href="http://m.dangdang.com/pub_search.php?key=%E5%B0%8F%E8%AF%B4&sort_type=sales_1&unionid=P-308411m"><span>购书</span></a>
							</li>
							<li>
								<a href="http://m.miot.cn/gionee/"><img src="<?php echo $appPic;?>/jiudian.png" alt="酒店" /></a>
								<a href="http://m.miot.cn/gionee/"><span>酒店</span></a>
							</li>
							<li>
								<a href="http://zuoche.com/m/"><img src="<?php echo $appPic;?>/gongjiao.png" alt="公交" /></a>
								<a href="http://zuoche.com/m/"><span>公交</span></a>
							</li>
							<li>
								<a href="http://wap.weather.com.cn/wap/"><img src="<?php echo $appPic;?>/tianqi.png" alt="天气" /></a>
								<a href="http://wap.weather.com.cn/wap/"><span>天气</span></a>
							</li>
							<li>
								<a href="http://wap.3g.qq.com/g/s?aid=starPet"><img src="<?php echo $appPic;?>/xinzuo.png" alt="星座" /></a>
								<a href="http://wap.3g.qq.com/g/s?aid=starPet"><span>星座</span></a>
							</li>
							<li>
								<a href="http://wap.kuaidi100.com/"><img src="<?php echo $appPic;?>/kuaidi.png" alt="快递" /></a>
								<a href="http://wap.kuaidi100.com/"><span>快递</span></a>
							</li>
						</ul>
					</div>
					<div class="btn-more app-more"><a href="../app/apps-wap.php">更多在线应用</a></div>
					</section>
				</div>
				<div data-tab="tabLink3" class="ishide">
					<section class="in-entry">
					<div class="in-entry-cont">
						<ul>
							<li>
								<a href="http://m.vancl.com/m/index/.mvc?source=gionee001"><img src="<?php echo $appPic;?>/vancl.png" alt="凡客诚品" /></a>
								<a href="http://m.vancl.com/m/index/.mvc?source=gionee001"><span>凡客诚品</span></a>
							</li>
							<li>
								<a href="http://m.dangdang.com/?unionid=P-308411m"><img src="<?php echo $appPic;?>/dangdang.png" alt="当当" /></a>
								<a href="http://m.dangdang.com/touch/index.php?unionid=P-308411m"><span>当当</span></a>
							</li>
							<li>
								<a href="http://music.baidu.com"><img src="<?php echo $appPic;?>/mp3.png" alt="MP3" /></a>
								<a href="http://music.baidu.com"><span>MP3</span></a>
							</li>
							<li>
								<a href="http://game.gionee.com/index/detail/?id=80"><img src="<?php echo $appPic;?>/eyu.png" alt="鳄鱼爱洗澡" /></a>
								<a href="http://game.gionee.com/index/detail/?id=80"><span>鳄鱼爱洗澡</span></a>
							</li>
							<li>
								<a href="http://game.gionee.com/index/detail/?id=23"><img src="<?php echo $appPic;?>/sangguo.png" alt="明珠三国" /></a>
								<a href="http://game.gionee.com/index/detail/?id=23"><span>明珠三国</span></a>
							</li>
							<li>
								<a href="http://m.mogujie.com"><img src="<?php echo $appPic;?>/mogujie.png" alt="蘑菇街" /></a>
								<a href="http://m.mogujie.com"><span>蘑菇街</span></a>
							</li>
							<li>
								<a href="http://wap.cmread.com/iread/wml/p/ycindex.jsp?cm=M2410001"><img src="<?php echo $appPic;?>/yuedu.png" alt="阅读" /></a>
								<a href="http://wap.cmread.com/iread/wml/p/ycindex.jsp?cm=M2410001"><span>阅读</span></a>
							</li>
							<li>
								<a href="http://douban.fm/partner/uc"><img src="<?php echo $appPic;?>/douban.png" alt="豆瓣FM" /></a>
								<a href="http://douban.fm/partner/uc"><span>豆瓣FM</span></a>
							</li>
						</ul>
					</div>
					<div class="btn-more download-more"><a href="###">更多软件下载</a></div>
					</section>
				</div>-->
			</div>
		</section>
	</div>
	<!-- 宜搜搜索 START-->
	<div class="module">
		<section class="ct-form-search">
			<form name="easou" method="get" action="http://ad3.easou.com:8080/j10ad/ea2.jsp">
				<input type="hidden" name="cid" value="bkcn3510_47908_D_1" />
				<div class="in-search">
					<input type="text" name="key" class="inp-search" autocomplete="off" value="免费小说" />
					<input type="submit" name="channel_11" class="btn-search" value="搜索" />
				</div>
				<div class="in-hot-world">
					<a href="http://i.easou.com/s.m?idx=0&sty=1&cid=bkcn3510_47908_21226_1&q=小行星战神">小行星战神</a>
					<a href="http://i.easou.com/s.m?idx=0&sty=1&cid=bkcn3510_47908_21226_1&q=视帝后今晚揭晓">视帝后今晚揭晓 </a>
					<a href="http://i.easou.com/s.m?idx=0&sty=1&cid=bkcn3510_47908_21226_1&q=热门免费小说">热门免费小说</a>
				</div>
			</form>
		</section>
	</div>
	<!-- 中间广告区域 START-->
	<div class="module">
		<section class="gn-ad">
			<div class="slide banner">
				<a href="##"><img src="<?php echo $appPic;?>/nav_ad_banner.jpg" alt="" /></a>
			</div>
		</section>
	</div>
	<div class="module">
		<section class="tag-box">
			<div class="navTag">
				<ul id="tabs02" data-ajaxUrl="json.php">
					<li data-tab="tabLink1" class="actived">头条</li>
					<li data-tab="tabLink2">时事</li>
					<li data-tab="tabLink3">娱乐</li>
					<li data-tab="tabLink4">军事</li>
				</ul>
			</div>
			<div id="tabs-cont02">
				<?php for($i = 1; $i< 5; $i++):?>
				<div data-tab="tabLink<?php echo $i; ?>" id="tabLink<?php echo $i;?>" <?php if($i != 1):?> class="ishide" <?php endif;?>>
					<div class="navCont">
						<div class="news-list">
							<ul>
								<li>
									<a href="###">
									<span>图片故事：胶囊公寓里的青春</span>
									<span>2小时以前</span>
									</a>
								</li>
								<li>
									<a href="###">
										<span>罗姆尼的中国账本</span>
										<span>2小时以前</span>
									</a>
								</li>
								<li>
									<a href="###">
										<span>月光女神将放歌太空</span>
										<span>2小时以前</span>
									</a>
								</li>
								<li>
									<a href="###">
										<span>我的获奖是文学的胜利</span>
										<span>2小时以前</span>
									</a>
								</li>
								<li class="btn-more news-more"><a href="http://i.ifeng.com/news/newsi?ch=zd_jl_dh&vt=5&dh=touch&mid=9yCLji">更多资讯</a></li>
							</ul>
						</div>
					</div>
				</div>
				<?php endfor;?>
			</div>
		</section>
	</div>
	<!-- 应用链接列表 -->
	<div class="module">
		<div class="link-list">
			<div class="block">
				<div class="cate">
					<div class="cate-title">新闻 资讯</div>
					<div class="cate-cont">
						<dl>
							<dt><a href="list.php">新闻</a></dt>
							<dd><a href="http://sina.cn/nc.php?pos=200&vt=4">新浪</a></dd>
							<dd><a href="http://nd3g.oeeee.com/">南都</a></dd>
							<dd><a href="http://3g.163.com/news/">网易</a></dd>
							<dd><a href="http://m.chinanews.com/iphone/">中新</a></dd>
						</dl>
						<dl>
							<dt><a href="list.php">体育</a></dt>
							<dd><a href="http://book.3g.cn">3G</a></dd>
							<dd><a href="http://qidian.cn/wap2/index.do?">起点</a></dd>
							<dd><a href="http://3g.17k.com/">17k</a></dd>
							<dd><a href="http://wap.hongxiu.com/">红袖</a></dd>
						</dl>
						<dl>
							<dt><a href="list.php">军事</a></dt>
							<dd><a href="http://sports.sina.cn/?pos=3&vt=4">新浪</a></dd>
							<dd><a href="http://m.sohu.com/c/3/">搜狐</a></dd>
							<dd><a href="http://3g.163.com/sports/">网易</a></dd>
							<dd><a href="http://3g.ifeng.com/sports/sportsi?dh=touch">凤凰</a></dd>
						</dl>
					</div>
				</div>
			</div>
			<div class="block">
				<div class="cate">
					<div class="cate-title">小说 阅读</div>
					<div class="cate-cont">
						<dl>
							<dt><a href="novel.php">小说</a></dt>
							<dd><a href="http://sina.cn/nc.php?pos=200&vt=4">新浪</a></dd>
							<dd><a href="http://nd3g.oeeee.com/">南都</a></dd>
							<dd><a href="http://3g.163.com/news/">网易</a></dd>
							<dd><a href="http://m.chinanews.com/iphone/">中新</a></dd>
						</dl>
						<dl>
							<dt><a href="list.php">体育</a></dt>
							<dd><a href="http://book.3g.cn">3G</a></dd>
							<dd><a href="http://qidian.cn/wap2/index.do?">起点</a></dd>
							<dd><a href="http://3g.17k.com/">17k</a></dd>
							<dd><a href="http://wap.hongxiu.com/">红袖</a></dd>
						</dl>
						<dl>
							<dt><a href="list.php">军事</a></dt>
							<dd><a href="http://sports.sina.cn/?pos=3&vt=4">新浪</a></dd>
							<dd><a href="http://m.sohu.com/c/3/">搜狐</a></dd>
							<dd><a href="http://3g.163.com/sports/">网易</a></dd>
							<dd><a href="http://3g.ifeng.com/sports/sportsi?dh=touch">凤凰</a></dd>
						</dl>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- 推荐应用 -->
	<!--<div class="module">
		<section class="in-entry">
			<div class="in-entry-title">
				<div class="l"><h2>推荐应用</h2></div>
				<div class="m"><em>无需下载 超省流量</em></div>
				<div class="r"><a href="apps.php" class="more">更多</a></div>
			</div>
			<div class="in-entry-cont">
				<ul>
					<li>
						<a href="http://weibo.cn/"><img src="<?php echo $appPic;?>/icon-weibo.png" alt="新浪微博" /></a>
						<a href="http://weibo.cn/"><span>微博</span></a>
					</li>
					<li>
						<a href="http://m.youku.com/smartphone/"><img src="<?php echo $appPic;?>/youku.png" alt="优酷视频" /></a>
						<a href="http://m.youku.com/smartphone/"><span>优酷视频</span></a>
					</li>
					<li>
						<a href="http://gou.gionee.com/?source=gionee%20brower1.2"><img src="<?php echo $appPic;?>/icon-gngou.png" alt="金立购" /></a>
						<a href="http://gou.gionee.com/?source=gionee%20brower1.2"><span>金立购</span></a>
					</li>
					<li>
						<a href="http://game.gionee.com/?source=gioneebrowser"><img src="<?php echo $appPic;?>/icon-gngame.png" alt="金立游戏" /></a>
						<a href="http://game.gionee.com/?source=gioneebrowser"><span>金立游戏</span></a>
					</li>
				</ul>
			</div>
		</section>
	</div>-->
	<!-- 底部链接模块 -->
	<!--<div class="module">
		<ul class="bottom-link">
			<li><a href="###">必玩游戏</a></li>
			<li><a href="###">装机必备</a></li>
			<li><a href="###">热门网游</a></li>
			<li><a href="###">网页游戏</a></li>
		</ul>
		<ul class="footer-link">
			<li><a href="../index.php">产品服务</a></li>
			<li><a href="../nav/index.php">网站导航</a></li>
			<li><a href="../app/wap/index.php">在线应用</a></li>
			<li><a href="../pcenter/index.php">个人中心</a></li>
			<li><a href="../v1.1/signin.php">签到</a></li>
		</ul>
	</div>-->
	<?php if(isset($hasFooter)):?>
	<div class="ft">
		<footer>
			<a href="feedback.php">提建议>></a><br/>
			<p class="copyright">深圳市金立通信设备有限公司<br/>Copyright© 2012 粤ICP备05087105号</p>
		</footer>
	</div>
	<?php endif; ?>
</div>
<!-- 内容区域 END-->
<!-- 顶部Tab切换区域模板 START -->
<script id="J_tabView" type="text/template">
	{each data}
		<li>
			<a href="{$value.link}"><img src="{$value.icon}" /></a>
			<a href="{$value.link}"><span>{$value.name}</span></a>
		</li>
	{/each}
</script>
<!-- 推荐区域模板 START -->
<script id="J_itemView" type="text/template">
	{each data}
		<li><a href="{$value.url}"><span>{$value.title}</span><span>{$value.ontime}</span></a></li>
	{/each}
</script>
</body>
</html>