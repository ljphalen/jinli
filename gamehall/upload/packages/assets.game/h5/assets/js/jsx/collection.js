var GameRowInfo = React.createClass({
	render:function(){
		var item = this.props.items
			,style = {}
		;
		if(item.typeName&&item.size){
			style.val = '|';
			style.name = 'padding';
		}
		return (
			<div className="game-info">
				<a href={item.href}>
					<img className="game-photo lazy" src={blankPic} data-original={item.imgUrl} />
					<h3 className="game-name omit">{item.name}</h3>
					<div className="game-size-type"><span>{item.typeName}</span><span className={style.name}>{style.val}</span><span>{item.size}</span></div>
				</a>
				<a href={item.download}><input type="button" value="下载" className="dow-list-but" /></a>
			</div>
		);
	}
});
var Collection  = React.createClass({
	render:function(){
		return (
				<div className="collection">
				<h1 className="group-name">{this.props.items.name}<span className="arrow-down"></span></h1>
				<ul>
				{
					this.props.items.data.map(function(item,i){
						return (
							<li key={i}>
								<GameRowInfo items={item} /> 
							</li>
						)
					})
				}
				</ul>
				<LoadMore name={this.props.items.LoadMoreName} href={this.props.items.LoadMoreHref}  />
				</div>
		);
	}
});