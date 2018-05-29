<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<title></title>
	<?php include '_inc.php'; ?>
</head>
<body>
	<div data-role="page" data-theme="no" id="page" class="feed">
		<header id="header" class="hd">
			<h1><strong>我有话要说</strong></h1>
			<p><a href="index.php"><img src="<?php echo $appPic; ?>/btn_backhome.png" alt="" /></a></p>
		</header>
		
		<div id="content" class="ct">
			<section>
				<form class="form-box" action="">
					<ul>
						<li><label for=""><em>手机号：</em></label><input type="text" name="" class="l" value="137" /></li>
						<li><label for=""><em>验证码：</em></label><input type="text" name="" class="n" />
							<a href="" class="check-code"><img src="<?php echo $appPic; ?>/pic_checkcode.jpg" alt="" /></a></li>
						<li><textarea name="" id="" cols="30" rows="10"></textarea>
							<button data-role="none" class="s-btn"><span>发布</span></button></li>
						
					</ul>
				</form>
				
				<div class="show-order">
					<ul>
						<?php for($i=0; $i<2; $i++){?>
						<li>
							<div class="msg">
								<span>我手机下单成功了，也晒单了，还没有收到充值话费呀！135*****12<s>2012-07-28 11:32</s></span>
							</div>
							<div class="reply"><p>答：亲，确认收货后3个工作日喔，表着急，肯定会有的啦~</p></div>
						</li>
						<?php }?>
					</ul>
					<div class="more"><a href="" dt-ajaxUrl="" class="J_showMore"><span>查看更多</span></a></div>
				</div>
			</section>
		</div>
	</div>
</body>
</html>