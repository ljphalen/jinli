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
			<h2>商品详情</h2>
			<div class="back-page">
				<a href="index.php"><img src="<?php echo $appPic;?>/btn_backPage.png" alt="" /></a>
			</div>
			<div class="head-pic">
			    <a href="account.php"><img src="<?php echo $appPic;?>/headpic.png" alt="" /></a>
			</div>
		</header>
		
		<article class="ac clearfix">
			<div class="free-period detail">
				<div class="title">
					<h3>雅顿绿茶女士淡香水</h3>
				</div>
				<div class="main J_scrollPro isTouch">
					<span class="handle ui-slide-prev"><img src="<?php echo $appPic;?>/ico_arrow.png" alt="" /></span>
					<span class="handle ui-slide-next"><img src="<?php echo $appPic;?>/ico_arrow.png" alt="" /></span>
                    <div class="pic ui-slide-scrollbox">
    					<ul class="ui-slide-scroll oz">
    						<?php for($i=0; $i<3; $i++){?>
    						<li class="ui-slide-item">
    							<span><img src="<?php echo $appPic;?>/pic_product.jpg" alt="" /></span>
    						</li>
    						<?php }?>
    					</ul>
    				</div>
					<div class="ui-slide-tabs tab-item">
						<span class="ui-slide-tab ui-slide-tabcur"></span>
						<span class="ui-slide-tab"></span>
						<span class="ui-slide-tab"></span>
					</div>
				</div>
			</div>
			<section class="pro-intro">
				<p>人都说法国的香水韩国的粉，可想韩国彩妆的出名。爱丽小屋家的BB霜质地清爽，毫无油腻感，上妆效果很自然！夏天马上就要到了,即使在火辣辣的大太阳底下也不必害怕被太阳晒黑喽~喜欢逛街的MM有福气啦。多款型号，多种选择~</p>
				<div class="inf">
					<span class="price-sale">￥88.00<span>254人已购买</span></span>
					<a href="" data-ajaxUrl="jsonDialog.php" class="add-one J_showDialog"><em>想要+1<span>286人想要</span></em></a>
					<a href=""><em>立即购买</em></a>
				</div>
			</section>
		</article>
	</div>
    
    <div class="JS-dbMask"></div>
    <div class="dialog-box J_dialogBox">
    	<p></p>
    	<div class="btn">
        	<a href="login.php">确定</a>
        </div>
    </div>
</body>
</html>