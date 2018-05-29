<!DOCTYPE HTML>
<?php include '_cfg.php';?>
<html<?php echo ($ucClass? ' class="'.$ucClass.'"' : '');?>>
<head>
	<meta charset="UTF-8">
	<title>商品详情</title>
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
					<h1>商品详情</h1>
					<span class="voice"></span>
				</div>
			</div>
		</header>

		<section id="iScroll">
			<div class="detail-wrap">
				<div class="pic-text-price">
					<p><img src="<?php echo "$webroot/$appPic/amigo_pic_proImage.jpg";?>" alt=""></p>
					<p>抢购AMIGO PLAY蓝牙游戏手柄专为爽快而生 安卓手柄 手机游戏手柄</p>
					<p>一口价：<em class="f-red">￥39.9</em>
						<span class="bg1">全网最低</span>
						<span class="bg2">限量100个</span>
						<span class="bg3">价格优惠</span>
					</p>
				</div>
				<form action="amigo_orderStep2.php">
					<div class="num J_quantity">数量：<span class="minus">-</span> <input type="text" data-vertiy="[1-9]\d*" data-error="数量格式不正确"> <span class="add">+</span></div>
					<div class="other-inf">
						<span>快递：￥0.0</span>
						<span>销量：1644件</span>
						<span>广东深圳</span>
					</div>
					<div class="tip-order">
						<!-- <p class="f-red">抢光了！！由于需求量太大，该商品已售完，没抢到的亲可先提前预订，先定先发货哦～！</p> -->
						<p class="f-red">现货不多了！！赶紧下单抢购哦，先买到先爽快～！</p>
						<div class="web-btn J_formSubmit"><button>立即<!-- 预订 -->抢购</button></div>
						<div class="view-order"><a href="">查询订单，请点击这里&gt;</a></div>
					</div>
				</form>
			</div>

			<div class="pro-detail J_proDetail" data-ajaxurl="amigo_api.php?page=2">
				<h3>图文详情</h3>
				<div class="wrap"></div>
				<script id="J_dtemplate" type="text/icat-template">
					<ul>
						<%for(var i=0, len=data.list.length; i<len; i++){%>
						<li><img src="<%=data.list[i]%>" alt=""></li>
						<%}%>
					</ul>
				</script>
				<div class="web-btn J_formSubmit"><button>立即<!-- 预订 -->抢购</button></div>
			</div>
		</section>
	</div>
</body>
</html>