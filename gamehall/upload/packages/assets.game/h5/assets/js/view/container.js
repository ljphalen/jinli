var Container  = React.createClass({displayName: "Container",
	render:function(){
		var imgUrl = this.props.imgUrl;
		return (
			React.createElement("div", null, 
			
				this.props.items.list.map(function(item,i){
					return (
						React.createElement("div", {key: i}, 
							getComponent(item.component,item,imgUrl)
						)
					);
				})
			
			)
		)
	}
});