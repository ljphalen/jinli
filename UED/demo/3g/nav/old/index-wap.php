<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>网址导航</title>
<?php
	/** 指定当前应用模块名称 START */
	$moduleName = "nav";
	$webTitle = "网址导航";
	/** 指定当前应用模块名称 END */
?>
<?php include '../_inc.php';?>
</head>
<body>

	<div id="page">
		<div class="pindex" id="iscrollWrap">
			<div class="mainWrap">
				<!-- 内容区域 START-->
				<?php include "../_header.php"; ?>
				<header class="hd">
					<div class="slide banner">
						<img src="<?php echo $appPic;?>/banner02.jpg" alt="移动官网-网址导航" />
					</div>
				</header>

				<div class="module">
					<section class="tag-box mainTag">
						<div class="navTag">
							<ul id="tabs01">
								<li data-tab="tabLink1" class="actived">热门站点</li>
								<li data-tab="tabLink2">生活便民</li>
								<li data-tab="tabLink3">金立推荐</li>
							</ul>
						</div>
						<div id="tabs-cont01">
							<div data-tab="tabLink1">
								<!-- 社区·交友 -->
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
							<div data-tab="tabLink2"  class="ishide">
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
								</section>
							</div>
						</div>
					</section>
				</div>
				<div class="module">
					<section class="ct-form-search">
						<form name="easou" method="get" action="http://ad3.easou.com:8080/j10ad/ea2.jsp">
							<input type="hidden" name="cid" value="bkcn3510_47908_D_1" />
							<div class="in-search">
								<input type="search" name="key" class="inp-search" autocomplete="off" value="免费小说" />
								<input type="submit" name="channel_11" class="btn-search" value="搜索" />
							</div>
						</form>
					</section>
				</div>
				<div class="module">
					<section class="tag-box">
						<div class="navTag">
							<ul id="tabs02">
								<li data-tab="tabLink1" class="actived">今日新闻</li>
								<li data-tab="tabLink2">热门资讯</li>
								<li data-tab="tabLink3">金立快讯</li>
							</ul>
						</div>
						<div id="tabs-cont02">
							<?php for($i = 1; $i< 4; $i++):?>
							<div data-tab="tabLink<?php echo $i;?>" <?php if($i !=1){echo "class=\"ishide\"";}?>>
								<div class="navCont">
									<div class="news-recommend" data-newsType="1">
										<a href="###">
											<h1>给“旧的环保主义”看看病</h1>
											<p>10:14 新浪网</p>
										</a>
									</div>
									<div class="news-recommend" data-newsType="2">
										<a href="###">
											<div class="pic">
												<img src="<?php echo $appPic;?>/news-banner02.jpg" alert="" />
											</div>
											<div class="txt">
												<h1>给“旧的环保主义”看看病</h1>
												<p>10:14 新浪网</p>
											</div>
										</a>
									</div>
									<div class="news-recommend" data-newsType="3">
										<a href="###">
											<div class="pic">
												<img src="<?php echo $appPic;?>/news-banner01.jpg" alert="" />
											</div>
											<div class="txt">
												<h1>给“旧的环保主义”看看病</h1>
												<p>10:14 新浪网</p>
											</div>
										</a>
									</div>
									<div class="news-list">
										<ul>
											<li>
												<a href="###">
												<span>图片故事：胶囊公寓里的青春</span>
												<span>10:05 网易新闻</span>
												</a>
											</li>
											<li>
												<a href="###">
													<span>罗姆尼的中国账本</span>
													<span>10:05 网易新闻</span>
												</a>
											</li>
											<li>
												<a href="###">
													<span>月光女神将放歌太空</span>
													<span>10:05 网易新闻</span>
												</a>
											</li>
											<li>
												<a href="###">
													<span>我的获奖是文学的胜利</span>
													<span>10:05 网易新闻</span>
												</a>
											</li>
										</ul>
									</div>
								</div>
							</div>
							<?php endfor;?>
					</section>
				</div>
				<div class="module">
					<section class="in-entry">
						<div class="in-entry-title">
							<div class="l"><h2>应用</h2></div>
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
				</div>
				<div class="module">
					<div class="dropdown">
						<div class="block">
							<div class="title arrow-up"><h2>名站精选</h2></div>
							<div class="cont">
								<div class="cate">
									<div class="cate-cont">
										<dl>
											<dt><a href="http://sina.cn/">新浪</a></dt>
											<dd><a href="http://3g.163.com/touch">网易</a></dd>
											<dd><a href="http://m.sohu.com/c/2/?v=3&refer=3g">搜狐</a></dd>
											<dd><a href="http://m.vancl.com/">凡客</a></dd>
											<dd><a href="http://3g.qq.com/">腾讯</a></dd>
										</dl>
										<dl>
											<dt><a href="http://wap.tiexue.net/complexversion/military.aspx?">铁血</a></dt>
											<dd><a href="http://ju.m.taobao.com/">聚划算</a></dd>
											<dd><a href="http://wap.ganji.com/">赶集</a></dd>
											<dd><a href="http://qidian.cn/wap2/index.do?">起点</a></dd>
											<dd><a href="http://wap.dianping.com/">大众</a></dd>
										</dl>
										<dl>
											<dt><a href="http://weibo.cn/">微博</a></dt>
											<dd><a href="http://m.iqiyi.com/">奇艺</a></dd>
											<dd><a href="http://wap.kaixin001.com/">开心</a></dd>
											<dd><a href="http://m.tianya.cn/">天涯</a></dd>
											<dd><a href="http://wap.ytao.cn/">移淘</a></dd>
										</dl>
									</div>
									</div>
							</div>

							<div class="title"><h2>分类导航</h2></div>
							<div class="cont ishide">
								<div class="cate">
									<div class="cate-title">资讯·阅读</div>
									<div class="cate-cont">
										<dl>
											<dt>[新闻]</dt>
											<dd><a href="http://sina.cn/nc.php?pos=200&vt=4">新浪</a></dd>
											<dd><a href="http://nd3g.oeeee.com/">南都</a></dd>
											<dd><a href="http://3g.163.com/news/">网易</a></dd>
											<dd><a href="http://m.chinanews.com/iphone/">中新</a></dd>
										</dl>
										<dl>
											<dt>[小说]</dt>
											<dd><a href="http://book.3g.cn">3G</a></dd>
											<dd><a href="http://qidian.cn/wap2/index.do?">起点</a></dd>
											<dd><a href="http://3g.17k.com/">17k</a></dd>
											<dd><a href="http://wap.hongxiu.com/">红袖</a></dd>
										</dl>
										<dl>
											<dt>[体育]</dt>
											<dd><a href="http://sports.sina.cn/?pos=3&vt=4">新浪</a></dd>
											<dd><a href="http://m.sohu.com/c/3/">搜狐</a></dd>
											<dd><a href="http://3g.163.com/sports/">网易</a></dd>
											<dd><a href="http://3g.ifeng.com/sports/sportsi?dh=touch">凤凰</a></dd>
										</dl>
										<dl>
											<dt>[军事]</dt>
											<dd><a href="http://wap.tiexue.net/complexversion/military.aspx?">铁血</a></dd>
											<dd><a href="http://wap.huanqiu.com/pd.html?id=23">环球</a></dd>
											<dd><a href="http://3g.china.com/mili/">中华</a></dd>
											<dd><a href="http://3g.ifeng.com/">凤凰</a></dd>
										</dl>
									</div>
								</div>

								<div class="cate">
									<div class="cate-title">影音·娱乐</div>
									<div class="cate-cont">
										<dl>
											<dt>[音乐]</dt>
											<dd><a href="http://m.easou.com/">MP3</a></dd>
											<dd><a href="http://www.xiami.com/web/radio/uc">虾米</a></dd>
											<dd><a href="http://m.kugou.com/">酷狗</a></dd>
											<dd><a href="http://douban.fm/partner/uc">豆瓣FM</a></dd>
										</dl>
										<dl>
											<dt>[视频]</dt>
											<dd><a href="http://m.youku.com/smartphone/">优酷</a></dd>
											<dd><a href="http://m.tudou.com/">土豆</a></dd>
											<dd><a href="http://m.tv.sohu.com/">搜狐</a></dd>
											<dd><a href="http://m.iqiyi.com/">奇艺</a></dd>
										</dl>
										<dl>
											<dt>[搞笑]</dt>
											<dd><a href="http://m.qiushibaike.com">糗百</a></dd>
											<dd><a href="httP://jandan.net">煎蛋</a></dd>
											<dd><a href="http://m.lengxiaohua.com/">冷兔</a></dd>
											<dd><a href="http://m.baozoumanhua.com/">爆漫</a></dd>
										</dl>
									</div>
								</div>

								<!-- 社区·交友 -->
								<div class="cate">
									<div class="cate-title">社区·交友</div>
									<div class="cate-cont">
										<dl>
											<dt>[微博]</dt>
											<dd><a href="http://weibo.cn/">新浪</a></dd>
											<dd><a href="http://t.3g.qq.com/">腾讯</a></dd>
											<dd><a href="http://3g.163.com/t/tiny_life">网易</a></dd>
											<dd><a href="http://w.sohu.com/t2/home.do?">搜狐</a></dd>
										</dl>
										<dl>
											<dt>[社区]</dt>
											<dd><a href="http://wapp.baidu.com/">贴吧</a></dd>
											<dd><a href="http://m.tianya.cn/">天涯</a></dd>
											<dd><a href="http://mt.renren.com/">人人</a></dd>
											<dd><a href="http://3g.mop.com/">猫扑</a></dd>
										</dl>
										<dl>
											<dt>[婚恋]</dt>
											<dd><a href="http://3g.jiayuan.com/">佳缘</a></dd>
											<dd><a href="http://m.zhenai.com">珍爱</a></dd>
											<dd><a href="http://3g.baihe.com/index/index.html">百合</a></dd>
											<dd><a href="http://w2.youyuan.com">有缘</a></dd>
										</dl>
									</div>
								</div>

								<!-- 生活·购物 -->
								<div class="cate">
									<div class="cate-title">生活·购物</div>
									<div class="cate-cont">
										<dl>
											<dt>[邮箱]</dt>
											<dd><a href="http://m.mail.163.com/">163</a></dd>
											<dd><a href="http://m.mail.qq.com/">QQ</a></dd>
											<dd><a href="http://wapmail.wo.com.cn/login.wo">WO邮</a></dd>
											<dd><a href="http://wapmail.10086.cn/">139</a></dd>
										</dl>
										<dl>
											<dt>[查询]</dt>
											<dd><a href="http://t.qunar.com/">列车</a></dd>
											<dd><a href="http://wap.weather.com.cn/wap/">天气</a></dd>
											<dd><a href="http://tools.wap.58.com/bus/">公交</a></dd>
											<dd><a href="http://wap.3g.qq.com/g/s?aid=starPet">星座</a></dd>
										</dl>
										<dl>
											<dt>[彩票]</dt>
											<dd><a href="http://wap.okooo.com/">澳客</a></dd>
											<dd><a href="http://wap.zch168.com/main?sid=LM20120412211">中彩</a></dd>
											<dd><a href="http://caipiao.m.taobao.com/lottery/wap/index.htm">淘彩</a></dd>
											<dd><a href="http://3g.cp2y.com/">2元</a></dd>
										</dl>
										<dl>
											<dt>[生活]</dt>
											<dd><a href="http://wap.dianping.com/">大众</a></dd>
											<dd><a href="http://m.58.com/">58</a></dd>
											<dd><a href="http://daguu.com/">打工</a></dd>
											<dd><a href="http://wapiknow.baidu.com/">百科</a></dd>
										</dl>
									</div>
								</div>

								<!-- 游戏·玩机 -->
								<div class="cate">
									<div class="cate-title">游戏·玩机</div>
									<div class="cate-cont">
										<dl>
											<dt>[应用]</dt>
											<dd><a href="http://m.anzhuoapk.com">易用汇</a></dd>
											<dd><a href="http://3g.gfan.com/">机锋</a></dd>
											<dd><a href="http://m.91.com/">助手</a></dd>
											<dd><a href="http://m.appchina.com/market-web/cherry/soft_main.action">应用汇</a></dd>
										</dl>
										<dl>
											<dt>[游戏]</dt>
											<dd><a href="http://3g.gfan.com/index.htm#index_game">推荐</a></dd>
											<dd><a href="http://game.gionee.com/index/detail/?id=38">风云</a></dd>
											<dd><a href="http://www.d.cn/mobile.html">当乐</a></dd>
											<dd><a href="http://a.155.cn">手游</a></dd>
										</dl>
										<dl>
											<dt>[论坛]</dt>
											<dd><a href="http://m.hiapk.com/ ">安卓</a></dd>
											<dd><a href="http://bbs.gfan.com/m/index.php">机锋</a></dd>
											<dd><a href="http://bbs.xda.cn/forum.php">XDA</a></dd>
											<dd><a href="http://apkrj.com/index.jsp?lnkId=15">软吧</a></dd>
										</dl>
									</div>
								</div>

								<!-- 其他 -->
								<div class="cate">
									<div class="cate-title">其他</div>
									<div class="cate-cont">
										<dl>
											<dd><a href="http://m.hao123.com/n/v/tupian/co?z=100000">美图</a></dd>
											<dd><a href="http://auto.sina.cn/">汽车</a></dd>
											<dd><a href="http://joke.3g.cn">笑话</a></dd>
											<dd><a href="http://m.youku.com/smartphone/channels?cid=100">动漫</a></dd>
											<dd><a href="http://m.xiachufang.com/">美食</a></dd>
										</dl>
									</div>
								</div>

							</div>
						</div>
					</div>
				</div>
				<!-- 内容区域 END-->
				<?php include '_footer.php';?>
