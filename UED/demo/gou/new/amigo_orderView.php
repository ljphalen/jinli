<!DOCTYPE HTML>
<?php include '_cfg.php';?>
<html<?php echo ($ucClass? ' class="'.$ucClass.'"' : '');?>>
<head>
	<meta charset="UTF-8">
	<title>订单查询</title>
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
					<h1>订单查询</h1>
					<span class="voice"></span>
				</div>
			</div>
		</header>

		<section id="iScroll">
			<div class="input-form">
				<form action="amigo_orderInf.php">
					<fieldset>
						<ul>
							<li><input type="text" placeholder="请输入收货人姓名"></li>
							<li><input type="text" placeholder="请输入手机号码"></li>
						</ul>
					</fieldset>
					<div class="web-btn"><button>立即查询</button></div>
				</form>
			</div>
		</section>
	</div>
</body>
</html>