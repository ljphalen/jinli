<!DOCTYPE HTML>
<?php include '_cfg.php';?>
<html<?php echo ($ucClass? 'class="'.$ucClass.'"' : '');?>>
<head>
	<meta charset="UTF-8">
	<title>market application</title>
	<?php include '_incfile.php';?>
</head>

<body id="subject_goods" data-pagerole="body">
	<div class="mainWrap">
		<header id="iHeader" class="hd">
			<div class="top-wrap">
				<div class="title">
					<a href="http://gou.3gtest.gionee.com?t_bi=_770570940" class="back"></a>
					<h1>春运神器</h1>
				</div>
			</div>
		</header>
	</div>
	<article class="ac">
		<div class="banner">
			<a href="javascript:void(0);">
				<img src="http://gou.3gtest.gionee.com/attachs/gou/subject/201307/171217.jpg" alt="">
			</a>
		</div>
	</article>
	<div class="wrap">
		<div id="waterfall" data-ajaxurl="subject_goods_api.php"></div>
	</div>
</body>
</html>