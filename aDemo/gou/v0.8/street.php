<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>金立购—触屏版</title>
	<?php include '_inc.php';?>
</head>
<body>
	<div id="page" data-ajaxUrl="json.php" data-curPage="1">
		<header class="hd">
			<h2>美食甜点</h2>
			<div class="back-page">
				<a href="index.php"><img src="<?php echo $appPic;?>/btn_backPage.png" alt="" /></a>
			</div>
			<div class="head-pic">
				<a href="myAccount.php"><img src="<?php echo $appPic;?>/headpic.png" alt="" /></a>
			</div>
		</header>
		
		<article class="ac">
			<div class="banner">
				<a href=""><img src="<?php echo $appPic;?>/pic_bbanner1.jpg" alt="" /></a>
			</div>
			
			<div class="item-grid">
				<ul class="oz">
					<?php for($i=0; $i<4; $i++){?>
					<li>
						<div class="pic"><a href=""><img src="<?php echo $appPic;?>/pic_searImg.jpg" alt="" /></a></div>
						<div class="desc"><a href="">超强欧美范 蝙蝠袖 宽松大气潮T 女短袖长款T恤</a></div>
						<div class="extra"><span class="price">￥480.00</span><span class="sale">254人想要</span></div>
					</li>
					<?php }?>
				</ul>
				<div class="JS-loadMore hidden"><a href="">点击加载更多</a></div>
			</div>
		</article>
	</div>
	<script id="J_itemView" type="template">
	{each data.list}
	<li>
		<div class="pic"><a href="{$value.href}"><img src="{$value.img}" alt="" /></a></div>
		<div class="desc"><a href="">{$value.text}</a></div>
		<div class="extra"><span class="price">￥{$value.price}</span><span class="sale">{$value.wants}人想要</span></div>
	</li>
	{/each}
    </script>
</body>
</html>