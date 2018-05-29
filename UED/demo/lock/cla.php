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
			<div class="item-list J_itemList clearfix" data-ajaxUrl="json.php">
				<ul>
					<?php for($i=0; $i<8; $i++){?>
					<li>
						<a href="detail.php">
							<div class="pic"><img src="<?php echo $appPic;?>/pic_themeItem.jpg"></div>
							<div class="desc">
								<h3>默认主题</h3>
							</div>
							<div class="mask"></div>
						</a>
					</li>
					<?php }?>
				</ul>
			</div>
		</article>
	</div>
	<script id="J_itemView" type="text/template">
		{each data.list}
		<li>
			<a data-infTheme="{$value.id},{$value.title},detail.php,download.php,{$value.since}">
				<div class="pic"><img class="lazy" src="{$value.img}" style="background:url({$value.bg_img1}),url({$value.bg_img2}); background-size:100% 100%;"></div>
				<div class="desc">	
					<h3>{$value.title}</h3>
				</div>
			</a>
		</li>
		{/each}
	</script>
</body>
</html>