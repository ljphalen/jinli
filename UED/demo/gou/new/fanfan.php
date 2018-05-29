<!DOCTYPE HTML>
<?php include '_cfg.php';?>
<html<?php echo ($ucClass? ' class="'.$ucClass.'"' : '');?>>
<head>
	<meta charset="UTF-8">
	<title>翻翻页面</title>
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
					<h1>可爱性感女孩将称霸世界</h1>
				</div>
			</div>
		</header>

		<section id="iScroll">
			<div class="ff-banner">
				<div class="pic">
					<a href=""><img src="<?php echo "$webroot/$appPic/ff_pic_topad.jpg";?>" alt=""></a>
				</div>
				<div class="desc">
					奢侈品已经不神秘，但是奢侈品的制造过程依然充满疑问。有一种认识是，奢侈品牌花大量金钱在推广与造势上，其制造成本只占到售价很小的比例。特别是服装、鞋子或包包，到底有多少量。
				</div>
			</div>
			<div class="ff-three">
				<div class="pic">
					<dl>
						<dt>
							<a href=""><img src="<?php echo "$webroot/$appPic/ff_pic_thimg1.jpg";?>" alt=""></a>
						</dt>
						<dd>
							<a href=""><img src="<?php echo "$webroot/$appPic/ff_pic_thimg2.jpg";?>" alt=""></a>
							<a href=""><img src="<?php echo "$webroot/$appPic/ff_pic_thimg3.jpg";?>" alt=""></a>
						</dd>
					</dl>
				</div>
				<div class="desc">
					事实上生产制造的质量控制比起品牌推广困难许多，而人才培养和管理能力更需要很长时间的积淀。为了培养制表师，卡地亚在拉夏德芳。
				</div>
			</div>
			<div class="ff-list">
				<ul>
					<?php for($i=0; $i<6; $i++){?>
					<li><a href=""><img src="<?php echo "$webroot/$appPic/ff_pic_listImg.jpg";?>" alt=""></a></li>
					<?php }?>
				</ul>
				<div class="web-btn"><a href="">更多可爱类服装 &gt;&gt;</a></div>
				<div class="more-links">
					<h3>更多精彩内容</h3>
					<div>
						<a href="">打底裤</a>
						<a href="">连衣裙</a>
						<a href="">打底毛衫</a>
					</div>
				</div>
			</div>
			<div class="ff-share">
				<div class="inner">
					<header><em>分享到</em></header>
					<div class="links">
						<span>
							<img src="<?php echo "$webroot/$appPic/ff_ico_weixin.png";?>" alt="">
							<em>微信好友</em>
						</span>
						<span>
							<img src="<?php echo "$webroot/$appPic/ff_ico_weixinp.png";?>" alt="">
							<em>微信朋友圈</em>
						</span>
						<span>
							<img src="<?php echo "$webroot/$appPic/ff_ico_weibo.png";?>" alt="">
							<em>新浪微博</em>
						</span>
					</div>
				</div>
			</div>
		</section>
	</div>
</body>
</html>