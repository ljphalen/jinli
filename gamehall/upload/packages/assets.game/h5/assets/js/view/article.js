var Article =  React.createClass({displayName: "Article",
	render:function(){
		var item = this.props.items
			,link
		;
		if(item.href){
			link = "<a href="+item.href+">查看原文</a>"
		}
		return (
			React.createElement("div", {className: "article"}, 
				React.createElement("h1", {className: "name"}, item.name), 
				React.createElement("div", {className: "date"}, React.createElement("span", {className: "source"}, "来源：", item.original), React.createElement("span", null, item.date)), 
				React.createElement("div", {className: "ui-editor ", dangerouslySetInnerHTML: {__html: this.props.items.info}}), 
				React.createElement("div", {className: "look-article", dangerouslySetInnerHTML: {__html: link}}
				)
			)
		);
	}
});