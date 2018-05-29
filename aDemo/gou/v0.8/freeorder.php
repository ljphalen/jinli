<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>金立购—触屏版</title>
	<?php include '_inc.php';?>
</head>
<body>
	<div id="page">
		<header class="hd">
			<h2>往期免单名单</h2>
			<div class="back-page">
				<a href="index.php"><img src="<?php echo $appPic;?>/btn_backPage.png" alt="" /></a>
			</div>
			<div class="view-rule"><a href="freerule.php">免单规则</a></div>
		</header>
		
		<article class="ac">
			<div class="free-order-box J_slideAjax" data-ajaxUrl="jsonFree.php"></div>
		</article>
	</div>
    <script id="J_itemView" type="template">
		<div class="J_slideItem" pid="{data.curpage}" hasnext="{data.hasnext}" style="position:absolute; width:100%; left:100%;">
	    	<div class="free-period">
				<div class="title">
					<h3>免单{data.list.number}期</h3>
					<time>{data.list.create_time.replace(/@/g,'<i></i>')}更新</time>
				</div>
				<div class="main">
					<span class="handle"><img src="<?php echo $appPic;?>/ico_arrow.png" alt="" /></span>
					<span class="handle r"><img src="<?php echo $appPic;?>/ico_arrow.png" alt="" /></span>
					<div class="pic">
						<a href="{data.list.link}">
							<span><img src="{data.list.img}" alt="" /></span>
							<em>{data.list.title}</em>
						</a>
					</div>
					<div class="desc">
						<span class="price">￥{data.list.price}</span>
						<span class="sale">{data.list.want_count}人想要</span>
						<a href="{data.list.link}">查看商品详情</a>
					</div>
				</div>
			</div>
			<section class="free-list">
				<h3>成功免单用户（{data.list.order_free_num}名）</h3>
				<ul>
					{each data.list.order_free}
					<li>
						会员:<em>{$value.username}</em><br />
						想要{!$value.want_num? 0:1}件商品，已免单{!$value.free_num? 0:1}件商品
					</li>
					{/each}
				</ul>
			</section>
	    </div>
	</script>
</body>
</html>