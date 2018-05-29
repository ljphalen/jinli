<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<title></title>
	<?php include '_inc.php';?>
</head>
<body>
	<div id="page">
		<header class="hd">
			<h1><div class="omit">捕鱼达人是一款深海捕鱼的游戏</div></h1>
			<div class="back-home"><a href="index.php">&lt;</a></div>
		</header>

		<article class="ac">
			<div class="detail-field">
				<dl>
					<dt><img src="<?php echo $appPic;?>/icon_game.jpg" alt=""></dt>
					<dd class="desc">
						<p class="name">苍穹之剑</p>
						<p class="info"><span>塔防</span><em>|</em><span>中文</span></p>
						<p class="origin">来源：北京触控科技有限</p>
					</dd>
				</dl>
				<div class="download"><a href="" class="btn">下载(25.6M)</a></div>
			</div>
			<div class=""></div>
			
			<div class="detail-scrollPic" id="J_screenshot">
				<div class="eva_link">
				<ul>
					<?php for($i=0; $i<2; $i++){?>
					<li>
						<a href="">《时空猎人》夏日换装季 时尚指数五颗星</a>
					</li>
					<?php }?>
				</ul>
			</div>
				<div class="pic-wrap">
					<?php for($i=0; $i<9; $i++){?>
					<span><img src="<?php echo $appPic;?>/pic_screenshot.jpg" alt=""></span>
					<?php }?>
				</div>
			</div>
			<!-- <div class="detail-text">
				<div data-text><div>捕鱼达人(英文Fishing Joy)是一款深海捕鱼游戏。...</div></div>
				<div data-text class="hidden"><div>捕鱼达人(英文Fishing Joy)是一款深海捕鱼游戏。玩家在游戏中用网可捕捉到多达15种鱼类, 从观赏鱼到大型鱼, 甚至包括鲨鱼!当你成功捕获时, 这些鱼同时也会化为数不尽的金币滚滚而来, 堆满你的船舱, 这将是一次新奇有趣的海洋探宝之旅!</div></div>
				<div class="open J_openBtnWrap"><span>展开</span></div>
			</div> -->
			<div class="detail-text ui-editor">
			    				<div data-text=""><br></div>
								                <div class="detail-text">
				   <div data-text="" class=""><div>asdfasdfasdfa</div>...</div>
                     <div data-text class="hidden">aaaaaaaaaaaaaaaaaaasdfsdf</div>
				     <div class="open J_openBtnWrap"><span>展开</span></div>
				</div>
							</div>
		</div>
		</div>
		</article>
		<div class="goTop J_gotoTop">
			<a ></a>
		</div>
		<footer class="ft">
			<div class="wchat" >
				<img src="<?php echo $appPic;?>/wechat.png" alt="" />
				<div>
				<p>欢迎关注我们的微信公共账户！</p>
				<p>帐号：JXH-20121220</p>
			</div>
		</div>
			<div class="quick-links">
				<a href="contact.php">联系我们</a>
				<a href="">意见反馈</a>
				<a href="">客户端下载</a>
			</div>
		</footer>
	</div>
	<?php include '_icat.php';?>
</body>
</html>