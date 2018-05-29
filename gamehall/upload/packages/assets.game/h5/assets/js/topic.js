$(function(){

	if(topicData.topicInfo.type=='blue'|| topicData.topicInfo.type=='white'){
		topicData.topicInfo.LoadMoreName = '加载更多';
		React.render(
			React.createElement(TopicSingleList, {items: topicData})
		  , document.getElementById('container')
		);
		return;
	}
	if(topicData.topicInfo.type == 'multicolor'){
		React.render(
			React.createElement(TopicDoubleList, {items: topicData})
			,document.getElementById('container')
		);
		return ;
	}
	
	React.render(
		React.createElement(TopicDoubleListTow,{items:topicData})
		,document.getElementById('container')
	);
})