var GameRowInfo = React.createClass({displayName: "GameRowInfo",
	render:function(){
		var item = this.props.items
			,style = {}
		;
		if(item.typeName&&item.size){
			style.val = '|';
			style.name = 'padding';
		}
		return (
			React.createElement("div", {className: "game-info"}, 
				React.createElement("a", {href: item.href}, 
					React.createElement("img", {className: "game-photo lazy", src: blankPic, "data-original": item.imgUrl}), 
					React.createElement("h3", {className: "game-name omit"}, item.name), 
					React.createElement("div", {className: "game-size-type"}, React.createElement("span", null, item.typeName), React.createElement("span", {className: style.name}, style.val), React.createElement("span", null, item.size))
				), 
				React.createElement("a", {href: item.download}, React.createElement("input", {type: "button", value: "下载", className: "dow-list-but"}))
			)
		);
	}
});
var Collection  = React.createClass({displayName: "Collection",
	render:function(){
		return (
				React.createElement("div", {className: "collection"}, 
				React.createElement("h1", {className: "group-name"}, this.props.items.name, React.createElement("span", {className: "arrow-down"})), 
				React.createElement("ul", null, 
				
					this.props.items.data.map(function(item,i){
						return (
							React.createElement("li", {key: i}, 
								React.createElement(GameRowInfo, {items: item})
							)
						)
					})
				
				), 
				React.createElement(LoadMore, {name: this.props.items.LoadMoreName, href: this.props.items.LoadMoreHref})
				)
		);
	}
});