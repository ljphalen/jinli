
var getComponent=function(name,item,imgUrl){switch(name){case'collection':return React.createElement(Collection,{items:item});case'advertising':return React.createElement(Advertising,{items:item});case'ranking':return React.createElement(Ranking,{items:item});case'activity':return React.createElement(Activity,{items:item});case'infoBulletin':return React.createElement(InfoBulletin,{items:item});case'rankingMany':return React.createElement(RankingMany,{items:item});}}
var LoadIcon=React.createClass({displayName:"LoadIcon",render:function(){return(React.createElement("div",{className:"layer-load",name:"ajaxLoad"},React.createElement("div",{className:"bg-layer"}),React.createElement("div",{className:"bg-icon"},React.createElement("i",{className:"load-icon"}),React.createElement("p",{className:"msg"},"加载中,请稍后...."))));}})
var LoadMore=React.createClass({displayName:"LoadMore",getInitialState:function(){return{style:{display:'block'}}},handleClick:function(){if(this.props.href)
location.href=this.props.href;var parentState=this.props.parent.state,file={};if(parentState.file){for(var i in parentState.file){file[i]=parentState.file[i];}}
file.page=this.props.parent.state.curPage;this.load.send({data:file});},componentDidMount:function(){if(!this.props.ajaxUrl)
return;var parent=this.props.parent,self=this,isData=this.props.isData,parentItems;this.load=new Model({url:this.props.ajaxUrl,succ:function(res){var list=res.data.list;if(res.data.hasNext===false||res.data.hasNext==="false")
{self.state.style.display='none';self.forceUpdate();if(!list.length)
return;}
if(!isData){if(parent.state.data)
parent.state.data=parent.state.data.concat(list);}else{parentItems=parent.props.items;if(parentItems.data){parentItems.data=parentItems.data.concat(list);}else if(parentItems.list){parentItems.list=parentItems.list.concat(list);}else if(parentItems.topicInfo){parentItems.topicInfo.list=parentItems.topicInfo.list.concat(list);}}
parent.state.curPage++
parent.forceUpdate();$("img[data-original]").lazyload();},loadName:'[name="ajaxLoad"]'});if(parent.state.hasNext){parent.state.curPage++}else{self.state.style.display='none';self.forceUpdate();return;}
if(isData){return;}
this.load.send({data:{page:parent.state.curPage}});},render:function(){var name=this.props.name||'加载更多',loadIng;if(this.props.ajaxUrl)
loadIng=React.createElement(LoadIcon);return(React.createElement("div",{},React.createElement("div",{className:"look-all",id:this.props.id,style:this.state.style,onClick:this.handleClick},name,React.createElement("div",{className:"load-all-array"},React.createElement("span",{className:"point"}),React.createElement("span",{className:"point"}),React.createElement("span",{className:"point"}),React.createElement("div",{className:"arrow"}))),loadIng));}});var Now=React.createClass({displayName:"Now",render:function(){var style={};switch(this.props.index){case 1:style.background='#ff7200';break;case 2:style.background='#7fcd2b';break;case 3:style.background='#00aeff';break;default:style.background='#b6b6b6';}
return(React.createElement("span",{style:style,className:"label"},this.props.index));}});var Star=React.createClass({displayName:"Star",getInitialState:function(){var stars=[],length=this.props.num;for(var i=0;i<length;i++){if(i%2){stars.pop();stars.push('star');}else{stars.push('half_star');}}
for(var i=stars.length;i<5;i++){stars.push('grey_star');}
return{stars:stars};},render:function(){return(React.createElement("div",{className:"stars"},this.state.stars.map(function(name){return(React.createElement("img",{src:this.props.url+'/'+name+'.png'}))},this)))}});var Row=React.createClass({displayName:"Row",render:function(){var item=this.props.items;return(React.createElement("a",{href:item.href},React.createElement("li",{className:"row"},React.createElement(Now,{index:this.props.index+1}),React.createElement("h3",{className:"game-name omit"},item.name),React.createElement("span",{className:"go-forward"}))));}});var ProgressBar=React.createClass({displayName:"ProgressBar",getInitialState:function(){var total=this.props.total,residual=this.props.residual,width;width=residual/total*100;if(width<8)
width=8;width=width.toFixed(2)+'%';return{width:width}},render:function(){return(React.createElement("div",{className:"progress-bar-bg"},React.createElement("div",{style:{width:this.state.width},className:"progress-bar"})));}});var GameColumnInfo=React.createClass({displayName:"GameColumnInfo",render:function(){var item=this.props.items,style={};if(item.typeName&&item.size){style.val='|';style.name='padding';}
return(React.createElement("div",{className:"game-info"},React.createElement("img",{className:"game-photo lazy",src:blankPic,'data-original':item.imgUrl}),React.createElement("h3",{className:"game-name omit"},item.name),React.createElement("div",{className:"game-size-type omit"},React.createElement("span",null,item.typeName),React.createElement("span",{className:style.name},style.val),React.createElement("span",null,item.size)),React.createElement("p",{className:"omit"},item.info)));}});var GameInfoStar=React.createClass({displayName:"GameInfoStar",render:function(){var item=this.props.items,style={};if(item.typeName&&item.size){style.val='|';style.name='padding';}
return(React.createElement("div",{className:"game-info"},React.createElement("img",{className:"game-photo lazy",src:blankPic,'data-original':item.imgUrl}),React.createElement("h3",{className:"game-name omit"},item.name),React.createElement(Star,{num:item.stars,url:item.picturePath}),React.createElement("div",{className:"game-size-type omit"},React.createElement("span",null,item.typeName),React.createElement("span",{className:style.name},style.val),React.createElement("span",null,item.size))));}});var PopLayerDouble=React.createClass({displayName:"PopLayerDouble",handleClick:function(type){if(type){this.props.click();}
this.props.parent.state.layer.display='none';this.props.parent.forceUpdate();},render:function(){var title=this.props.title||'提示';return(React.createElement("div",{className:this.props.parent.state.layer.display+' pop-layer'},React.createElement("div",{className:"position"},React.createElement("div",{className:"title"},title),React.createElement("p",{className:"info"},this.props.msg),React.createElement("div",{className:"double-but"},React.createElement("input",{type:"button",onClick:this.handleClick.bind(this,true),className:"dow",value:"是"}),React.createElement("input",{type:"button",onClick:this.handleClick.bind(this,false),className:"cancel",value:"否"})))))}});var Edition=React.createClass({displayName:"Edition",render:function(){var item=this.props.items;return(React.createElement("div",{className:"edition"},React.createElement("div",{className:"group-name"},"相关信息"),React.createElement("div",null,"当前片本：",item.edition),React.createElement("div",null,"更新时间：",item.date),React.createElement("div",null,"来源：",item.source)));}});var StrategyList=React.createClass({displayName:"StrategyList",getInitialState:function(){return{curPage:0,data:[],hasNext:this.props.type}},render:function(){return(React.createElement("div",{className:"strategy"},this.state.data.map(function(item){return(React.createElement("a",{href:item.href},React.createElement("div",{className:"strategy-info"},React.createElement("div",null,React.createElement("h3",{className:"strategy-name omit"},item.name),React.createElement("span",{className:"date"},item.date)),React.createElement("p",{className:"multi-line-omit"},item.info))))}),React.createElement(LoadMore,{ajaxUrl:this.props.ajaxUrl,parent:this})));}})
var GameDoubleInfo=React.createClass({displayName:"GameDoubleInfo",render:function(){var item=this.props.items;return(React.createElement("a",{href:item.href},React.createElement("div",{className:"game-info"},React.createElement("img",{className:"game-photo lazy",src:blankPic,'data-original':item.imgUrl}),React.createElement("h3",{className:"game-name omit"},item.name),React.createElement("div",{className:"game-size-type omit"},React.createElement("input",{type:"button",className:'dow-list-but',style:{background:this.props.color},value:'下载'}))),React.createElement("p",{className:"multi-line-omit"},item.info)));}});
var ProbablyLike =  React.createClass({displayName: "ProbablyLike",
	render:function(){
			var name = "你可能还喜欢"
				,ProbablyShow = "block"
			;
			if(!this.props.items.length){
				ProbablyShow = "none";
			}
		return (
			React.createElement("div", {className: "probably-like",style:{"display":ProbablyShow}}, 
			React.createElement("h3", {className: "group-name"}, name), 
			React.createElement("ul", null, 
			
				this.props.items.map(function(item,i){
					return (
					React.createElement("li", null, 
						React.createElement("a", {href: item.href, key: i}, 
							React.createElement("img", {className: "game-photo lazy", src: blankPic, "data-original": item.imgUrl}), 
							React.createElement("h3", {className: "game-name omit"}, item.name)
						)
					)
					);
				})
			
			)
			)
		);
	}
});

