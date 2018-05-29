<!DOCTYPE HTML>
<?php include '_cfg.php';?>
<html<?php echo ($ucClass? 'class="'.$ucClass.'"' : '');?>>
<head>
	<meta charset="UTF-8">
	<title>market application</title>
	<?php include '_incfile.php';?>
</head>

<body id="weather" data-pagerole="body">
	<div class="wrap">
		<div id="waterfall" data-ajaxurl="weather_api.php"></div>
	</div>
</body>
</html>