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
			<div class="search-list J_gameItem">
				<div class="everyone-search">大家都在搜：</div>
				<ul>
					<?php for($i=0; $i<7; $i++){?>
					<li>
						<a href="detail.php">
							<!-- <span class="rank-num"><?php echo $i;?></span> -->
							<span class="rank-title">植物大战僵尸</span>
						</a>
					</li>
					<?php }?>
				</ul>
			</div>
		</section>
	</div>

	
</body>
</html>