var Roll=React.createClass({displayName:"Roll",render:function(){return(React.createElement("ul",{className:"pgwSlider"},this.props.items.data.map(function(item,i){return(React.createElement("li",{key:i},React.createElement("img",{src:item.imgUrl})))},this)))}});

var SearchGameList = React.createClass({displayName: "SearchGameList",
	render:function(){
		var item = this.props.items
			,style = {};
		if(item.category&&item.size){
			style.val = '|';
			style.name = 'padding';
		}
		return (
			React.createElement("div", {className: "game-list"}, 
				React.createElement("div", {className: "game-list-row"}, 
					React.createElement("a", {href: item.href}, 
						React.createElement("div", {className: "game-list-left"}, 
								React.createElement("div", {className: "game-info"}, 
								React.createElement("img", {className: "game-photo lazy", src: blankPic, "data-original": item.img}), 
								React.createElement("h3", {className: "game-name omit", dangerouslySetInnerHTML: {__html:item.name}}), 
								React.createElement("div", {className: "game-size-stars"}, 
									React.createElement("div", {className: "game-size-type"}, React.createElement("span", null, item.category), React.createElement("span", {className: style.name}, style.val), React.createElement("span", null, item.size))
								), 
								React.createElement("p", {className: "intr omit"}, item.resume)
							)
						)
					), 
					React.createElement("div", {className: "game-list-right"}, 
						React.createElement("a", {href: item.link?item.link:'javascript:'}, 
							React.createElement("input", {className: item.link?"dow-list-but":"dow-list-but no-dow-list-but", type: "button", value: "下载"})
						)
					)
				)
			)
		);
	}
});
var SearchGameGuessList=React.createClass({displayName:"SearchGameGuessList",render:function(){var item=this.props.items;return(React.createElement("a",{href:item.href},React.createElement("div",{className:"game-list guess-list"},React.createElement("div",{className:"game-list-row"},React.createElement("span",{className:"icon-search","data-reactid":".0.0.1.0"}),React.createElement("div",{className:"game-name omit",dangerouslySetInnerHTML:{__html:item.name}})))));}});var SearchDelBug=React.createClass({displayName:"SearchDelBug",handleClick:function(){$('#search').val('');$('#hotWrds').show();$('#searchDel').hide();this.props.parent.state.list=[];this.props.parent.state.gameItems=[];this.props.parent.forceUpdate();},render:function(){return(React.createElement("div",{className:"icon-del-padding"},React.createElement("span",{className:"icon-del-but",id:"searchDel",onClick:this.handleClick})))}});var SearchText=React.createClass({displayName:"SearchText",getInitialState:function(){return{list:[],gameItems:[]}},handleClick:function(){var val=$('#search').val();$('#searchDel').css({display:'inline-block'});if(!val){$('#searchDel').hide();}
this.search.send({data:{keyword:val}});},componentDidMount:function(){var self=this;this.search=new Model({url:this.props.ajaxUrl,succ:function(res){self.state.gameItems=res.data.gameItems;self.state.list=[];self.forceUpdate();self.setState({list:res.data.list});$("img[data-original]").lazyload();if(res.data.gameItems.length)
$('#hotWrds').hide();else
$('#hotWrds').show();}});$('#search').focus();},render:function(){var imgUrl=this.props.imgUrl;return(React.createElement("div",null,React.createElement("div",{className:"search-text"},React.createElement("input",{type:"text",onKeyUp:this.handleClick,placeholder:this.props.defaultSearch,id:"search"}),React.createElement(SearchDelBug,{parent:this})),React.createElement("div",{className:"search-key-content"},this.state.gameItems.map(function(item,i){return(React.createElement(SearchGameList,{items:item,imgUrl:imgUrl,key:i}))}),this.state.list.map(function(item,i){return(React.createElement(SearchGameGuessList,{items:item}))}))));}});var SearchHeader=React.createClass({displayName:"SearchHeader",handleClick:function(){
	var val=$('#search').val();
if(val.replace(/(^\s*)|(\s*$)/g,"")){
		location.href=this.props.href+'&keyword='+val+'&intersrc='+'isearch';return;
}
if(this.props.defaultSearch){location.href=this.props.href+'&keyword='+this.props.defaultSearch+'&intersrc='+'dsearch';return;}
alert('请输入搜索词');},render:function(){return(React.createElement("div",{className:"search-header"},React.createElement(SearchText,{ajaxUrl:this.props.ajaxUrl,imgUrl:this.props.imgUrl,defaultSearch:this.props.defaultSearch}),React.createElement("div",{className:"absolute-vCenter search-page"},React.createElement("span",{className:"icon-search",onClick:this.handleClick}))));}})
var SearchGameHotWords=React.createClass({displayName:"SearchGameHotWords",render:function(){return(React.createElement("div",{className:"search"},React.createElement(SearchHeader,{ajaxUrl:this.props.items.searchMoreUrl,href:this.props.items.href,imgUrl:this.props.items.picturePath,defaultSearch:this.props.items.defaultSearch}),React.createElement("div",{id:"hotWrds"},React.createElement("div",null,React.createElement("h1",{className:"title"},"热词排行榜")),React.createElement("ul",null,this.props.items.list.map(function(item,i){return(React.createElement(Row,{items:item,index:i,key:i}))})))));}});var SearchGame=React.createClass({displayName:"SearchGame",getInitialState:function(){return{curPage:this.props.items.curPage,hasNext:this.props.items.hasNext}},render:function(){var imgUrl=this.props.items.picturePath;return(React.createElement("div",{className:"search"},React.createElement(SearchHeader,{ajaxUrl:this.props.items.searchMoreUrl,keyword:this.props.items.keyword,href:this.props.items.href,imgUrl:imgUrl}),React.createElement("div",{className:"prompt"},this.props.items.resum?'以下内容来自互联网,可能不适配您的手机':''),React.createElement("div",{id:"hotWrds",className:"search-key-content"},this.props.items.list.map(function(item,i){return(React.createElement(SearchGameList,{items:item,key:i,imgUrl:imgUrl}))}),React.createElement(LoadMore,{ajaxUrl:this.props.items.ajaxUrl,parent:this,isData:this.props.items.list.length,id:'loadMore'}))));}});

