<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>登陆</title>
	<?php include '_inc.php';?>
</head>
<body>
	<div id="page">
		<?php $title = '登陆'; include '_sheader.php';?>
		
		<article class="ac reg-login">
			<div class="form-box">
				<form method="post" action="logindo.php" id="J_login">
					<ul>
						<li>
							<label for="">用户名：</label>
							<input type="text" name="username" id="J_UserNameTxt" required="required" autocomplete="off" placeholder="您的手机号" pattern="((\+86)|86|0)?(13\d|15[012356789]|18[0236789]|14[57])\d{8}$" value="" />
						</li>
						<li>
							<label for="">密码：</label>
							<input type="password" maxLength="16" name="password" id="J_PassWordTxt" required="required" />
						</li>
						<li class="tips">
							<a href="">忘记密码&gt;&gt;</a>
							<a data-brief="true" href="register.php">(注册)</a>
						</li>
						<li><input type="submit" name="submit" value="登陆" class="btn" />
						<!--<li><button class="btn">登陆</button></li>-->
						<!--<li><input type="button" class="btn" value="登陆" /></li>-->
					</ul>
				</form>
			</div>
		</article>
	</div>
</body>
</html>