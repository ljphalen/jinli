<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>金立购—触屏版-意见反馈</title>
<?php include '_inc.php';?>
</head>

<body>
<section id="page">
	<?php include '_header.php';?>
	<!-- 主体内容 -->
	<section id="content">
		<section class="pfeedback">
			<h3>欢迎您给我们提出宝贵的意见，让我们一起把金立购变的更好。</h3>
			<div class="pinfo mod-form-list">
				<form action="json.php" method="post" id="J_checkForm">
					<div class="field tt-input">
						<div class="numTips"></div>
						<textarea name="react" placeholder="请输入您的反馈意见（字数500字以内）" maxLength="500"></textarea>

					</div>
					<div class="btm-line00"></div>
					<div class="btm-line01"></div>
					<div class="field button"><button type="button" id="J_saveForm" class="btn orange">确认</button></div>
				</form>
			</div>
		</section>
	</section>
	<!--  /主体内容 -->
	<?php include '_footer.php';?>
</section>
</body>
</html>