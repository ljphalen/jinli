(function(iCat){
	var _cfg_ = {
		'recommend':{
			curIndex:1,
			hasnext:true
		}
	};
	//template
	GameApk.template =
	{
		slidePic: //旧版首页图片轮播
		'<div><section class="wrap first-grade">\
			<div class="slide-pic ">\
			<%if(data.length){%>\
				<div class="slideWrap">\
					<div class="pic">\
					<%for(var i=0, len=data.length; i<len; i++){%>\
						<a <%if(version!="v1"){%> data-type="<%=data[i]["data-type"]%>" <%}%>  data-infpage="<%=data[i]["data-infpage"]%>" >\
							<img src="<%=blankPic%>" data-src="<%=data[i].img%>">\
						</a>\
						<%}%>\
					</div>\
				</div>\
				<%}%>\
			</div>\
		</section>\
		</div>',
		recItem: //旧版推荐栏目
		'<div><section class="wrap first-grade">\
			<div class="channel-list ">\
			<%if(data.length&&data.length>=4){%>\
				<ul>\
				<%for(var i=0, len=data.length; i<len; i++){%>\
					<%if(i>=4){%>\
					<%}else{%>\
						<li>\
							<a <%if(version!="v1"){%> data-type="<%=data[i]["data-type"]%>" <%}%> data-infpage="<%=data[i]["data-infpage"]%>">\
								<div class="img-box">\
									<div class="panel">\
										<img src="<%=blankPic%>" data-src="<%=data[i].icon%>" alt="">\
									</div>\
								</div>\
								<div class="layer"><%=data[i].title%></div>\
							</a>\
						</li>\
					<%}}%>\
				</ul>\
			<%}%>\
			</div>\
		</section>\
		</div>',
		recommendlist://旧版推荐游戏
		'<div><section class="wrap first-grade">\
			<div class="rec3-list clearfix">\
			<%if(data.list.length){%>\
				<ul>\
					<%for(var i=0, len=data.list.length; i<len; i++){%>\
						<li>\
						<%if(data.list[i].hot=="最新"){%>\
							<div class="new"><span><%=data.list[i].hot%></span></div>\
						<%}else if(data.list[i].hot=="最热"){%>\
							<div class="hot"><span><%=data.list[i].hot%></span></div>\
						<%}else if(data.list[i].hot=="首发"){%>\
							<div class="first"><span><%=data.list[i].hot%></span></div>\
						<%}else if(data.list[i].hot=="活动"){%>\
						<div class="event"><span><%=data.list[i].hot%></span></div>\
						<%}else{%>\
						<%}%>\
					<a <%if(version!="v1"){%> data-type="<%=data.list[i]["data-type"]%>"  <%}%> data-infpage="<%=data.list[i]["data-infpage"]%>">\
						<span class="icon">\
						<img src="<%=blankPic%>" data-src="<%=data.list[i].img%>" alt="">\
						</span>\
						<span class="desc">\
						<em><%=data.list[i].name%></em>\
						<p><em><%=data.list[i].category%></em><em>|</em><em><%=data.list[i].size%></em>\
						<%if(version=="v2"){%>\
						<%if(data.list[i].attach!=""||data.list[i].device!=0){%>\
							<em class="tips">\
							<%var tipArr=data.list[i].attach.split(",");while(tipArr.length){%>\
								<%if(tipArr[0]=="礼"){%>\
									<span class="gift"></span>\
								<%}if(tipArr[0]=="评"){%>\
									<span class="comment"></span>\
									<%}tipArr.shift();}%>\
									<%if(data.list[i].device==1){%>\
										<span class="grip"></span>\
									<% } %>\
							</em>\
						<%}}%>\
						</p>\
						<b><%=data.list[i].resume%></b>\
						</span>\
						</a>\
					</li>\
					<%}%>\
				</ul>\
				<%}%>\
			</div>\
		</section>\
		</div>'
	};

	//view
	var bi = typeof t_bi != 'undefined'? ('t_bi=' + t_bi) : '',
		i = 0;
	GameApk.view = iCat.View.extend({
		tmplData:{
			// http://assets.3gtest.gionee.com/apps/game/apk/pic/blank.gif
			blankPic:(iCat.PathConfig.staticRef+iCat.PathConfig.appRef).replace(/assets\/js\//g, 'pic/blank.gif'),
			version:GameApk.ApiVersion
		}
	});

	GameApk.view.setting = {
		'slidePic':{//首页 图片轮播
				config:{
					tmplId:'slidePic',
					wrap:'#container',
					ajaxUrl:GameApk.ApiVersion=='v2'?'/api/indexi/turn':'/api/index/turn',
				}
		  	},
		'recItem':{
				config:{
					tmplId:'recItem',
					ajaxUrl:GameApk.ApiVersion=='v2'?'/api/indexi/channel':'/api/index/channel',
					wrap: '#container'
				}
		},
		'recommend':{
			config:{
				tmplId:'recommendlist',
				wrap: '#container',
				ajaxUrl:GameApk.ApiVersion=='v2'?'/api/indexi/recommend':'/api/index/recommend',
				clear:false,
				callback:function(warp,cfg,data){
					if(!data.data||!data.data.list) return;
					if(data.data&&data.data.list.length>0){
						_cfg_.recommend.hasnext=data.data.hasnext;
						_cfg_.recommend.curIndex=data.data.curpage+1;
					}
					var J_loadMore=document.getElementsByClassName('J_loadMore');
					if(_cfg_.recommend.hasnext){
						if(J_loadMore.length){
							J_loadMore[0].style.display='none';
						}
					}
					else{
						if(J_loadMore.length){
							//大侠，已加载到底部了
							J_loadMore[0].children[0].className='bottom';
							J_loadMore[0].children[0].innerHTML='到底了，去其它页面看看吧';
							
						}
					}
				},
				events:[{
					selector:window,type:'scroll',preventDefault:true,stopPropagation:true,
					callback:function(c,el,evt){
						if(_cfg_.recommend.hasnext==true){
							var boxHeight=document.body.clientHeight,
								visibleHeight=document.documentElement.clientHeight,
								boxScrollTop=document.body.scrollTop;
							if(Math.abs(boxHeight-visibleHeight)<=boxScrollTop+50){
								if(!$("#container").find('.J_loadMore').length){
									$("#container").append('<div class="loading J_loadMore"><span class="load"></span></div>')
								}
								$('.J_loadMore').show();
								this.setConfig({
									ajaxUrl:GameApk.ApiVersion=='v2'?'/api/indexi/recommend?page='+_cfg_.recommend.curIndex:'/api/index/recommend?page='+_cfg_.recommend.curIndex,
									subkey:_cfg_.recommend.curIndex
								});
							}
						}
						else{
							c.unbindEvents(this.config.id);
						}
					}
				}]
			},
		},
	};
})(ICAT);