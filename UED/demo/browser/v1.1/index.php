<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>金立世界-金立手机</title>
	<?php include '_inc.php';?>
</head>
<body>
	<div id="page">
		<?php $navIndex = 1; include '_header.php';?>
		
		<article class="ac">
			<div class="slide-pic banner">
				<div class="pic">
					<?php for($a=0; $a<4; $a++){?><a href="###"><img src="<?php echo $appPic;?>/pic_banner.jpg" alt="" /></a><?php }?>
				</div>
				<div class="handle">
					<?php for($a=0; $a<4; $a++){?><span <?php echo ( $a== 0 ?  'class=on':'' );?>></span><?php }?>
				</div>
			</div>
			
			<div class="series">
				<div class="item-list">
					<ul>
						<?php for($i=0; $i<4; $i++){?>
						<li>
							<a href="series.php">
								<dl>
									<dt class="r-line">天鉴系列</dt>
									<dd><p>一键搜索海量曲库，精心打造权威榜单早秋第一搭 it girl教你...</p></dd>
								</dl>
							</a>
						</li>
						<?php }?>
					</ul>
			</div>
		</article>
	</div>
</body>
</html>