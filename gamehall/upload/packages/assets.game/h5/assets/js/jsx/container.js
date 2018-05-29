var Container  = React.createClass({
	render:function(){
		var imgUrl = this.props.imgUrl;
		return (
			<div>
			{
				this.props.items.list.map(function(item,i){
					return (
						<div key={i}>
							{getComponent(item.component,item,imgUrl)}
						</div>
					);
				})
			}
			</div>
		)
	}
});