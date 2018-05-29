<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<?php include "_inc.php";?>
<title></title>
<style type="text/css">
	html{position:relative; width:100%; height:100%; display:-webkit-box; -webkit-box-align:center; -webkit-box-pack:center;}
	#header{position:absolute; left:0; top:0; width:100%;}
	#footer{position:absolute; left:0; bottom:0; width:100%;}
	#wrapper .unnetwork{width:100%; text-align:center; padding:20px 0;}
	#wrapper .unnetwork p img{width:164px; height:99px;}
	#wrapper .unnetwork .button{margin-top:20px;}
	#wrapper .unnetwork .btn-gray{height:44px; line-height:44px;}
	#wrapper .unnetwork h2{margin-top:20px; font-size:16px; color:#5c5c5c;}
</style>
</head>

<body>
<section id="wrapper">
	<section id="content">
		<section class="unnetwork">
			<div class="wrap">
				<p><img src="<?php echo $appPic;?>/unnetwork.png" /></p>
				<h2>加载失败，点击屏幕刷新！</h2>
			</div>
		</section>
	</section>
</section>
</body>
</html>