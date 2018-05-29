var isLandscape = false;
$(function(){
 
	var length = pageData.list.length
		,config = {
			collection:{
				component:'collection',
				LoadMoreName:'查看更多'
			},
			advertising:{
				component:'advertising'
			},
			ranking:{
				now:true,
				component:'ranking',
				LoadMoreName:'更多排行榜'
			},
			activity:{
				component:'activity',
				LoadMoreName:'更多精彩活动'
			},
			news:{
				component:'infoBulletin',
				LoadMoreName : '更多资讯公告'
			}
		}
		,pageType
		, biVar = pageData.biVar
		,jLength
		,dataList
		;

	for(var i=0; i<length; i++){
		pageType = pageData.list[i].type;
		
		if(pageData.list[i].LoadMoreHref)
			pageData.list[i].LoadMoreHref = pageData.list[i].LoadMoreHref + biVar;
		
		if(pageData.list[i].href)
			pageData.list[i].href += biVar;
		
		dataList = pageData.list[i].data || pageData.list[i].list
		if(dataList){
			jLength = dataList.length;
			for(var j=0; j<jLength; j++){
				if(dataList[j].href)
					dataList[j].href += biVar;
				
				if(dataList[j].download)
					dataList[j].download += biVar;
			}
		}
		for(var name in config[pageType]){
			pageData.list[i][name] =  config[pageType][name];
		}
	}
	length = menuData.list.length;
	for(var i=0; i<length; i++){
		menuData.list[i].href += biVar;
	}

	React.render(
		React.createElement(Roll,{items:rollData})
		,document.getElementById('roll')
	);

	React.render(
		React.createElement(Menu,{items:menuData})
	,document.getElementById('menu')
	);

	React.render(
	  React.createElement(Container, {items: pageData})
	  , document.getElementById('container')
	);

	$('#prompt [name="del"]').swipe({
		swipe:function(){
			$('#prompt').hide();
			$.get(pageData.biUri,function(){})
		},
		threshold:0
	});
	$('.index-search').show();
	
})
