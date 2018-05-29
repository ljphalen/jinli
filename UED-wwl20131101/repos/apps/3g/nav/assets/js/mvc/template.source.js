GNnav.template =
{
	home: //精品导航首页
		'<section class="ui-nav-wrap">\
			<%for(var i=0, len=nav.length; i<len; i++){%>\
			<div class="block">\
				<div class="ui-nav-title J_nav_title <%if(i == len-1){%>ui-nav-last<%}%>" data-id="<%=i%>"><h2><%=nav[i]%></h2></div>\
				<div class="ui-nav-content J_nav_content">\
					<ul class="ui-nav-ul"></ul>\
				</div>\
			</div>\
			<%}%>\
		</section>',
	navItem:
		'<%if(data.list.data.length){%>\
		<%for(var i=0, len=data.list.data.length; i<len; i++){%>\
		<li>\
			<a href="<%=data.list.data[i].link%>" <%if(data.list.data[i].color != ""){%>style="color:<%=data.list.data[i].color%>"<%}%>>\
				<div>\
					<img src="<%=data.list.data[i].icon%>" />\
					<span><%=data.list.data[i].name%></span>\
				</div>\
			</a>\
		</li>\
		<%}%>\
		<%}%>',
};