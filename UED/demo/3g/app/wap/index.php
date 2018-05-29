<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>在线应用</title>
<!-- 指定当前应用模块名称 START-->
<?php $moduleName = "app-wap"; $webTitle="在线应用Wap版"; ?>
<!-- 指定当前应用模块名称 END-->
<?php include '../../_inc.php';?>
</head>
<body>
	<div id="page">
		<div class="papps wrapper" id="J_scrollWrap">
			<div class="mainWrap">
				<!-- 内容区域 START-->
				<header class="hd">
					<div class="slide banner">
						<img src="<?php echo $appPic;?>/apps_banner.jpg" alt="在线应用" />
					</div>
				</header>

				<div class="module">
					<section class="tag-box mainTag">
						<div class="navTag">
							<ul id="app-tabs">
								<li data-tab="app-reco" id="default-tab" class="actived" data-ajaxUrl="json.php">推荐</li>
								<li data-tab="app-cate">分类</li>
								<li data-tab="app-rank">排行</li>
								<li data-tab="app-news" data-ajaxUrl="json.php">新品</li>
							</ul>
						</div>
						<div id="app-cont">
							<!-- 推荐 -->
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
												<div class="star" data-star="3"></div>
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
							<!-- 分类 -->
							<div data-tab="app-cate" id="app-cateWrap" class="app-cate ishide">
								<ul>
									<li><a href="detail.php"><div>生活</div><div>点评 美食 团购</div></a></li>
									<li><a href="detail.php"><div>资讯</div><div>点评 美食 团购</div></a></li>
									<li><a href="detail.php"><div>小说</div><div>点评 美食 团购</div></a></li>
									<li><a href="detail.php"><div>影音</div><div>看看 优酷 奇艺</div></a></li>
									<li><a href="detail.php"><div>娱乐</div><div>丑事 爆漫 冷兔</div></a></li>
									<li><a href="detail.php"><div>交友</div><div>点评 美食 团购</div></a></li>
									<li><a href="detail.php"><div>购物</div><div>看看 优酷 奇艺</div></a></li>
									<li><a href="detail.php"><div>游戏</div><div>丑事 爆漫 冷兔</div></a></li>
									<li><a href="detail.php"><div>生活</div><div>点评 美食 团购</div></a></li>
									<li><a href="detail.php"><div>生活</div><div>点评 美食 团购</div></a></li>
									<li><a href="detail.php"><div>生活</div><div>点评 美食 团购</div></a></li>
									<li><a href="detail.php"><div>生活</div><div>点评 美食 团购</div></a></li>
								</ul>
							</div>
							<!-- 排行 -->
							<div data-tab="app-rank" id="app-rankWrap" class="ishide">
								<div class="app-list">
									<!-- 列表 -->
									<?php for($i = 0; $i < 10; $i++):?>
										<div class="app-item-list">
											<div class="app-item-img">
												<img src="<?php echo $appPic;?>/caipiao.png" />
											</div>
											<div class="app-item-info">
												<div class="app-item-title">彩票</div>
												<div class="star" data-star="3"></div>
												<div class="app-item-text">一款购物软件 | 购物</div>
											</div>
											<div class="app-item-bton button"><a href="###" class="btn open-btn">打开</a></div>
										</div>
									<?php endfor;?>
								</div>
							</div>
							<!-- 新品 -->
							<div data-tab="app-news" id="app-newsWrap" class="ishide">
								<div class="app-list">
									<!-- 列表 -->
									<?php for($i = 0; $i < 10; $i++):?>
										<div class="app-item-list">
											<div class="app-item-img">
												<img src="<?php echo $appPic;?>/caipiao.png" />
											</div>
											<div class="app-item-info">
												<div class="app-item-title">彩票</div>
												<div class="star" data-star="3"></div>
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
						</div>
					</section>
				</div>
				<!-- 内容区域 END-->
			</div>
		</div>
	</div>
	<!-- 推荐区域模板 START -->
	<script id="J_itemView" type="text/template">
		{each data.list}
			<div class="app-item-list">
				<div class="app-item-img">
					<img src="{$value.img}" />
				</div>
				<div class="app-item-info">
					<div class="app-item-title">{$value.title}</div>
					<div class="start" data-star="{$value.star}"></div>
					<div class="app-item-text">{$value.appInfo} | {$value.appType}</div>
				</div>
				<div class="app-item-bton button"><a href="{$value.link}" class="btn open-btn">打开</a></div>
			</div>
		{/each}
	</script>
	<!-- 推荐区域模板 END -->
</body>
</html>