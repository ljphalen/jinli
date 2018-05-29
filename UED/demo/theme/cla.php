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
	<article class="ac">
		<div id="J_dataList" class="item-box data-box" data-ajaxUrl="json.php">
			<ul>
				<?php for($i=0; $i<8; $i++){?>
				<li>
					<a href="detail.php">
						<span class="pic"><img src="<?php echo $appPic;?>/pic_themeItem.jpg"></span>
						<em>默认主题</em>
					</a>
				</li>
				<?php }?>
			</ul>
			<div class="ui-refresh-down none clearfix"></div>
		</div>
	</article>
</div>
<script id="J_itemView" type="text/html">
	<% for (var i = 0; i < data.list.length; i++) { %>
    	<li>
    		<a data-infTheme="<%= data.list[i].id %>,<%= data.list[i].title %>,detail.php,download.php,<%= data.list[i].since %>">
    			<span class="pic"><img data-lazyload="true" src="<%= data.list[i].img %>" data-src="<%= data.list[i].bg_img1 %>"></span>
    			<em><%= data.list[i].title %></em>
    		</a>
    	</li>
	<% } %>
</script>
</body>
</html>