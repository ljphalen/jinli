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
		<div class="wrapper">
			<div class="pnav-list">
				<!-- 页面内容区域 -->
				<!-- <?php include "../_header.php"; ?> -->
				<!-- 导航链接列表 -->
				<div class="module">
					<div class="nav-link-list">
						<?php for($i = 0; $i < 5; $i++): ?>
							<div class="link-item-list">
								<div class="cate">
									<div class="cate-title">新闻 资讯</div>
									<div class="cate-cont">
										<ul>
											<li><a href="http://nd3g.oeeee.com/">南都</a></li>
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
						<?php endfor; ?>
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
