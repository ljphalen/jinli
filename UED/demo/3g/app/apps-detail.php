<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>在线应用</title>
<!-- 指定当前应用模块名称 START-->
<?php $moduleName = "app"; $webTitle="应用分类"; ?>
<!-- 指定当前应用模块名称 END-->
<?php include '../_inc.php';?>
</head>

<body>
	<div id="page">
		<div class="papps" id="J_scrollWrap">
			<div class="mainWrap">
				<!-- 内容区域 START-->
				<header class="hdWrap">
					<div class="hd-l"><a href="apps-wap.php" class="l-arrow">back</a></div>
					<div class="hd-r"><h1><?php echo $webTitle; ?></h1></div>	
				</header>

				<div class="module">
					<section class="">
						<div id="app-cont">
							<!-- 分类列表 START -->
							<div id="category-app-list" class="app-list" data-ajaxUrl="../json.php">
								<!-- 列表 -->
								<?php for($i = 0; $i < 10; $i++):?>
									<div class="app-item-list">
										<div class="app-item-img">
											<img src="<?php echo $appPic;?>/caipiao.png" />
										</div>
										<div class="app-item-info">
											<div class="app-item-title">彩票</div>
											<div class="star" data-star="5"></div>
											<div class="app-item-text">一款购物软件 | 购物</div>
										</div>
										<div class="app-item-bton button"><a href="###" class="btn open-btn">打开</a></div>
									</div>
								<?php endfor;?>
							</div>
							<div id="pullUp-recom">
								<span class="pullUpIcon"></span>
								<span class="pullUpLabel">上拉加载更多应用</span>
							</div>
							<!-- 分类列表 END -->
						</div>
					</section>
				</div>

<!-- 分类应用模板 -->
<script id="category_app" type="text/template">
	<div class="app-item-list">
		<div class="app-item-img">
			<img src="{img}" />
		</div>
		<div class="app-item-info">
			<div class="app-item-title">{title}</div>
			<div class="star star{star}"></div>
			<div class="app-item-text">{appInfo} | {appType}</div>
		</div>
		{if appState == true}
		<div class="app-item-bton button"><a href="{link}" class="btn open-btn">打开</a></div>
		{else appState == false}
			<div class="app-item-bton button"><span data-href="{link}" class="btn add-btn">添加</span></div>
		{/if}
	</div>
</script>
<!-- 页面底部 -->
<!-- <?php include '_footer.php';?> -->
