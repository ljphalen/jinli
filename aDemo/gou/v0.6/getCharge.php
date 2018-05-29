<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<title></title>
	<?php include '_inc.php'; ?>
</head>
<body>
	<div data-role="page" data-theme="no" id="page" class="gcharge">
		<header id="header" class="hd">
			<h1><strong>晒单拿话费</strong></h1>
			<p><a href="index.php"><img src="<?php echo $appPic; ?>/btn_backhome.png" alt="" /></a></p>
		</header>
		
		<div id="content" class="ct">
			<div class="desc">
				<ul>
					<li><s></s><span>晒单入口开启时间：每天上午10:00-12:00，下午15:00-17:00.</span></li>
				</ul>
				<ul class="J_toggleUL">
					<li><s></s><span>你晒订单，我送话费！</span></li>
					<li><s></s><span>通过金立购提供的链接下单后，可以在晒单区晒单，在每个开启时间段内前5名有效晒单的用户，订单确认收货后，会收到10-30元不等的话费奖励。(无效订单不计入名额范围内)</span></li>
					<li><s></s><span>订单金额在50-300元间，订单确认收货将获得10元话费奖励！订单满300元以上，将获得30元话费奖励。</span></li>
					<li><s></s><span>活动最终解释权归金立购运营团队所有。</span></li>
				</ul>
				<div class="JS-handle">
					<span class="wrap"><span>展开</span></span>
				</div>
			</div>
			
			<div class="box">
				<h2><em>晒单达人</em></h2>
				<p class="tip"><span>(已核实0人，还有5个名额)</span></p>
				<div class="list">
					<ul>
						<?php for($i=0; $i<3; $i++){?>
						<li>
							<p>135*****212王**在酒仙网成功下单，订单号32019******92<s>2012-07-28 11:32</s></p>
						</li>
						<?php }?>
					</ul>
				</div>
				<div class="btn">
					<a href="showOrder.php"><span>立马去晒单</span></a>
				</div>
			</div>
			
			<div class="box">
				<h2><em>已发奖</em></h2>
				<div class="list">
					<ul>
						<?php for($i=0; $i<3; $i++){?>
						<li>
							<p>135*****212王**下单信息已核实，获得<em>30</em>元话费奖励</p>
						</li>
						<?php }?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</body>
</html>