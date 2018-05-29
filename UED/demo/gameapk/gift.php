<!DOCTYPE HTML>
<?php include '_cfg.php';?>
<html class="<?php echo $ucClass;?>">
<head>
	<meta charset="UTF-8">
	<title>游戏大厅客户端</title>
	<?php include '_inc.php';?>
</head>
<body>
	<div id="page">
		<section class="wrap first-grade">
			<div class="gift-list J_giftItem">
				<ul>
					<?php for($i=0; $i<7; $i++){?>
					<li>
						<a data-infpage="">
							<span class="icon"><img src="<?php echo "$appPic";?>/pic_icon.jpg" alt=""></span>
							<span class="desc">
								<em>植物大战僵尸2大礼包</em>
								<?php if($i==0){?>
									<p><em>剩余：106个</em></p>
								<?php } else if($i==1) {?>
									<p><em>大侠，礼包已经被抢完了</em></p>
								<?php } else{?>
									<p><em>已抢到</em></p>
								<?php } ?>
								<b>时空维度中进行续写！新的植物</b>
							</span>
						</a>
					</li>
					<?php }?>
				</ul>
			</div>
		</section>
		<div class="load-more J_loadMore" data-ajaxurl="json.php" data-hasnext="1" data-curpage="1">
			<span>加载更多</span>
		</div>
	</div>
</body>
</html>