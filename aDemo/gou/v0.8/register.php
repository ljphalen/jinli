<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>金立购—触屏版</title>
	<?php include '_inc.php';?>
</head>
<body>
	<div id="page">
		<header class="hd">
			<h2>登录注册</h2>
			<div class="back-page">
				<a href="index.php"><img src="<?php echo $appPic;?>/btn_backPage.png" alt="" /></a>
			</div>
		</header>
		
		<article class="ac">
			<div class="banner">
				<a href=""><img src="<?php echo $appPic;?>/pic_bnLogin.jpg" alt="" /></a>
			</div>
			
			<div class="form-box">
				<form action="json.php">
					<ul>
						<li>
							<label for="">账号：</label>
							<input type="text" name="" placeholder="请输入手机号" required="required" data-null="手机号码不能为空" pattern="^1[3458]\d{9}$" data-error="手机号码格式不正确" value="" />
						</li>
						<li>
							<label for="">密码：</label>
							<input type="password" name="" placeholder="至少6位" value="" />
							<input type="text" placeholder="至少6位" value="" class="JS-showPwd" />
						</li>
						<li class="extra J_showPwd"><input type="checkbox" name="" /> 明文显示密码</li>
						<li class="btn"><button>注册</button></li>
					</ul>
				</form>
			</div>
		</article>
	</div>
	
	<div class="JS-dbMask"></div>
    <div class="dialog-box J_dialogBox">
    	<p></p>
    	<div class="btn"><span>取消</span></div>
    </div>
</body>
</html>