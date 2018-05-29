<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<title></title>
	<meta name="keyword" content="金立" />
	<?php include '_inc.php';?>
</head>
<body>
<div id="page" class="ui-refresh">
	<div id="J_dataList" class="data-box topic-box" data-ajaxurl="json.php?firstpage=6">
		<ul>
			<?php for($i=0; $i<10; $i++){?>
			<li><a href="">
				<img src="<?php echo "$appPic/pic_ktTopic.jpg";?>" alt="">
				<em>卡通专题</em>
			</a></li>
			<?php }?>
		</ul>
		<div class="ui-refresh-down none clearfix"></div>
	</div>
</div>
<script id="J_itemView" type="text/html">
	<% for (var i = 0; i < data.list.length; i++) { %>
    	<li>
    		<a data-infTheme="<%= data.list[i].id %>,<%= data.list[i].title %>,detail.php,download.php,<%= data.list[i].since %>">
    			<img data-lazyload="true" src="<%= data.list[i].img %>" data-src="<%= data.list[i].bg_img1 %>">
    			<em><%= data.list[i].title %></em>
    		</a>
    	</li>
	<% } %>
</script>
</body>
</html>