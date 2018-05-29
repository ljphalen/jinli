<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>DPL</title>
	<?php include '_inc.php';?>
</head>
<body>
	<div id="page">
		<header class="hd">
			<div class="menu">
				<ul>
					<li>
						<span class="top-arrow"><i></i></span>
						<a href="">金立世界</a>
					</li>
					<li class="selected">
						<span class="top-arrow"><i></i></span>
						<a>我的乐园</a>
					</li>
				</ul>
			</div>
			<div class="sub-menu">
				<ul>
					<li><a class="selected" href="">新闻资讯</a></li>
					<li><a href="">美图在线</a></li>
					<li><a href="">彩票天地</a></li>
					<li><a href="">我要签到</a></li>
				</ul>
			</div>
		</header>
		<header class="hd">
			<h1>
				<span class="top-arrow"><i></i></span>
				智能系列
			</h1>
			<div class="back">
				<a href=""><span class="left-arrow"><i></i></span></a>
			</div>
			<div class="cla-menu">
				<span><i></i></span>
				<span><i></i></span>
				<div class="wrap">
					<ul>
						<li><a class="selected" href="">GN700W</a></li>
						<li><a href="">GN777</a></li>
						<li><a href="">GN180</a></li>
						<li><a href="">GN868</a></li>
					</ul>
				</div>
			</div>
		</header>
		
		<article class="ac contact"><!-- hot-news/download/mobile-parts/series-->
			<div class="item-list">
				<ul>
					<li>
						<!-- <a href="">
							<dl>
								<dt>
									<div class="pic"><img src="<?php echo $appPic;?>/pic_newsImg.jpg" alt="" /></div>
								</dt>
								<dd>
									<h2 class="l-line">欲望革命系列之柳岩：欲望是危险的搭配</h2>
									<p>欲望革命系列之柳岩：欲望是危险的搭配早秋第一搭 it girl教你选外套时尚</p>
								</dd>
							</dl>
						</a> -->
						<!-- <div class="extra"><a href="" class="btn">下载</a></div> -->
						<!-- <div class="extra"><span class="price">￥35元</span></div> -->
					</li>
					<li>
						<!-- <a href="">
							<dl>
								<dt class="r-line">天鉴系列</dt>
								<dd><p>一键搜索海量曲库，精心打造权威榜单早秋第一搭 it girl教你选外套...</p></dd>
							</dl>
						</a>
						<div class="extra"><img src="<?php echo $appPic;?>/ico_jt.png" alt="" /></div> -->
					</li>
					<li>
						<div class="wrap">
							<dl>
								<dt>
									<div class="pic"><img src="<?php echo $appPic;?>/ico_phone.png" alt="" /></div>
								</dt>
								<dd><h2>400-777-999</h2></dd>
							</dl>
						</div>
					</li>
				</ul>
			</div>
			
			<div class="resource-detail">
				<div class="inform">
					<div class="pic"><img src="<?php echo $appPic;?>/ico_phone.png" alt="" /></div>
					<div class="desc">
						<h2>应用名称：QQ游戏2012</h2>
						<p>公司：腾讯<br />大小：4.5MB</p>
					</div>
					<div class="extra"><a href="" class="btn">下载</a></div>
				</div>
			</div>
			
			<div class="radius-table">
				<table>
					<tr>
						<th>服务网点</th>
						<th>店面地址</th>
						<th>咨询电话</th>
					</tr>
					<?php for($i=0; $i<3; $i++){?>
					<tr>
						<td>广东中域电讯连锁股份有限公司</td>
						<td>东莞市万江区新和社区新华南路49号中域集团员工生活区B栋4楼资源部</td>
						<td>0769<br/>23661618/8835</td>
					</tr>
					<?php }?>
				</table>
			</div>
			
			<div class="form-box">
				<form action="">
					<h3>选择你所在的：</h3>
					<ul>
						<li>
							<label for="">省份：</label>
							<select name="" id="">
								<option value="">广东</option>
							</select>
						</li>
						<li>
							<label for="">市区：</label>
							<select name="" id="">
								<option value="">深圳</option>
							</select>
						</li>
						<li>
							<select name="" id="">
								<option value="">客服中心</option>
							</select>
						</li>
						<li><button class="btn">搜索</button></li>
					</ul>
				</form>
			</div>
			
			<div class="slide-pic banner">
				<div class="pic">
					<?php for($a=0; $a<4; $a++){?><a href=""><img src="<?php echo $appPic;?>/pic_banner.jpg" alt="" /></a><?php }?>
				</div>
				<div class="handle">
					<?php for($a=0; $a<4; $a++){?><span <?php echo ($a==0? 'class=on':'');?>></span><?php }?>
				</div>
			</div>
			
			<div class="slide-pic proPic">
				<div class="wrap">
					<div class="pic">
						<?php for($a=0; $a<4; $a++){?>
						<a href="">
							<span><img src="<?php echo $appPic;?>/pic_banner.jpg" alt="" /></span>
						</a>
						<?php }?>
					</div>
				</div>
				<div class="handle">
					<?php for($a=0; $a<4; $a++){?><span <?php echo ($a==0? 'class=on':'');?>></span><?php }?>
				</div>
			</div>
			
			<?php
				$cols = 3; $len = pow($cols,2);
				$lb = $cols*($cols-1)+1;
			?>
			<div class="puzzle-box" style="width:<?php echo $cols*5.15;?>rem; height:<?php echo $cols*5.15;?>rem;">
				<div class="pic"><img src="<?php echo $appPic;?>/pic_banner.jpg" alt="" /></div>
				<div class="cell" style="width:<?php echo $cols*5.15;?>rem; height:<?php echo $cols*5.15;?>rem;">
					<ul>
						<?php for($i=1; $i<=9; $i++){?>
						<?php
							switch($i){
								case 1: $cla='class="lt"'; break;
								case $cols: $cla='class="rt"'; break;
								case $lb: $cla='class="lb"'; break;
								case $len: $cla='class="rb"'; break;
								default: $cla='';
							}
						?>
						<li <?php echo $cla;?>><span <?php if($i==2 || $i==9){?>class="done"<?php }?>></span></li>
						<?php }?>
					</ul>
				</div>
			</div>
			
			<div class="textpic-desc">
				<h3>e-life 系列 GN205  保护套</h3>
				<p>简单的图案，清新的微笑，可是是您心情的写照。温馨时尚的黄色搭配简单有趣的图案，让您的手机个性十足，光彩耀人。</p>
				<div class="btn-wrap">
					<a href="" class="btn"><img src="<?php echo $appPic;?>/ico_btnGou.png" alt="" />去购买</a>
					<a href="" class="btn"><img src="<?php echo $appPic;?>/ico_btnPhone.png" alt="" />400-799-6666</a>
				</div>
			</div>
		</article>
	</div>
</body>
</html>