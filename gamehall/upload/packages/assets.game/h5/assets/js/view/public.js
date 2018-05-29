var getComponent = function(name,item,imgUrl){
	switch(name){
		case 'collection':
			return React.createElement(Collection, {items: item});
		case 'advertising':
			return React.createElement(Advertising, {items: item});
		case 'ranking':
			return React.createElement(Ranking, {items: item});
		case 'activity':
			return React.createElement(Activity, {items: item});
		case 'infoBulletin':
			return React.createElement(InfoBulletin, {items: item});
		case 'rankingMany':
			return React.createElement(RankingMany, {items: item});
	}
}
var LoadIcon = React.createClass({displayName: "LoadIcon",
	render:function(){
		return(
			React.createElement("div", {className: "layer-load", name: "ajaxLoad"}, 
				React.createElement("div", {className: "bg-layer"}
				), 
				React.createElement("div", {className: "bg-icon"}, 
					React.createElement("i", {className: "load-icon"}), 
					React.createElement("p", {className: "msg"}, "加载中,请稍后....")
				)
			)
		);
	}
})
var LoadMore = React.createClass({displayName: "LoadMore",
	getInitialState:function(){
		return {
			style:{
				display:'block'
			}
		}
	},
	handleClick:function(){
		if(this.props.href)
			location.href = this.props.href;
		var parentState = this.props.parent.state
			,file = {}
		;
		if(parentState.file){
			for(var i in parentState.file){
				file[i] = parentState.file[i];
			}
		}
		file.page = this.props.parent.state.curPage;
		this.load.send({
			data:file
		});
	}, 
	componentDidMount: function() {
		if(!this.props.ajaxUrl)
			return;

		var parent = this.props.parent
			,self = this
			,isData = this.props.isData
			,parentItems
		;
		
		this.load = new Model({
			url:this.props.ajaxUrl
			,succ:function(res){
				var list = res.data.list;

				if(res.data.hasNext === false
					||res.data.hasNext === "false")
				{
					self.state.style.display = 'none';
					self.forceUpdate();
					if(!list.length)
						return;
				}
				if(!isData){   //初始化 未加载数据
					if(parent.state.data)
						parent.state.data =	parent.state.data.concat(list);
				}else{
					parentItems = parent.props.items;
					if(parentItems.data){
						parentItems.data = parentItems.data.concat(list);
					}else if(parentItems.list){
						parentItems.list = parentItems.list.concat(list);
					}else if(parentItems.topicInfo){
						parentItems.topicInfo.list = parentItems.topicInfo.list.concat(list);
					}
				}

				parent.state.curPage++
				parent.forceUpdate();
				$("img[data-original]").lazyload();
			},
			loadName:'[name="ajaxLoad"]'
		});
		if(parent.state.hasNext){
			parent.state.curPage++
		}else{
			self.state.style.display = 'none';
			self.forceUpdate();
			return;
		}
		if(isData){
			return;
		}
		
		this.load.send({
			data:{
				page:parent.state.curPage
			}
		});
	},
	render:function(){
		var name = this.props.name || '加载更多'
			,loadIng
			;
		if(this.props.ajaxUrl)
			loadIng = React.createElement(LoadIcon);
		
		return (
			React.createElement("div",
			{},
				React.createElement("div", {className: "look-all",id:this.props.id, style: this.state.style, onClick: this.handleClick}, 
					name, 
					React.createElement("div", {className: "load-all-array"}, 
						React.createElement("span", {className: "point"}), 
						React.createElement("span", {className: "point"}), 
						React.createElement("span", {className: "point"}), 
						React.createElement("div", {className: "arrow"})
					)
				),
				loadIng
			)
			
		);
	}
});

var Now = React.createClass({displayName: "Now",
	render:function(){
		var style = {};
		switch(this.props.index){
			case 1:
				style.background = '#ff7200';
				break;
			case 2:
				style.background = '#7fcd2b';
				break;
			case 3:
				style.background = '#00aeff';
				break;
			default:
				style.background = '#b6b6b6';
		}
		return (
			React.createElement("span", {style: style, className: "label"}, 
				this.props.index
			)
		);
	}
});
var Star = React.createClass({displayName: "Star",
	getInitialState:function(){
		var stars = []
		,length = this.props.num
		;
		
		for(var i=0; i<length;i++){
			if(i%2){
				stars.pop();
				stars.push('star');
			}else{
				stars.push('half_star');
			}
		}

		for(var i=stars.length; i<5; i++){
			stars.push('grey_star');
		}
		return {stars:stars};
	},
	render:function(){
		return (
			React.createElement("div", {className: "stars"}, 
			
				this.state.stars.map(function(name){
					return (
						React.createElement("img", {src: this.props.url+'/'+name+'.png'})
					)
				},this)
			
			)
		)
		
	}
});
var Row =  React.createClass({displayName: "Row",
	
	render:function(){
		var item = this.props.items;
		return(
			React.createElement("a", {href: item.href}, 
				React.createElement("li", {className: "row"}, 
					React.createElement(Now, {index: this.props.index+1}), 
					React.createElement("h3", {className: "game-name omit"}, item.name), 
					React.createElement("span", {className: "go-forward"})
				)
			)
		);
		
	}
});
var ProgressBar = React.createClass({displayName: "ProgressBar",
	getInitialState:function(){
		var total = this.props.total
			,residual = this.props.residual
			,width
		;
		width = residual/total*100;
		if(width<8)
			width = 8;
		width = width.toFixed(2)+'%';
		return {
			width:width
		}
	}
	,render:function(){
		return (
			React.createElement("div", {className: "progress-bar-bg"}, React.createElement("div", {style: {width:this.state.width}, className: "progress-bar"}))
		);
	}
});
var GameColumnInfo = React.createClass({displayName: "GameColumnInfo",
	render:function(){
		var item = this.props.items
			,style = {};
		if(item.typeName&&item.size){
			style.val = '|';
			style.name = 'padding';
		}
		return (
			React.createElement("div", {className: "game-info"}, 
				React.createElement("img", {className: "game-photo lazy", src:blankPic,'data-original': item.imgUrl}), 
				React.createElement("h3", {className: "game-name omit"}, item.name), 
				React.createElement("div", {className: "game-size-type omit"}, React.createElement("span", null, item.typeName), React.createElement("span", {className: style.name },  style.val ), React.createElement("span", null, item.size)), 
				React.createElement("p", {className: "omit"}, item.info)
			)
		);
	}
});
var GameInfoStar = React.createClass({displayName: "GameInfoStar",
	render:function(){
		var item = this.props.items
			,style = {};
		if(item.typeName&&item.size){
			style.val = '|';
			style.name = 'padding';
		}
		return (
			React.createElement("div", {className: "game-info"}, 
				React.createElement("img", {className: "game-photo lazy", src:blankPic,'data-original': item.imgUrl}), 
				React.createElement("h3", {className: "game-name omit"}, item.name), 
				React.createElement(Star, {num: item.stars, url: item.picturePath}), 
				React.createElement("div", {className: "game-size-type omit"}, React.createElement("span", null, item.typeName), React.createElement("span", {className: style.name}, style.val), React.createElement("span", null, item.size))
			)
		);
	}
});

