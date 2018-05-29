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
			<div class="ad-pic ad-topic">
				<a data-infpage="">
					<img src="http://assets.3gtest.gionee.com/apps/game/apk/pic/blank.gif" alt="" data-src="http://game.3gtest.gionee.com/attachs/ad/201309/111520.jpg">
				</a>
			</div>
			<div class="name-tips">植物大战僵尸2</div>
			<div class="item-list J_gameItem">
				<ul>
					<?php for($i=0; $i<7; $i++){?>
					<li>
						<a data-infpage="detail.php">
							<span class="icon"><img src="<?php echo "$appPic";?>/pic_icon.jpg" alt=""></span>
							<span class="desc">
								<em>游戏A</em>
								<p><em>大小：7.98M</em>
								<?php if($i==0){?>
								<em class="tips">
									<span class="gift">礼</span></em>
								<?php }else if($i==1){?>
									<em class="tips"><span class="comment">评</span></em>
								<?php } else if($i==2){?>
									<em class="tips"><span class="gift">礼</span>
									<span class="comment">评</span></em>
								<?php }else{?>
								<?php } ?>
								</p>
								<b>一个非常好玩的游戏,一个非常好玩的游戏</b>
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