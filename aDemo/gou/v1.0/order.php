<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>金立购—触屏版</title>
<?php include '_inc.php';?>
</head>

<body>
<section id="page">
	<?php include '_header.php';?>
	<!-- 主体内容 -->
	<section id="content">
		<section class="order">
			<section class="create">
				<ul class="mod-text-list">
					<li>
						<ul class="mod-tw-list">
							<li><img src="<?php echo $appRef;?>/pic/goods-detail-230-310.jpg" alt="" /></li>
							<li>
								<h3>润本宝宝清新花露水</h3>
								<p>单价：<span>15.00元</span></p>
							</li>
						</ul>
					</li>
					<li>
						<a href="order-cnee.php" class="r-arrow">
							<dl class="mod-dl-list">
								<dd>收货人：王富帅</dd>
								<dd>手机：1866579815821</dd>
								<dd>收货地址：广东省深圳市福田区 南海大道7888号东海国际中心</dd>
								<dd>邮编：510000</dd>
								<input type="hidden" value="1" class="J_cneeInfoFlag" />
							</dl>
						</a>
					</li>
					<li>
						<dl class="mod-dl-list">
							<dd>购买数量：<input type="number" min='1' max='999999' value="1" class="quantity orderInput" style= "ime-mode:Disabled" /><span class="fr subtotal">28.00元</span></dd>
							<dd class="mr-t10"><strong class="orange-dashed">限购<span class="maxLimitNum">5</span>件</strong></dd>
					</li>
					<li>
						<dl class="mod-dl-list">
							<dd>可用银币：<input type="number" value="19.00" min='1' max='999999' class="coinNum orderInput" /><!-- <em>19.00元</em> --><span class="fr subcointotal">-19.00元</span></dd>
							<dd class="mr-t10"><strong class="orange-dashed">账户可用银币余额为<span class="maxCoinLimit">38.00</span>元</strong></dd>
						</dl>
					</li>
					<li>
						<dl class="mod-dl-list">
							<dd>实际付款：<span class="total-price">9.00元</span></dd>
							<dd>支付方式：货到付款</dd>
							<dd class="button mr-t40"><button type="button" id="J_createOrder" data-ajaxUrl="json.php" class="btn orange">确认订单</button></dd>
						</dl>
					</li>
				</ul>
			</section>
		</section>
	</section>
	<!--  /主体内容 -->
	<?php include '_footer.php';?>
</section>
</body>
</html>