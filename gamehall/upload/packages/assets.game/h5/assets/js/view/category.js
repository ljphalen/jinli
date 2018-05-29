var HotSubject=React.createClass({displayName: "HotSubject",
	render: function() {
		var items=this.props.data;
		return (
			React.createElement("div", null, 
				React.createElement("div", {className: "title-container"}, 
					React.createElement("div", {className: "absolute-vCenter"}, "热门合辑"), 
					React.createElement("a", {className: "absolute-vCenter skip", href: items.viewAllHref}, "查看全部", React.createElement("i", {className: "icon-skip"}))
				), 
				React.createElement("ul", {className: "hotSubject-list"}, 
				
					items.list.map(function(item,i){
						return(
							React.createElement("li", {key: i}, 
								React.createElement("a", {href: item.url}, 
									React.createElement("img", {src: blankPic, "data-original": item.imgUrl, alt: ""}), 
									React.createElement("div", {className: "name omit"}, item.name)
								)
							)
						)
					})
				
				)
			)
		);
	}
});

var Category=React.createClass({displayName: "Category",
	getInitialState: function() {
	    return {
	    	curPage:this.props.items.curPage
			,hasNext:this.props.items.hasNext
	    };
  	},
	render: function() {
		var data=this.props.items;
		return (
			React.createElement("div", null, 
				React.createElement("div", {className: "title-container"}, 
					React.createElement("div", {className: "absolute-vCenter"}, "游戏分类")
				), 
				React.createElement("ul", {className: "categorys-list"}, 
				
					data.list.map(function(list, i) {
						return (
							React.createElement("li", {className: "category", key: i}, 
								React.createElement("a", {className: "wrapper-link", href: list.url}, 
									React.createElement("img", {src: blankPic, "data-original": list.imgUrl, alt: ""})
								), 
								React.createElement("ul", {className: "items-list "}, 
								
									list.items.map(function(item, j) {
										return (
											React.createElement("li", {className: "item", key: j}, 
												React.createElement("a", {href: item.url}, 
												React.createElement("div", {className: "omit", dangerouslySetInnerHTML: {__html: item.title}})
												)
											)
										)
									})
								
								)
							)
						)
					})
				
				), 
				React.createElement(LoadMore, {parent: this, isData: this.props.items.list.length, ajaxUrl: categoryAjaxUrl})
			)
		);
	}
});


var activeClass="active";
var CategoryNameList=React.createClass({displayName: "CategoryNameList",
	/*getSubListFromServer:function(ajaxUrl){
		$.ajax({
			url: ajaxUrl,
			type: 'POST',
			dataType: 'json',
			data: {token: token},
			success:function(data){
				React.render(
					<GameList items={data.data}/>
					,document.getElementById('subListContainer')
				);
			}
		});
	},
	handleClick:function(i,ajaxUrl){
		var liElement=$(".J_categoryNameList").children('li').eq(i);
		if(liElement.hasClass(activeClass)) return;
		liElement.siblings('li').removeClass(activeClass);
		liElement.addClass(activeClass);
		this.getSubListFromServer(ajaxUrl);
	},*/
	render: function() {
		var items=this.props.data;
		var id=this.props.id;
		return (
			React.createElement("ul", {className: "J_categoryNameList categoryName-list"}, 
			
				items.map(function(item,i){
					var className=item.id==id?activeClass:'';
					return(
						React.createElement("li", {"data-id": item.id, key: i, className: className}, 
							React.createElement("a", {href: item.url}, 
								React.createElement("span", {className: "omit"}, item.title)
							)
						)
					)
				},this)
			
			)
		);
	}
});

var SubCategory=React.createClass({displayName: "SubCategory",
	render:function(){
		var subObj=this.props.data;
		return (
			React.createElement("div", {className: "subCategory-list"}, 
				React.createElement(CategoryNameList, {data: subObj.nameList, id: subObj.id})
			)
		)
	}
});