var TopicInfo = React.createClass({displayName: "TopicInfo",
	render:function(){
		var typeIcon,
			item = this.props.items,
			infoStyle
		;
		if(item.type)
			typeIcon = React.createElement(TypeIcon, {type: item.type});
		if(!item.info){
			infoStyle = {
				display:'none'
			}
		}
		return (
				React.createElement("div", null, 
					typeIcon, 
					React.createElement("img", {src: blankLongPic, "data-original": item.imgUrl, className: "topic-poster lazy"}), 
					React.createElement("div", {className: "topic-info"}, 
						React.createElement("h3", {className: "topic-name omit"}, React.createElement("span", {className: "name omit"}, item.activityName||item.name), React.createElement("span", {className: "date"}, item.date)), 
						React.createElement("div", {className: "multi-line-omit ui-editor", style: infoStyle, dangerouslySetInnerHTML: {__html:item.info}})
					)
				)
		);		
	}
});
var TopicInfoStar = React.createClass({displayName: "TopicInfoStar",
	render:function(){
		var item = this.props.items;
		return (
			React.createElement("div", {className: "game-info"}, 
				React.createElement("img", {className: "game-photo lazy", src: blankPic, "data-original": item.imgUrl}), 
				React.createElement("h3", {className: "game-name omit"}, item.name), 
				React.createElement("div", {className: "game-size-stars"}, 
					React.createElement(Star, {num: item.stars, url: this.props.imgUrl}), 
					React.createElement("div", {className: "game-size"}, React.createElement("span", null, item.size))
				), 
				React.createElement("p", {className: "intr omit"}, item.info)
			)
		);
	}
});
var TopicDoubleInfo = React.createClass({displayName: "TopicDoubleInfo",
	render:function(){
		var item = this.props.items;
		return (
			React.createElement("div", null, 
				React.createElement("div", null, 
					React.createElement("a", {href: item.href}, 
						React.createElement("div", {className: "game-info game-list-left"}, 
							React.createElement("img", {className: "game-photo lazy", src: blankPic, "data-original": item.imgUrl}), 
							React.createElement("h3", {className: "game-name omit"}, item.name), 
							React.createElement(Star, {num: item.stars, url: this.props.imgUrl}), 
							React.createElement("div", {className: "game-type"}, React.createElement("span", null, item.type), "|", React.createElement("span", null, item.size), "|", React.createElement("span", null), item.language)
						)
					), 
					React.createElement("div", {className: "game-list-right"}, 
						React.createElement("a", {href: item.download}, React.createElement("input", {className: "dow-list-but", type: "button", value: "下载"}))
					)
				), 
				React.createElement("div", {className: "intr-img"}, 
					React.createElement("img", {src: blankPic, "data-original": item.posters[0], className: "lazy"}), 
					React.createElement("img", {src: blankPic, "data-original": item.posters[1], className: "right lazy"})
				), 
				React.createElement("p", {className: "intr multi-line-omit"}, item.info)
			)
		);
	}
});
var TopicDoubleInfoTwo = React.createClass({displayName: "TopicDoubleInfoTwo",
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
			React.createElement("div", {className: "game-double-info", style: {backgroundColor:this.state.bg}}, 
				React.createElement("div", null, 
					React.createElement("a", {href: item.href}, 
						React.createElement("div", {className: "game-info game-list-left"}, 
							React.createElement("img", {className: "game-photo lazy", src: blankPic, "data-original": item.imgUrl}), 
							React.createElement("h3", {style: {color:this.state.color}, className: "game-name omit"}, item.name), 
							React.createElement(Star, {num: item.stars, url: this.props.imgUrl}), 
							React.createElement("div", {className: "game-type"}, React.createElement("span", null, item.type), "|", React.createElement("span", null, item.size), "|", React.createElement("span", null), item.language)
						)
					), 
					React.createElement("div", {className: "game-list-right"}, 
						React.createElement("a", {href: item.download}, React.createElement("input", {style: {backgroundColor:this.state.color}, className: "dow-list-but", type: "button", value: "下载"}))
					)
				), 
				React.createElement("div", {className: "intr-img"}, 
					React.createElement("img", {src: blankPic, "data-original": item.posters[0], className: "lazy"}), 
					React.createElement("img", {src: blankPic, "data-original": item.posters[1], className: "right lazy"})
				), 
				React.createElement("p", {className: "intr multi-line-omit"}, item.info)
			)
		);
	}
});
var TypeIcon = React.createClass({displayName: "TypeIcon",
	getInitialState:function(){
		var type = {
			end:'已过期',
			in:'进行中'
		}
		return type;
	},
	render:function(){

		return (
			React.createElement("div", {className: "icon-type"}, 
				React.createElement("div", {className: "font"}, this.state[this.props.type]), 
				React.createElement("div", {className: this.props.type})
			)
		);
	}
});
var TopicList  = React.createClass({displayName: "TopicList",
	getInitialState:function(){
		return {
			curPage:this.props.items.curPage
			,hasNext:this.props.items.hasNext
			
		}
	},
	render:function(){
		return (
			React.createElement("div", {className: "topic"}, 
			
				this.props.items.list.map(function(item,i){
					return(
						React.createElement("a", {href: item.href, key: i}, 
							React.createElement(TopicInfo, {items: item})
						)
					);
				}), 
			
			React.createElement(LoadMore, {name: this.props.items.LoadMoreName, href: this.props.items.LoadMoreHref, ajaxUrl: this.props.items.ajaxUrl, isData: this.props.items.list.length, parent: this})
			)
		);
	}
});
var TopicDoubleHot = React.createClass({displayName: "TopicDoubleHot",
	render:function(){
		var item = this.props.items;
		return (
			React.createElement("div", {className: "other"}, 
			React.createElement("a", {href: item[0].href}, 
				React.createElement("div", {className: "row"}, 
					React.createElement("div", {style: {marginRight:'10px'}}, 
						React.createElement("img", {src: blankLongPic, "data-original": item[0].imgUrl, className: "lazy"}), 
						React.createElement("h1", {className: "group-name omit"}, item[0].name)
					)
				)
			), 
			React.createElement("a", {href: item[1].href}, 
				React.createElement("div", {className: "row"}, 
					React.createElement("div", {style: {marginLeft:'10px'}}, 
						React.createElement("img", {className: "lazy", src: blankLongPic, "data-original": item[1].imgUrl}), 
						React.createElement("h1", {className: "group-name omit"}, item[1].name)
					)
				)
			)
			)
		)
	}
})
var TopicDoubleList = React.createClass({displayName: "TopicDoubleList",
	render:function(){
		return (
		React.createElement("div", {className: this.props.items.topicInfo.type+" topic game-list"}, 
			React.createElement(TopicInfo, {items: this.props.items.topic, type: this.props.items.topicInfo.type}), 
			React.createElement("div", {className: "customize-block"}, 
			
				this.props.items.topicInfo.list.map(function(item,index){
					var info;
					if( item.type=='list' ){
						info = React.createElement(RankingDouble, {items: item, index: index})
					}else{
						info = React.createElement(TopicDoubleInfo, {items: item, imgUrl: this.props.items.picturePath, index: index})
					}
					return (
						React.createElement("div", null, 
						info
						)
					)
				},this), 
			
			React.createElement(TopicDoubleHot, {items: this.props.items.hot})
			)
		)
		)
	}
});
var TopicDoubleListTow = React.createClass({displayName: "TopicDoubleListTow",
	render:function(){
		return (
		React.createElement("div", {className: this.props.items.topicInfo.type+" topic game-list"}, 
			React.createElement(TopicInfo, {items: this.props.items.topic, type: this.props.items.topicInfo.type}), 
			React.createElement("div", {className: "customize-block"}, 
			
				this.props.items.topicInfo.list.map(function(item,index){
					var info = "";
					if( item.type=='list' ){
						info = React.createElement(RankingDoubleTwo, {items: item, index: index})
					}else{
						info = React.createElement(TopicDoubleInfoTwo, {items: item, index: index, imgUrl: this.props.items.picturePath})
					}
					return (
						React.createElement("div", null, 
							info
						)
					);
				},this), 
				
			
			React.createElement(TopicDoubleHot, {items: this.props.items.hot})
			)
		)
		)
	}
});

