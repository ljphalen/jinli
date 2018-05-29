<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>注册</title>
	<?php include '_inc.php';?>
</head>
<body>
	<div id="page">
		<?php $title = '注册'; include '_sheader.php';?>
		
		<article class="ac reg-login">
			<div class="form-box">
				<form action="json.php" id="J_login">
					<ul>
						<li>
							<label for="">用户名：</label>
							<input type="text" name="username" id="J_UserNameTxt" vaule="" autocomplete="off" placeholder="您的手机号" pattern="((\+86)|86|0)?(13\d|15[012356789]|18[012356789]|14[57])\d{8}$"  />
						</li>
						<li>
							<label for="">密码：</label>
							<input type="password" name="password" maxLength="16" id="J_PassWordTxt" autocomplete="off" value="" placeholder="至少6位"  />
							<input type="text" name="passText" autocomplete="off" value="" class="J_showText" />
						</li>
						<li class="tips">
							<span><input type="checkbox" id="checkbox_pwd" name="pwd" class="J_ctrlShow" /></span>
							<span><label for="checkbox_pwd">密码显示明文</label></span>
						</li>
						<li><input type="submit" name="submit" class="btn" value="注册" /></li>
					</ul>
				</form>
			</div>
		</article>
	</div>
	
	<!--<div class="JS-dbMask"></div>
    <div class="dialog-box J_dialogBox">
    	<p></p>
    	<div class="btn-wrap"><span class="btn">取消</span></div>
	</div>-->
	
</body>
</html>