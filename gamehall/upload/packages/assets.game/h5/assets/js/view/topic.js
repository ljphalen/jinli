var TopicInfo = React.createClass({displayName: "TopicInfo",
	render:function(){
		var typeIcon,
			item = this.props.items,
			infoStyle
		;
		if(item.type)
			typeIcon = React.createElement(TypeIcon, {type: item.type});
		if(!item.info){
			infoStyle = {
				display:'none'
			}
		}
		return (
				React.createElement("div", null, 
					typeIcon, 
					React.createElement("img", {src: blankPic, "data-original": item.imgUrl, className: "topic-poster lazy"}), 
					React.createElement("div", {className: "topic-info"}, 
						React.createElement("h3", {className: "topic-name omit"}, React.createElement("span", {className: "name omit"}, item.activityName||item.name), React.createElement("span", {className: "date"}, item.date)), 
						React.createElement("div", {className: "multi-line-omit ui-editor", style: infoStyle, dangerouslySetInnerHTML: {__html:item.info}})
					)
				)
		);		
	}
});
var TopicInfoStar = React.createClass({displayName: "TopicInfoStar",
	render:function(){
		var item = this.props.items;
		return (
			React.createElement("div", {className: "game-info"}, 
				React.createElement("img", {className: "game-photo lazy", src: blankPic, "data-original": item.imgUrl}), 
				React.createElement("h3", {className: "game-name omit"}, item.name), 
				React.createElement("div", {className: "game-size-stars"}, 
					React.createElement(Star, {num: item.stars, url: this.props.imgUrl}), 
					React.createElement("div", {className: "game-size"}, React.createElement("span", null, item.size))
				), 
				React.createElement("p", {className: "intr omit"}, item.info)
			)
		);
	}
});
var TopicDoubleInfo = React.createClass({displayName: "TopicDoubleInfo",
	render:function(){
		var item = this.props.items;
		return (
			React.createElement("div", null, 
				React.createElement("div", null, 
					React.createElement("a", {href: item.href}, 
						React.createElement("div", {className: "game-info game-list-left"}, 
							React.createElement("img", {className: "game-photo lazy", src: blankPic, "data-original": item.imgUrl}), 
							React.createElement("h3", {className: "game-name omit"}, item.name), 
							React.createElement(Star, {num: item.stars, url: this.props.imgUrl}), 
							React.createElement("div", {className: "game-type"}, React.createElement("span", null, item.type), "|", React.createElement("span", null, item.size), "|", React.createElement("span", null), item.language)
						)
					), 
					React.createElement("div", {className: "game-list-right"}, 
						React.createElement("a", {href: item.download}, React.createElement("input", {className: "dow-list-but", type: "button", value: "下载"}))
					)
				), 
				React.createElement("div", {className: "intr-img"}, 
					React.createElement("img", {src: blankPic, "data-original": item.posters[0], className: "lazy"}), 
					React.createElement("img", {src: blankPic, "data-original": item.posters[1], className: "right lazy"})
				), 
				React.createElement("p", {className: "intr multi-line-omit"}, item.info)
			)
		);
	}
});
var TopicDoubleInfoTwo = React.createClass({displayName: "TopicDoubleInfoTwo",
	getInitialState:function(){
		var color = ['#ff6b50','#00d8ff','#ff9c00','#53d37e']
			,bg = ['#fff4ef','#f0fdff','#fff7ed','#f8ffee',]
			,index = this.props.index
		;
		return {
			color:color[index],
			bg:bg[index]
		}
	},
	render:function(){
		var item = this.props.items;
		return (
			React.createElement("div", {className: "game-double-info", style: {backgroundColor:this.state.bg}}, 
				React.createElement("div", null, 
					React.createElement("a", {href: item.href}, 
						React.createElement("div", {className: "game-info game-list-left"}, 
							React.createElement("img", {className: "game-photo lazy", src: blankPic, "data-original": item.imgUrl}), 
							React.createElement("h3", {style: {color:this.state.color}, className: "game-name omit"}, item.name), 
							React.createElement(Star, {num: item.stars, url: this.props.imgUrl}), 
							React.createElement("div", {className: "game-type"}, React.createElement("span", null, item.type), "|", React.createElement("span", null, item.size), "|", React.createElement("span", null), item.language)
						)
					), 
					React.createElement("div", {className: "game-list-right"}, 
						React.createElement("a", {href: item.download}, React.createElement("input", {style: {backgroundColor:this.state.color}, className: "dow-list-but", type: "button", value: "下载"}))
					)
				), 
				React.createElement("div", {className: "intr-img"}, 
					React.createElement("img", {src: blankPic, "data-original": item.posters[0], className: "lazy"}), 
					React.createElement("img", {src: blankPic, "data-original": item.posters[1], className: "right lazy"})
				), 
				React.createElement("p", {className: "intr multi-line-omit"}, item.info)
			)
		);
	}
});
var TypeIcon = React.createClass({displayName: "TypeIcon",
	getInitialState:function(){
		var type = {
			end:'已过期',
			in:'进行中'
		}
		return type;
	},
	render:function(){

		return (
			React.createElement("div", {className: "icon-type"}, 
				React.createElement("div", {className: "font"}, this.state[this.props.type]), 
				React.createElement("div", {className: this.props.type})
			)
		);
	}
});
var TopicList  = React.createClass({displayName: "TopicList",
	getInitialState:function(){
		return {
			curPage:this.props.items.curPage
			,hasNext:this.props.items.hasNext
			
		}
	},
	render:function(){
		return (
			React.createElement("div", {className: "topic"}, 
			
				this.props.items.list.map(function(item,i){
					return(
						React.createElement("a", {href: item.href, key: i}, 
							React.createElement(TopicInfo, {items: item})
						)
					);
				}), 
			
			React.createElement(LoadMore, {name: this.props.items.LoadMoreName, href: this.props.items.LoadMoreHref, ajaxUrl: this.props.items.ajaxUrl, isData: this.props.items.list.length, parent: this})
			)
		);
	}
});
var TopicDoubleHot = React.createClass({displayName: "TopicDoubleHot",
	render:function(){
		var item = this.props.items;
		return (
			React.createElement("div", {className: "other"}, 
			React.createElement("a", {href: item[0].href}, 
				React.createElement("div", {className: "row"}, 
					React.createElement("div", {style: {marginRight:'10px'}}, 
						React.createElement("img", {src: blankPic, "data-original": item[0].imgUrl, className: "lazy"}), 
						React.createElement("h1", {className: "group-name omit"}, item[0].name)
					)
				)
			), 
			React.createElement("a", {href: item[1].href}, 
				React.createElement("div", {className: "row"}, 
					React.createElement("div", {style: {marginLeft:'10px'}}, 
						React.createElement("img", {className: "lazy", src: blankPic, "data-original": item[1].imgUrl}), 
						React.createElement("h1", {className: "group-name omit"}, item[1].name)
					)
				)
			)
			)
		)
	}
})
var TopicDoubleList = React.createClass({displayName: "TopicDoubleList",
	render:function(){
		return (
		React.createElement("div", {className: this.props.items.topicInfo.type+" topic game-list"}, 
			React.createElement(TopicInfo, {items: this.props.items.topic, type: this.props.items.topicInfo.type}), 
			React.createElement("div", {className: "customize-block"}, 
			
				this.props.items.topicInfo.list.map(function(item,index){
					var info;
					if( item.type=='list' ){
						info = React.createElement(RankingDouble, {items: item, index: index})
					}else{
						info = React.createElement(TopicDoubleInfo, {items: item, imgUrl: this.props.items.picturePath, index: index})
					}
					return (
						React.createElement("div", null, 
						info
						)
					)
				},this), 
			
			React.createElement(TopicDoubleHot, {items: this.props.items.hot})
			)
		)
		)
	}
});
var TopicDoubleListTow = React.createClass({displayName: "TopicDoubleListTow",
	render:function(){
		return (
		React.createElement("div", {className: this.props.items.topicInfo.type+" topic game-list"}, 
			React.createElement(TopicInfo, {items: this.props.items.topic, type: this.props.items.topicInfo.type}), 
			React.createElement("div", {className: "customize-block"}, 
			
				this.props.items.topicInfo.list.map(function(item,index){
					var info = "";
					if( item.type=='list' ){
						info = React.createElement(RankingDoubleTwo, {items: item, index: index})
					}else{
						info = React.createElement(TopicDoubleInfoTwo, {items: item, index: index, imgUrl: this.props.items.picturePath})
					}
					return (
						React.createElement("div", null, 
							info
						)
					);
				},this), 
				
			
			React.createElement(TopicDoubleHot, {items: this.props.items.hot})
			)
		)
		)
	}
});

