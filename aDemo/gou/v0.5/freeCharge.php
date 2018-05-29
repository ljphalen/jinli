<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<title></title>
	<?php include '_inc.php'; ?>
</head>
<body>
	<div data-role="page" data-theme="no" id="page" class="free-charge">
		<header id="header" class="hd">
			<h1><strong>免费赢话费</strong></h1>
			<p><a href="index.php"><img src="<?php echo $appPic; ?>/btn_backhome.png" alt="" /></a></p>
		</header>
		
		<div id="content" class="ct">
			<section>
				<p class="tip"><span>恭喜您抽中了100元话费，请输入您参加活动时的手机号和通关密语，以便核实您的身份。</span></p>
				<form class="form-box" action="">
					<ul>
						<li><label for=""><em>手机号：</em></label><input type="text" name="" class="l" value="137" /></li>
						<li><label for=""><em>通关密语：</em></label>
							<select name="">
								<option value="">你最想去的城市是？</option>
								<option value="standard">深圳</option>
								<option value="rush">伦敦</option>
								<option value="express">纽约</option>
								<option value="overnight">新加坡</option>
							</select>
							<input type="text" name="" class="l" /></li>
						<li><button data-role="none"><span>确定</span></button></li>
					</ul>
				</form>
			</section>
		</div>
	</div>
</body>
</html>