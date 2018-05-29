<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<title></title>
	<meta name="keyword" content="金立" />
	<?php include '_inc.php';?>
</head>
<body>
<!-- new -->
<div id="page" class="ui-refresh home">
	<article class="ac">
		<div class="topic-box clearfix">
			<ul>
				<li><a href="">
					<img src="<?php echo "$appPic/pic_ktTopic.jpg";?>" alt="">
					<em>卡通专题</em>
				</a></li>
				<li><a href="">
					<img src="<?php echo "$appPic/pic_dcTopic.jpg";?>" alt="">
					<em>多彩专题</em>
				</a></li>
			</ul>
		</div>
		<div class="item-box clearfix">
			<h3>新品推荐</h3>
			<ul>
				<?php for($i=0; $i<6; $i++){?>
				<li>
					<a data-infTheme="12,世界之窗,detail.php,download.php,since">
						<span class="pic"><img src="<?php echo $appPic;?>/pic_nopreview.png"
							data-lazyload="true" data-src="http://theme.3gtest.gionee.com/attachs/file/201303/qcns203301/pre_face_s.jpg"></span>
						<em>青春光影</em>
					</a>
				</li>
				<?php }?>
			</ul>
		</div>
		<div id="J_dataList" class="data-box item-box" data-ajaxurl="json.php?firstpage=6">
            <h3>精品推荐</h3>
            <ul>
				<?php for($i=0; $i<2; $i++){?>
				<li>
					<a data-infTheme="12,世界之窗,detail.php,download.php,since">
						<span class="pic"><img src="<?php echo $appPic;?>/pic_nopreview.png"
							data-lazyload="true" data-src="http://theme.3gtest.gionee.com/attachs/file/201303/qcns203301/pre_face_s.jpg"></span>
						<em>青春光影</em>
					</a>
				</li>
				<?php }?>
				<li><a href=""><span><img src="http://dev.assets.gionee.com/apps/theme/pic/banner_theme_02.png"></span></a></li>
		    </ul>
            <div class="ui-refresh-down none clearfix"></div>
		</div>
		<div class="topic-box clearfix J_home_topic">
			<ul>
				<li>
					<a href="topic.php">
						<img src="<?php echo "$appPic/pic_ktTopic.jpg";?>" alt="">
						<em>历史专题</em>
					</a>
				</li>
				<li>
					<a href="cla.php">
						<img src="<?php echo "$appPic/pic_dcTopic.jpg";?>" alt="">
						<em>精品专题</em>
					</a>
				</li>
			</ul>
		</div>
	</article>
</div>
<script id="J_itemView" type="text/html">
	<% for (var i = 0; i < data.list.length; i++) { %>
    	<li>
    		<a data-infTheme="<%= data.list[i].id %>,<%= data.list[i].title %>,detail.php,download.php,<%= data.list[i].since %>">
    			<span class="pic"><img data-lazyload="true" src="<?php echo $appPic; ?>/pic_nopreview.png" data-src="<%= data.list[i].img %>"></span>
    			<em><%= data.list[i].title %></em>
    		</a>
    	</li>
	<% } %>
    <% if (data.hasmore === true) { %>
        <li><a href=""><span><img src="http://assets.3gtest.gionee.com/apps/theme/pic/banner_theme.png"></span></a></li>
    <% } %>
</script>
</body>
</html>