var TopicSingleList = React.createClass({displayName: "TopicSingleList",
	getInitialState:function(){
		return {
			curPage:this.props.items.topicInfo.curPage
			,hasNext:this.props.items.topicInfo.hasNext
		}
	},
	render:function(){
		var img;
		if(this.props.items.topicInfo.type == 'blue'){
			img = "<img src="+this.props.items.picturePath+"/galf.png />";
		}
		return(
			React.createElement("div", {className: this.props.items.topicInfo.type+" topic game-list"}, 
				React.createElement(TopicInfo, {items: this.props.items.topic, type: this.props.items.topicInfo.type}), 
				React.createElement("div", {className: "galf-line", dangerouslySetInnerHTML: {__html:img}}), 
				React.createElement("div", {className: "customize-block"}, 
				
					this.props.items.topicInfo.list.map(function(item,i){
						return (
							React.createElement("div", {className: "game-list-row", key: i}, 
								React.createElement("a", {href: item.href}, 
									React.createElement("div", {className: "game-list-left"}, 
										React.createElement(TopicInfoStar, {items: item, imgUrl: this.props.items.picturePath})
									)
								), 
								React.createElement("div", {className: "game-list-right"}, 
									React.createElement("a", {href: item.download}, React.createElement("input", {className: "dow-list-but", type: "button", value: "下载"}))
								)
							)
						);
					},this), 
				
				React.createElement(LoadMore, {name: this.props.items.topicInfo.LoadMoreName, ajaxUrl: this.props.items.topicInfo.ajaxUrl, parent: this, isData: this.props.items.topicInfo.list.length})
				)
			)
		);
	}
});

