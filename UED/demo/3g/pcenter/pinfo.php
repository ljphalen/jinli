<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>个人中心</title>
<?php
	/** 指定当前应用模块名称 START */
	$moduleName = "pcenter";
	$webTitle = "基本资料";
	/** 指定当前应用模块名称 END */
?>
<?php include '../_inc.php';?>
</head>
<body>
	<div id="page">
		<div id="paccount">
			<div class="mainWrap">
				<!-- 公共头部 -->
				<header class="hdWrap">
					<div class="hd-l"><a href="index.php" class="l-arrow">back</a></div>
					<div class="hd-r"><h1><?php echo $webTitle; ?></h1></div>	
				</header>
				<!-- 内容区域 START-->
				<div class="module">
					<div class="form">
						<form action="###" method="post" name="pinfo-form" id="J_saveForm">
							<div class="form-field">
								<div class="form-input form-text">
									<div><label>QQ：</label></div>
									<div><input type="text" name="qq" maxLength="20" autocomplete="off" value="" /></div>
								</div>
							</div>
							<div class="form-field">
								<div class="form-input form-text">
									<div><label for="">Email：</label></div>
									<div><input type="email" autocomplete="off" name="email" value="" /></div>
								</div>
							</div>
							
							<div class="form-field">
								<div class="form-select">
									<div><label for="">生日：</label></div>
									<div>
										<select name="year">
											<option>请选择年</option>
											<option>1985</option>
										</select>
									</div>
								</div>
							</div>
							<div class="form-field">
								<div class="form-select">
									<div><label for=""></label></div>
									<div>
										<select name="month">
											<option>请选择月</option>
											<option>11</option>
										</select>
									</div>
								</div>
							</div>
							<div class="form-field">
								<div class="form-select">
									<div><label for=""></label></div>
									<div>
										<select name="day">
											<option>请选择日</option>
											<option>15</option>
										</select>
									</div>
								</div>
							</div>
							<div class="form-field">
								<div class="form-input form-radio">
									<div><label for="">性别：</label></div>
									<div>
										<span>
											<input type="radio" name="sex" value="0" checked id="manLabel" />
											<label for="manLabel">男</label>
										</span>
										<span>
											<input type="radio" name="sex" value="1" id="womanLabel" /> <label for="womanLabel">女</label>
										</span>
									</div>
								</div>
							</div>
							<div class="form-field">
								<div class="button">
									<input type="submit" name="submit" value="保存" class="btn gray" />
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>