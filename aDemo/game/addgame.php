<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>金立游戏—客户端</title>
	<?php include '_inc.php';?>
</head>
<body>
	<div id="page">
		<header class="hd">
			<div class="wrap has-shadow">
				<h2>添加游戏</h2>
				<div class="back-home">
					<a href="index.php"></a>
				</div>
			</div>
		</header>
		
		<article class="ac add-game">
			<div class="speed-dial J_addGame">
				<ul>
					<?php for($i=0; $i<12; $i++){?>
					<li>
						<a href="javascript:;">
							<span class="pic"><img src="<?php echo $appPic;?>/pic_appImg.png" alt="" /></span>
							<em>会说话的Tom猫</em>
							<span class="icon"><img src="<?php echo $appPic;?>/ico_tick.png" alt="" /></span>
							<input type="hidden" name="" />
						</a>
					</li>
					<?php }?>
				</ul>
			</div>
			<div class="tip-submit">
				<div class="tip">您当前还可以添加<span>9</span>个游戏</div>
				<div class="submit">
					<span class="btn"><a class="orange"><i>确认</i></a></span>
				</div>
			</div>
		</article>
	</div>
	</body>
</html>