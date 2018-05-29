var InfoBulletin  = React.createClass({displayName: "InfoBulletin",
	render:function(){
		var imgUrl = this.props.imgUrl;
		return (
				React.createElement("div", {className: "info-bulletin-list"}, 
				React.createElement("h1", {className: "group-name"}, this.props.items.name, React.createElement("span", {className: "arrow-down"})), 
				
					this.props.items.list.map(function(item,i){
						return (
							React.createElement("div", {className: "info-bulletin-list-row", key: i}, 
								React.createElement("a", {href: item.href}, 
									React.createElement("div", {className: "info-bulletin-list-left"}, 
										React.createElement("img", {"data-original": item.imgUrl, className: "posters lazy", src: blankPic})
									), 
									React.createElement("div", {className: "info-bulletin-list-right"}, 
										React.createElement("h3", {className: "bulletin-name"}, item.name), 
										React.createElement("div", {className: "multi-line-omit ui-editor", dangerouslySetInnerHTML: {__html:item.info}})
									)
								)
							)
						)
					}), 
				
				React.createElement(LoadMore, {name: this.props.items.LoadMoreName, href: this.props.items.LoadMoreHref})
				)
		);
	}
});
var InfoBulletinList = React.createClass({displayName: "InfoBulletinList",
	getInitialState:function(){
		return {
			curPage:this.props.items.curPage
			,hasNext:this.props.items.hasNext
		}
	},
	render:function(){
		var imgUrl = this.props.imgUrl;
		return (
				React.createElement("div", {className: "info-bulletin-list bulletin-list-page"}, 
				
					this.props.items.list.map(function(item,i){
						return (
							React.createElement("div", {className: "info-bulletin-list-row", key: i}, 
								React.createElement("a", {href: item.href}, 
									React.createElement("div", {className: "info-bulletin-list-left"}, 
										React.createElement("img", {className: "posters lazy", src: blankPic, "data-original": item.imgUrl})
									), 
									React.createElement("div", {className: "info-bulletin-list-right"}, 
										React.createElement("h3", {className: "bulletin-name omit"}, item.name), 
										React.createElement("div", {className: "date"}, item.date), 
										React.createElement("div", {className: "omit ui-editor", dangerouslySetInnerHTML: {__html:item.info}})
									)
								)
							)
						)
					}), 
				
				React.createElement(LoadMore, {ajaxUrl: this.props.items.ajaxUrl, parent: this, isData: this.props.items.list.length})
				)
		);
	}
});