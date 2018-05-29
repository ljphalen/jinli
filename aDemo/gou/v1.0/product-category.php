<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>金立购—触屏版-分类</title>
<?php include '_inc.php';?>
</head>

<body>
<section id="page">
	<?php include '_header.php';?>
	<!-- 主体内容 -->
	<section id="content">
		<section class="goods">
			<section class="mod-search">
				<div class="long">
					<form action="#" method="get" name="form-search" id="J_form_search">
						<input type="text" name="keyword" autocomplete="off" placeholder="请输入商品或分类名" class="inp" />
						<input type="submit" name="search-btn" value="" />
					</form>
				</div>
			</section>

			<section class="category">
				<ul>
					<?php for($i = 0; $i < 6; $i++){ ?>

					<li>
						<a href="product-category-detail.php">
						<dl>
							<dd><img src="<?php echo $appRef;?>/pic/goods-pic-129X129.jpg" alt="" width="129" height="129" /></dd>
							<dt>女装</dt>
						</dl>
						</a>
					</li>
					<?php }?>
				</ul>
			</section>
		</section>
	</section>
	<!--  /主体内容 -->
	<?php include '_footer.php';?>
</section>
</body>
</html>