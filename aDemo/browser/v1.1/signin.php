<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>我的乐园-签到及完成签到</title>
	<?php include '_inc.php';?>
</head>
<body>
	<div id="page">
		<?php $page = '2-4'; include '_header.php';?>
		
		<article class="ac sign-in">
			<?php
				//$len = 16; $cols = sqrt($len);
				$cols = 5; $len = pow($cols,2);
				$lb = $cols*($cols-1)+1;
				$cw = ($cols == 5 ? 4.12 : 4.12);
			?>
			<div class="puzzle-box" style="width:<?php echo $cols*$cw;?>rem; height:<?php echo $cols*$cw?>rem;">
				<div class="pic"><img src="<?php echo $appPic;?>/pic_banner.jpg" alt="" /></div>
				<div class="cell" style="width:<?php echo $cols*$cw;?>rem; height:<?php echo $cols*$cw;?>rem;">
					<ul <?php if($cols == 2){echo 'style="border-right:none;"';}?>>
						<?php for($i=1; $i<=$len; $i++){?>
						<?php
							switch($i){
								case 1: $cla='class="lt"'; break;
								case $cols: $cla='class="rt"'; break;
								case $lb: $cla='class="lb"'; break;
								case $len: $cla='class="rb"'; break;
								default: $cla='';
							}
						?>
						<li <?php echo $cla;?>><span <?php if($cols ==5) echo 'class="cols-five"';?>></span></li>
						<?php }?>
					</ul>
				</div>
			</div>
			
			<div class="btn-wrap">
				<a href="winprize1.php" data-url="winprize1.php" id="J_reward_btn" class="btn disabled">兑奖</a>
				<a href="" data-ajaxUrl="signindo.php" data-picId="1" class="J_checkInDialog btn">签到</a>
			</div>
			<div class="sign-rule">
				<dl>
					<dt>签到规则：</dt>
					<dd>每日签到一次，每多签到一次即可多拼一张拼图<br />如果连续五天参与签到，既可双倍获取拼图数。</dd>
					
					<dt>签到奖励：</dt>
					<dd>每完成拼图一副，既有好运降临。可能是惊喜礼品，可能是意外话费，更有可能是签到好人品哦……拼图美女等来来完善。</dd>
				</dl>
			</div>
			
			<div class="prize-his">
				<div class="prize-his-head">
					<h2>我的拼图库</h2>
				</div>
				<div class="prize-his-main">
					<ul>
						<li><a href=""><img src="<?php echo $appRef?>/pic/pic_chartlet4.jpg" alt="" /><span class="unprize">未兑奖</span></a></li>
						<li><a href=""><img src="<?php echo $appRef?>/pic/pic_chartlet4.jpg" alt="" /><span>已兑奖</span></a></li>
						<li><a href=""><img src="<?php echo $appRef?>/pic/pic_chartlet4.jpg" alt="" /><span>已兑奖</span></a></li>
						<li><a href=""><img src="<?php echo $appRef?>/pic/pic_chartlet4.jpg" alt="" /><span>已兑奖</span></a></li>
						<li><a href=""><img src="<?php echo $appRef?>/pic/pic_chartlet4.jpg" alt="" /><span>已兑奖</span></a></li>
					</ul>
				</div>
				<div class="btn-wrap">
					<a href="login.php" class="btn">登陆</a>
					<a href="info.php" class="btn">我的帐号：198854821544</a>
				</div>
			</div>

			<!-- 弹出层信息显示窗口 -->
			<div class="JS-dbMask"></div>
			<div class="dialog-box J_dialogBox">
				<p></p>
				<div class="btn-wrap">
					<span class="btn gray">取消</span>
					<span class="btn gray">登陆</span>
				</div>
			</div>
			<!-- /弹出层信息显示窗口 -->
		</article>
	</div>
</body>
</html>