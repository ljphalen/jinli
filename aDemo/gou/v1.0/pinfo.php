<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>金立购—触屏版</title>
<?php include '_inc.php';?>
</head>

<body>
<section id="page">
	<?php include '_header.php';?>
	<!-- 主体内容 -->
	<section id="content">
		<section class="account">
			<div class="pinfo mod-form-list">
				<form action="json.php" method="post" id="J_checkForm">
					<div class="field"><label>账号 ：</label><input type="text" autocomplete="off" name="umid" value="" /></div>
					<div class="field"><label>姓名 ：</label><input type="text" autocomplete="off" name="uname" value="" /></div>
					<div class="field">
						<label>性别 ：</label>
						<input type="radio" name="usex" value="0" checked="checked" /> 男
						<input type="radio" name="usex" value="1" /> 女
					</div>
					<div class="field">
						<label>生日 ：</label>
						年<select name="uyear"><option>1989</option></select>
						月<select name="umonth"><option>1989</option></select>
						日<select name="uday"><option>1989</option></select>
					</div>
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