<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<title></title>
	<?php include '_inc.php';?>
</head>
<body>
	<div id="page">
		<header class="hd">
			<h1>反馈</h1>
			<div class="back-home"><a href="index.php">&lt;</a></div>
		</header>

		<article class="ac">
			<div class="feedback-text">
				<p>欢迎您给我们提出宝贵的意见，让我们一起把金立游戏变得更好。</p>
			</div>
			<div class="feedback-form">
				<form action="">
					<dl>
						<dt>反馈内容 <span>(不能少于6个字符)</span></dt>
						<dd><textarea name="content" pattern=".{6,500}" maxlength="10" rqd="true" placeholder="请输入您的反馈意见（字数500字以内）" data-null="请先输入您的建议" data-error="反馈内容不能少于6个字"></textarea></dd>
						<dt>联系方式 <span>(QQ号码为必填项)</span></dt>
						<dd><input type="text" name="qq" pattern="^[1-9]\d{6,11}$" rqd="true" placeholder="请留下您的QQ号码" data-null="请输入您的QQ号码" data-error="qq号码错误"> <i>*</i></dd>
						<dd><input type="text" name="phone" pattern="^((\+86)|(86))?\d{11}$" placeholder="请留下您的手机号码" data-error="手机号码错误"></dd>
					</dl>
					<div class="submit-btn"><button class="btn J_submitBtn">提交</button></div>
				</form>
			</div>
			<div class="">
				<div class="wchat" >
				<img src="<?php echo $appPic;?>/wechat.png" alt="" />
				<div>
				<p>欢迎关注我们的微信公共账户！</p>
				<p>帐号：JXH-20121220</p>
			</div>
			</div>
		</article>
		<div class="goTop J_gotoTop">
			<a ></a>
		</div>
	</div>
	<div class="J_tipBox tip-box invisible">
		<p></p>
		<div class="mask"></div>
	</div>
	<?php include '_icat.php';?>
</body>
</html>