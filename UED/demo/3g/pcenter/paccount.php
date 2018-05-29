<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>个人中心</title>
<?php
	/** 指定当前应用模块名称 START */
	$moduleName = "pcenter";
	$webTitle = "详细资料";
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
						<form action="###" method="post" name="account-form" id="J_saveForm">
							<div class="form-field">
								<div class="form-input form-text">
									<div><label>账号：</label></div>
									<div>13510729370</div>
								</div>
							</div>
							<div class="form-field">
								<div class="form-input form-text">
									<div><label>昵称：</label></div>
									<div><input type="text" name="nickname" autocomplete="off" value="" maxlength="20" /></div>
								</div>
							</div>
							<div class="form-field">
								<div class="form-select">
									<div><label for=""></label></div>
									<div>
										<select name="model">
											<option value="0">选择机型</option>
											<option value="GN305">GN305</option>
										</select>
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