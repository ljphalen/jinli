var SearchGameList = React.createClass({displayName: "SearchGameList",
	render:function(){
		var item = this.props.items;
		return (
			React.createElement("div", {className: "game-list"}, 
				React.createElement("div", {className: "game-list-row"}, 
					React.createElement("a", {href: item.href}, 
						React.createElement("div", {className: "game-list-left"}, 
								React.createElement("div", {className: "game-info"}, 
								React.createElement("img", {className: "game-photo lazy", src: blankPic, "data-original": item.img}), 
								React.createElement("h3", {className: "game-name omit", dangerouslySetInnerHTML: {__html:item.name}}), 
								React.createElement("div", {className: "game-size-stars"}, 
									React.createElement(Star, {num: item.score, url: this.props.imgUrl}), 
									React.createElement("div", {className: "game-size"}, React.createElement("span", null, item.size))
								), 
								React.createElement("p", {className: "intr omit"}, item.resume)
							)
						)
					), 
					React.createElement("div", {className: "game-list-right"}, 
						React.createElement("a", {href: item.link?item.link:'javascript:'}, 
							React.createElement("input", {className: item.link?"dow-list-but":"dow-list-but no-dow-list-but", type: "button", value: "下载"})
						)
					)
				)
			)
		);
	}
});
var SearchGameGuessList = React.createClass({displayName: "SearchGameGuessList",
	render:function(){
		var item = this.props.items;
		return (
			React.createElement("a", {href: item.href}, 
			React.createElement("div", {className: "game-list guess-list"}, 
				React.createElement("div", {className: "game-list-row"}, 
					React.createElement("span", {className: "icon-search", "data-reactid": ".0.0.1.0"}), 
					React.createElement("div", {className: "game-name omit", dangerouslySetInnerHTML: {__html:item.name}})
				)
			)
			)
		);
	}
});
var SearchDelBug =  React.createClass({displayName: "SearchDelBug",
	handleClick:function(){
		$('#search').val('');
		$('#hotWrds').show();
		$('#searchDel').hide();
		this.props.parent.state.list = [];
		this.props.parent.state.gameItems = [];
		this.props.parent.forceUpdate();
	},
	render:function(){
		return(
			React.createElement("div", {className: "icon-del-padding"}, 
				React.createElement("span", {className: "icon-del-but", id: "searchDel", onClick: this.handleClick})
			)
		)
	}
});
var SearchText = React.createClass({displayName: "SearchText",
	getInitialState:function(){
		return {
			list:[]
			,gameItems:[]
		}
	},
	handleClick:function(){
		var val = $('#search').val();
		
		$('#searchDel').css({display:'inline-block'});
		if(!val){
			$('#searchDel').hide();
		}
		
		this.search.send({
			data:{
				keyword:val
			}
		});
	},
	componentDidMount:function(){
		var self = this;
		this.search = new Model({
			url:this.props.ajaxUrl
			,succ:function(res){
				self.state.gameItems = res.data.gameItems;
				self.state.list = [];
				self.forceUpdate();
				self.setState({list:res.data.list });
				$("img[data-original]").lazyload();
				
				if( res.data.gameItems.length )
					$('#hotWrds').hide();
				else
					$('#hotWrds').show();
			}
		});
		$('#search').focus();
	},
	render:function(){
		var imgUrl = this.props.imgUrl;
		return (
			React.createElement("div", null, 
				React.createElement("div", {className: "search-text"}, 
					React.createElement("input", {type: "text", onKeyUp: this.handleClick, placeholder: this.props.defaultSearch, id: "search"}), 
					React.createElement(SearchDelBug, {parent: this})
				), 
				React.createElement("div", {className: "search-key-content"}, 
				
					this.state.gameItems.map(function(item,i){
						return (
							React.createElement(SearchGameList, {items: item, imgUrl: imgUrl, key: i})
						)
					}), 
				
				
					this.state.list.map(function(item,i){
						return (
							React.createElement(SearchGameGuessList, {items: item})
						)
					})
				
				)
			)
		);
	}
});
var SearchHeader = React.createClass({displayName: "SearchHeader",
	handleClick:function(){
		var val = $('#search').val();
		if(val.replace(/(^\s*)|(\s*$)/g, "")){
			location.href = this.props.href+'&keyword='+val+'&intersrc='+'isearch';
			return;
		}
		if(this.props.defaultSearch){
			location.href = this.props.href+'&keyword='+this.props.defaultSearch+'&intersrc='+'dsearch';
			return;
		}
		alert('请输入搜索词');
	},
	render:function(){
		return (
			React.createElement("div", {className: "search-header"}, 
				React.createElement(SearchText, {ajaxUrl: this.props.ajaxUrl, imgUrl: this.props.imgUrl, defaultSearch: this.props.defaultSearch}), 
				React.createElement("div", {className: "absolute-vCenter search-page"}, 
					React.createElement("span", {className: "icon-search", onClick: this.handleClick})
				)
			)
		);
	}
})

var SearchGameHotWords = React.createClass({displayName: "SearchGameHotWords",
	render:function(){
		return(
			React.createElement("div", {className: "search"}, 
				React.createElement(SearchHeader, {ajaxUrl: this.props.items.searchMoreUrl, href: this.props.items.href, imgUrl: this.props.items.picturePath, defaultSearch: this.props.items.defaultSearch}), 
				React.createElement("div", {id: "hotWrds"}, 
				React.createElement("div", null, 
					React.createElement("h1", {className: "title"}, "热词排行榜")
				), 
					React.createElement("ul", null, 
					
						this.props.items.list.map(function(item,i){
							return (
								React.createElement(Row, {items: item, index: i, key: i})
							)
						})
					
					)
				)
			)
		);
	}
});

var SearchGame = React.createClass({displayName: "SearchGame",
	getInitialState:function(){
		return {
			curPage:this.props.items.curPage
			,hasNext:this.props.items.hasNext
		}
	},
	render:function(){
		var imgUrl = this.props.items.picturePath;
		return(
			React.createElement("div", {className: "search"}, 
				React.createElement(SearchHeader, {ajaxUrl: this.props.items.searchMoreUrl, keyword: this.props.items.keyword, href: this.props.items.href, imgUrl: imgUrl}), 
				React.createElement("div", {className: "prompt"}, 
					this.props.items.resum?'以下内容来自互联网,可能不适配您的手机':''
				), 
				React.createElement("div", {id: "hotWrds", className: "search-key-content"}, 
				
					this.props.items.list.map(function(item,i){
						return (
							React.createElement(SearchGameList, {items: item, key: i, imgUrl: imgUrl})
						)
					}), 
				
					React.createElement(LoadMore, {ajaxUrl: this.props.items.ajaxUrl, parent: this, isData: this.props.items.list.length, id: 'loadMore'})
				)
			)
		);
	}
});