<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<title></title>
	<?php include '_inc.php'; ?>
</head>
<body>
	<div data-role="page" data-theme="no" id="page" class="sorder">
		<header id="header" class="hd">
			<h1><strong>晒单</strong></h1>
			<p><a href="index.php"><img src="<?php echo $appPic; ?>/btn_backhome.png" alt="" /></a></p>
		</header>
		
		<div id="content" class="ct">
			<section>
				<form class="form-box" action="">
					<div class="tip">
						<p>0000</p>
					</div>
					<ul>
						<li><label for=""><em>收货人：</em></label><input type="text" name="user" class="l" placeholder="张三" required="required" data-null="收货人不能为空" /></li>
						<li><label for=""><em>手机号：</em></label><input type="text" name="mobile" class="l" placeholder="137..." pattern="^((\+86)|86|0)?(13\d|15[012356789]|18[0236789]|14[57])\d{8}$" required="required" data-null="手机号不能为空" data-error="手机号错误"  /></li>
						<li><label for=""><em>下单网站：</em></label>
							<span class="select-main">
								<select name="website">
									<option value="letao">乐淘网</option>
									<option value="taobao">淘宝网</option>
								</select>
							</span><span class="select-handle"><s></s></span>
						</li>
						<li><label for=""><em>订单号：</em></label><input type="text" name="order" class="l" /></li>
						<li><button type="button" id="J_submitForm" data-role="none" class="btn"><span>确定</span></button></li>
						
					</ul>
				</form>
			</section>
		</div>
		
		<div id="J_dialogBox" class="dialog-box">
			<p><span>恭喜您，晒单信息已提交，我们会尽快进行核实，订单成功后3天个工作日内，我们会将相应额度话费充值至您的手机上！</span></p> 
			<div class="btn">
				<a><span>继续晒单</span></a>
			</div>
		</div>
		<div class="dialog-mask"></div>
	</div>
</body>
</html>