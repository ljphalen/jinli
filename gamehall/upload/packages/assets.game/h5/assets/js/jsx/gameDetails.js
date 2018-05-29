var GameDetailsMenu = React.createClass({
	getInitialState:function(){
		return {
			introduction:{
				name:'介绍'
				,Class:'on'
			}
			,package:{
				name:'礼包'
				,Class:''
			}
			,strategy:{
				name:'攻略'
				,Class:''
			}
		}
	},
	handleClick:function(name){
		
		var parState = this.props.parent.state;
		
		for(var i in parState){
			if(this.state[i]){
				parState[i].display = 'none';
				this.state[i].Class ='';
			}
		}
		parState[name].display = 'block';
		this.state[name].Class = 'on';
		this.setState();
		this.props.parent.setState();
	},
	render:function(){
		var menuArr = []
			,liWidth
			,menuShow = "block"
		;
		for(var i in this.props.items){
			if(this.props.items[i]){
				this.state[i].type = i;
				menuArr.push(this.state[i]);
			}
		}
		if(menuArr.length == 1){
			menuShow = "none";
		}
		liWidth = 100 / menuArr.length;
		return (
			<ul className="game-details-menu" style={{'display':menuShow}}>
				{
					menuArr.map(function(item,i){
						{var cc = this.handleClick.bind(this,item.type)}
						return (<li key={i} style={{width:liWidth+'%'}} onClick={cc} className={item.Class}>{item.name}</li>)
					},this)
				}
			</ul>
		);
	}
});

var Introduction = React.createClass({
	getInitialState:function(){
		return {
			style:{
				WebkitLineClamp: '3',
			},
			showAll:{
				name:'展开'
				,class:'icon-show-all-bottom'
			}
		}
	}
	,handleClick:function(){
		var styleVal = 'inherit'
			,showName = '收起'
			,showClass = 'icon-show-all-top'
			
		;
		if( this.state.style.WebkitLineClamp==styleVal ){
			styleVal = '3';
			styleVal = '3';
			showName = '展开';
			showClass = 'icon-show-all-bottom';
		}
		
		
		this.setState({style:{WebkitLineClamp:styleVal},showAll:{name:showName,class:showClass}});
	}
	,render:function(){
		var gossipStyle = 'auto'
			,introductionStyle = 'auto'
		;
		if(!this.props.items.gossip){
			gossipStyle = 'none';
		}
		if(!this.props.items.introduction){
			introductionStyle = 'none';
		}
		return ( 
			<div>
				<div className="poster-introduction">
					<div className="game-images-introduction">
					{
						this.props.items.imgUrl.map(function(item,i){
							return (<img key={i} src={blankPic} data-original={item}  className="lazy" />);
						})
					}
					</div>
				</div>
				<div className="gossip" style={{display:gossipStyle}}>
					<h1 className="group-name">小编八卦</h1>
					<div className="ui-editor" dangerouslySetInnerHTML={{__html: this.props.items.gossip}}></div>
				</div>
				<div className="introduction" style={{display:introductionStyle}}>
					<h1 className="group-name">游戏简介</h1>
					<div className="multi-line-omit ui-editor" style={this.state.style}><div dangerouslySetInnerHTML={{__html: this.props.items.introduction}}></div></div>
					<div className="show-all">
						<span onClick={this.handleClick}>
							<span className={this.state.showAll.class}></span>{this.state.showAll.name}
						</span>
					</div>
				</div>
			</div>
		);
	}
});

var GameDetails  = React.createClass({
	getInitialState:function(){
		return {
			introduction:{
				display:'block'
			},
			package:{
				display:'none'
			},
			strategy:{
				display:'none'
			},
			layer:{
				display:'none'
			}
		}
	},
	handleClick:function(type){
		if(type){
			this.state.layer.display = type;
			this.setState();
		}else{
			location.href=this.props.items.clientDownUrl;
		}
	},
	render:function(){
		var activity;
		if(this.props.items.activity.length){
			activity = <ActivityInfo items={this.props.items.activity} />;
		}
		return (
			<div className="game-details">
				<div className="basic">
					<div className="game-details-left">
						<GameInfoStar items={this.props.items} />
					</div>
					<div className="game-details-right">
						<input onClick={this.handleClick.bind(this,'block')} className="dow-list-but" type="button" value="极速下载" />
						<a href={this.props.items.href?this.props.items.href:'javascript:'}><input className="dow-list-but grey" type="button" value="普通下载" /></a>
					</div>
				</div>
				{activity}
				<GameDetailsMenu parent={this} items={this.props.items.menu} />
				<div style={this.state.introduction}>
					<Introduction items={this.props.items.introduction} />
					<Edition items={this.props.items.edition} />
					<ProbablyLike items={this.props.items.probablyLike} />
				</div>
				<div style={this.state.package}>
					<PackageList type={this.props.items.menu.package} ajaxUrl={this.props.items.packageAjaxUrl} />
				</div>
				<div style={this.state.strategy}>
					<StrategyList type={this.props.items.menu.strategy} ajaxUrl={this.props.items.strategyAjaxUrl} />
				</div>
				<PopLayerDouble msg="需要安装游戏大厅客户端才能体验极速下载，现在是否下载？" click={this.handleClick} parent={this} />
			</div>
			
		)
	}
});