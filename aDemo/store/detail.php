<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>金立游戏</title>
	<?php include '_inc.php'?>
</head>
<body>
	<div id="page" data-role="page" data-theme="no" class="detail">
		<header>
			<h2><strong>游戏介绍</strong></h2>
			<div class="back"><a href="index.php" data-ajax="false"><span>返回</span></a></div>
		</header>
		<div class="game-title clearfix">
			<div class="inf">
				<h1><strong>应用名称：QQ游戏 2012</strong></h1>
				<p><span>公司：腾讯</span></p>
				<p><span>大小：4.5MB</span></p>
			</div>
			<div class="pic">
				<div class="icon"><img src="<?php echo $appPic?>/pic_storeApp.jpg" alt="" /></div>
				<a href="" class="download"><span>下载</span></a>
			</div>
		</div>
		<div class="game-intro">
			<h2><strong>应用详情介绍</strong></h2>
			<p><span>鲁大师安卓版是一款给安卓手机、平板进行性能评测、跑分的软件。通过八大测试，让你对爱机了如指掌。</span></p>
			<p class="JS-hide"><span>1、可查看整体和单项硬件的性能得分，通过分数判断各硬件的性能。<br />2、可查看本机操作系统的详细信息，包话SD卡容量，CPU型号、频率，系统版本号等多项信息。<br />3、可上传分数并查看排名，通过排名查看分数高的机型，也可查看当前手机的排名位置。</span></p>
			<div class="JS-readMore"><span class="link"><span>展开<i>&gt;&gt;</i></span></span></div>
		</div>
		<div class="game-pic JS-swapPic">
			<div class="pic-wrap">
				<ul>
					<?php for($i=0; $i<2; $i++){?>
					<li><img src="<?php echo $appPic?>/pic_storeBimg.jpg" alt="" /></li>
					<?php }?>
				</ul>
			</div>
			<!-- <span class="handle"></span>
			<div class="mask"></div>
			<div class="range"><input type="range" value="0" min="0" max="100" /></div>
			<div class="mask"></div>
			<input id="J_handle" type="range" name="slider" value="0" min="0" max="5" data-theme="handle" /> -->
		</div>
		<div class="contact">
			<p>客服邮箱：game@gionee.com<br/>客服QQ:1732001808</p>
		</div>
	</div>
</body>
</html>