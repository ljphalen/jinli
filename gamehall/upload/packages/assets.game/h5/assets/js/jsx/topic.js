var TopicInfo = React.createClass({
	render:function(){
		var typeIcon,
			item = this.props.items,
			infoStyle
		;
		if(item.type)
			typeIcon = <TypeIcon type={item.type} />;
		if(!item.info){
			infoStyle = {
				display:'none'
			}
		}
		return (
				<div>
					{typeIcon}
					<img src={blankPic} data-original={item.imgUrl} className="topic-poster lazy" />
					<div  className="topic-info">
						<h3 className="topic-name omit"><span className="name omit">{item.activityName||item.name}</span><span className="date">{item.date}</span></h3>
						<div className="multi-line-omit ui-editor" style={infoStyle} dangerouslySetInnerHTML={{__html:item.info}}></div>
					</div>
				</div>
		);		
	}
});
var TopicInfoStar = React.createClass({
	render:function(){
		var item = this.props.items;
		return (
			<div className="game-info">
				<img className="game-photo lazy" src={blankPic} data-original={item.imgUrl} />
				<h3 className="game-name omit">{item.name}</h3>
				<div className="game-size-stars">
					<Star num={item.stars} url={this.props.imgUrl} />
					<div className="game-size"><span>{item.size}</span></div>
				</div>
				<p className="intr omit">{item.info}</p>
			</div>
		);
	}
});
var TopicDoubleInfo = React.createClass({
	render:function(){
		var item = this.props.items;
		return (
			<div>
				<div>
					<a href={item.href}>
						<div className="game-info game-list-left">
							<img className="game-photo lazy" src={blankPic} data-original={item.imgUrl} />
							<h3 className="game-name omit">{item.name}</h3>
							<Star num={item.stars} url={this.props.imgUrl} />
							<div className="game-type"><span>{item.type}</span>|<span>{item.size}</span>|<span></span>{item.language}</div>
						</div>
					</a>
					<div className="game-list-right">
						<a href={item.download}><input className="dow-list-but" type="button" value="下载" /></a>
					</div>
				</div>
				<div className="intr-img">
					<img src={blankPic} data-original={item.posters[0]}  className="lazy"  />
					<img src={blankPic} data-original={item.posters[1]} className="right lazy" />
				</div> 
				<p className="intr multi-line-omit">{item.info}</p>
			</div>
		);
	}
});
var TopicDoubleInfoTwo = React.createClass({
	getInitialState:function(){
		var color = ['#ff6b50','#00d8ff','#ff9c00','#53d37e']
			,bg = ['#fff4ef','#f0fdff','#fff7ed','#f8ffee',]
			,index = this.props.index
		;
		return {
			color:color[index],
			bg:bg[index]
		}
	},
	render:function(){
		var item = this.props.items;
		return (
			<div className="game-double-info" style={{backgroundColor:this.state.bg}}>
				<div>
					<a href={item.href}>
						<div className="game-info game-list-left">
							<img className="game-photo lazy" src={blankPic} data-original={item.imgUrl} />
							<h3 style={{color:this.state.color}} className="game-name omit">{item.name}</h3>
							<Star num={item.stars} url={this.props.imgUrl} />
							<div className="game-type"><span>{item.type}</span>|<span>{item.size}</span>|<span></span>{item.language}</div>
						</div>
					</a>
					<div className="game-list-right">
						<a href={item.download}><input  style={{backgroundColor:this.state.color}} className="dow-list-but" type="button" value="下载" /></a>
					</div>
				</div>
				<div className="intr-img">
					<img src={blankPic} data-original={item.posters[0]} className="lazy" />
					<img src={blankPic} data-original={item.posters[1]} className="right lazy" />
				</div>
				<p className="intr multi-line-omit">{item.info}</p>
			</div>
		);
	}
});
var TypeIcon = React.createClass({
	getInitialState:function(){
		var type = {
			end:'已过期',
			in:'进行中'
		}
		return type;
	},
	render:function(){

		return (
			<div className="icon-type">
				<div className="font">{this.state[this.props.type]}</div>
				<div className={this.props.type}></div>
			</div>
		);
	}
});
var TopicList  = React.createClass({
	getInitialState:function(){
		return {
			curPage:this.props.items.curPage
			,hasNext:this.props.items.hasNext
			
		}
	},
	render:function(){
		return (
			<div className="topic">
			{
				this.props.items.list.map(function(item,i){
					return(
						<a href={item.href} key={i}>
							<TopicInfo items={item} />
						</a>
					);
				})
			}
			<LoadMore name={this.props.items.LoadMoreName} href={this.props.items.LoadMoreHref} ajaxUrl={this.props.items.ajaxUrl} isData={this.props.items.list.length} parent={this} />
			</div>
		);
	}
});
var TopicDoubleHot = React.createClass({
	render:function(){
		var item = this.props.items;
		return (
			<div className="other">
			<a href={item[0].href}>
				<div className="row">
					<div style={{marginRight:'10px'}}>
						<img src={blankPic} data-original={item[0].imgUrl} className="lazy" />
						<h1 className="group-name omit">{item[0].name}</h1>
					</div>
				</div>
			</a>
			<a href={item[1].href}>
				<div className="row">
					<div style={{marginLeft:'10px'}} >
						<img className="lazy" src={blankPic} data-original={item[1].imgUrl}/>
						<h1 className="group-name omit">{item[1].name}</h1>
					</div>
				</div>
			</a>
			</div>
		)
	}
})
var TopicDoubleList = React.createClass({
	render:function(){
		return (
		<div className={this.props.items.topicInfo.type+" topic game-list"} >
			<TopicInfo items={this.props.items.topic} type={this.props.items.topicInfo.type} />
			<div className="customize-block">
			{
				this.props.items.topicInfo.list.map(function(item,index){
					var info;
					if( item.type=='list' ){
						info = <RankingDouble items={item} index={index} />
					}else{
						info = <TopicDoubleInfo items={item} imgUrl={this.props.items.picturePath} index={index} />
					}
					return (
						<div>
						{info}
						</div>
					)
				},this)
			}
			<TopicDoubleHot items = {this.props.items.hot} />
			</div>
		</div>
		)
	}
});
var TopicDoubleListTow = React.createClass({
	render:function(){
		return (
		<div className={this.props.items.topicInfo.type+" topic game-list"} >
			<TopicInfo items={this.props.items.topic} type={this.props.items.topicInfo.type} />
			<div className="customize-block">
			{
				this.props.items.topicInfo.list.map(function(item,index){
					var info = "";
					if( item.type=='list' ){
						info = <RankingDoubleTwo items={item} index={index} />
					}else{
						info = <TopicDoubleInfoTwo items={item} index={index} imgUrl={this.props.items.picturePath} />
					}
					return (
						<div>
							{info}
						</div>
					);
				},this)
				
			}
			<TopicDoubleHot items = {this.props.items.hot} />
			</div>
		</div>
		)
	}
});

