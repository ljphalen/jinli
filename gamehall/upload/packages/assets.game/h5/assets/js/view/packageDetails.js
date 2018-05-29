var PackageDetails  = React.createClass({displayName: "PackageDetails",
	getInitialState:function(){
		return {
			layer:{
				display:'none'
			}
		}
	},
	handleClick:function(type){
		if(type){
			this.state.layer.display = type;
			this.setState();
		}else{
			location.href=this.props.items.download;
		}
	},
	render:function(){
		var item = this.props.items;
		return (
			React.createElement("div", {className: "package-details"}, 
				React.createElement("div", {className: "basic"}, 
					React.createElement("div", {className: "package-details-left"}, 
						React.createElement(PackageDetailsInfo, {items: item})
					), 
					React.createElement("div", {className: "package-details-right"}, 
						React.createElement("span", {onClick: this.handleClick, className: "icon-grab"})
					)
				), 
				React.createElement("div", {className: "explain"}, 
					React.createElement("h3", null, "礼包内容"), 
					React.createElement("p", null, item.contents)
				), 
				React.createElement("div", {className: "explain"}, 
					React.createElement("h3", null, "使用时间"), 
					React.createElement("p", null, item.date)
				), 
				React.createElement("div", {className: "explain"}, 
					React.createElement("h3", null, "使用方法"), 
					React.createElement("p", null, item.useMethod)
				), 
				React.createElement(PopLayerDouble, {msg: "需要安装游戏大厅客户端才能领取礼包， 现在是否下载？", click: this.handleClick, parent: this})
			)
		)
	}
});

var PackageList =  React.createClass({displayName: "PackageList",
	getInitialState:function(){
		return {
			curPage:0
			,data:[]
			,hasNext:this.props.type
		}
	},
	render:function(){
		return(
			React.createElement("div", {className: "package-list"}, 
			
				this.state.data.map(function(item,i){
					return (
						React.createElement("a", {href: item.href, key: i}, 
							React.createElement("div", {className: "package-list-row"}, 
								React.createElement("div", {className: "package-info"}, 
									React.createElement("img", {className: "game-photo lazy", src: blankPic, "data-original": item.imgUrl}), 
									React.createElement("h3", {className: "package-name omit"}, item.name), 
									React.createElement(ProgressBar, {total: item.total, residual: item.residual}), 
									React.createElement("p", null, React.createElement("span", null, "剩余：", item.residual, "个"))
								)
							)
						)
					)
				}), 
			
			React.createElement(LoadMore, {name: "加载更多", ajaxUrl: this.props.ajaxUrl, parent: this})
			)
		);
	}
});
var PackageDetailsInfo = React.createClass({displayName: "PackageDetailsInfo",
	render:function(){
		var item = this.props.items;
		return (
			React.createElement("div", {className: "package-info"}, 
				React.createElement("img", {className: "package-photo lazy", src: blankPic, "data-original": item.imgUrl}), 
				React.createElement("h3", {className: "package-name omit"}, item.name), 
				React.createElement(ProgressBar, {total: item.total, residual: item.residual}), 
				React.createElement("div", {className: "residual omit"}, "剩余：", item.residual, "个")
			)
		);
	}
});