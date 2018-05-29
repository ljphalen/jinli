var SearchGameList = React.createClass({
	render:function(){
		var item = this.props.items;
		return (
			<div className="game-list">
				<div className="game-list-row">
					<a href={item.href}>
						<div className="game-list-left">
								<div className="game-info">
								<img className="game-photo lazy" src={blankPic} data-original={item.img} />
								<h3 className="game-name omit"  dangerouslySetInnerHTML={{__html:item.name}}></h3>
								<div className="game-size-stars">
									<Star num={item.score} url={this.props.imgUrl} />
									<div className="game-size"><span>{item.size}</span></div>
								</div>
								<p className="intr omit">{item.resume}</p>
							</div>
						</div>
					</a>
					<div className="game-list-right">
						<a href={item.link?item.link:'javascript:'}>
							<input className={item.link?"dow-list-but":"dow-list-but no-dow-list-but"} type="button" value="下载" />
						</a>
					</div>
				</div>
			</div>
		);
	}
});
var SearchGameGuessList = React.createClass({
	render:function(){
		var item = this.props.items;
		return (
			<a href={item.href}>
			<div className="game-list guess-list">
				<div className="game-list-row">
					<span className="icon-search" data-reactid=".0.0.1.0"></span>
					<div className="game-name omit" dangerouslySetInnerHTML={{__html:item.name}}></div>
				</div>
			</div>
			</a>
		);
	}
});
var SearchDelBug =  React.createClass({
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
			<div className="icon-del-padding">
				<span className="icon-del-but" id="searchDel" onClick={this.handleClick}></span>
			</div>
		)
	}
});
var SearchText = React.createClass({
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
			<div>
				<div className="search-text">
					<input type="text" onKeyUp={this.handleClick} placeholder={this.props.defaultSearch} id="search" />
					<SearchDelBug parent={this} />
				</div>
				<div className="search-key-content">
				{
					this.state.gameItems.map(function(item,i){
						return (
							<SearchGameList items={item} imgUrl={imgUrl} key={i} />
						)
					})
				}
				{
					this.state.list.map(function(item,i){
						return (
							<SearchGameGuessList items={item} />
						)
					})
				}
				</div>
			</div>
		);
	}
});
var SearchHeader = React.createClass({
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
			<div className="search-header">
				<SearchText ajaxUrl={this.props.ajaxUrl} imgUrl={this.props.imgUrl} defaultSearch={this.props.defaultSearch} />
				<div className="absolute-vCenter search-page">
					<span className="icon-search" onClick={this.handleClick}></span>
				</div>
			</div>
		);
	}
})

var SearchGameHotWords = React.createClass({
	render:function(){
		return(
			<div className="search">
				<SearchHeader ajaxUrl={this.props.items.searchMoreUrl} href={this.props.items.href} imgUrl={this.props.items.picturePath} defaultSearch={this.props.items.defaultSearch} />
				<div id="hotWrds">
				<div>
					<h1 className="title">热词排行榜</h1>
				</div>
					<ul>
					{
						this.props.items.list.map(function(item,i){
							return (
								<Row items={item} index={i} key={i} />
							)
						})
					}
					</ul>
				</div>
			</div>
		);
	}
});

var SearchGame = React.createClass({
	getInitialState:function(){
		return {
			curPage:this.props.items.curPage
			,hasNext:this.props.items.hasNext
		}
	},
	render:function(){
		var imgUrl = this.props.items.picturePath;
		return(
			<div className="search">
				<SearchHeader ajaxUrl={this.props.items.searchMoreUrl} keyword={this.props.items.keyword} href={this.props.items.href} imgUrl={imgUrl} />
				<div className="prompt">
					{this.props.items.resum?'以下内容来自互联网,可能不适配您的手机':''}
				</div>
				<div id="hotWrds" className="search-key-content">
				{
					this.props.items.list.map(function(item,i){
						return (
							<SearchGameList items={item} key={i} imgUrl={imgUrl} />
						)
					})
				}
					<LoadMore ajaxUrl={this.props.items.ajaxUrl} parent={this} isData={this.props.items.list.length} id={'loadMore'} />
				</div>
			</div>
		);
	}
});