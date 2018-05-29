<!DOCTYPE HTML>
<?php include '_cfg.php';?>
<html class="<?php echo $ucClass;?>">
<head>
	<meta charset="UTF-8">
	<title>游戏大厅客户端</title>
	<?php include '_inc.php';?>
	<!-- <link rel="stylesheet" href="http://18.8.2.98:8899/sys/reset/phonecore.css">
	<link rel="stylesheet" href="http://18.8.2.98:8899/apps/game/apk/assets/css/game.source.css"> -->
	<style type="text/css">
	.app-list{
		width:310px;
		height:80px;
	}
	li{
		width:76px;float: left;height: 76px;
		border-top:2px solid red;
		border-bottom:2px solid red;
		border-left:1px solid red;
	}

	.te-wrapper{
		position: relative;
		width:76px;
	}
		.te-cover.te-hide,
.te-transition,
.te-images{ display: none; }
.te-transition.te-show { display: block; }
.te-back,
.te-front{
	position: absolute;
	width: 100%;
	height: 100%;
}
.te-front{ z-index: 2; }
.te-back{
	z-index: 1;
	-webkit-backface-visibility: hidden;
}

		/***************** Flip1 ********************/
		.te-example1.te-show .te-front{
	-webkit-animation: example1Front 0.6s linear forwards;
}
@-webkit-keyframes example1Front{
	0% { opacity: 1; }
	100% { opacity: 0; }
}

	</style>
</head>
<body>
	<div class="app-list">
		<ul>
			<?php for($i=0;$i<4;$i++){?>
			<li><a class="te-wrapper" href="">
				<div class="te-images">
					<img src="images/1.jpg"/>
					<img src="images/2.jpg"/>
				</div>
				<div class="te-cover">
					<img src="images/1.jpg"/>
				</div>
				<div class="te-transition te-example1">
					<div class="te-card">
						<div class="te-front"></div>
						<div class="te-back"></div>
					</div>
				</div>		
			</a></li>
			<?php }?>
		</ul>
	</div>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script type="text/javascript" src="http://18.8.2.98/gameapk/rotate/js/jquery.transitions.js"></script>
</body>
</html>