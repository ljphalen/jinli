<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<title></title>
	<meta name="keyword" content="金立" />
	<?php include '_inc.php';?>
</head>
<body>
	<div id="page">
		<article class="ac">
			<div class="scroll-pic J_scrollPic" data-prevtheme="10,世界之窗,detail.php,download.php,since" data-nexttheme="12,世界之窗,detail.php,download.php,since">
				<div class="slideWrap">
					<ul class="pic-wrap">
						<?php for($i=0; $i<10; $i++){?>
							<?php if($i == 1):?>
							<li class="intro">
								<div class="intro-box">
									<div class="intro-top">主题详情</div>
									<div class="intro-btm">
										<div id="intro-wrap">
											<div id="intro-scroller">
												<div class="intro-div">
													<dl>
														<dd class="title"><label>主题名称：</label><span>可拉乐园可拉乐园可拉乐园</span></dd>
														<dd><label>作者：</label><span>金立</span></dd>
														<dd><label>大小：</label><span>448kb</span></dd>
														<dd><label>下载量：</label><span>78920</span></dd>
														<dd><label>更新时间：</label><span>2013-10-29</span></dd>
														<dd class="desc"><label>主题介绍：</label><span>随着中央领导层准备宣布下月举行的十八届三中全会召开日期，有关经济改革措施的力度和范围的猜测正在增加。在下月这个里程碑式的会议上，执政党将考虑一揽子措施以扩大经济改革。</span></dd>
													</dl>
												</div>
											</div>
										</div>
									</div>
								</div>
							</li>
							<?php else : ?>
							<li><span class="span-pic"><img src="<?php echo $appPic;?>/pic_imgview.jpg" data-src="<?php echo $appPic;?>/pic_nopreview.jpg" onerror="this.src='<?php echo $appPic;?>/pic_nopreview.jpg';"></span></li>
							<?php endif;?>
						<?php }?>
					</ul>
				</div>
				<div class="handle">
					<?php for($i=0; $i<11; $i++){?>
					<span></span>
					<?php }?>
				</div>
			</div>
		</article>
	</div>
</body>
</html>