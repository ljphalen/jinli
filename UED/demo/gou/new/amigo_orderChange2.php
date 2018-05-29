<!DOCTYPE HTML>
<?php include '_cfg.php';?>
<html<?php echo ($ucClass? ' class="'.$ucClass.'"' : '');?>>
<head>
	<meta charset="UTF-8">
	<title>退/换货</title>
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
					<h1>退/换货</h1>
					<span class="voice"></span>
				</div>
			</div>
		</header>

		<section id="iScroll">
			<div class="select-order">
				<form action="amigo_orderChange3.php">
					<div class="t">请选择需要退/换货的订单：</div>
					<input type="hidden" name="parm" data-error="请选择订单">
					<ul class="J_selectOrder">
						<?php for($i=0; $i<3; $i++){?>
						<li>
							<time>下单时间：2014-10-12 08:45</time>
							<figure class="item-pictext">
								<div class="pic">
									<span><img src="<?php echo "$webroot/$appPic/amigo_pic_orderImg.jpg";?>" alt=""></span>
								</div>
								<div class="desc">
									<h3>罗曼史移动电源10400mah</h3>
									<p class="price">￥69.99</p>
									<p class="text">收货地址：广东省深圳市深南大道7022号</p>
								</div>
								<div class="options">
									<input type="checkbox" value="<?php echo $i;?>" />
								</div>
							</figure>
						</li>
						<?php }?>
					</ul>
					<div class="f web-btn"><button class="J_formVerify">下一步</button></div>
				</form>
			</div>
		</section>
	</div>
</body>
</html>