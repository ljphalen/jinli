var GameList =  React.createClass({
	getInitialState:function(){
		return {
			curPage:this.props.items.curPage
			,hasNext:this.props.items.hasNext
		}
	},
	render:function(){
		return (
				<div className="game-list">
				{
					this.props.items.list.map(function(item,i){
						return (
							<div className="game-list-row" key={i}>
								<a href={item.href}>
									<div className="game-list-left">
										<GameColumnInfo items={item} />
									</div>
								</a>
								<div className="game-list-right">
									<a href={item.download}><input className="dow-list-but" type="button" value="下载" /></a>
								</div>
							</div>
						)
					})
				}
				<LoadMore name={this.props.items.LoadMoreName} href={this.props.items.LoadMoreHref} ajaxUrl={this.props.items.ajaxUrl} parent={this} isData={this.props.items.list.length} />
				</div>
		);
	}
});

var Ranking  = React.createClass({
	getInitialState:function(){
		return {
			curPage:this.props.items.curPage
			,hasNext:this.props.items.hasNext
		}
	},
	render:function(){
		var now = this.props.items.now
			,href = this.props.items.LoadMoreHref || this.props.items.href
		;
		return (
				<div className="ranking-list game-list">
				<h1 className="group-name ">{this.props.items.name}<span className="arrow-down"></span></h1>
				{
					this.props.items.data.map(function(item,i){
						return (
							<div className="game-list-row" key={i}>
								<a href={item.href}>
									<div className="game-list-left">
										{now?<Now index={i+1} />:""}
										<GameColumnInfo items={item} />
									</div>
								</a>
								<div className="game-list-right">
									<a href={item.download}><input className="dow-list-but" type="button" value="下载" /></a>
								</div>
							</div>
						)
					})
				}
				<LoadMore name={this.props.items.LoadMoreName} href={href} ajaxUrl={this.props.items.ajaxUrl} parent={this} isData={this.props.items.data.length} />
				</div>
		);
	}
});
var DateRanking = React.createClass({
	getInitialState:function(){
		return {
			curPage:this.props.items.curPage
			,hasNext:this.props.items.hasNext
		}
	},
	render:function(){
		return (
			<div className="game-list date-ranking-list">
			<span className="line-orange"></span>
			{
				this.props.items.list.map(function(item,i){
					var start
						,paddingTop = '0px'
					;
					if(item.date!=this.date){
						paddingTop = '15px';
						this.date = item.date;
						start = '<h1 class="group-name"><span class="icon-hollow-circle"></span>'+ item.date +'</h1>';
					}
					return (
						<div key={i}>
							<div dangerouslySetInnerHTML={{__html:start}}>
							</div>
							<div className="date-ranking-row" style={{paddingTop:paddingTop}}>
								<a href={item.href}>
									<div className="game-list-left">
										<GameColumnInfo items={item} />
									</div>
								</a>
								<div className="game-list-right"> 
									<a href={item.download}><input className="dow-list-but" type="button" value="下载" /></a>
								</div>
							</div>
						</div>
					)
				},this)
			}
			<LoadMore name={this.props.items.LoadMoreName} ajaxUrl={this.props.items.ajaxUrl} parent={this} isData={this.props.items.list.length} />
			</div>
		);
	}
});

var RankingDouble  = React.createClass({
	getInitialState:function(){
		var color = ['#ff6b50','#00d8ff','#9654b6','#53d37e'];
		return {
			color:color[this.props.index]
		}
	},
	render:function(){
		var color = this.state.color;
		return (
				<div className="game-double-list">
				<h1 className="group-name" style={{color:color}}>
				<span className="omit">{this.props.items.name}</span>
				<span className="arrow-down" style={{borderTopColor:color}}></span></h1>
				<p className="intr">{this.props.items.info}</p>
				<ul className="table">
				{
					this.props.items.data.map(function(item,i){
						var right;
						if(i%2){
							right = 'game-double-list-right'
						}
						return (
							<li className={right} key={i}>
								<GameDoubleInfo items={item} color={color} />
							</li>
						)
					})
				}
				</ul>
				</div>
		);
	}
});

var RankingDoubleTwo  = React.createClass({
	getInitialState:function(){
		var color = ['#ff6b50','#00d8ff','#ff9c00','#53d37e']
			,bg = ['#fff4ef','#f0fdff','#fff7ed','#f8ffee']
			,shadow = ['#ae3c27','#0093ad','#ae6200','#378b53']
			,index = this.props.index
		;
		return {
			color:color[index],
			bg:bg[index],
			shadow:shadow[index]
		}
	},
	render:function(){
		var color = this.state.color;
		return (
				<div className="game-double-list" style={{backgroundColor:this.state.bg}}>
				<h1 className="group-name icon-title" style={{backgroundColor:this.state.color}}>
				<span style={{borderTopColor:this.state.shadow}} className="icon-title-arrow-bg"></span>
				<span className="omit">{this.props.items.name}</span>
				<span style={{borderRightColor:this.state.bg}} className="icon-title-arrow"></span>
				</h1>
				<p className="intr">{this.props.items.info}</p>
				<ul className="table">
				{
					this.props.items.data.map(function(item,i){
						var right;
						if(i%2){
							right = 'game-double-list-right'
						}
						return (
							<li className={right} key={i}>
								<GameDoubleInfo items={item} index={i} color={color} />
							</li>
						)
					})
				}
				</ul>
				</div>
		);
	}
});
