var Menu  = React.createClass({
	render:function(){
		return (
			<div className="menu">
				<ul>
				{
					this.props.items.list.map(function(item,i){
						if(i>3)
							return;
						return(
								<li key={i}>
									<a href={item.href}>
									<i className={item.type}></i>
									{item.name}
									</a>
								</li>
						)
					})
				}
				</ul>
				<ul>
					{
						this.props.items.list.map(function(item,i){
							if(i<4)
								return;
							return(
									<li key={i}>
										<a href={item.href}>
										<i className={item.type}></i>
										{item.name}
										</a>
										
									</li>
							)
						})
					}
				</ul>
			</div>
		)
	}
});