<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<title></title>
	<?php include '_inc.php';?>
</head>
<body>
<div id="page">
	<header>
		<div class="ui-toolbar">
			<div class="ui-toolbar-wrap">
				<div class="ui-toolbar-title single-title"><h1>金币支付</h1></div>
			</div>
		</div>
	</header>

	<section class="content">
		<section class="order-info has-border-bottom">
			<ul>
				<li>商品名称：家纺</li>
				<li>订单金额：<strong>243</strong>元</li>
			</ul>
		</section>

		<section class="ui-form ui-form-login">
			<form action="###" method="post">
				<div class="ui-login-tips"><p class="ui-login-error">账号密码错误，请重新输入</p></div>
				<div class="ui-form-field">
					<div class="ui-form-card">
						<div><label>金币余额：</label></div>
						<div></div>
					</div>
				</div>
				<!-- 当选择某类卡时显示 START-->
				<div class="ui-form-field">
					<div class="ui-form-input">
						<div><label>账号：</label></div>
						<div><input type="text" name="number" autocomplete="off" placeholder="请输入您的11位手机号码" /></div>
					</div>
				</div>

				<div class="ui-form-field">
					<div class="ui-form-input">
						<div><label>密码：</label></div>
						<div><input type="text" name="number" autocomplete="off" placeholder="请输入你的密码" /></div>
					</div>
				</div>

				<div class="ui-form-field">
					<div class="ui-form-img">
							<div><label>图形验证码：</label></div>
							<div>
								<input type="text" name="codeimg" autocomplete="off" />
								<span class="ui-login-codeimg"><img src="<?php echo $appPic;?>/codeimg.png" /></span>
								<a href="###">看不清?<br>换一张.</a>
							</div>
					</div>
				</div>

				<!-- 当选择某类卡时显示 END-->
				<div class="button mrt20">
					<input type="submit" name="submit" class="btn gray" value="确定支付" />
				</div>
			</form>
		</section>
	</section>
	
	<footer class="ft-smallpage"><p><a href="tel:400-779-6666">客服热线：400-779-6666</a></p></footer>
</div>
</body>
</html>