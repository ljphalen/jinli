var PackageDetails  = React.createClass({
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
			<div className="package-details">
				<div className="basic">
					<div className="package-details-left">
						<PackageDetailsInfo items={item} />
					</div>
					<div className="package-details-right">
						<span onClick={this.handleClick} className="icon-grab"></span>
					</div>
				</div>
				<div className="explain">
					<h3>礼包内容</h3>
					<p>{item.contents}</p>
				</div> 
				<div className="explain">
					<h3>使用时间</h3>
					<p>{item.date}</p>
				</div>
				<div className="explain">
					<h3>使用方法</h3>
					<p>{item.useMethod}</p>
				</div>
				<PopLayerDouble msg="需要安装游戏大厅客户端才能领取礼包， 现在是否下载？" click={this.handleClick} parent={this} />
			</div>
		)
	}
});

var PackageList =  React.createClass({
	getInitialState:function(){
		return {
			curPage:0
			,data:[]
			,hasNext:this.props.type
		}
	},
	render:function(){
		return(
			<div className="package-list">
			{
				this.state.data.map(function(item,i){
					return (
						<a href={item.href} key={i}>
							<div className="package-list-row">
								<div className="package-info">
									<img className="game-photo lazy" src={blankPic} data-original={item.imgUrl} />
									<h3 className="package-name omit">{item.name}</h3>
									<ProgressBar total={item.total} residual={item.residual} />
									<p><span>剩余：{item.residual}个</span></p>
								</div>
							</div>
						</a>
					)
				})
			}
			<LoadMore name="加载更多" ajaxUrl={this.props.ajaxUrl} parent={this} />
			</div>
		);
	}
});
var PackageDetailsInfo = React.createClass({
	render:function(){
		var item = this.props.items;
		return (
			<div className="package-info">
				<img className="package-photo lazy" src={blankPic} data-original={item.imgUrl} />
				<h3 className="package-name omit">{item.name}</h3>
				<ProgressBar total={item.total} residual={item.residual} />
				<div className="residual omit">剩余：{item.residual}个</div>
			</div>
		);
	}
});