<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>我的乐园-新闻咨询</title>
	<?php include '_inc.php';?>
</head>
<body>
	<div id="page">
		<?php $page = '2-1'; include '_header.php';?>
		
		<article class="ac">
			<div class="slide-pic banner">
				<div class="pic">
					<?php for($a=0; $a<4; $a++){?><a href=""><img src="<?php echo $appPic;?>/pic_banner.jpg" alt="" /></a><?php }?>
				</div>
				<div class="handle">
					<?php for($a=0; $a<4; $a++){?><span <?php echo ($a==0? 'class=on':'');?>></span><?php }?>
				</div>
			</div>
			
			<div class="hot-news">
				<h2 class="main-title">热点</h2>
				<div class="item-list">
					<ul>
						<?php for($i=0; $i<4; $i++){?>
						<li>
							<a href="">
								<dl>
									<dt>
										<div class="pic"><img src="<?php echo $appPic;?>/pic_newsImg.jpg" alt="" /></div>
									</dt>
									<dd class="l-line">
										<h2>欲望革命系列之柳岩：欲望是危险的搭配</h2>
										<p>欲望革命系列之柳岩：欲望是危险的搭配早秋第一搭 it girl教你选外套时尚</p>
									</dd>
								</dl>
							</a>
						</li>
						<?php }?>
					</ul>
				</div>
			</div>
			<div class="btn-wrap">
				<a href="" class="btn fixed-btn">更多新闻</a>
			</div>
		</article>
	</div>
</body>
</html>