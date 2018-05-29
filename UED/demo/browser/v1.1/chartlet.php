<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>我的乐园-彩票天地</title>
	<?php include '_inc.php';?>
</head>
<body>
	<div id="page">
		<?php $page = '2-2'; include '_header.php';?>
		
		<article class="ac chartlet">
			<div class="pic-wrap">
			<div class="pic-box"  class="J_itemWrap" data-ajaxUrl="json.php">
				<div id="curInfo" class="hidden" curpage="1" hasnext="true"></div>
				<div class="l-wrap mr-r10">
					<ul>
						<li>
							<a href="http://i.ifeng.com/ent/photo/hotpic/news&#63;aid=44387437&amp;ch=zd_jl_llq">
								<img class="scrollLoading" src="http://res01.mimg.ifeng.com/g&#63;url=http://y0.ifengimg.com/11bc3d0677642853/2012/0929/rdn_5066435f2675f.jpg&amp;w=128&amp;h=-1&amp;v=171b997ca7&amp;r=1"  alt="" />
							</a>
						</li>
						<li>
							<a href="http://i.ifeng.com/ent/photo/hotpic/news&#63;aid=44250080&amp;ch=zd_jl_llq">
								<img class="scrollLoading" src="http://res01.mimg.ifeng.com/g&#63;url=http://y2.ifengimg.com/a900648388825563/2012/0927/rdn_5063d8b026b80.jpg&amp;w=128&amp;h=-1&amp;v=145e6c587e&amp;r=1"  alt="" />
							</a>
						</li>
						<li>
							<a href="http://i.ifeng.com/news/history/lishiyingxiangshi/news?aid=44598353&ch=zd_jl_llq">
								<img class="scrollLoading" src="http://res01.mimg.ifeng.com/g?url=http://y0.ifengimg.com/958dcda298b573f7/2012/1008/rdn_5072472ded43f.jpg&w=128&h=-1&v=d062629e2f&r=1"  alt="" />
							</a>
						</li>
					</ul>
				</div>
				<div class="r-wrap">
					<ul>
						<li>
							<a href="http://i.ifeng.com/news/history/lishiyingxiangshi/news?aid=44579385&ch=zd_jl_llq">
								<img class="scrollLoading" src="http://res01.mimg.ifeng.com/g?url=http://y2.ifengimg.com/958dcda298b573f7/2012/1008/rdn_50720dd5b489c.jpg&w=128&h=-1&v=533bbffdda&r=1"  alt="" />
							</a>
						</li>
						<li>
							<a href="http://i.ifeng.com/auto/fun/news?aid=44563228&ch=zd_jl_llq">
								<img class="scrollLoading" src="http://res01.mimg.ifeng.com/g?url=http://y0.ifengimg.com/9ff64e6dd1891f7a/2012/1007/rdn_50716c72e03d0.jpg&w=128&h=-1&v=57f659345e&r=1"  alt="" />
							</a>
						</li>
						<li>
							<a href="http://i.ifeng.com/auto/fun/chemo/news?aid=44563227&ch=zd_jl_llq">
								<img class="scrollLoading" src="http://res01.mimg.ifeng.com/g?url=http://y0.ifengimg.com/9ff64e6dd1891f7a/2012/1007/rdn_50716c72e03d0.jpg&w=128&h=-1&v=57f659345e&r=1"  alt="" />
							</a>
						</li>
					</ul>
				</div>
			</div>
			<div class="btn-wrap">
				<a href="" id="J_loadPicBtn" data-appUrl="<?php echo $appPic; ?>" data-ajaxUrl="chartletdo.php" class="btn"><span>更多美图</span><img src="<?php echo $appPic;?>/loading.gif" alt="正在加载..." style="display:none;" /></a>
			</div>
		</article>
	</div>
	<script id="J_picItemView" type="template">
		{each data.list}
		<li>
			<a href="{$value.href}">
				<img class="scrollLoading" xSrc="{$value.img}" src="<?php echo $appPic;?>/loading.gif" alt="" />
			</a>
		</li>
		{/each}
	</script>
</body>
</html>