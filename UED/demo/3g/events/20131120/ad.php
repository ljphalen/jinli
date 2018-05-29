<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection" content="telephone=no" />
<title>游戏手柄活动</title>
<?php include "../../_inc.php" ?>
<style type="text/css">
	*{-webkit-tap-highlight-color:rgba(0,0,0,0);}
	html{font-size:20px; -webkit-text-size-adjust:none;}
	@media all and (min-width:359px){.uc-hack{font-size:22.5px;}}
	@media all and (min-width:359px) and (-webkit-min-device-pixel-ratio:1.5){html{font-size:22.5px;}}
	body,div,p,form,input,h1,h2,h3,h4,h5,ul,ol,li,dl,dt,dd,table,tbody,tr,td,textarea,img,iframe,figure{margin:0; padding:0; list-style:none; vertical-align:middle;}
	body{font:14px/1.5 "\5FAE\8F6F\96C5\9ED1",Helvetica,Arial; color:#000;-webkit-text-size-adjust:none;}
	img{border:0; -webkit-touch-callout:none;}
	a,*[onclick]{-webkit-tap-highlight-color:rgba(0,0,0,.5); text-decoration:none; color:#000;}
	a:active,a:hover{outline:none;}
	h1,h2,h3,h4,h5,h6 {font-size:100%; font-weight:500;}
	body{padding:10px; background: #000;}
	#events-box{background:#fff; text-align: center;}
	#events-box img {width:14.1rem;}
	.events-notes{text-align: center; color:#000; background:#ccc; padding:10px 0;}
	.buttons{padding:15px 0; text-align: center; overflow: hidden;}
	.btn{
		height:34px; line-height: 34px; padding-right:42px;
		display:inline-block; margin: 0 auto; color:#fff; font-size: 18px;  font-weight: bold; text-align:center;
		background: url("<?php echo $staticPath;?>/apps/3g/events/assets/img/gamepad-btn-01.png") no-repeat right center;
		-webkit-background-size:32px 34px;
	}
	.btn:active,.btn:hover{
		background-image: url("<?php echo $staticPath;?>/apps/3g/events/assets/img/gamepad-btn-02.png");
	}
</style>
</head>
<body>
<div id="wrapper">
	<div id="events-box">
		<p><img src="<?php echo $staticPath;?>/apps/3g/events/pic/gamepad_t1_1.jpg" /></p>
		<p><img src="<?php echo $staticPath;?>/apps/3g/events/pic/gamepad_t1_2.jpg" /></p>
		<p><img src="<?php echo $staticPath;?>/apps/3g/events/pic/gamepad_t1_3.jpg" /></p>
		<p><img src="<?php echo $staticPath;?>/apps/3g/events/pic/gamepad_t1_4.jpg" /></p>
	</div>
	<div class="events-notes">未完待续...</div>
	<div id="events-buttons" class="buttons">
		<a href="index.php" class="btn">填问卷，赢手柄</a>
	</div>
</div>
</body>
</html>