var TopicSingleList = React.createClass({displayName: "TopicSingleList",
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
			React.createElement("div", {className: this.props.items.topicInfo.type+" topic game-list"}, 
				React.createElement(TopicInfo, {items: this.props.items.topic, type: this.props.items.topicInfo.type}), 
				React.createElement("div", {className: "galf-line", dangerouslySetInnerHTML: {__html:img}}), 
				React.createElement("div", {className: "customize-block"}, 
				
					this.props.items.topicInfo.list.map(function(item,i){
						return (
							React.createElement("div", {className: "game-list-row", key: i}, 
								React.createElement("a", {href: item.href}, 
									React.createElement("div", {className: "game-list-left"}, 
										React.createElement(TopicInfoStar, {items: item, imgUrl: this.props.items.picturePath})
									)
								), 
								React.createElement("div", {className: "game-list-right"}, 
									React.createElement("a", {href: item.download}, React.createElement("input", {className: "dow-list-but", type: "button", value: "下载"}))
								)
							)
						);
					},this), 
				
				React.createElement(LoadMore, {name: this.props.items.topicInfo.LoadMoreName, ajaxUrl: this.props.items.topicInfo.ajaxUrl, parent: this, isData: this.props.items.topicInfo.list.length})
				)
			)
		);
	}
});

