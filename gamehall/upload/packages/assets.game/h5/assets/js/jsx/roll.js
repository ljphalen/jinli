var Roll  = React.createClass({
	render:function(){
		return (
				<ul className="pgwSlider">
				{
					this.props.items.data.map(function(item,i){
						return (
							<li key={i} >
								<img src={item.imgUrl}/>
							</li>
						)
					},this)
				}
				</ul>
		)
	}
});