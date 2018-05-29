<!DOCTYPE HTML>
<?php include '_cfg.php';?>
<html class="<?php echo $ucClass;?>">
<head>
	<meta charset="UTF-8">
	<title>游戏大厅客户端</title>
	<?php include '_inc.php';?>
	<script type="text/javascript">var token = '00safd';</script>
</head>
<body >
	<div  id="list-page">
		<section class="wrap">
			<div class="item-list J_gameItem">
				<ul>
					<?php for($i=0; $i<10; $i++){?>
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
									<span class="comment">评</span>
									<span class="grip"></span>
								</em>
									
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
		<div class="loading J_loadMore"  style="display: none;" data-ajaxurl="json.php" data-hasnext="true" data-curpage="1">
			<!-- <span>加载更多</span> -->
			<span class="load"></span>
			<!-- <span class="bottom">到底了，去其它页面看看吧</span> -->
		</div>
	</div> 
</body>
</html>