var Activity  = React.createClass({displayName: "Activity",

	render:function(){
		var item = this.props.items
			,infoStyle;
		if(!item.info){
			infoStyle = {
				display:'none'
			}
		}
		return (
				React.createElement("div", {className: "activity"}, 
				React.createElement("a", {href: this.props.items.href}, 
					React.createElement("div", null, 
						React.createElement("img", {src: blankPic, "data-original": item.imgUrl, className: "activity-poster lazy"}), 
						React.createElement("div", {className: "activity-info"}, 
							React.createElement("h3", {className: "activity-name omit"}, item.activityName||item.name), 
							React.createElement("div", {className: "multi-line-omit ui-editor", style: infoStyle, dangerouslySetInnerHTML: {__html:item.info}})
						)
					)
				), 
				React.createElement(LoadMore, {name: this.props.items.LoadMoreName, href: this.props.items.LoadMoreHref})
				)
		);
	}
});

var ActivityInfo = React.createClass({displayName: "ActivityInfo",
	render:function(){
		return (
			React.createElement("div", {id: "activity", className: "activity"}, 
			React.createElement("div", {id: "activityRoll"}, 
			
				this.props.items.map(function(item,i){
					return (
						
						React.createElement("div", {key: i, className: i?"activity-info":"on activity-info"}, 
							React.createElement("a", {href: item.href}, 
							React.createElement("div", {className: "activity-name"}, "【活动】", item.name), 
							React.createElement("div", null, "活动时间：", item.date), 
							React.createElement("div", null, "奖励内容：", item.rewardRule)
							)
						)
					);
				})
			
			), 
			React.createElement("div", {id: "activityRollCopy"})
			)
		);
	}
});
var ActivityDetails  = React.createClass({displayName: "ActivityDetails",
	render:function(){
		var item = this.props.items
			,displayImg = 'none'
			,download

			;
		
		if(item.download){
			download = "<a href="+item.download+"><input type='button' value='下载' class='dow-list-but' /></a>";
		}
		if(item.imgUrl){
			displayImg = 'block';
		}

		return (
			React.createElement("div", {className: "activity-details"}, 
				React.createElement("h1", {className: "name"}, item.name), 
				React.createElement("img", {style: {display:displayImg}, src: blankLongPic, "data-original": item.imgUrl, className: "topic-poster lazy"}), 
				React.createElement("div", {className: "activity-info"}, 
					React.createElement("div", {className: "block"}, 
						React.createElement("div", {className: "ui-editor", dangerouslySetInnerHTML: {__html:item.contents}})
					), 
					React.createElement("div", {className: "block", dangerouslySetInnerHTML: {__html:download}}
					
					)
				)
			)
		)
	}
});


var PackageDetails=React.createClass({displayName:"PackageDetails",getInitialState:function(){return{layer:{display:'none'}}},handleClick:function(type){if(type){this.state.layer.display=type;this.setState();}else{location.href=this.props.items.download;}},render:function(){var item=this.props.items;return(React.createElement("div",{className:"package-details"},React.createElement("div",{className:"basic"},React.createElement("div",{className:"package-details-left"},React.createElement(PackageDetailsInfo,{items:item})),React.createElement("div",{className:"package-details-right"},React.createElement("span",{onClick:this.handleClick,className:"icon-grab"}))),React.createElement("div",{className:"explain"},React.createElement("h3",null,"礼包内容"),React.createElement("p",null,item.contents)),React.createElement("div",{className:"explain"},React.createElement("h3",null,"使用时间"),React.createElement("p",null,item.date)),React.createElement("div",{className:"explain"},React.createElement("h3",null,"使用方法"),React.createElement("p",null,item.useMethod)),React.createElement(PopLayerDouble,{msg:"需要安装游戏大厅客户端才能领取礼包， 现在是否下载？",click:this.handleClick,parent:this})))}});var PackageList=React.createClass({displayName:"PackageList",getInitialState:function(){return{curPage:0,data:[],hasNext:this.props.type}},render:function(){return(React.createElement("div",{className:"package-list"},this.state.data.map(function(item,i){return(React.createElement("a",{href:item.href,key:i},React.createElement("div",{className:"package-list-row"},React.createElement("div",{className:"package-info"},React.createElement("img",{className:"game-photo lazy",src:blankPic,"data-original":item.imgUrl}),React.createElement("h3",{className:"package-name omit"},item.name),React.createElement(ProgressBar,{total:item.total,residual:item.residual}),React.createElement("p",null,React.createElement("span",null,"剩余：",item.residual,"个"))))))}),React.createElement(LoadMore,{name:"加载更多",ajaxUrl:this.props.ajaxUrl,parent:this})));}});var PackageDetailsInfo=React.createClass({displayName:"PackageDetailsInfo",render:function(){var item=this.props.items;return(React.createElement("div",{className:"package-info"},React.createElement("img",{className:"package-photo lazy",src:blankPic,"data-original":item.imgUrl}),React.createElement("h3",{className:"package-name omit"},item.name),React.createElement(ProgressBar,{total:item.total,residual:item.residual}),React.createElement("div",{className:"residual omit"},"剩余：",item.residual,"个")));}});


