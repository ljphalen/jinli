<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>在线应用Wap版</title>
<!-- 指定当前应用模块名称 START-->
<?php $moduleName = "app-wap"; $webTitle="分类名称"; ?>
<!-- 指定当前应用模块名称 END-->
<?php include '../../_inc.php';?>
</head>
<body>
	<div id="page">
		<div class="papps wrapper" id="J_scrollWrap">
			<div class="mainWrap">
				<!-- 内容区域 START-->
				<header class="hdWrap">
					<div class="hd-l"><a href="index.php" class="l-arrow">back</a></div>
					<div class="hd-r"><h1><?php echo $webTitle; ?></h1></div>	
				</header>

				<div class="module">
					<section id="default-tab" data-ajaxUrl="json.php">
						<div id="app-cont">
							<!-- 分类列表 START -->
							<div data-tab="app-reco" id="app-recoWrap">
								<div class="app-list">
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
								<div id="pullUp">
									<span class="pullUpIcon"></span>
									<span class="pullUpLabel">上拉加载更多应用</span>
								</div>
							</div>
							<!-- 分类列表 END -->
						</div>
					</section>
				</div>
				<!-- 内容区域 END-->
			</div>
		</div>
	</div>
<!-- 分类模板 START -->
<script id="J_itemView" type="text/template">
	{each data.list}
		<div class="app-item-list">
			<div class="app-item-img">
				<img src="{$value.img}" />
			</div>
			<div class="app-item-info">
				<div class="app-item-title">{$value.title}</div>
				<div class="start star{$value.star}"></div>
				<div class="app-item-text">{$value.appInfo} | {$value.appType}</div>
			</div>
			<div class="app-item-bton button"><a href="{$value.link}" class="btn open-btn">打开</a></div>
		</div>
	{/each}
</script>
<!-- 分类模板 END -->
</body>
</html>