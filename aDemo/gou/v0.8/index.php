<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>金立购—触屏版</title>
	<?php include '_inc.php';?>
</head>
<body>
	<div id="page">
		<header class="hd">
			<h1>金立购</h1>
			<p class="sub-logo">手机购物这最省 <a style="font-size:1.4rem;" href="login.php">登录</a></p>
			<div class="head-pic">
				<a href="account.php"><img src="<?php echo $appPic;?>/headpic.png" alt="" /></a>
			</div>
		</header>
		
		<article class="ac">
			<div class="mainfocus isTouch" id="mainfocus">
				<div class="ui-slide-scrollbox">
					<ul class="ui-slide-scroll clearfix">
						<li class="ui-slide-item"><a href=""><img src="<?php echo $appPic;?>/pic_bbanner1.jpg" alt=""></a></li>
						<li class="ui-slide-item"><a href=""><img src="<?php echo $appPic;?>/pic_bbanner2.jpg" alt=""></a></li>
						<li class="ui-slide-item"><a href=""><img src="<?php echo $appPic;?>/pic_bbanner3.jpg" alt=""></a></li>
					</ul>
				</div>
				<div class="ui-slide-tabs">
					<span class="ui-slide-tab ui-slide-tabcur"></span>
					<span class="ui-slide-tab"></span>
					<span class="ui-slide-tab"></span>
				</div>
				<span class="ui-slide-prev"></span>
				<span class="ui-slide-next"></span>
			</div>
			
			<div class="search">
				<form action="search.php" class="webkitbox">
					<input class="item" type="text" />
					<button><img src="<?php echo $appPic;?>/ico_btnSearch.png" alt="" /></button>
				</form>
			</div>
			
			<div class="item-box free-order">
				<h2><strong>免单活动专区 - 你想要，我买单</strong></h2>
				<div class="view-old"><a href="freeorder.php">查看往期免单</a></div>
                <div class="slide-box J_slideAjax clearfix" data-ajaxUrl="json.php"></div>
				<div class="slide-handle J_slideSpan"></div>
			</div>
			
			<div class="item-box icon-text">
				<div class="b-box webkitbox">
					<a href=""><span>
						话费快充
						<s class="hot"><img src="<?php echo $appPic;?>/ico_hot.png" alt="" /></s>
					</span></a>
					<a href=""><span>
						手机购彩
						<s class="hot"><img src="<?php echo $appPic;?>/ico_hot.png" alt="" /></s>
					</span></a>
					<a href=""><span>
						手机书城
						<s class="hot"><img src="<?php echo $appPic;?>/ico_hot.png" alt="" /></s>
					</span></a>
					<a href=""><span>
						本地服务
						<s class="hot"><img src="<?php echo $appPic;?>/ico_hot.png" alt="" /></s>
					</span></a>
				</div>
			</div>
			
            <?php for($i=0; $i<3; $i++){?>
			<div class="item-box tab-type-box<?php echo $i+1;?>">
				<div class="JS-tabItem" data-ajaxUrl="json.php?page=1">
					<ul class="oz">
						<li class="selected" data-type="1">男装</li>
						<li data-type="2">女装</li>
						<li data-type="3">内衣</li>
						<li data-type="4">情侣</li>
						<li data-type="5">男鞋</li>
					</ul>
				</div>
				<div class="JS-tabMain"></div>
			</div>
            <?php }?>
			
			<div class="item-box fashion">
				<div class="links clearfix">
					<?php for($i=0; $i<2; $i++){?>
					<a href="">雪纺</a>
					<a href="">七夕情人节</a>
					<a href="">舌尖上的中国</a>
					<a href="">初秋单鞋</a>
					<?php }?>
				</div>
			</div>
			
			<div class="item-box shopping">
				<h2><strong>我再逛逛</strong></h2>
				<div class="links clearfix">
					<?php for($i=0; $i<7; $i++){?>
					<a href="http://m.taobao.com">
						<div class="pic"><img src="<?php echo $appPic;?>/ico_shopping.png" alt="淘宝" /></div>
					</a>
					<?php }?>
				</div>
			</div>
		</article>
		
		<footer class="ft">
			<div class="contact">
				<span>官方QQ群：54879895</span>
				<address>深圳市金立通信设备有限公司</address>
			</div>
			<div class="copyright">Copyright&copy;2012</div>
		</footer>
	</div>
    
    <!--模板层-->
    <script id="J_itemView" type="template">
	<div class="pictext J_slideItem clearfix" type="{type}" pid="{data.curpage}" hasnext="{data.hasnext}" style="position:absolute; width:100%; left:100%;">
		{each data.list}
		<div class="pt-box">
			<a href="{$value.href}" class="webkitbox">
				<div class="pic"><img src="{$value.img}" alt="" /></div>
				<div class="text item"><span>{$value.text}</span></div>
			</a>
		</div>
		{/each}
	</div>
    </script>
    <!--<div class="pictext clearfix">
        <?php // for($j=0; $j<4; $j++){?>
        <div class="pt-box">
            <a href="street.php" class="webkitbox">
                <div class="pic"><img src="<?php // echo $appPic;?>/pic_baobei<?php // echo $j+1;?>.jpg" alt="" /></div>
                <div class="text item"><span>茅台王子，新品特价，买到赚到</span></div>
            </a>
        </div>
        <?php // }?>
    </div>-->
</body>
</html>