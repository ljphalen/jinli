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
				<form action="">
					<ul>
						<li>
							<label for="">账号：</label>
							<input type="text" name="" placeholder="请输入手机号" value="" />
						</li>
						<li>
							<label for="">密码：</label>
							<input type="password" name="" placeholder="至少6位" value="" />
						</li>
						<li class="btn"><button>登录</button></li>
						<li class="extra">还没有账号？<a href="register.php">立即注册</a></li>
					</ul>
				</form>
			</div>
		</article>
	</div>
</body>
</html>