<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<title></title>
	<?php include '_inc.php';$onEvent=true;$isPC=false ?>
</head>
<body>
	<div id="page">
		<header class="hd">
			<h1>手柄活动专区</h1>
			<div class="back-home"><a href="index.php">&lt;</a></div>
		</header>
		<article class="ac">
			<div class="grip-box">
				<?php if(!$onEvent||$isPC){?>
					<!-- 活动结束后  或者 通过PC参加的用户不能参与抽奖-->
					<div class="gripZone">
						<a href="">
							<img src="<?php echo $appPic;?>/blank.gif" data-src="<?php echo $appPic;?>/grip.png" alt="" />
						</a>
					</div>
				<?php }else{?>
					<!-- 活动中 -->
					<div class="gripEvent J_event" data-ajaxUrl="verifyChance.php">
						<a href="">
							<div class="pic"></div>
							<div class="prize"><span class="level1">蓝牙手柄</span><span class="level2">50元话费</span><span class="level3">10个Q币</span></div>
							<div class="rule">
								活动时间：2013-12-25至2013-12-28<br/>
								活动内容：活动期间，玩家只要下载活动页面推荐的任意一款游戏，即可获得一个金蛋，砸开金蛋即有机会获得蓝牙手柄、话费、Q币～
								每个玩家最多可获得10个金蛋。
							</div>
							<a class="detail" href="">查看活动详情</a>
						</a>
					</div>
				<?php }?>

				<div class="grip-btn">
					<div class="go">
						<a class="buy" href="">购买蓝牙手柄</a>
						<a class="introduce" href="">玩转蓝牙手柄</a>
					</div>
				</div>
			</div>
			<div class="grip-list">
				<ul>
					<?php for($i=0;$i<10;$i++){?>
					<li>
						<a class="J_item" data-id="11" data-href="http://game.gionee.com/index/tj?id=1053&type=AD411_ADID508_GID1053&_url=http%3A%2F%2Fgamedl.gionee.com%2Fapps%2Fgames%2Fnew%2Fzhishangtanbing-release-cmge-99957434.apk&t_bi=_2165812491">
							<div class="pic">
								<img src="<?php echo $appPic;?>/blank.gif" data-src="<?php echo $appPic;?>/pic_topic.jpg" alt="">
							</div>
							<div class="title">我是火影</div>
							<span class="btn-install J_install" >安装</span>
						</a>
					</li>
					<?php } ?>
				</ul>
			</div>
		</article>
	</div>
	<!-- 活动进行中 -->
	<?php if($onEvent){?>
		<div class="modal-dialog J_dialog">
			<div class="egg J_egg" data-ajaxUrl="prize.php"></div>
			<div class="egg-broken"></div>
			<div class="dialog">
				<div class="dialog-container J_container">
					<span class="dialog-header">系统提示</span>
					<div class="dialog-body">
					</div>
					<span class="dialog-footer J_confirm" data-ajaxUrl="verfiyPhone.php" >确定</span>
				</div>
			</div>
		</div>
	<?php }?>
	<?php include '_icat.php';?>
</body>
</html>