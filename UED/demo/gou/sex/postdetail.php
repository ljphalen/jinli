<!DOCTYPE HTML>
<?php include '_cfg.php';?>
<html class="<?php echo $ucClass;?>">
<head>
	<meta charset="UTF-8">
	<title>demo</title>
	<?php include '_inc.php';?>
</head>

<body id="detail" data-pagerole="body">
	<div class="module">
		<section id="iScroll">
			<div class="post-detail">
				<h2>范冰冰回应神秘钻戒事件 自己送自己的生日礼物.</h2>
				<em class="sub-title">挑战性官方 10-22 11:32</em>
				<p>范冰冰（Bingbing Fan）， 中国著名女演员、歌手，国际影星。出生于青岛，毕业于上海师范大学谢晋影视艺术学院。1998年参演琼瑶剧《还珠格格》一举成名。</p>
				<p>2010年5月13日，范冰冰亮相“戛纳国际电影节”，被评为当届“戛纳电影节”最佳着装女星，这位中国美女谋杀无数菲林。</p>
				<p><span><img src="<?php echo "$webroot/$appPic/post_pic.jpg";?>" alt=""></span></p>
			</div>
			<div class="hot-choose">
				<h3>热门商品推荐</h3>
				<ul>
					<?php for($i=0; $i<4; $i++){?>
					<li><a href=""><img src="<?php echo "$webroot/$appPic/item_pic.jpg";?>" alt=""></a></li>
					<?php }?>
				</ul>
			</div>
			<div class="comments">
				<h4>全部评论: 2000条</h4>
				<?php for($i=0; $i<2; $i++){?>
				<div class="item">
					<div class="user-inf">
						<em>用户1881&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;10-22 11:32</em>
						<span>1楼</span>
					</div>
					<p>范冰冰通过VCR正式宣布与国内著名互联网公司网易合作，联手推出国内首款由电影级标准打造的动作武侠网游《武魂》。</p>
				</div>
				<?php }?>
			</div>
		</section>
	</div>
</body>
</html>