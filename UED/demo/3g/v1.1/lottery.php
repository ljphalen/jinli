<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>我的乐园-彩票天地</title>
	<?php include '_inc.php';?>
</head>
<body>
	<div id="page">
		<?php $page = '2-3'; include '_header.php';?>
		
		<article class="ac lottery">
			<div class="item-list prize-number">
				<ul>
					<?php for($i=0; $i<4; $i++){?>
					<li>
						<div class="wrap">
							<dl>
								<dt>双色球<br />2012016期</dt>
								<dd>
									<span class="red-ball">05</span>
									<span class="red-ball">09</span>
									<span class="red-ball">15</span>
									<span class="red-ball">20</span>
									<span class="red-ball">24</span>
									<span class="red-ball">30</span>
									<span class="blue-ball">13</span>
								</dd>
							</dl>
						</div>
					</li>
					<?php }?>
				</ul>
			</div>
			<div class="btn-wrap"><a href="" class="btn">我要买彩票</a></div>
			<div class="lottery-news">
				<h2 class="main-title">彩名之窗</h2>
				<div class="item-list">
					<ul>
						<?php for($i=0; $i<4; $i++){?>
						<li>
							<a href=""><i>●</i>欲望革命系列之柳岩：欲望是危险的搭配早秋第一搭 it girl教你选外套时尚</a>
						</li>
						<?php }?>
					</ul>
				</div>
			</div>
		</article>
	</div>
</body>
</html>