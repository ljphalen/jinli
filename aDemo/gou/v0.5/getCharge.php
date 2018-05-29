<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<title></title>
	<?php include '_inc.php'; ?>
</head>
<body>
	<div data-role="page" data-theme="no" id="page" class="get-charge">
		<header id="header" class="hd">
			<h1><strong>免费赢话费</strong></h1>
			<p><a href="index.php"><img src="<?php echo $appPic; ?>/btn_backhome.png" alt="" /></a></p>
		</header>
		
		<div id="content" class="ct">
			<div class="desc">
				<dl>
					<dt><em>你敢抽，我就敢送</em></dt>
					<dd><s></s><span>抽中后您填写的手机号将收到由金立购发送的中奖确认短信。点击短信链接后输入中奖密语，确认无误后话费奖品将直接充入您填写的手机号。</span></dd>
					<dd><s></s><span>手机实物大奖将在七个工作日内发放，300元现金券可在酒仙网抵现使用。</span></dd>
					
					<dt><em>抽奖池</em></dt>
					<dd>
						<img src="<?php echo $appPic; ?>/pic_jiang.jpg" alt="" />
					</dd>
				</dl>
			</div>
			<form class="form-box" action="dialog.php">
				<ul>
					<li><label for=""><em>手机号：</em></label><input type="text" name="" class="l" value="137" /></li>
					<li><label for=""><em>通关密语：</em></label>
						<select name="">
							<option value="">你最想去的城市是？</option>
							<option value="standard">深圳</option>
							<option value="rush">伦敦</option>
							<option value="express">纽约</option>
							<option value="overnight">新加坡</option>
						</select>
						<input type="text" name="" class="l" /></li>
					<li><button data-role="none" data-rel="dialog" data-transition="pop"><span>开始抽奖</span></button></li>
				</ul>
			</form>
			<div class="yq"><a href="" data-role="none">邀请好友参与 &gt;</a></div>
			<div class="winners">
				<ul>
					<?php for($i=0; $i<4; $i++){?>
					<li><span><em>186****8456</em>刚刚抽中了100元话费，手气不错；</span></li>
					<?php }?>
				</ul>
			</div>
		</div>
	</div>
</body>
</html>