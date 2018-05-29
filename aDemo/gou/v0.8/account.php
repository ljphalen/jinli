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
			<div class="back-page">
				<a href="index.php"><img src="<?php echo $appPic;?>/btn_backPage.png" alt="" /></a>
			</div>
			<h2>我的账户</h2>
		</header>
		
		<article class="ac J_skipLeft" data-toUrl="wants.php">
			<div class="account-tab webkitbox">
				<a href="wants.php">想要的商品</a>
				<a class="selected">账号设置</a>
			</div>
			
			<div class="account-inf">
				<ul>
					<li>
						<h3>基本信息</h3>
						<div class="desc">
							<p>
							账号:1865975457465<br />
							姓名:周杰伦<br />
							性别:男<br />
							生日:1980年5月21日
							</p>
							<div class="edit"><a href="baseinf.php">编辑<i>&raquo;</i></a></div>
						</div>
					</li>
					<li>
						<h3>第三方账号绑定</h3>
						<div class="desc">
							<p>淘宝账号:Jay_chau</p>
							<div class="edit"><a href="">换绑<i>&raquo;</i></a></div>
						</div>
					</li>
					<li>
						<h3>收货人信息</h3>
						<div class="desc">
							<p>广东省深圳市福田区深南大道7888号东海国际中心B幢12层</p>
							<div class="edit"><a href="consigneeaddr.php">编辑<i>&raquo;</i></a></div>
						</div>
					</li>
				</ul>
				<div class="log-out"><a href="" class="J_showDialog">退出登录</a></div>
			</div>
		</article>
	</div>
    
    <div class="JS-dbMask"></div>
    <div class="dialog-box J_dialogBox">
    	<p>确定退出金立购</p>
    	<div class="btn"><a href="">确定</a><span>取消</span></div>
    </div>
</body>
</html>