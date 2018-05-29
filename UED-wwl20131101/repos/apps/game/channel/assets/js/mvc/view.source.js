(function(iCat){
	var _cfg_ = {
		curIndex:1,
		hasnext:true
	};
	//template
	GameApk.template =
	{
		slidePic: //首页大广告位
		'<section >\
			<div class="slide-pic ">\
			<%if(data.length){%>\
				<div class="slideWrap">\
					<div class="pic">\
					<%for(var i=0, len=data.length; i<len; i++){%>\
						<a <%if(version!="v1"){%> data-type="<%=data[i]["data-type"]%>" <%}%>  data-infpage="<%=data[i]["data-infpage"]%>" >\
							<img src="<%=blankPic%>" data-special data-src="<%=data[i].img%>">\
							<div><span><%=data[i].title%></span></div>\
						</a>\
						<%}%>\
					</div>\
				</div>\
				<%}%>\
			</div>\
		</section>',
		recItem: //推荐栏目
		'<section>\
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
								<div class="layer"><span><%=data[i].title%></span></div>\
							</a>\
						</li>\
					<%}}%>\
				</ul>\
			<%}%>\
			</div>\
		</section>',
		activity://活动
		'<section >\
			<%if(data.length!=0){%>\
			<div class="activity-list ">\
				<a data-type="-1" data-infpage="<%=data["data-infpage"]%>"><img src="<%=blankPic%>" data-src="<%=data.img%>" alt="">\
				<div><span><%=data.title%></span></div>\
				</a>\
			</div>\
			<%}%>\
		</section>',
		recommendlist://旧版推荐游戏
		'<section class="wrap" >\
			<div class="rec3-list">\
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
						<img src="<%=blankPic%>" data-src="<%=data.list[i]["img"]%>" alt="">\
						</span>\
						<span class="desc">\
						<em><%=data.list[i].name%></em>\
						<p><%if(data.list[i].category){%>\
							<em><%=data.list[i].category%></em><em>|</em>\
						<%}else{}%>\
						<em><%=data.list[i].size%></em>\
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
									<%}%>\
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
		</section>',
		category://分类列表页
		'<section class="wrap">\
			<div class="category-list">\
			<%if(data.list.length){%>\
				<ul>\
				<%for(var i=0,j=data.list.length;i<j;i++){%>\
					<li>\
						<a <%if(version!="v1"){%> data-type="<%=data.list[i]["data-type"]%>" <%}%> data-infpage="<%=data.list[i]["data-infpage"]%>" >\
							<span class="pic"><img src="<%=blankPic%>" data-src="<%=data.list[i]["img"]%>" alt=""></span>\
							<span class="desc"><b><%=data.list[i]["title"]%></b><em><%=data.list[i]["game_title"]%></em></span>\
						</a>\
					</li>\
				<%}%>\
				</ul>\
			<%}%>\
			</div>\
		</section>',
		subject://专题列表页
		'<section class="wrap">\
			<%if(data.list.length){%>\
			<div class="subject-list">\
				<ul>\
				<%for(var i=0,j=data.list.length;i<j;i++){%>\
					<li>\
						<a <%if(version!="v1"){%> data-type="<%=data.list[i]["data-type"]%>" <%}%> data-infpage="<%=data.list[i]["data-infpage"]%>" >\
							<div class="intro">\
								<span class="pic"><img src="<%=blankPic%>" data-src="<%=data.list[i]["img"]%>" alt="" ></span>\
								<span class="title"><b><%=data.list[i]["title"]%></b><em><%=data.list[i]["start_time"]%></em></span>\
							</div>\
							<div class="content"><span class="desc"><%=data.list[i]["resume"]%></span></div>\
						</a>\
					</li>\
				<%}%>\
				</ul>\
			</div>\
			<%}%>\
		</section>'
	};
	//view
	var bi = typeof t_bi != 'undefined'? ('t_bi=' + t_bi) : '',
		i = 0;
	GameApk.view = iCat.View.extend({
		tmplData:{
			blankPic:iCat.PathConfig.picPath +'blank.gif',
			version:GameApk.ApiVersion
		}
	});
	GameApk.view.setting = {
		'slidePic':{//首页 图片轮播
				config:{
					tmplId:'slidePic',
					wrap:'.module',
					ajaxUrl:'/api/Channel_Index/turn',
				}
		  	},
		'recItem':{
				config:{
					tmplId:'recItem',
					ajaxUrl:'/api/Channel_Index/channel',
					wrap: '.module'
				}
		},
		'recommend':{
			config:{
				tmplId:'recommendlist',
				wrap: '.module',
				ajaxUrl:'/api/Channel_Index/recommend'+location.search,
				clear:false,
				callback:function(warp,cfg,data){
					GameApk.view.setting.ajaxSucCallback(warp,cfg,data);
				},
				events:[{
					selector:window,type:'scroll',preventDefault:true,stopPropagation:true,
					callback:function(el,evt){
						GameApk.view.setting.eventCallback(el,evt,this);
					}
				}]
			},
		},
		'category':{
			config:{
				wrap:'.module',
				tmplId:'category',
				ajaxUrl:'/api/Channel_Category/index'+location.search,
				clear:false,
				callback:function(warp,cfg,data){
					GameApk.view.setting.ajaxSucCallback(warp,cfg,data);
				},
				events:[{
					selector:window,type:'scroll',preventDefault:true,stopPropagation:true,
					callback:function(el,evt){
						GameApk.view.setting.eventCallback(el,evt,this);
					}
				}]
			}
		},
		'subject':{
			config:{
				wrap:'.module',
				tmplId:'subject',
				ajaxUrl:'/api/Channel_Subject/index'+location.search,
				clear:false,
				callback:function(warp,cfg,data){
					GameApk.view.setting.ajaxSucCallback(warp,cfg,data);
				},
				events:[{
					selector:window,type:'scroll',preventDefault:true,stopPropagation:true,
					callback:function(el,evt){
						GameApk.view.setting.eventCallback(el,evt,this);
					}
				}]
			}
		},
		ajaxSucCallback:function(warp,cfg,data){
			if(!data.data||!data.data.list) return;
			if(data.data&&data.data.list.length>0){
				_cfg_.hasnext=data.data.hasnext;
				_cfg_.curIndex=data.data.curpage+1;
			}
			var J_loadMore=document.getElementsByClassName('J_loadMore');
			if(_cfg_.hasnext){
				if(J_loadMore.length){
					J_loadMore[0].style.display='none';
				}
			}
			else{
				if(J_loadMore.length){
					J_loadMore[0].children[0].className='bottom';
					J_loadMore[0].children[0].innerHTML='<em></em>到底了，去其它页面看看吧';
				}
			}
		},
		eventCallback:function(el,evt,that){
			if(_cfg_.hasnext==true){
				var boxHeight=document.body.clientHeight,
					visibleHeight=document.documentElement.clientHeight,
					boxScrollTop=document.body.scrollTop;
				if(Math.abs(boxHeight-visibleHeight)<=boxScrollTop+50){
					if(!$(".module").find('.J_loadMore').length){
						$(".module").append('<div class="loading J_loadMore"><span class="load"></span></div>')
					}
					$('.J_loadMore').show();
					var url="";
					if(that.config.ajaxUrl.indexOf('?sp')>-1){
						if(that.config.ajaxUrl.indexOf('&page')>-1){
							url=that.config.ajaxUrl.split('&')[0]+"&page="+_cfg_.curIndex;
						} else{
							url=that.config.ajaxUrl+"&page="+_cfg_.curIndex;
						}
					}
					else{
						url=that.config.ajaxUrl.indexOf('?page')>-1?that.config.ajaxUrl.split('?')[0]+"?page="+_cfg_.curIndex:that.config.ajaxUrl+"?page="+_cfg_.curIndex;
					}
					that.setConfig({
						ajaxUrl:url,
						subkey:_cfg_.curIndex
					});
				}
			}
			else{
				$(window).unbind('scroll');
			}
		}
	};
})(ICAT);