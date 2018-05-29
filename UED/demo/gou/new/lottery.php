<!DOCTYPE HTML>
<?php include '_cfg.php';?>
<html<?php echo ($ucClass? 'class="'.$ucClass.'"' : '');?>>
<head>
	<meta charset="UTF-8">
	<title>幸运大抽奖</title>
	<?php include '_incfile.php';?>
</head>

<body id="lottery" data-pagerole="body">
	<div class="module">
		<section class="lottery-main">
			<div class="ly-plate">
				<div class="roulette" id="J_lotteryBoard"></div>
				<div class="pointer abled" id="J_lotteryBtn" data-ajaxurl="api/gou/lottery-api.php"></div>
				<div class="scroll-text" id="J_scrollText">
					<ul class="wrap">
						<li>恭喜134********获得旅行包</li>
						<li>恭喜185********获得T恤</li>
						<li>恭喜156********获得50元话费</li>
					</ul>
				</div>
			</div>
			<div class="ly-rule">
				<div class="item">
					<h3>活动规则</h3>
					<p>
						1、每位朋友只可抽奖一次，中奖率100%；<br/>
						2、请填写并提交正确的手机号和微信名称；<br/>
						3、中奖后请加微信：shopping8019，与小惠核实信息；<br/>
						4、此次活动最终解释权归购物大厅所有。<br/></p>
				</div>
				<div class="item">
					<h3>兑奖流程</h3>
					<p>
						1、输入正确的手机号和微信名称，提交兑奖信息；<br/>
						2、加入微信：shopping8019，与小惠核实信息；<br/>
						3、奖品将在活动结束后7个工作日内发放。</p>
				</div>
			</div>
		</section>
	</div>
	<div class="full-mask J_fullMask"></div>
	<div class="dialog-box J_dialogBox"></div>
</body>
</html>