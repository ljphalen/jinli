<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection" content="telephone=no" />
<title>Elife-网址导航</title>
<?php include "../../_inc.php"; ?>
<link id="linkMain" rel="stylesheet" type="text/css" href="<?php echo $staticPath;?>/apps/3g/elife/assets/css/e3.nav.source.css">
<script src="<?php echo $staticPath;?>/sys/icat/1.1.5/icat.js"></script>
<script src="<?php echo $staticPath;?>/apps/3g/elife/assets/js/e3.nav.source.js"></script>
</head>

<body>
<div class="wrapper">
	<header>
		<nav>
			<ul class="nav-tab" id="J_nav_tab">
				<li class="t1 sel" data-id="1" data-remote="<?php echo $webroot; ?>/3g/api/nav_e3_data.json">
					<div>
						<i class="ui-nav-icon"><img src="<?php echo $staticPath;?>/apps/3g/elife/pic/changyong.png" width="36" height="36" /></i>
						<span class="ui-nav-icon-text">常用</span>
					</div>
				</li>
				<li class="t2 nextbg" data-id="2" data-remote="<?php echo $webroot; ?>/3g/api/nav_e3_data.json">
					<div>
						<i class="ui-nav-icon"><img src="<?php echo $staticPath;?>/apps/3g/elife/pic/zixun.png" width="36" height="36" /></i>
						<span class="ui-nav-icon-text">咨询</span>
					</div>
				</li>
				<li class="t3" data-id="3" data-remote="<?php echo $webroot; ?>/3g/api/nav_e3_data.json">
					<div>
						<i class="ui-nav-icon"><img src="<?php echo $staticPath;?>/apps/3g/elife/pic/yuedu.png" width="36" height="36" /></i>
						<span class="ui-nav-icon-text">阅读</span>
					</div>
				</li>
				<li class="t4" data-id="4" data-remote="<?php echo $webroot; ?>/3g/api/nav_e3_data.json">
					<div>
						<i class="ui-nav-icon"><img src="<?php echo $staticPath;?>/apps/3g/elife/pic/shipin.png" width="36" height="36" /></i>
						<span class="ui-nav-icon-text">娱乐</span>
					</div>
				</li>
				<li class="t5" data-id="5" data-remote="<?php echo $webroot; ?>/3g/api/nav_e3_data.json">
					<div>
						<i class="ui-nav-icon"><img src="<?php echo $staticPath;?>/apps/3g/elife/pic/yingyong.png" width="36" height="36" /></i>
						<span class="ui-nav-icon-text">软件</span>
					</div>
				</li>
				<li class="t6" data-id="6" data-remote="<?php echo $webroot; ?>/3g/api/nav_e3_data.json">
					<div>
						<i class="ui-nav-icon"><img src="<?php echo $staticPath;?>/apps/3g/elife/pic/gouwu.png" width="36" height="36" /></i>
						<span class="ui-nav-icon-text">生活</span>
					</div>
				</li>
			</ul>
		</nav>
	</header>
	<section class="content">
		<section id="J_nav_box"></section>
	</section>
	<section class="nav-info">
		<p>IT'S MY LIFE --elife智能手机改变生活!</p>
	</section>
</div>
</body>
</html>