var Menu=React.createClass({displayName:"Menu",render:function(){return(React.createElement("div",{className:"menu"},React.createElement("ul",null,this.props.items.list.map(function(item,i){if(i>3)
return;return(React.createElement("li",{key:i},React.createElement("a",{href:item.href},React.createElement("i",{className:item.type}),item.name)))})),React.createElement("ul",null,this.props.items.list.map(function(item,i){if(i<4)
return;return(React.createElement("li",{key:i},React.createElement("a",{href:item.href},React.createElement("i",{className:item.type}),item.name)))}))))}});


var InfoBulletin  = React.createClass({displayName: "InfoBulletin",
	render:function(){
		var imgUrl = this.props.imgUrl;
		return (
				React.createElement("div", {className: "info-bulletin-list"}, 
				React.createElement("h1", {className: "group-name"}, this.props.items.name, React.createElement("span", {className: "arrow-down"})), 
				
					this.props.items.list.map(function(item,i){
						return (
							React.createElement("div", {className: "info-bulletin-list-row", key: i}, 
								React.createElement("a", {href: item.href}, 
									React.createElement("div", {className: "info-bulletin-list-left"}, 
										React.createElement("img", {"data-original": item.imgUrl, className: "posters lazy", src: blankLongPic})
									), 
									React.createElement("div", {className: "info-bulletin-list-right"}, 
										React.createElement("h3", {className: "bulletin-name"}, item.name), 
										React.createElement("div", {className: "multi-line-omit ui-editor", dangerouslySetInnerHTML: {__html:item.info}})
									)
								)
							)
						)
					}), 
				
				React.createElement(LoadMore, {name: this.props.items.LoadMoreName, href: this.props.items.LoadMoreHref})
				)
		);
	}
});
var InfoBulletinList = React.createClass({displayName: "InfoBulletinList",
	getInitialState:function(){
		return {
			curPage:this.props.items.curPage
			,hasNext:this.props.items.hasNext
		}
	},
	render:function(){
		var imgUrl = this.props.imgUrl;
		return (
				React.createElement("div", {className: "info-bulletin-list bulletin-list-page"}, 
				
					this.props.items.list.map(function(item,i){
						return (
							React.createElement("div", {className: "info-bulletin-list-row", key: i}, 
								React.createElement("a", {href: item.href}, 
									React.createElement("div", {className: "info-bulletin-list-left"}, 
										React.createElement("img", {className: "posters lazy", src: blankLongPic, "data-original": item.imgUrl})
									), 
									React.createElement("div", {className: "info-bulletin-list-right"}, 
										React.createElement("h3", {className: "bulletin-name omit"}, item.name), 
										React.createElement("div", {className: "date"}, item.date), 
										React.createElement("div", {className: "omit ui-editor", dangerouslySetInnerHTML: {__html:item.info}})
									)
								)
							)
						)
					}), 
				
				React.createElement(LoadMore, {ajaxUrl: this.props.items.ajaxUrl, parent: this, isData: this.props.items.list.length})
				)
		);
	}
});
var ComIndex=React.createClass({displayName:"ComIndex",render:function(){return(React.createElement("div",null,this.props.items.list.map(function(item){return(React.createElement("div",null,getComponent(item.typeName,item)));})))}});


var GameList=React.createClass({displayName:"GameList",getInitialState:function(){return{curPage:this.props.items.curPage,hasNext:this.props.items.hasNext}},render:function(){return(React.createElement("div",{className:"game-list"},this.props.items.list.map(function(item,i){return(React.createElement("div",{className:"game-list-row",key:i},React.createElement("a",{href:item.href},React.createElement("div",{className:"game-list-left"},React.createElement(GameColumnInfo,{items:item}))),React.createElement("div",{className:"game-list-right"},React.createElement("a",{href:item.download},React.createElement("input",{className:"dow-list-but",type:"button",value:"下载"})))))}),React.createElement(LoadMore,{name:this.props.items.LoadMoreName,href:this.props.items.LoadMoreHref,ajaxUrl:this.props.items.ajaxUrl,parent:this,isData:this.props.items.list.length})));}});var Ranking=React.createClass({displayName:"Ranking",getInitialState:function(){return{curPage:this.props.items.curPage,hasNext:this.props.items.hasNext}},render:function(){var now=this.props.items.now,href=this.props.items.LoadMoreHref||this.props.items.href;return(React.createElement("div",{className:"ranking-list game-list"},React.createElement("h1",{className:"group-name "},this.props.items.name,React.createElement("span",{className:"arrow-down"})),this.props.items.data.map(function(item,i){return(React.createElement("div",{className:"game-list-row",key:i},React.createElement("a",{href:item.href},React.createElement("div",{className:"game-list-left"},now?React.createElement(Now,{index:i+1}):"",React.createElement(GameColumnInfo,{items:item}))),React.createElement("div",{className:"game-list-right"},React.createElement("a",{href:item.download},React.createElement("input",{className:"dow-list-but",type:"button",value:"下载"})))))}),React.createElement(LoadMore,{name:this.props.items.LoadMoreName,href:href,ajaxUrl:this.props.items.ajaxUrl,parent:this,isData:this.props.items.data.length})));}});var DateRanking=React.createClass({displayName:"DateRanking",getInitialState:function(){return{curPage:this.props.items.curPage,hasNext:this.props.items.hasNext}},render:function(){return(React.createElement("div",{className:"game-list date-ranking-list"},React.createElement("span",{className:"line-orange"}),this.props.items.list.map(function(item,i){var start,paddingTop='0px';if(item.date!=this.date){paddingTop='15px';this.date=item.date;start='<h1 class="group-name"><span class="icon-hollow-circle"></span>'+item.date+'</h1>';}
return(React.createElement("div",{key:i},React.createElement("div",{dangerouslySetInnerHTML:{__html:start}}),React.createElement("div",{className:"date-ranking-row",style:{paddingTop:paddingTop}},React.createElement("a",{href:item.href},React.createElement("div",{className:"game-list-left"},React.createElement(GameColumnInfo,{items:item}))),React.createElement("div",{className:"game-list-right"},React.createElement("a",{href:item.download},React.createElement("input",{className:"dow-list-but",type:"button",value:"下载"}))))))},this),React.createElement(LoadMore,{name:this.props.items.LoadMoreName,ajaxUrl:this.props.items.ajaxUrl,parent:this,isData:this.props.items.list.length})));}});var RankingDouble=React.createClass({displayName:"RankingDouble",getInitialState:function(){var color=['#ff6b50','#00d8ff','#9654b6','#53d37e'];return{color:color[this.props.index]}},render:function(){var color=this.state.color;return(React.createElement("div",{className:"game-double-list"},React.createElement("h1",{className:"group-name",style:{color:color}},React.createElement("span",{className:"omit"},this.props.items.name),React.createElement("span",{className:"arrow-down",style:{borderTopColor:color}})),React.createElement("p",{className:"intr"},this.props.items.info),React.createElement("ul",{className:"table"},this.props.items.data.map(function(item,i){var right;if(i%2){right='game-double-list-right'}
return(React.createElement("li",{className:right,key:i},React.createElement(GameDoubleInfo,{items:item,color:color})))}))));}});var RankingDoubleTwo=React.createClass({displayName:"RankingDoubleTwo",getInitialState:function(){var color=['#ff6b50','#00d8ff','#ff9c00','#53d37e'],bg=['#fff4ef','#f0fdff','#fff7ed','#f8ffee'],shadow=['#ae3c27','#0093ad','#ae6200','#378b53'],index=this.props.index;return{color:color[index],bg:bg[index],shadow:shadow[index]}},render:function(){var color=this.state.color;return(React.createElement("div",{className:"game-double-list",style:{backgroundColor:this.state.bg}},React.createElement("h1",{className:"group-name icon-title",style:{backgroundColor:this.state.color}},React.createElement("span",{style:{borderTopColor:this.state.shadow},className:"icon-title-arrow-bg"}),React.createElement("span",{className:"omit"},this.props.items.name),React.createElement("span",{style:{borderRightColor:this.state.bg},className:"icon-title-arrow"})),React.createElement("p",{className:"intr"},this.props.items.info),React.createElement("ul",{className:"table"},this.props.items.data.map(function(item,i){var right;if(i%2){right='game-double-list-right'}
return(React.createElement("li",{className:right,key:i},React.createElement(GameDoubleInfo,{items:item,index:i,color:color})))}))));}});


