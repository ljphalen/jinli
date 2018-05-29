<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>我的乐园-资源下载</title>
	<?php include '_inc.php';?>
</head>
<body>
	<div id="page">
		<?php $navIndex=2; include '_header.php';?>
		
		<article class="ac">
			<div class="slide-pic banner">
				<div class="pic">
					<?php for($a=0; $a<4; $a++){?><a href=""><img src="<?php echo $appPic;?>/pic_banner.jpg" alt="" /></a><?php }?>
				</div>
				<div class="handle">
					<?php for($a=0; $a<4; $a++){?><span <?php echo ($a==0? 'class=on':'');?>></span><?php }?>
				</div>
			</div>
			
			<div class="download">
				<div class="item-list">
					<ul class="J_itemWrap" data-ajaxUrl="json.php">
						<li id="curInfo" class="hidden" curpage="1" hasnext="true"></li>
					<?php for($i = 0; $i < 6; $i++):?>
						<li>
							<a href="http://www.baidu.com">
								<dl>
									<dt>
										<div class="pic"><img src="<?php echo $appPic;?>/pic_newsImg.jpg" alt="" /></div>
									</dt>
									<dd class="l-line">
										<h2>sdfsdafdsa</h2>
										<p>12312312312vdsfsfsad</p>
									</dd>
								</dl>
							</a>
							<div class="extra"><a href="" class="btn">下载</a></div>
						</li>
					<?php endfor; ?>
					</ul>
				</div>
			</div>
		</article>
	</div>
	
	<script id="J_itemView" type="template">
		<li id="curInfo" class="hidden" curpage="{data.curpage}" hasnext="{data.hasnext}"></li>
		{each data.list}
		<li>
			<a href="{$value.href}">
				<dl>
					<dt>
						<div class="pic"><img class="scrollLoading" xSrc="{$value.img}" src="<?php echo $appPic;?>/loading.gif" alt="" /></div>
					</dt>
					<dd  class="l-line">
						<h2>{$value.title}</h2>
						<p>{$value.text}</p>
					</dd>
				</dl>
			</a>
			<div class="extra"><a href="" class="btn">下载</a></div>
		</li>
		{/each}
	</script>
</body>
</html>