var Advertising  = React.createClass({displayName: "Advertising",
	render:function(){
		return (
			React.createElement("div", {style: {margin: "20px 0px"}}, 
				React.createElement("a", {href: this.props.items.href}, React.createElement("img", {className: "posters posters-advertising lazy", src: blankPic, "data-original": this.props.items.imgUrl}))
			)
		);
	}
});