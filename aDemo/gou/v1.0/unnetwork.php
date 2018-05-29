<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>金立购—触屏版</title>
<?php include '_inc.php';?>
<style type="text/css">
	html{background:white;display:-webkit-box; -webkit-box-align:center; -webkit-box-pack:center;}
	body{background:white;}
	#page .unnetwork{text-align: center; width:100%; height:100%; padding:1rem 0;}
	#page .unnetwork p img{width:6rem; height:6rem;}
	#page .unnetwork h2{margin-top:1rem; font-size:1.2rem; color:#5c5c5c;}
</style>
</head>

<body>
<section id="page">
	<?php include '_header.php';?>
	<!-- 主体内容 -->
	<section id="content">
		<section class="unnetwork">
			<div class="wrap">
				<p><img src="<?php echo $appRef;?>/assets/img/unnetwork.png" /></p>
				<h2>网络无法连接，请检查网络设置！</h2>
			</div>
		</section>
	</section>
	<!--  /主体内容 -->
	<?php include '_footer.php';?>
</section>
</body>
</html>