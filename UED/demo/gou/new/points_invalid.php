<!DOCTYPE HTML>
<?php include '_cfg.php';?>
<html<?php echo ($ucClass? ' class="'.$ucClass.'"' : '');?>>
<head>
	<meta charset="UTF-8">
	<title>功能失效</title>
	<script>var webPage = true;</script>
	<?php include '_inc.php';?>
	<link rel="stylesheet" href="<?php echo "$webroot/$appRef/assets/css/web$source.css$timestamp";?>">
</head>

<body data-pagerole="body">
	<div class="module">
		<div class="invalid-page">
			<img src="<?php echo "$webroot/$appPic/pic_invalidPage.png";?>" alt="">
			<p>尊敬的用户，积分换购已下线<br>现全新开放AMIGO商城，去逛逛吧！</p>
		</div>
	</div>
</body>
</html>