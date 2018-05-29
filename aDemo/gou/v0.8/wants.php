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
			<div class="back-page">
				<a href="index.php"><img src="<?php echo $appPic;?>/btn_backPage.png" alt="" /></a>
			</div>
			<h2>我的账户</h2>
		</header>
		
		<article class="ac J_skipRight" data-toUrl="account.php">
			<div class="account-tab webkitbox">
				<a class="selected">想要的商品</a>
				<a href="account.php">账号设置</a>
			</div>
			
			<div class="item-list">
				<ul class="oz">
					<?php for($i=0; $i<6; $i++){?>
					<li>
						<a href="" class="webkitbox">
							<div class="pic"><img src="<?php echo $appPic;?>/pic_searImg.jpg" alt="" /></div>
							<div class="desc item">
								韩版女装长款短袖T恤
								<span class="price">￥480.00</span>
								<span class="sale">324人想要</span>
							</div>
						</a>
						<div class="buy-now"><a href="">立即购买<i>&raquo;</i></a></div>
					</li>
					<?php }?>
				</ul>
				<div class="JS-loadMore"><a href="">点击加载更多</a></div>
			</div>
		</article>
	</div>
    <script id="J_itemView" type="template">
	{each data.list}
	<li>
		<a href="{$value.href}" class="webkitbox">
			<div class="pic"><img src="{$value.img}" alt="" /></div>
			<div class="desc item">
				韩版女装长款短袖T恤
				<span class="price">￥{$value.price}</span>
				<span class="sale">{$value.wants}人想要</span>
			</div>
		</a>
		<div class="buy-now"><a href="{$value.buyhref}">立即购买<i>&raquo;</i></a></div>
	</li>
	{/each}
    </script>
</body>
</html>