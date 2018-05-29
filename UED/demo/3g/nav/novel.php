<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>网址导航</title>
<!--指定当前应用模块名称 START-->
	<?php $moduleName = "nav-novel"; $webTitle = "网址导航--小说"; $hasFooter = false; ?>
<!--指定当前应用模块名称 END-->
<?php include '../__inc.php';?>
</head>
<body>
	<div id="page">
		<div class="wrapper">
			<div class="pnav-list">
				<!-- 页面内容区域 -->
				<div class="module">
					<div class="nav-link-list">
						<!--每日推荐-->
						<div class="link-item-list">
							<div class="cate">
								<div class="cate-title">每日推荐</div>
								<div class="novel-recommend">
									<section class="item-cont">
										<dl class="clearfix">
											<dt class="item-icon"><img src="<?php echo $appPic;?>/novel.jpg" /></dt>
											<dd class="item-title">都市桃花运</dd>
											<dd class="item-star">
												<span class="score-star score<?php echo rand(0,5);?>">
													<span class="star1"></span>
													<span class="star2"></span>
													<span class="star3"></span>
													<span class="star4"></span>
													<span class="star5"></span>
												</span>
											</dd>
											<dd class="item-text">这是一本介绍用户体验实践的书籍</dd>
											<dd class="item-side"><a href="###" class="btn gray">阅读</a></dd>
										</dl>
									</section>
								</div>
								<ul class="novel-list">
									<li class="item"><a href="#">奇幻修行打造Facebook爆发打造Facebook:亲历Facebook爆发</a></li>
									<li class="item"><a href="#">打造Facebook:亲历Facebook爆发</a></li>
									<li class="item"><a href="#">启示录</a></li>
									<li class="item"><a href="#">白帽子讲WEB安全</a></li>
									<li class="item list-more"><a href="#">更多&gt;&gt;</a></li>
								</ul>
							</div>
						</div>
						<!--男生频道-->
						<div class="module link-item-list">
							<div class="cate">
								<div class="cate-title">男生频道</div>
								<div class="cate-cont">
									<ul>
										<li><a href="http://nd3g.oeeee.com/">奇幻修行</a></li>
										<li><a href="http://3g.163.com/news/">网易</a></li>
										<li><a href="http://m.chinanews.com/iphone/">中新</a></li>
										<li><a href="http://qidian.cn/wap2/index.do?">起点</a></li>
										<li><a href="http://3g.17k.com/">17k</a></li>
										<li><a href="http://wap.hongxiu.com/">红袖</a></li>
										<li><a href="http://m.sohu.com/c/3/">搜狐</a></li>
										<li><a href="http://3g.163.com/sports/">网易</a></li>
										<li><a href="http://3g.ifeng.com/sports/sportsi?dh=touch">凤凰</a></li>
									</ul>
								</div>
							</div>
						</div>
						<!--女生频道-->
						<div class="module link-item-list">
							<div class="cate">
								<div class="cate-title">女生频道</div>
								<div class="cate-cont">
									<ul>
										<li><a href="http://nd3g.oeeee.com/">奇幻修行</a></li>
										<li><a href="http://3g.163.com/news/">网易</a></li>
										<li><a href="http://m.chinanews.com/iphone/">中新</a></li>
										<li><a href="http://qidian.cn/wap2/index.do?">起点</a></li>
										<li><a href="http://3g.17k.com/">17k</a></li>
										<li><a href="http://wap.hongxiu.com/">红袖</a></li>
										<li><a href="http://m.sohu.com/c/3/">搜狐</a></li>
										<li><a href="http://3g.163.com/sports/">网易</a></li>
										<li><a href="http://3g.ifeng.com/sports/sportsi?dh=touch">凤凰</a></li>
									</ul>
								</div>
							</div>
						</div>
						<!--排行榜-->
						<div class="link-item-list">
							<div class="cate">
								<div class="cate-title mrb0">排行榜</div>
								<div class="">
									<ul class="novel-list">
										<li class="item"><a href="#"><i>1</i>奇幻修行</a></li>
										<li class="item"><a href="#"><i>2</i>打造Facebook:亲历Facebook爆发打造Facebook:亲历Facebook爆发</a></li>
										<li class="item"><a href="#"><i>3</i>启示录</a></li>
										<li class="item"><a href="#"><i>4</i>白帽子讲WEB安全</a></li>
										<li class="item"><a href="#"><i>5</i>集体智慧编程</a></li>
										<li class="item list-more"><a href="#">更多&gt;&gt;</a></li>
									</ul>
								</div>
							</div>
						</div>
						<!--小说站点推荐-->
						<div class="link-item-list">
							<div class="cate">
								<div class="cate-title">小说站点推荐</div>
								<div class="cate-cont">
									<ul>
										<li><a href="http://nd3g.oeeee.com/">奇幻修行</a></li>
										<li><a href="http://3g.163.com/news/">网易</a></li>
										<li><a href="http://m.chinanews.com/iphone/">中新</a></li>
										<li><a href="http://qidian.cn/wap2/index.do?">起点</a></li>
										<li><a href="http://3g.17k.com/">17k</a></li>
										<li><a href="http://wap.hongxiu.com/">红袖</a></li>
										<li><a href="http://m.sohu.com/c/3/">搜狐</a></li>
										<li><a href="http://3g.163.com/sports/">网易</a></li>
										<li><a href="http://3g.ifeng.com/sports/sportsi?dh=touch">凤凰</a></li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<footer id="footer">
		<p class="copyright">Copyright© 2012 粤ICP备05087105号</p>
	</footer>
</body>
</html>
