var GameDetailsMenu = React.createClass({displayName: "GameDetailsMenu",
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
			React.createElement("ul", {className: "game-details-menu", style: {'display':menuShow}}, 
				
					menuArr.map(function(item,i){
						{var cc = this.handleClick.bind(this,item.type)}
						return (React.createElement("li", {key: i, style: {width:liWidth+'%'}, onClick: cc, className: item.Class}, item.name))
					},this)
				
			)
		);
	}
});

var Introduction = React.createClass({displayName: "Introduction",
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
			React.createElement("div", null, 
				React.createElement("div", {className: "poster-introduction"}, 
					React.createElement("div", {className: "game-images-introduction"}, 
					
						this.props.items.imgUrl.map(function(item,i){
							return (React.createElement("img", {key: i, src: blankPic, "data-original": item, className: "lazy"}));
						})
					
					)
				), 
				React.createElement("div", {className: "gossip", style: {display:gossipStyle}}, 
					React.createElement("h1", {className: "group-name"}, "小编八卦"), 
					React.createElement("div", {className: "ui-editor", dangerouslySetInnerHTML: {__html: this.props.items.gossip}})
				), 
				React.createElement("div", {className: "introduction", style: {display:introductionStyle}}, 
					React.createElement("h1", {className: "group-name"}, "游戏简介"), 
					React.createElement("div", {className: "multi-line-omit ui-editor", style: this.state.style}, React.createElement("div", {dangerouslySetInnerHTML: {__html: this.props.items.introduction}})), 
					React.createElement("div", {className: "show-all"}, 
						React.createElement("span", {onClick: this.handleClick}, 
							React.createElement("span", {className: this.state.showAll.class}), this.state.showAll.name
						)
					)
				)
			)
		);
	}
});

var GameDetails  = React.createClass({displayName: "GameDetails",
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
			activity = React.createElement(ActivityInfo, {items: this.props.items.activity});
		}
		return (
			React.createElement("div", {className: "game-details"}, 
				React.createElement("div", {className: "basic"}, 
					React.createElement("div", {className: "game-details-left"}, 
						React.createElement(GameInfoStar, {items: this.props.items})
					), 
					React.createElement("div", {className: "game-details-right"}, 
						React.createElement("input", {onClick: this.handleClick.bind(this,'block'), className: "dow-list-but", type: "button", value: "极速下载"}), 
						React.createElement("a", {href: this.props.items.href?this.props.items.href:'javascript:'}, React.createElement("input", {className: "dow-list-but grey", type: "button", value: "普通下载"}))
					)
				), 
				activity, 
				React.createElement(GameDetailsMenu, {parent: this, items: this.props.items.menu}), 
				React.createElement("div", {style: this.state.introduction}, 
					React.createElement(Introduction, {items: this.props.items.introduction}), 
					React.createElement(Edition, {items: this.props.items.edition}), 
					React.createElement(ProbablyLike, {items: this.props.items.probablyLike})
				), 
				React.createElement("div", {style: this.state.package}, 
					React.createElement(PackageList, {type: this.props.items.menu.package, ajaxUrl: this.props.items.packageAjaxUrl})
				), 
				React.createElement("div", {style: this.state.strategy}, 
					React.createElement(StrategyList, {type: this.props.items.menu.strategy, ajaxUrl: this.props.items.strategyAjaxUrl})
				), 
				React.createElement(PopLayerDouble, {msg: "需要安装游戏大厅客户端才能体验极速下载，现在是否下载？", click: this.handleClick, parent: this})
			)
			
		)
	}
});