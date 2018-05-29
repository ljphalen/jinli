<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<title></title>
	<meta name="keyword" content="金立" />
	<?php include '_inc.php';?>
</head>
<body>
	<!-- old -->
	<!-- <div id="page">
		<article class="ac">
			<div class="slide-pic">
				<div class="slideWrap">
					<div class="pic">
						<a href="#"><img src="<?php echo $appPic;?>/pic_banner.jpg"></a>
					</div>
					<div class="pic">
						<a href="#"><img src="<?php echo $appPic;?>/pic_banner.jpg"></a>
					</div>
				</div>
			</div>
			<div class="home-list item-list J_itemList clearfix" data-ajaxUrl="json.php">
				<ul>
					<?php for($i=0; $i<8; $i++){?>
					<?php if($i==1){?>
					<li>
						<a data-infTheme="cla.php">
							<div class="pic"><img src="<?php echo $appPic;?>/pic_themeClaNew.png"></div>
						</a>
					</li>
					<li>
						<a data-infTheme="12,世界之窗,detail.php,download.php,since">
							<div class="pic"><img src="<?php echo $appPic;?>/pic_themeItem.jpg" class="lazy" data-original="http://theme.3gtest.gionee.com/attachs/file/201303/qcns203301/pre_face_s.jpg"></div>
							<div class="desc">	
								<h3>默认主题</h3>
							</div>
						</a>
					</li>
					<?php }else{?>
					<li>
						<a data-infTheme="12,世界之窗,detail.php,download.php,since">
							<div class="pic"><img src="<?php echo $appPic;?>/pic_themeItem.jpg" class="lazy" data-original="http://theme.3gtest.gionee.com/attachs/file/201303/qcns203301/pre_face_s.jpg"></div>
							<div class="desc">	
								<h3>默认主题</h3>
							</div>
						</a>
					</li>
					<?php }}?>
				</ul>
			</div>
		</article>
	</div> -->

	<!-- new -->
	<div id="page">
		<article class="ac">
			<div class="slide-pic">
				<div class="slideWrap">
					<div class="pic">
						<a href="javascript:void(0)"><img src="<?php echo $appPic;?>/banner_0001.png"></a>
					</div>
					<!-- <div class="pic">
						<a href="#"><img src="<?php echo $appPic;?>/banner_topic.png"></a>
					</div>
					<div class="pic">
						<a href="#"><img src="<?php echo $appPic;?>/banner_theme.png"></a>
					</div> -->
				</div>
			</div>
			<div class="home-list item-list J_itemList clearfix" data-ajaxUrl="json.php">
				<ul>
					<?php for($i=0; $i<8; $i++){?>
					
					<li>
						<a class="<?php if($i == 2){?>isnew<?php }?>" data-infTheme="12,世界之窗,detail.php,download.php,since">
							<div class="pic"><img src="<?php echo $appPic;?>/pic_themeItem.jpg" data-lazyload="true" data-src="http://theme.3gtest.gionee.com/attachs/file/201303/qcns203301/pre_face_s.jpg"></div>
							<div class="desc">	
								<h3>默认主题</h3>
							</div>
						</a>
					</li>
					<?php }?>
				</ul>
				<div class="ui-refresh-down"></div>
			</div>
		</article>
	</div>

<script id="J_itemView" type="text/template">
	{each data.list}
	<li>
		<a data-infTheme="{$value.title},{$value.link},{$value.down}">
			<div class="pic"><img src="<?php echo $appPic;?>/pic_themeItem.jpg" data-lazyload="true" data-src="{$value.img}"></div>
			<div class="desc">	
				<h3>{$value.title}</h3>
			</div>
		</a>
	</li>
	{/each}
</script>
</body>
</html>