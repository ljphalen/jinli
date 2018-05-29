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
	<div  id="list-page">
		<section class="wrap">
			<div class="search-tips-title">共5条结果来自百度应用：</div>
			<div class="baiduSearch-tips">百度应用的游戏可能不适配您的手机</div>
			<div class="item-list J_gameItem">
				<ul>
					<?php for($i=0; $i<7; $i++){?>
					<li>
						<a href="detail.php">
							<span class="icon"><img src="<?php echo $appPic;?>/pic_icon.jpg" alt=""></span>
							<span class="desc">
								<em>游戏A</em>
								<p><em>2.3M</em></p>
								<b>一个上手简单的冒险解谜游戏，探索</b>
							</span>
							
						</a>
					</li>
					<?php }?>
				</ul>
			</div>
		</section>
	</div>
</body>
</html>