var GameDetailsMenu=React.createClass({displayName:"GameDetailsMenu",getInitialState:function(){return{introduction:{name:'介绍',Class:'on'},package:{name:'礼包',Class:''},strategy:{name:'攻略',Class:''}}},handleClick:function(name){var parState=this.props.parent.state;for(var i in parState){if(this.state[i]){parState[i].display='none';this.state[i].Class='';}}
parState[name].display='block';this.state[name].Class='on';this.setState();this.props.parent.setState();},render:function(){var menuArr=[],liWidth,menuShow="block";for(var i in this.props.items){if(this.props.items[i]){this.state[i].type=i;menuArr.push(this.state[i]);}}if( menuArr.length==1 ){menuShow = "none";}
liWidth=100/menuArr.length;return(React.createElement("ul",{className:"game-details-menu",style:{"display":menuShow}},menuArr.map(function(item,i){{var cc=this.handleClick.bind(this,item.type)}
return(React.createElement("li",{key:i,style:{width:liWidth+'%'},onClick:cc,className:item.Class},item.name))},this)));}});var Introduction=React.createClass({displayName:"Introduction",getInitialState:function(){return{style:{WebkitLineClamp:'3',},showAll:{name:'展开',class:'icon-show-all-bottom'}}},handleClick:function(){var styleVal='inherit',showName='收起',showClass='icon-show-all-top';if(this.state.style.WebkitLineClamp==styleVal){styleVal='3';styleVal='3';showName='展开';showClass='icon-show-all-bottom';}
this.setState({style:{WebkitLineClamp:styleVal},showAll:{name:showName,class:showClass}});},render:function(){var gossipStyle='auto',introductionStyle='auto';if(!this.props.items.gossip){gossipStyle='none';}
if(!this.props.items.introduction){introductionStyle='none';}
return(React.createElement("div",null,React.createElement("div",{className:"poster-introduction"},React.createElement("div",{className:"game-images-introduction"},this.props.items.imgUrl.map(function(item,i){return(React.createElement("img",{key:i,src:blankPic,"data-original":item,className:"lazy"}));}))),React.createElement("div",{className:"gossip",style:{display:gossipStyle}},React.createElement("h1",{className:"group-name"},"小编八卦"),React.createElement("div",{className:"ui-editor",dangerouslySetInnerHTML:{__html:this.props.items.gossip}})),React.createElement("div", {className: "introduction", style: {display:introductionStyle}}, React.createElement("h1", {className: "group-name"}, "游戏简介"), React.createElement("div", {className: "multi-line-omit ui-editor", style: this.state.style}, React.createElement("div", {dangerouslySetInnerHTML: {__html: this.props.items.introduction}})), React.createElement("div", {className: "show-all"}, React.createElement("span", {onClick: this.handleClick}, React.createElement("span", {className: this.state.showAll.class}), this.state.showAll.name)))));
}});var GameDetails=React.createClass({displayName:"GameDetails",getInitialState:function(){return{introduction:{display:'block'},package:{display:'none'},strategy:{display:'none'},layer:{display:'none'}}},handleClick:function(type){if(type){this.state.layer.display=type;this.setState();}else{location.href=this.props.items.clientDownUrl;}},render:function(){var activity;if(this.props.items.activity.length){activity=React.createElement(ActivityInfo,{items:this.props.items.activity});}
return(React.createElement("div",{className:"game-details"},React.createElement("div",{className:"basic"},React.createElement("div",{className:"game-details-left"},React.createElement(GameInfoStar,{items:this.props.items})),React.createElement("div",{className:"game-details-right"},React.createElement("input",{onClick:this.handleClick.bind(this,'block'),className:"dow-list-but",type:"button",value:"极速下载"}),React.createElement("a",{href:this.props.items.href?this.props.items.href:'javascript:'},React.createElement("input",{className:"dow-list-but grey",type:"button",value:"普通下载"})))),activity,React.createElement(GameDetailsMenu,{parent:this,items:this.props.items.menu}),React.createElement("div",{style:this.state.introduction},React.createElement(Introduction,{items:this.props.items.introduction}),React.createElement(Edition,{items:this.props.items.edition}),React.createElement(ProbablyLike,{items:this.props.items.probablyLike})),React.createElement("div",{style:this.state.package},React.createElement(PackageList,{type:this.props.items.menu.package,ajaxUrl:this.props.items.packageAjaxUrl})),React.createElement("div",{style:this.state.strategy},React.createElement(StrategyList,{type:this.props.items.menu.strategy,ajaxUrl:this.props.items.strategyAjaxUrl})),React.createElement(PopLayerDouble,{msg:"需要安装游戏大厅客户端才能体验极速下载，现在是否下载？",click:this.handleClick,parent:this})))}});