var Activity  = React.createClass({displayName: "Activity",

	render:function(){
		var item = this.props.items
			,infoStyle;
		if(!item.info){
			infoStyle = {
				display:'none'
			}
		}
		return (
				React.createElement("div", {className: "activity"}, 
				React.createElement("a", {href: this.props.items.href}, 
					React.createElement("div", null, 
						React.createElement("img", {src: blankPic, "data-original": item.imgUrl, className: "activity-poster lazy"}), 
						React.createElement("div", {className: "activity-info"}, 
							React.createElement("h3", {className: "activity-name omit"}, item.activityName||item.name), 
							React.createElement("div", {className: "multi-line-omit ui-editor", style: infoStyle, dangerouslySetInnerHTML: {__html:item.info}})
						)
					)
				), 
				React.createElement(LoadMore, {name: this.props.items.LoadMoreName, href: this.props.items.LoadMoreHref})
				)
		);
	}
});

var ActivityInfo = React.createClass({displayName: "ActivityInfo",
	render:function(){
		return (
			React.createElement("div", {id: "activity", className: "activity"}, 
			React.createElement("div", {id: "activityRoll"}, 
			
				this.props.items.map(function(item,i){
					return (
						
						React.createElement("div", {key: i, className: i?"activity-info":"on activity-info"}, 
							React.createElement("a", {href: item.href}, 
							React.createElement("div", {className: "activity-name"}, "【活动】", item.name), 
							React.createElement("div", null, "活动时间：", item.date), 
							React.createElement("div", null, "奖励内容：", item.rewardRule)
							)
						)
					);
				})
			
			), 
			React.createElement("div", {id: "activityRollCopy"})
			)
		);
	}
});
var ActivityDetails  = React.createClass({displayName: "ActivityDetails",
	render:function(){
		var item = this.props.items
			,download
			;
		
		if(item.download){
			download = "<a href="+item.download+"><input type='button' value='下载' class='dow-list-but' /></a>";
		}
		return (
			React.createElement("div", {className: "activity-details"}, 
				React.createElement("h1", {className: "name omit"}, item.name), 
				React.createElement("img", {src: blankPic, "data-original": item.imgUrl, className: "topic-poster lazy"}), 
				React.createElement("div", {className: "activity-info"}, 
					React.createElement("div", {className: "block"}, 
						React.createElement("div", {className: "ui-editor", dangerouslySetInnerHTML: {__html:item.contents}})
					), 
					React.createElement("div", {className: "block", dangerouslySetInnerHTML: {__html:download}}
					
					)
				)
			)
		)
	}
});

