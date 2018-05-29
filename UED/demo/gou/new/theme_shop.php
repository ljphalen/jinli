<!DOCTYPE HTML>
<?php include '_cfg.php';?>
<html<?php echo ($ucClass? ' class="'.$ucClass.'"' : '');?>>
<head>
	<meta charset="UTF-8">
	<title>女装</title>
	<script>var webPage = true;</script>
	<?php include '_inc.php';?>
	<link rel="stylesheet" href="<?php echo "$webroot/$appRef/assets/css/web$source.css$timestamp";?>">
</head>

<body data-pagerole="body">
	<div class="module">
		<header id="iHeader" class="hd">
			<div class="top-wrap">
				<div class="title">
					<a href="" class="back"></a>
					<h1>女装</h1>
				</div>
			</div>
		</header>
		<section class="theme-banner">
			<a class="pic J_lazyload_img" href="">
				<!-- <img src="<?php echo $webroot?>/apps/gou/pic/theme_banner.png" alt=""> -->
				<img src="<?php echo $webroot?>/apps/gou/pic/blank.gif" data-src="<?php echo $webroot?>/apps/gou/pic/theme_banner.png">
			</a>
		</section>
		<section class="theme-recom">
			<h3>推荐平台</h3>
			<ul class="pic J_lazyload_img">
				<li>
					<a href="">
						<i class="J_mask"></i>
						<span>
							<img src="<?php echo $webroot?>/apps/gou/pic/blank.gif" data-src="<?php echo $webroot?>/apps/gou/pic/theme_recom.png" alt="">
<!-- 							<img src="<?php echo $webroot?>/apps/gou/pic/theme_recom.png" alt="">
 -->						</span>
						<em class="title">爱淘宝女装</em>
					</a>
				</li>
				<li>
					<a href="">
						<i class="J_mask"></i>
						<span>
							<img src="<?php echo $webroot?>/apps/gou/pic/blank.gif" data-src="<?php echo $webroot?>/apps/gou/pic/theme_recom.png" alt="">
<!-- 							<img src="<?php echo $webroot?>/apps/gou/pic/theme_recom.png" alt="">
 -->						</span>
						<em class="title">爱淘宝女装</em>
					</a>
				</li>
				<li>
					<a href="">
						<i class="J_mask"></i>
						<span>
							<img src="<?php echo $webroot?>/apps/gou/pic/blank.gif" data-src="<?php echo $webroot?>/apps/gou/pic/theme_recom.png" alt="">
<!-- 							<img src="<?php echo $webroot?>/apps/gou/pic/theme_recom.png" alt="">
 -->						</span>
						<em class="title">爱淘宝女装</em>
					</a>
				</li>
				<li>
					<a href="">
						<i class="J_mask"></i>
						<span>
							<img src="<?php echo $webroot?>/apps/gou/pic/blank.gif" data-src="<?php echo $webroot?>/apps/gou/pic/theme_recom.png" alt="">
<!-- 							<img src="<?php echo $webroot?>/apps/gou/pic/theme_recom.png" alt="">
 -->						</span>
						<em class="title">爱淘宝女装</em>
					</a>
				</li>
				<li>
					<a href="">
						<i class="J_mask"></i>
						<span>
							<img src="<?php echo $webroot?>/apps/gou/pic/blank.gif" data-src="<?php echo $webroot?>/apps/gou/pic/theme_recom.png" alt="">
<!-- 							<img src="<?php echo $webroot?>/apps/gou/pic/theme_recom.png" alt="">
 -->						</span>
						<em class="title">爱淘宝女装</em>
					</a>
				</li>
				<li>
					<a href="">
						<i class="J_mask"></i>
						<span>
							<img src="<?php echo $webroot?>/apps/gou/pic/blank.gif" data-src="<?php echo $webroot?>/apps/gou/pic/theme_recom.png" alt="">
<!-- 							<img src="<?php echo $webroot?>/apps/gou/pic/theme_recom.png" alt="">
 -->						</span>
						<em class="title">爱淘宝女装</em>
					</a>
				</li>
			</ul>
		</section>

		<section id="iScroll">
			<div class="theme-sel J_proDetail" data-ajaxurl="api/gou/theme.php">
				<h3>本周精选</h3>
				<div class="wrap"></div>
				<script id="J_dtemplate" type="text/icat-template">
					<ul>
						<%for(var i=0, len=data.list.length; i<len; i++){%>
						<li>
							<a href="<%=data.list[i].link%>">
								<i class="J_mask"></i>
								<div class="pic">
									<img src="<%=blankPic%>" data-src="<%=data.list[i].link%>" alt="<%=data.list[i].name%>">
								</div>
								<p class="desc">
									<%=data.list[i].name%>
								</p>
							</a>
						</li>
						<%}%>
					</ul>
				</script>
			</div>
		</section>
	</div>
</body>
</html>