var Container=React.createClass({displayName:"Container",render:function(){var imgUrl=this.props.imgUrl;return(React.createElement("div",null,this.props.items.list.map(function(item,i){return(React.createElement("div",{key:i},getComponent(item.component,item,imgUrl)));})))}});


var GameRowInfo=React.createClass({displayName:"GameRowInfo",render:function(){var item=this.props.items,style={};if(item.typeName&&item.size){style.val='|';style.name='padding';}
return(React.createElement("div",{className:"game-info"},React.createElement("a",{href:item.href},React.createElement("img",{className:"game-photo lazy",src:blankPic,"data-original":item.imgUrl}),React.createElement("h3",{className:"game-name omit"},item.name),React.createElement("div",{className:"game-size-type"},React.createElement("span",null,item.typeName),React.createElement("span",{className:style.name},style.val),React.createElement("span",null,item.size))),React.createElement("a",{href:item.download},React.createElement("input",{type:"button",value:"下载",className:"dow-list-but"}))));}});var Collection=React.createClass({displayName:"Collection",render:function(){return(React.createElement("div",{className:"collection"},React.createElement("h1",{className:"group-name"},this.props.items.name,React.createElement("span",{className:"arrow-down"})),React.createElement("ul",null,this.props.items.data.map(function(item,i){return(React.createElement("li",{key:i},React.createElement(GameRowInfo,{items:item})))})),React.createElement(LoadMore,{name:this.props.items.LoadMoreName,href:this.props.items.LoadMoreHref})));}});


var HotSubject=React.createClass({displayName:"HotSubject",render:function(){var items=this.props.data;return(React.createElement("div",null,React.createElement("div",{className:"title-container"},React.createElement("div",{className:"absolute-vCenter"},"热门合辑"),React.createElement("a",{className:"absolute-vCenter skip",href:items.viewAllHref},"查看全部",React.createElement("i",{className:"icon-skip"}))),React.createElement("ul",{className:"hotSubject-list"},items.list.map(function(item,i){return(React.createElement("li",{key:i},React.createElement("a",{href:item.url},React.createElement("img",{src:blankPic,"data-original":item.imgUrl,alt:""}),React.createElement("div",{className:"name omit"},item.name))))}))));}});var Category=React.createClass({displayName:"Category",getInitialState:function(){return{curPage:this.props.items.curPage,hasNext:this.props.items.hasNext};},render:function(){var data=this.props.items;return(React.createElement("div",null,React.createElement("div",{className:"title-container"},React.createElement("div",{className:"absolute-vCenter"},"游戏分类")),React.createElement("ul",{className:"categorys-list"},data.list.map(function(list,i){return(React.createElement("li",{className:"category",key:i},React.createElement("a",{className:"wrapper-link",href:list.url},React.createElement("img",{src:blankPic,"data-original":list.imgUrl,alt:""})),React.createElement("ul",{className:"items-list "},list.items.map(function(item,j){return(React.createElement("li",{className:"item",key:j},React.createElement("a",{href:item.url},React.createElement("div",{className:"omit",dangerouslySetInnerHTML:{__html:item.title}}))))}))))})),React.createElement(LoadMore,{parent:this,isData:this.props.items.list.length,ajaxUrl:categoryAjaxUrl})));}});var activeClass="active";var CategoryNameList=React.createClass({displayName:"CategoryNameList",render:function(){var items=this.props.data;var id=this.props.id;return(React.createElement("ul",{className:"J_categoryNameList categoryName-list"},items.map(function(item,i){var className=item.id==id?activeClass:'';return(React.createElement("li",{"data-id":item.id,key:i,className:className},React.createElement("a",{href:item.url},React.createElement("span",{className:"omit"},item.title))))},this)));}});var SubCategory=React.createClass({displayName:"SubCategory",render:function(){var subObj=this.props.data;return(React.createElement("div",{className:"subCategory-list"},React.createElement(CategoryNameList,{data:subObj.nameList,id:subObj.id})))}});


var Article=React.createClass({displayName:"Article",render:function(){var item=this.props.items,link;if(item.href){link="<a href="+item.href+">查看原文</a>"}
return(React.createElement("div",{className:"article"},React.createElement("h1",{className:"name"},item.name),React.createElement("div",{className:"date"},React.createElement("span",{className:"source"},"来源：",item.original),React.createElement("span",null,item.date)),React.createElement("div",{className:"ui-editor ",dangerouslySetInnerHTML:{__html:this.props.items.info}}),React.createElement("div",{className:"look-article",dangerouslySetInnerHTML:{__html:link}})));}});

var Advertising  = React.createClass({displayName: "Advertising",
	render:function(){
		return (
			React.createElement("div", {style: {margin: "20px 0px"}}, 
				React.createElement("a", {href: this.props.items.href}, React.createElement("img", {className: "posters posters-advertising lazy", src: blankLongPic, "data-original": this.props.items.imgUrl}))
			)
		);
	}
});