var TopicSingleList = React.createClass({
	getInitialState:function(){
		return {
			curPage:this.props.items.topicInfo.curPage
			,hasNext:this.props.items.topicInfo.hasNext
		}
	},
	render:function(){
		var img;
		if(this.props.items.topicInfo.type == 'blue'){
			img = "<img src="+this.props.items.picturePath+"/galf.png />";
		}
		return(
			<div className={this.props.items.topicInfo.type+" topic game-list"} >
				<TopicInfo items={this.props.items.topic} type={this.props.items.topicInfo.type} />
				<div className="galf-line" dangerouslySetInnerHTML={{__html:img}}></div>
				<div className="customize-block">
				{
					this.props.items.topicInfo.list.map(function(item,i){
						return (
							<div className="game-list-row" key={i}>
								<a href={item.href}>
									<div className="game-list-left">
										<TopicInfoStar items={item} imgUrl={this.props.items.picturePath} />
									</div>
								</a>
								<div className="game-list-right">
									<a href={item.download}><input className="dow-list-but" type="button" value="下载" /></a>
								</div>
							</div>
						);
					},this)
				}
				<LoadMore name={this.props.items.topicInfo.LoadMoreName} ajaxUrl={this.props.items.topicInfo.ajaxUrl} parent={this} isData={this.props.items.topicInfo.list.length}  />
				</div>
			</div>
		);
	}
});

var Activity  = React.createClass({

	render:function(){
		var item = this.props.items
			,infoStyle;
		if(!item.info){
			infoStyle = {
				display:'none'
			}
		}
		return (
				<div className="activity">
				<a href={this.props.items.href}>
					<div>
						<img src={blankPic} data-original={item.imgUrl} className="activity-poster lazy" />
						<div  className="activity-info">
							<h3 className="activity-name omit">{item.activityName||item.name}</h3>
							<div className="multi-line-omit ui-editor" style={infoStyle} dangerouslySetInnerHTML={{__html:item.info}}></div>
						</div>
					</div>
				</a>
				<LoadMore name={this.props.items.LoadMoreName} href={this.props.items.LoadMoreHref} />
				</div>
		);
	}
});

var ActivityInfo = React.createClass({
	render:function(){
		return (
			<div id="activity" className="activity">
			<div id="activityRoll">
			{
				this.props.items.map(function(item,i){
					return (
						
						<div key={i} className={i?"activity-info":"on activity-info"}>
							<a href={item.href}>
							<div className="activity-name">【活动】{item.name}</div>
							<div>活动时间：{item.date}</div>
							<div>奖励内容：{item.rewardRule}</div>
							</a>
						</div>
					);
				})
			}
			</div>
			<div id="activityRollCopy"></div>
			</div>
		);
	}
});
var ActivityDetails  = React.createClass({
	render:function(){
		var item = this.props.items
			,download
			;
		
		if(item.download){
			download = "<a href="+item.download+"><input type='button' value='下载' class='dow-list-but' /></a>";
		}
		return (
			<div className="activity-details">
				<h1 className="name omit">{item.name}</h1>
				<img src={blankPic} data-original={item.imgUrl} className="topic-poster lazy" />
				<div className="activity-info">
					<div className="block">
						<div className="ui-editor" dangerouslySetInnerHTML={{__html:item.contents}}></div>
					</div>
					<div className="block" dangerouslySetInnerHTML={{__html:download}}>
					
					</div>
				</div>
			</div>
		)
	}
});

