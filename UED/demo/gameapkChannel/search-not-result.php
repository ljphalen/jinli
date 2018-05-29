<!DOCTYPE HTML>
<?php include '_cfg.php';?>
<html class="<?php echo $ucClass;?>">
<head>
	<meta charset="UTF-8">
	<title>游戏大厅客户端</title>
	<?php include '_inc.php';?>
	<script type="text/javascript">var token = '00safd';</script>
</head>
<body>
	<div  id="page">
		<section class="wrap">
			<div class="search-tips">
					<p><h2>对不起，没有找到你想要的结果！</h2></p>
					<p><img src="<?php echo $appPic;?>/unsearch.png" /></p>
				</div>
				<div class="search-tips-title">大家都在搜：</div>
			<div class="search-list J_gameItem">
				<ul>
					<?php for($i=0; $i<7; $i++){?>
					<li>
						<a href="detail.php">
							<!-- <span class="rank-num"><?php echo $i;?></span> -->
							<span class="rank-title">捕鱼达人</span>
						</a>
					</li>
					<?php }?>
				</ul>
			</div>
		</section>
	</div>	
</body>
</html>