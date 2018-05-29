var InfoBulletin  = React.createClass({
	render:function(){
		var imgUrl = this.props.imgUrl;
		return (
				<div className="info-bulletin-list">
				<h1 className="group-name">{this.props.items.name}<span className="arrow-down"></span></h1>
				{
					this.props.items.list.map(function(item,i){
						return (
							<div className="info-bulletin-list-row" key={i}>
								<a href={item.href}>
									<div className="info-bulletin-list-left">
										<img data-original={item.imgUrl} className="posters lazy" src={blankPic} />
									</div>
									<div className="info-bulletin-list-right">
										<h3 className="bulletin-name">{item.name}</h3>
										<div className="multi-line-omit ui-editor" dangerouslySetInnerHTML={{__html:item.info}}></div>
									</div>
								</a>
							</div>
						)
					})
				}
				<LoadMore name={this.props.items.LoadMoreName} href={this.props.items.LoadMoreHref} />
				</div>
		);
	}
});
var InfoBulletinList = React.createClass({
	getInitialState:function(){
		return {
			curPage:this.props.items.curPage
			,hasNext:this.props.items.hasNext
		}
	},
	render:function(){
		var imgUrl = this.props.imgUrl;
		return (
				<div className="info-bulletin-list bulletin-list-page">
				{
					this.props.items.list.map(function(item,i){
						return (
							<div className="info-bulletin-list-row" key={i}>
								<a href={item.href}>
									<div className="info-bulletin-list-left">
										<img className="posters lazy" src={blankPic}  data-original={item.imgUrl} />
									</div>
									<div className="info-bulletin-list-right">
										<h3 className="bulletin-name omit">{item.name}</h3>
										<div className="date">{item.date}</div>
										<div className="omit ui-editor" dangerouslySetInnerHTML={{__html:item.info}}></div>
									</div>
								</a>
							</div>
						)
					})
				}
				<LoadMore ajaxUrl={this.props.items.ajaxUrl} parent={this} isData={this.props.items.list.length} />
				</div>
		);
	}
});