var ComIndex  = React.createClass({displayName: "ComIndex",
	render:function(){
		return (
			React.createElement("div", null, 
			
				this.props.items.list.map(function(item){
					return (
						React.createElement("div", null, 
							getComponent(item.typeName,item)
						)
					);
				})
			
			)
		)
	}
});