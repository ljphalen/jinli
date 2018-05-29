<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>金立购—触屏版</title>
<?php include '_inc.php';?>
</head>

<body>
<?php
	$list = array(
		array('date' => '9月20日20:00','img' => $appRef.'/pic/theme-list-pic-01.jpg','title'=> '这个10.1，你准备好了吗？','goods_num' => 11),
		array('date' => '9月20日20:00','img' => $appRef.'/pic/theme-list-pic-02.jpg','title'=> '这个10.1，你准备好了吗？','goods_num' => 65),
		array('date' => '9月20日20:00','img' => $appRef.'/pic/theme-list-pic-03.jpg','title'=> '这个10.1，你准备好了吗？','goods_num' => 25),
		array('date' => '9月20日20:00','img' => $appRef.'/pic/theme-list-pic-04.jpg','title'=> '这个10.1，你准备好了吗？','goods_num' => 8),
		array('date' => '9月20日20:00','img' => $appRef.'/pic/theme-list-pic-01.jpg','title'=> '这个10.1，你准备好了吗？','goods_num' => 2),
		array('date' => '9月20日20:00','img' => $appRef.'/pic/theme-list-pic-02.jpg','title'=> '这个10.1，你准备好了吗？','goods_num' => 91),
		array('date' => '9月20日20:00','img' => $appRef.'/pic/theme-list-pic-03.jpg','title'=> '这个10.1，你准备好了吗？','goods_num' => 121),
	);
?>
<section id="page">
	<?php include '_header.php';?>
	<!-- 主体内容 -->
	<section id="content">
		<section class="ptheme lazyLoad">
		<?php foreach($list as $val):?>
			<div class="block">
				<ul class="mod-text-list">
					<li class="bline"><?php echo $val['date'];?></li>
					<li>
						<a href="recommend.php">
						<dl class="mod-dl-list">
							<dd><img src="<?php echo $val['img'];?>" alt="" /></dd>
							<dd>
								<span><?php echo $val['title']; ?></span>
								<span class="fr">商品数：<?php echo $val['goods_num'];?></span>
							</dd>
						</dl>
						</a>
					</li>
				</ul>
			</div>
		<?php endforeach;?>
		<script type="template" id="J_itemView">
			{each data.list}
			<div class="block">
				<ul class="mod-text-list">
					<li class="bline">{$value.time}</li>
					<li>
						<a href="{$value.href}">
						<dl class="mod-dl-list">
							<dd><img src="<?php echo $appRef;?>/pic/theme-list-pic-02.jpg" alt="" /></dd>
							<dd><span>{$value.title}</span><span class="fr">商品数：{$value.price}</span></dd>
						</dl>
						</a>
					</li>
				</ul>
			</div>
			{/each}
		</script>
		</section>
	</section>
	<!--  /主体内容 -->
	<?php include '_footer.php';?>
</section>
</body>
</html>