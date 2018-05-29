var GameList =  React.createClass({displayName: "GameList",
	getInitialState:function(){
		return {
			curPage:this.props.items.curPage
			,hasNext:this.props.items.hasNext
		}
	},
	render:function(){
		return (
				React.createElement("div", {className: "game-list"}, 
				
					this.props.items.list.map(function(item,i){
						return (
							React.createElement("div", {className: "game-list-row", key: i}, 
								React.createElement("a", {href: item.href}, 
									React.createElement("div", {className: "game-list-left"}, 
										React.createElement(GameColumnInfo, {items: item})
									)
								), 
								React.createElement("div", {className: "game-list-right"}, 
									React.createElement("a", {href: item.download}, React.createElement("input", {className: "dow-list-but", type: "button", value: "下载"}))
								)
							)
						)
					}), 
				
				React.createElement(LoadMore, {name: this.props.items.LoadMoreName, href: this.props.items.LoadMoreHref, ajaxUrl: this.props.items.ajaxUrl, parent: this, isData: this.props.items.list.length})
				)
		);
	}
});

var Ranking  = React.createClass({displayName: "Ranking",
	getInitialState:function(){
		return {
			curPage:this.props.items.curPage
			,hasNext:this.props.items.hasNext
		}
	},
	render:function(){
		var now = this.props.items.now
			,href = this.props.items.LoadMoreHref || this.props.items.href
		;
		return (
				React.createElement("div", {className: "ranking-list game-list"}, 
				React.createElement("h1", {className: "group-name "}, this.props.items.name, React.createElement("span", {className: "arrow-down"})), 
				
					this.props.items.data.map(function(item,i){
						return (
							React.createElement("div", {className: "game-list-row", key: i}, 
								React.createElement("a", {href: item.href}, 
									React.createElement("div", {className: "game-list-left"}, 
										now?React.createElement(Now, {index: i+1}):"", 
										React.createElement(GameColumnInfo, {items: item})
									)
								), 
								React.createElement("div", {className: "game-list-right"}, 
									React.createElement("a", {href: item.download}, React.createElement("input", {className: "dow-list-but", type: "button", value: "下载"}))
								)
							)
						)
					}), 
				
				React.createElement(LoadMore, {name: this.props.items.LoadMoreName, href: href, ajaxUrl: this.props.items.ajaxUrl, parent: this, isData: this.props.items.data.length})
				)
		);
	}
});
var DateRanking = React.createClass({displayName: "DateRanking",
	getInitialState:function(){
		return {
			curPage:this.props.items.curPage
			,hasNext:this.props.items.hasNext
		}
	},
	render:function(){
		return (
			React.createElement("div", {className: "game-list date-ranking-list"}, 
			React.createElement("span", {className: "line-orange"}), 
			
				this.props.items.list.map(function(item,i){
					var start
						,paddingTop = '0px'
					;
					if(item.date!=this.date){
						paddingTop = '15px';
						this.date = item.date;
						start = '<h1 class="group-name"><span class="icon-hollow-circle"></span>'+ item.date +'</h1>';
					}
					return (
						React.createElement("div", {key: i}, 
							React.createElement("div", {dangerouslySetInnerHTML: {__html:start}}
							), 
							React.createElement("div", {className: "date-ranking-row", style: {paddingTop:paddingTop}}, 
								React.createElement("a", {href: item.href}, 
									React.createElement("div", {className: "game-list-left"}, 
										React.createElement(GameColumnInfo, {items: item})
									)
								), 
								React.createElement("div", {className: "game-list-right"}, 
									React.createElement("a", {href: item.download}, React.createElement("input", {className: "dow-list-but", type: "button", value: "下载"}))
								)
							)
						)
					)
				},this), 
			
			React.createElement(LoadMore, {name: this.props.items.LoadMoreName, ajaxUrl: this.props.items.ajaxUrl, parent: this, isData: this.props.items.list.length})
			)
		);
	}
});

var RankingDouble  = React.createClass({displayName: "RankingDouble",
	getInitialState:function(){
		var color = ['#ff6b50','#00d8ff','#9654b6','#53d37e'];
		return {
			color:color[this.props.index]
		}
	},
	render:function(){
		var color = this.state.color;
		return (
				React.createElement("div", {className: "game-double-list"}, 
				React.createElement("h1", {className: "group-name", style: {color:color}}, 
				React.createElement("span", {className: "omit"}, this.props.items.name), 
				React.createElement("span", {className: "arrow-down", style: {borderTopColor:color}})), 
				React.createElement("p", {className: "intr"}, this.props.items.info), 
				React.createElement("ul", {className: "table"}, 
				
					this.props.items.data.map(function(item,i){
						var right;
						if(i%2){
							right = 'game-double-list-right'
						}
						return (
							React.createElement("li", {className: right, key: i}, 
								React.createElement(GameDoubleInfo, {items: item, color: color})
							)
						)
					})
				
				)
				)
		);
	}
});

var RankingDoubleTwo  = React.createClass({displayName: "RankingDoubleTwo",
	getInitialState:function(){
		var color = ['#ff6b50','#00d8ff','#ff9c00','#53d37e']
			,bg = ['#fff4ef','#f0fdff','#fff7ed','#f8ffee']
			,shadow = ['#ae3c27','#0093ad','#ae6200','#378b53']
			,index = this.props.index
		;
		return {
			color:color[index],
			bg:bg[index],
			shadow:shadow[index]
		}
	},
	render:function(){
		var color = this.state.color;
		return (
				React.createElement("div", {className: "game-double-list", style: {backgroundColor:this.state.bg}}, 
				React.createElement("h1", {className: "group-name icon-title", style: {backgroundColor:this.state.color}}, 
				React.createElement("span", {style: {borderTopColor:this.state.shadow}, className: "icon-title-arrow-bg"}), 
				React.createElement("span", {className: "omit"}, this.props.items.name), 
				React.createElement("span", {style: {borderRightColor:this.state.bg}, className: "icon-title-arrow"})
				), 
				React.createElement("p", {className: "intr"}, this.props.items.info), 
				React.createElement("ul", {className: "table"}, 
				
					this.props.items.data.map(function(item,i){
						var right;
						if(i%2){
							right = 'game-double-list-right'
						}
						return (
							React.createElement("li", {className: right, key: i}, 
								React.createElement(GameDoubleInfo, {items: item, index: i, color: color})
							)
						)
					})
				
				)
				)
		);
	}
});
