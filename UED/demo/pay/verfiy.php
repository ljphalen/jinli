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
				<div class="ui-toolbar-left">
					<a class="ui-toolbar-backbtn" href="index.php">返回</a>
				</div>
				<div class="ui-toolbar-title"><h1>身份验证</h1></div>
			</div>
		</div>
	</header>
	<article>
	<div class="J_tipBox tip-box">账号密码错误，请重新输入</div>
	<form action="###" method="post">	
		<div class="verify">
			<div class="info-input">
				<p>
				<span>账号：</span>
				<input type="text" placeholder="13421827169" />
				</p>
				<p>
				<span>密码：</span>
				<input type="text" placeholder="请输入密码" />
			</p>
			</div>
			<div class="verify-code">
				<div class="code"><input type="text" placeholder="请输入验证码" /></div>
				<span>FFBD</span><a>换一张</a>
				
			</div>
			<div class="back">
				<input type="submit" name="submit" class="btn" value="登录" onclick="var tip=document.getElementsByClassName('J_tipBox')[0];tip.style.display='block';setTimeout(function(){tip.style.display='none';},3000);return false;" />
			</div>
		</div>
	</form>
	</article>
</div>
</body>
</html>