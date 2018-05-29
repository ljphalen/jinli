var Menu  = React.createClass({displayName: "Menu",
	render:function(){
		return (
			React.createElement("div", {className: "menu"}, 
				React.createElement("ul", null, 
				
					this.props.items.list.map(function(item,i){
						if(i>3)
							return;
						return(
								React.createElement("li", {key: i}, 
									React.createElement("a", {href: item.href}, 
									React.createElement("i", {className: item.type}), 
									item.name
									)
								)
						)
					})
				
				), 
				React.createElement("ul", null, 
					
						this.props.items.list.map(function(item,i){
							if(i<4)
								return;
							return(
									React.createElement("li", {key: i}, 
										React.createElement("a", {href: item.href}, 
										React.createElement("i", {className: item.type}), 
										item.name
										)
										
									)
							)
						})
					
				)
			)
		)
	}
});