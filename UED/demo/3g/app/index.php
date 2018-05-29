<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection" content="telephone=no" />
<title>在线应用</title>
<?php include "../_inc.php"; ?>
<link id="linkMain" rel="stylesheet" type="text/css" href="<?php echo $staticPath;?>/apps/3g/app/assets/css/gnapp.source.css">
<script src="<?php echo $staticPath;?>/sys/icat/1.1.5/icat.js"></script>
<script src="<?php echo $staticPath;?>/apps/3g/app/assets/js/gnapp.source.js"></script>
</head>
<body>
	<div id="page">
		<div class="papps" id="J_scrollWrap">
			<div class="mainWrap">
				<!-- 内容区域 START-->
				<header class="hd">
					<div class="slide banner">
						<img src="<?php echo $staticPath;?>/apps/3g/app/pic/apps_banner.jpg" alt="在线应用" />
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
								</div>
							</div>
							<!-- 新品 -->
							<div data-tab="app-news" id="app-newsWrap" class="ishide">
								<div class="app-list">
									<!-- 列表 -->
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
				<!-- 存放首页数据 -->
				<div class="index_data" style="display:none;">
				{"recommend_data_list":{"data":[{"id":"21","link":"http:\/\/www.qq.com","img":"http:\/\/3gtest.gionee.com:88\/attachs\/App\/201212\/145606.jpg","title":"\u5e94\u7528_\u6c14\u7403","star":"5","appInfo":"\u63cf\u8ff0_\u6c14\u7403","appType":"\u5206\u7c7b4_!@$","addUrl":"http:\/\/3gtest.gionee.com:88\/api\/app\/index?id=21"},{"id":"22","link":"http:\/\/www.qq.com","img":"http:\/\/3gtest.gionee.com:88\/attachs\/App\/201212\/145736.jpg","title":"\u5e94\u7528_\u72d7","star":"3","appInfo":"\u63cf\u8ff0_\u72d7","appType":"\u5206\u7c7b4_!@$","addUrl":"http:\/\/3gtest.gionee.com:88\/api\/app\/index?id=22"},{"id":"19","link":"http:\/\/www.qq.com","img":"http:\/\/3gtest.gionee.com:88\/attachs\/App\/201212\/145204.jpg","title":"\u5e94\u7528_\u96e8\u4f1e","star":"1","appInfo":"\u63cf\u8ff0_\u96e8\u4f1e","appType":null,"addUrl":"http:\/\/3gtest.gionee.com:88\/api\/app\/index?id=19"},{"id":"17","link":"http:\/\/www.qq.com","img":"http:\/\/3gtest.gionee.com:88\/attachs\/App\/201212\/145036.jpg","title":"\u5e94\u7528_\u62d6\u978b","star":"4","appInfo":"\u63cf\u8ff0_\u62d6\u978b","appType":null,"addUrl":"http:\/\/3gtest.gionee.com:88\/api\/app\/index?id=17"},{"id":"16","link":"http:\/\/www.qq.com","img":"http:\/\/3gtest.gionee.com:88\/attachs\/App\/201212\/145001.jpg","title":"\u5e94\u7528_\u7530\u91ce","star":"2","appInfo":"\u63cf\u8ff0_\u7530\u91ce","appType":"\u5206\u7c7b4_!@$","addUrl":"http:\/\/3gtest.gionee.com:88\/api\/app\/index?id=16"},{"id":"3","link":"http:\/\/www.sina.com","img":"http:\/\/3gtest.gionee.com:88\/attachs\/App\/201212\/135351.jpg","title":"\u5e94\u7528_\u653e\u5927\u955c","star":"5","appInfo":"\u63cf\u8ff0_\u653e\u5927\u955c11","appType":"SS6","addUrl":"http:\/\/3gtest.gionee.com:88\/api\/app\/index?id=3"},{"id":"13","link":"http:\/\/www.qq.com","img":"http:\/\/3gtest.gionee.com:88\/attachs\/App\/201212\/144711.jpg","title":"\u5e94\u7528_\u53e3\u7f69","star":"2","appInfo":"\u63cf\u8ff0_\u53e3\u7f69","appType":"\u5206\u7c7b4_!@$","addUrl":"http:\/\/3gtest.gionee.com:88\/api\/app\/index?id=13"},{"id":"4","link":"http:\/\/www.qq.com","img":"http:\/\/3gtest.gionee.com:88\/attachs\/App\/201212\/162408.jpg","title":"\u5e94\u7528_\u526a\u5200","star":"1","appInfo":"\u63cf\u8ff0_\u526a\u5200","appType":"\u5e94\u7528\u5206\u7c7b1","addUrl":"http:\/\/3gtest.gionee.com:88\/api\/app\/index?id=4"},{"id":"2","link":"http:\/\/www.qq.com","img":"http:\/\/3gtest.gionee.com:88\/attachs\/App\/201212\/155939.jpg","title":"\u5e94\u7528_\u5439\u98ce\u673a","star":"3","appInfo":"\u63cf\u8ff0_\u5439\u98ce\u673a","appType":"\u5206\u7c7b4_!@$","addUrl":"http:\/\/3gtest.gionee.com:88\/api\/app\/index?id=2"},{"id":"15","link":"http:\/\/www.qq.com","img":"http:\/\/3gtest.gionee.com:88\/attachs\/App\/201212\/144917.jpg","title":"\u5e94\u7528_\u6811\u6797","star":"3","appInfo":"\u63cf\u8ff0_\u6811\u6797","appType":"\u5206\u7c7b4_!@$","addUrl":"http:\/\/3gtest.gionee.com:88\/api\/app\/index?id=15"}],"hasnext":"true","curpage":1},"rank_data_list":{"data":[{"id":"21","link":"http:\/\/www.qq.com","img":"http:\/\/3gtest.gionee.com:88\/attachs\/App\/201212\/145606.jpg","title":"\u5e94\u7528_\u6c14\u7403","star":"5","appInfo":"\u63cf\u8ff0_\u6c14\u7403","appType":"\u5206\u7c7b4_!@$","addUrl":"http:\/\/3gtest.gionee.com:88\/api\/app\/index?id=21"},{"id":"20","link":"http:\/\/www.qq.com","img":"http:\/\/3gtest.gionee.com:88\/attachs\/App\/201212\/145523.jpg","title":"\u5e94\u7528_\u6307\u5357\u9488","star":"4","appInfo":"\u63cf\u8ff0_\u6307\u5357\u9488","appType":"\u5206\u7c7b4_!@$","addUrl":"http:\/\/3gtest.gionee.com:88\/api\/app\/index?id=20"},{"id":"22","link":"http:\/\/www.qq.com","img":"http:\/\/3gtest.gionee.com:88\/attachs\/App\/201212\/145736.jpg","title":"\u5e94\u7528_\u72d7","star":"3","appInfo":"\u63cf\u8ff0_\u72d7","appType":"\u5206\u7c7b4_!@$","addUrl":"http:\/\/3gtest.gionee.com:88\/api\/app\/index?id=22"},{"id":"19","link":"http:\/\/www.qq.com","img":"http:\/\/3gtest.gionee.com:88\/attachs\/App\/201212\/145204.jpg","title":"\u5e94\u7528_\u96e8\u4f1e","star":"1","appInfo":"\u63cf\u8ff0_\u96e8\u4f1e","appType":null,"addUrl":"http:\/\/3gtest.gionee.com:88\/api\/app\/index?id=19"},{"id":"17","link":"http:\/\/www.qq.com","img":"http:\/\/3gtest.gionee.com:88\/attachs\/App\/201212\/145036.jpg","title":"\u5e94\u7528_\u62d6\u978b","star":"4","appInfo":"\u63cf\u8ff0_\u62d6\u978b","appType":null,"addUrl":"http:\/\/3gtest.gionee.com:88\/api\/app\/index?id=17"},{"id":"16","link":"http:\/\/www.qq.com","img":"http:\/\/3gtest.gionee.com:88\/attachs\/App\/201212\/145001.jpg","title":"\u5e94\u7528_\u7530\u91ce","star":"2","appInfo":"\u63cf\u8ff0_\u7530\u91ce","appType":"\u5206\u7c7b4_!@$","addUrl":"http:\/\/3gtest.gionee.com:88\/api\/app\/index?id=16"},{"id":"3","link":"http:\/\/www.sina.com","img":"http:\/\/3gtest.gionee.com:88\/attachs\/App\/201212\/135351.jpg","title":"\u5e94\u7528_\u653e\u5927\u955c","star":"5","appInfo":"\u63cf\u8ff0_\u653e\u5927\u955c11","appType":"SS6","addUrl":"http:\/\/3gtest.gionee.com:88\/api\/app\/index?id=3"},{"id":"13","link":"http:\/\/www.qq.com","img":"http:\/\/3gtest.gionee.com:88\/attachs\/App\/201212\/144711.jpg","title":"\u5e94\u7528_\u53e3\u7f69","star":"2","appInfo":"\u63cf\u8ff0_\u53e3\u7f69","appType":"\u5206\u7c7b4_!@$","addUrl":"http:\/\/3gtest.gionee.com:88\/api\/app\/index?id=13"},{"id":"4","link":"http:\/\/www.qq.com","img":"http:\/\/3gtest.gionee.com:88\/attachs\/App\/201212\/162408.jpg","title":"\u5e94\u7528_\u526a\u5200","star":"1","appInfo":"\u63cf\u8ff0_\u526a\u5200","appType":"\u5e94\u7528\u5206\u7c7b1","addUrl":"http:\/\/3gtest.gionee.com:88\/api\/app\/index?id=4"},{"id":"2","link":"http:\/\/www.qq.com","img":"http:\/\/3gtest.gionee.com:88\/attachs\/App\/201212\/155939.jpg","title":"\u5e94\u7528_\u5439\u98ce\u673a","star":"3","appInfo":"\u63cf\u8ff0_\u5439\u98ce\u673a","appType":"\u5206\u7c7b4_!@$","addUrl":"http:\/\/3gtest.gionee.com:88\/api\/app\/index?id=2"}],"hasnext":false,"curpage":1},"new_data_list":{"data":[{"id":"23","link":"http:\/\/www.qq.com","img":"http:\/\/3gtest.gionee.com:88\/attachs\/App\/201212\/150217.jpg","title":"\u5e94\u7528_\u8001\u864e","star":"3","appInfo":"\u63cf\u8ff0_\u8001\u864e","appType":"\u5206\u7c7b4_!@$","addUrl":"http:\/\/3gtest.gionee.com:88\/api\/app\/index?id=23"},{"id":"22","link":"http:\/\/www.qq.com","img":"http:\/\/3gtest.gionee.com:88\/attachs\/App\/201212\/145736.jpg","title":"\u5e94\u7528_\u72d7","star":"3","appInfo":"\u63cf\u8ff0_\u72d7","appType":"\u5206\u7c7b4_!@$","addUrl":"http:\/\/3gtest.gionee.com:88\/api\/app\/index?id=22"},{"id":"21","link":"http:\/\/www.qq.com","img":"http:\/\/3gtest.gionee.com:88\/attachs\/App\/201212\/145606.jpg","title":"\u5e94\u7528_\u6c14\u7403","star":"5","appInfo":"\u63cf\u8ff0_\u6c14\u7403","appType":"\u5206\u7c7b4_!@$","addUrl":"http:\/\/3gtest.gionee.com:88\/api\/app\/index?id=21"},{"id":"20","link":"http:\/\/www.qq.com","img":"http:\/\/3gtest.gionee.com:88\/attachs\/App\/201212\/145523.jpg","title":"\u5e94\u7528_\u6307\u5357\u9488","star":"4","appInfo":"\u63cf\u8ff0_\u6307\u5357\u9488","appType":"\u5206\u7c7b4_!@$","addUrl":"http:\/\/3gtest.gionee.com:88\/api\/app\/index?id=20"},{"id":"19","link":"http:\/\/www.qq.com","img":"http:\/\/3gtest.gionee.com:88\/attachs\/App\/201212\/145204.jpg","title":"\u5e94\u7528_\u96e8\u4f1e","star":"1","appInfo":"\u63cf\u8ff0_\u96e8\u4f1e","appType":null,"addUrl":"http:\/\/3gtest.gionee.com:88\/api\/app\/index?id=19"},{"id":"18","link":"http:\/\/www.qq.com","img":"http:\/\/3gtest.gionee.com:88\/attachs\/App\/201212\/145124.jpg","title":"\u5e94\u7528_\u5c0f\u6cb3","star":"4","appInfo":"\u63cf\u8ff0_\u5c0f\u6cb3","appType":"\u5206\u7c7b4_!@$","addUrl":"http:\/\/3gtest.gionee.com:88\/api\/app\/index?id=18"},{"id":"17","link":"http:\/\/www.qq.com","img":"http:\/\/3gtest.gionee.com:88\/attachs\/App\/201212\/145036.jpg","title":"\u5e94\u7528_\u62d6\u978b","star":"4","appInfo":"\u63cf\u8ff0_\u62d6\u978b","appType":null,"addUrl":"http:\/\/3gtest.gionee.com:88\/api\/app\/index?id=17"},{"id":"16","link":"http:\/\/www.qq.com","img":"http:\/\/3gtest.gionee.com:88\/attachs\/App\/201212\/145001.jpg","title":"\u5e94\u7528_\u7530\u91ce","star":"2","appInfo":"\u63cf\u8ff0_\u7530\u91ce","appType":"\u5206\u7c7b4_!@$","addUrl":"http:\/\/3gtest.gionee.com:88\/api\/app\/index?id=16"},{"id":"15","link":"http:\/\/www.qq.com","img":"http:\/\/3gtest.gionee.com:88\/attachs\/App\/201212\/144917.jpg","title":"\u5e94\u7528_\u6811\u6797","star":"3","appInfo":"\u63cf\u8ff0_\u6811\u6797","appType":"\u5206\u7c7b4_!@$","addUrl":"http:\/\/3gtest.gionee.com:88\/api\/app\/index?id=15"},{"id":"14","link":"http:\/\/www.qq.com","img":"http:\/\/3gtest.gionee.com:88\/attachs\/App\/201212\/144750.jpg","title":"\u5e94\u7528_\u67e0\u6aac","star":"5","appInfo":"\u63cf\u8ff0_\u67e0\u6aac","appType":"\u5206\u7c7b4_!@$","addUrl":"http:\/\/3gtest.gionee.com:88\/api\/app\/index?id=14"}],"hasnext":"true","curpage":1}}
				</div>
				<!--*********************************************下面是页面模板 **************************************-->
				<!-- 推荐应用模板 -->
				<script id="J_itemView" type="text/template">
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
			</div>
		</div>
	</div>
</body>
</html>
