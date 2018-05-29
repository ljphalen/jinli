var Advertising  = React.createClass({
	render:function(){
		return (
			<div style={{margin: "20px 0px"}}>
				<a href={this.props.items.href}><img className="posters posters-advertising lazy" src={blankPic} data-original={this.props.items.imgUrl} /></a>
			</div>
		);
	}
});