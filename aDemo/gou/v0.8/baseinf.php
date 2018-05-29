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
			<h2>收货人信息</h2>
			<div class="back-page">
				<a href="index.php"><img src="<?php echo $appPic;?>/btn_backPage.png" alt="" /></a>
			</div>
		</header>
		
		<article class="ac">
			<div class="form-box long-label">
				<form action="json.php">
					<ul>
						<li>
							<label for="">账号：</label>
							<input type="text" name="xxx" value="" />
						</li>
						<li>
							<label for="">姓名：</label>
							<input type="text" name="yyy" value="" />
						</li>
                        <li>
							<label for="">性别：</label>
							<label for="man"><input type="radio" name="sex" value="" id="man" checked="checked" /> 男</label>
                            <label for="woman"><input type="radio" name="sex" id="woman" value="" /> 女</label>
						</li>
                        <li>
							<label for="">出生日月：</label>
                            年 <span class="select">
								<select name="" id="">
									<option value="">1950</option>
								</select>
	                            <span></span>
                            </span><br/>
							月  <span class="select">
								<select name="" id="">
									<option value="">1</option>
								</select>
	                            <span></span>
                            </span><br/>
                            日  <span class="select">
								<select name="" id="">
									<option value="">1</option>
								</select>
	                            <span></span>
                            </span>
						</li>
						<li class="btn"><button type="submit">保存</button></li>
					</ul>
				</form>
			</div>
		</article>
	</div>
</body>
</html>