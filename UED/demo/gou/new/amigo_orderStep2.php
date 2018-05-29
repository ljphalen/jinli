<!DOCTYPE HTML>
<?php include '_cfg.php';?>
<html<?php echo ($ucClass? ' class="'.$ucClass.'"' : '');?>>
<head>
	<meta charset="UTF-8">
	<title>创建订单</title>
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
					<h1>创建订单</h1>
					<span class="voice"></span>
				</div>
			</div>
		</header>

		<section id="iScroll">
			<div class="goods-inf">
				<figure>
					<div class="pic"><img src="<?php echo "$webroot/$appPic/amigo_pic_proImg.jpg";?>" alt=""></div>
					<div class="desc">
						<dl>
							<dt>抢购AMIGO PLAY蓝牙游戏手柄专为爽快而生 安卓手柄 手机游戏手柄</dt>
							<dd>单价：<em class="f-red">￥39.9</em></dd>
							<dd>数量：1</dd>
							<dd>快递：￥0.0</dd>
						</dl>
					</div>
				</figure>
				<div class="buy-price">
					<span>应支付：<em class="f-red">￥39.9</em></span>
				</div>
			</div>

			<div class="input-inf">
				<form action="amigo_orderStep3.php">
					<div class="item">
						<h3>请填写收货信息</h3>
						<fieldset>
							<ul>
								<li><label for="">收<b></b>货<b></b>人：</label><input type="text"></li>
								<li><label for="">手<i></i>机：</label><input type="text"></li>
								<li class="J_areaWrap">
									<label for="">收货地区：</label><select name="" id=""></select>
									<select name="" id=""></select>
									<select name="" id=""></select>
								</li>
								<li><label for="">街道地址：</label><input type="text"></li>
								<li><label for="">邮编号码：</label><input type="text"></li>
								<li><label for="">留<i></i>言：</label><input type="text"></li>
							</ul>
						</fieldset>
					</div>
					<div class="item">
						<h3>请填写支付方式</h3>
						<fieldset>
							<ul class="pay-ways">
								<li>
									<label for="">在线支付</label>
									<span><input type="radio" name="pay"></span>
								</li>
								<li>
									<label for="">货到付款</label>
									<span><input type="radio" name="pay"></span>
								</li>
							</ul>
						</fieldset>
					</div>
					<div class="web-btn J_formSubmit"><button>下一步</button></div>
				</form>
			</div>
		</section>
	</div>
</body>
</html>