var Article =  React.createClass({
	render:function(){
		var item = this.props.items
			,link
		;
		if(item.href){
			link = "<a href="+item.href+">查看原文</a>"
		}
		return (
			<div className="article">
				<h1 className="name">{item.name}</h1>
				<div className="date"><span className="source">来源：{item.original}</span><span>{item.date}</span></div>
				<div className="ui-editor " dangerouslySetInnerHTML={{__html: this.props.items.info}}></div>
				<div className="look-article" dangerouslySetInnerHTML={{__html: link}}>
				</div>
			</div>
		);
	}
});