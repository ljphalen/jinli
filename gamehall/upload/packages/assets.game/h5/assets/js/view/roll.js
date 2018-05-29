var Roll  = React.createClass({displayName: "Roll",
	render:function(){
		return (
				React.createElement("ul", {className: "pgwSlider"}, 
				
					this.props.items.data.map(function(item,i){
						return (
							React.createElement("li", {key: i}, 
								React.createElement("img", {src: item.imgUrl})
							)
						)
					},this)
				
				)
		)
	}
});