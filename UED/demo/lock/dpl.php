<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>锁屏DPL</title>
	<?php include '_inc.php';?>
</head>
<body>
	<div id="page">
		<header class="hd home">
			<div class="wrap">
				<h1>在线锁屏</h1>
			</div>
		</header>
		<header class="hd">
			<div class="wrap">
				<h1>保钓勇士</h1>
				<div class="back-home"><a href="index.php"></a></div>
				<div class="extra-btn">
					<span class="tleft"></span>
					<span class="tright"></span>
				</div>
			</div>
		</header>
		
		<article class="ac">
			<div class="pic-text-list">
				<ul>
					<?php for($i=0; $i<3; $i++){?>
					<li>
						<a href="">
							<div class="pic">
								<span><img src="<?php echo $appPic?>/pic_testIcon.jpg" /></span>
							</div>
							<div class="desc">
								<h3>打瞌睡</h3>
								<p>快快叫醒绿豆蛙！</p>
							</div>
						</a>
					</li>
					<?php }?>
				</ul>
			</div>
			
			<div class="pic-showcase">
				<ul>
					<?php for($i=0; $i<8; $i++){?>
					<li class="pic"><a href=""><img src="<?php echo $appPic?>/pic_testIcon.jpg" /></a></li>
					<?php }?>
				</ul>
			</div>
		</article>
		
		<footer class="ft">
			<div class="load-status">
				<span class="download">下载</span>
				<span class="useit">应用</span>
				<span class="continue">继续</span>
				<label class="progress-bar">
					<em>45%(等待下载)</em>
					<i class="done" style="width:45%;"></i>
				</label>
				<span class="cancel">取消</span>
				<span class="pause">暂停</span>
			</div>
			<p>
				<span>&copy; 千机解锁</span>
				<span>copyright2012</span>
				<span>苏州天平</span>
			</p>
		</footer>
	</div>
</body>
</html>