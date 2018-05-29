<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<title></title>
	<?php include '_inc.php'; ?>
</head>
<body>
	<style type="text/css">
		.item-box h2{padding:.8em 0 .8em 3em; background:#e9e9e9;}
		.item-box h2 strong{font-size:2em; font-weight:normal;}
		.item-box a span{font-size:2em; color:#005fd4;}
		.item-box li{margin:1em 0; padding-left:3.4em;}
		.item-box .pic{display:inline-block; border:solid 1px #c4c4c4; padding:1px;}
		.item-box .pic img{width:25.5em; height:auto;}
		.item-box .links{margin-right:2em;}
		.item-box .links a{display:inline-block; width:33%; padding:.8em 0; text-align:center;}
		.item-box .links a span{text-decoration:underline;}
		.item-box .keys{margin-left:3em; padding-bottom:.8em; overflow:hidden; *zoom:1;}
		.item-box .keys a{float:left; width:9em; margin-top:.8em; border-right:1px solid #000; text-align:center;}
	</style>
	<div id="page" class="simple-home">
		<div class="item-box">
			<h2><strong>【最新优惠商品】</strong></h2>
			<ul>
				<li><a class="pic" href=""><img src="<?php echo $appPic; ?>/pic_bbanner1.jpg" alt="" /></a></li>
				<?php for($i=0; $i<4; $i++){?>
				<li><a href=""><span>精品玛瑙项链仅需169</span></a></li>
				<?php }?>
			</ul>
		</div>
		<div class="item-box">
			<h2><strong>【编辑推荐产品】</strong></h2>
			<ul>
				<?php for($i=0; $i<4; $i++){?>
				<li><a href=""><span>精品玛瑙项链仅需169</span></a></li>
				<?php }?>
			</ul>
		</div>
		<div class="item-box">
			<h2><strong>【百宝箱】</strong></h2>
			<div class="links">
				<a href=""><span>话费快充</span></a>
				<a href=""><span>彩票</span></a>
				<a href=""><span>图书馆</span></a>
			</div>
		</div>
		<div class="item-box">
			<h2><strong>【流行风向标】</strong></h2>
			<ul>
				<li><a class="pic" href=""><img src="<?php echo $appPic; ?>/pic_bbanner2.jpg" alt="" /></a></li>
				<?php for($i=0; $i<3; $i++){?>
				<li><a href=""><span>精品玛瑙项链仅需169</span></a></li>
				<?php }?>
			</ul>
		</div>
		<div class="item-box">
			<h2><strong>【女人看这里】</strong></h2>
			<div class="keys">
				<a href=""><span>话费快充</span></a>
				<a href=""><span>彩票</span></a>
				<a href=""><span>图书馆</span></a>
				<a href=""><span>话费快充</span></a>
				<a href=""><span>彩票</span></a>
				<a href=""><span>图书馆</span></a>
				<a href=""><span>话费快充</span></a>
				<a href=""><span>彩票</span></a>
			</div>
		</div>
		<div class="item-box">
			<h2><strong>【男人看这里】</strong></h2>
			<div class="keys">
				<a href=""><span>话费快充</span></a>
				<a href=""><span>彩票</span></a>
				<a href=""><span>图书馆</span></a>
				<a href=""><span>话费快充</span></a>
				<a href=""><span>彩票</span></a>
				<a href=""><span>图书馆</span></a>
				<a href=""><span>话费快充</span></a>
				<a href=""><span>彩票</span></a>
			</div>
		</div>
		<div class="item-box">
			<h2><strong>【猜你还喜欢】</strong></h2>
			<div class="keys">
				<a href=""><span>话费快充</span></a>
				<a href=""><span>彩票</span></a>
				<a href=""><span>图书馆</span></a>
				<a href=""><span>话费快充</span></a>
				<a href=""><span>彩票</span></a>
				<a href=""><span>图书馆</span></a>
				<a href=""><span>话费快充</span></a>
				<a href=""><span>彩票</span></a>
			</div>
		</div>
		<div class="item-box">
			<h2><strong>【有话要说】</strong></h2>
			<div class="links">
				<a href=""><span>有话要说</span></a>
			</div>
		</div>
	</div>
</body>
</html>