<!DOCTYPE HTML>
<?php include '_cfg.php';?>
<html<?php echo ($ucClass? ' class="'.$ucClass.'"' : '');?>>
<head>
	<meta charset="UTF-8">
	<title>退/换货</title>
	<script>var webPage = true;</script>
	<?php include '_inc.php';?>
	<link rel="stylesheet" href="<?php echo "$webroot/$appRef/assets/css/web$source.css$timestamp";?>">
</head>

<body data-pagerole="body">
	<div class="module">
		<header id="iHeader" class="hd">
			<div class="top-wrap">
				<div class="title">
					<a href="" class="back"></a>
					<h1>退/换货</h1>
					<span class="voice"></span>
				</div>
			</div>
		</header>

		<section id="iScroll">
			<div class="input-form">
				<form action="amigo_orderChange2.php">
					<fieldset>
						<ul>
							<li><input type="text" name="user" placeholder="请输入姓名"></li>
							<li><input type="text" name="mobile" placeholder="请输入手机号码"></li>
						</ul>
					</fieldset>
					<div class="web-btn"><button class="J_formVerify">立即查询</button></div>
				</form>
			</div>
		</section>
	</div>
</body>
</html>