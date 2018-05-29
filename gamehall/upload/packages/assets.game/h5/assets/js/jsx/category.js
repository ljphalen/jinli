var HotSubject=React.createClass({
	render: function() {
		var items=this.props.data;
		return (
			<div>
				<div className="title-container">
					<div className="absolute-vCenter">热门合辑</div>
					<a className="absolute-vCenter skip" href={items.viewAllHref}>查看全部<i className="icon-skip"></i></a>
				</div>
				<ul className="hotSubject-list">
				{
					items.list.map(function(item,i){
						return(
							<li key={i}>
								<a href={item.url} >
									<img src={blankPic} data-original={item.imgUrl} alt="" />
									<div className="name omit">{item.name}</div>
								</a>
							</li>
						)
					})
				}
				</ul>
			</div>
		);
	}
});

var Category=React.createClass({
	getInitialState: function() {
	    return {
	    	curPage:this.props.items.curPage
			,hasNext:this.props.items.hasNext
	    };
  	},
	render: function() {
		var data=this.props.items;
		return (
			<div >
				<div className="title-container">
					<div className="absolute-vCenter">游戏分类</div>
				</div>
				<ul className="categorys-list">
				{
					data.list.map(function(list, i) {
						return (
							<li className="category" key={i}>
								<a className="wrapper-link" href={list.url} >
									<img src={blankPic} data-original={list.imgUrl} alt="" />
								</a>
								<ul className="items-list ">
								{
									list.items.map(function(item, j) {
										return (
											<li className="item" key={j}>
												<a  href={item.url}>
												<div className="omit" dangerouslySetInnerHTML={{__html: item.title}}></div>
												</a>
											</li>
										)
									})
								}
								</ul>
							</li>
						)
					})
				}
				</ul>
				<LoadMore parent={this} isData={this.props.items.list.length} ajaxUrl={categoryAjaxUrl} />
			</div>
		);
	}
});


var activeClass="active";
var CategoryNameList=React.createClass({
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
			<ul  className="J_categoryNameList categoryName-list">
			{
				items.map(function(item,i){
					var className=item.id==id?activeClass:'';
					return(
						<li data-id={item.id} key={i}  className={className}  >
							<a href={item.url}>
								<span className="omit">{item.title}</span>
							</a>
						</li>
					)
				},this)
			}
			</ul>
		);
	}
});

var SubCategory=React.createClass({
	render:function(){
		var subObj=this.props.data;
		return (
			<div className="subCategory-list">
				<CategoryNameList  data={subObj.nameList} id={subObj.id} />
			</div>
		)
	}
});