var PopLayerDouble = React.createClass({displayName: "PopLayerDouble",
	handleClick:function(type){
		if(type){
			this.props.click();
		}
		this.props.parent.state.layer.display = 'none';
		this.props.parent.forceUpdate();
	},
	render:function(){
		var title = this.props.title ||'提示';
		
		return (
			React.createElement("div", {className: this.props.parent.state.layer.display+' pop-layer'}, 
				React.createElement("div", {className: "position"}, 
					React.createElement("div", {className: "title"}, 
					title
					), 
					React.createElement("p", {className: "info"}, 
					
						this.props.msg
					
					), 
					React.createElement("div", {className: "double-but"}, 
						React.createElement("input", {type: "button", onClick: this.handleClick.bind(this,true), className: "dow", value: "是"}), 
						React.createElement("input", {type: "button", onClick: this.handleClick.bind(this,false), className: "cancel", value: "否"})
					)
				)
			)
		)
	}
});


var Edition = React.createClass({displayName: "Edition",
	render:function(){
		var item = this.props.items;
		return(
			React.createElement("div", {className: "edition"}, 
				React.createElement("div", {className: "group-name"}, "相关信息"), 
				React.createElement("div", null, "当前片本：", item.edition), 
				React.createElement("div", null, "更新时间：", item.date), 
				React.createElement("div", null, "来源：", item.source)
			)
		);
	}
});

var StrategyList = React.createClass({displayName: "StrategyList",
	getInitialState:function(){
		return {
			curPage:0
			,data:[] 
			,hasNext:this.props.type
		}
	},
	render:function(){
		return (
			React.createElement("div", {className: "strategy"}, 
			
				this.state.data.map(function(item){
					return (
						React.createElement("a", {href: item.href}, 
							React.createElement("div", {className: "strategy-info"}, 
								React.createElement("div", null, 
									React.createElement("h3", {className: "strategy-name omit"}, item.name), React.createElement("span", {className: "date"}, item.date)
								), 
								React.createElement("p", {className: "multi-line-omit"}, 
									item.info
								)
							)
						)
					)
				}), 
			
			React.createElement(LoadMore, {ajaxUrl: this.props.ajaxUrl, parent: this})
			)
		);
	}
})
var GameDoubleInfo = React.createClass({displayName: "GameDoubleInfo",
	render:function(){
	var item = this.props.items;
		return (
			React.createElement("a", {href: item.href},
				React.createElement("div", {className: "game-info"}, 
					React.createElement("img", {className: "game-photo lazy", src:blankPic,'data-original': item.imgUrl}), 
					React.createElement("h3", {className: "game-name omit"}, item.name), 
					React.createElement("div", {className: "game-size-type omit"}, React.createElement("input", {type: "button",className:'dow-list-but',style:{background:this.props.color},value:'下载'}))
				),
				React.createElement("p", {className: "multi-line-omit"}, item.info)
			)
		);
	}
});
var ProbablyLike =  React.createClass({displayName: "ProbablyLike",
	render:function(){
			var name = "你可能还喜欢"
				,ProbablyShow = "block"
			;
			if(!this.props.items.length){
				ProbablyShow = "none";
			}
		return (
			React.createElement("div", {className: "probably-like", style: {"display":ProbablyShow}}, 
			React.createElement("h3", {className: "group-name"}, name), 
			React.createElement("ul", null, 
			
				this.props.items.map(function(item,i){
					return (
					React.createElement("li", null, 
						React.createElement("a", {href: item.href, key: i}, 
							React.createElement("img", {className: "game-photo lazy", src: blankPic, "data-original": item.imgUrl}), 
							React.createElement("h3", {className: "game-name omit"}, item.name)
						)
					)
					);
				})
			
			)
			)
		);
	}
});