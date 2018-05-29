<!DOCTYPE HTML>
<?php include '_cfg.php';?>
<html class="<?php echo $ucClass;?>">
<head>
	<meta charset="UTF-8">
	<title>游戏大厅客户端</title>
	<?php include '_inc.php';?>
</head>
<body>
	<div id="detail-page">
		<section>
			<div class="detail-field">
				<dl>
					<dt class="icon"><img src="<?php echo $appPic;?>/blank.gif" data-src="<?php echo $appPic;?>/pic_icon.jpg" alt=""></dt>
					<dd class="desc">
						<p class="name">苍穹之剑</p>
						<p class="info"><span>塔防</span><em>|</em><span>中文</span><em>|</em><span>12.6M</span></p>
						<p class="origin">北京触控科技有限</p>
					</dd>
				</dl>
			</div>
			<div class="gift-field">
				<a href="">
					<span class="">植物大战僵尸礼包</span><span>礼</span>
				</a>
			</div>
			<div class="suggest_field">
				<h1>相关推荐</h1>
				<ul class="list">
					<?php for($i=0;$i<4;$i++){?>
					<li><a href=""><img src="<?php echo $appPic;?>/blank.gif" data-src="<?php echo $appPic;?>/pic_icon.jpg" alt=""></a></li>
					<?php }?>
				</ul>
			</div>
			<div class="detail-scrollPic" id="J_screenshot">
				<div class="pic-container">
					<h1>屏幕截图</h1>
					<div class="pic-wrap">
						<?php for($i=0; $i<9; $i++){?>
						<span><img src="<?php echo $appPic;?>/blank.gif" data-bigpic="<?php echo $appPic;?>/pic_screenshot.jpg"  data-src="<?php echo $appPic;?>/pic_screenshot.jpg" alt=""></span>
						<?php }?>
					</div>
				</div>
			</div>
			<div class="detail-text ui-editor">
				<div class="text-container">
					<p>
					<p>&nbsp; &nbsp; &nbsp;&nbsp;1fdgdfgdfgdf<span style="white-space:nowrap;"><br></span></p><p><span style="white-space:nowrap;">&nbsp; &nbsp; &nbsp;&nbsp;在文章中加入分页标记，你是说在发布文章的时候加对l</span></p>
					<p style=""><span style="white-space:nowrap;">吧？发布的时候怎么加标记也需要对<a href="http://www.hao123.com" target="_blank">标签的判断啊</a>，对标签的移动，而且现在已经发</span></p><p><span style="white-space:nowrap;">&nbsp; &nbsp;&nbsp;布了很多文章了，如果使用这种方法，以前的就没法实现分页了。这个尝试过，最终决定放</span></p><p><span style="white-space:nowrap;">弃这种方法。朋友，除了这方法，你平时开发还有其他处理方法没？</span></p><p style="text-align:center;"><span style="white-space:nowrap;"><img src="http://game.3gtest.gionee.com/attachs/resource/201308/145954.png" alt=""><br></span></p>
				</p></div>
			</div>
		</section>
	</div>
</body>
</html>