(function(iCat){
	var _cfg_ = {
		curIndex:1,
		hasnext:true
	};
	//template
	GameApk.template =
	{
		slidePic: //首页大广告位
		'<%if(data.length){%>\
			<%var d;if(data[0]["data-type"]!=4){d=data[0];}else{for(var i=0,len=data.length;i<len;i++){if(data[i]["data-type"]!=4){d=data[i];break;}}}%>\
			<%if(d&&d!=""){%>\
				<div class="slide-pic ">\
					<a <%if(version!="v1"){%> data-type="<%=d["data-type"]%>" <%}%>  data-infpage="<%=d["data-infpage"]%>" >\
						<img src="<%=blankPic%>" data-special data-src="<%=d.img%>">\
						<div><span><%=d.title%></span></div>\
					</a>\
				</div>\
			<%}%>\
		<%}%>',
		recItem: //推荐栏目
		'<div class="channel-list ">\
			<%if(data.length&&data.length>=4){%>\
				<ul>\
				<%for(var i=0, len=data.length; i<len; i++){%>\
					<%if(i>=4){%>\
					<%}else{%>\
						<li>\
							<a <%if(version!="v1"){%> data-type="<%=data[i]["data-type"]%>" <%}%> data-infpage="<%=data[i]["data-infpage"]%>">\
								<div class="img-box">\
									<img src="<%=blankPic1%>" data-src="<%=data[i].icon%>" alt="">\
								</div>\
								<div class="layer"><span><%=data[i].title%></span></div>\
							</a>\
						</li>\
					<%}}%>\
				</ul>\
			<%}%>\
		</div>',
		activity://活动
		'<%if(data&&data["data-infpage"]){%>\
		<%if(data["data-type"]!=4){%>\
			<div class="activity-list ">\
				<a data-type="<%=data["data-type"]%>" data-infpage="<%=data["data-infpage"]%>">\
				<div>\
				<span><%=data.title%></span></div>\
				</a>\
			</div>\
		<%}%>\
		<%}%>',
		recommendlist://首页推荐游戏
		'<div class="home-item-list">\
			<%if(data.list.length){%>\
				<ul>\
					<%for(var i=0, len=data.list.length; i<len; i++){%>\
						<li>\
					<a <%if(version!="v1"){%> data-type="<%=data.list[i]["data-type"]%>"  <%}%> data-infpage="<%=data.list[i]["data-infpage"]%>">\
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
						<span class="icon">\
						<img src="<%=blankPic%>" data-src="<%=data.list[i]["img"]%>" alt="">\
						</span>\
						<span class="desc">\
						<em><%=data.list[i].name%></em>\
						<p><%if(data.list[i].category){%>\
							<em><%=data.list[i].category%></em><em>|</em>\
						<%}else{}%>\
						<em><%=data.list[i].size%></em>\
						<%if(version!="v1"){%>\
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
		</div>',
		category://分类列表页
		'<div class="category-list">\
			<%if(data.list.length){%>\
				<ul>\
				<%for(var i=0,j=data.list.length;i<j;i++){%>\
					<li>\
						<a data-id="<%=data.list[i]["id"]%>" <%if(version!="v1"){%> data-type="0" data-viewType="<%=data.list[i]["viewType"]%>" <%}%> data-infpage="<%=data.list[i]["data-infpage"]%>" >\
							<span class="pic"><img src="<%=blankPic%>" data-src="<%=data.list[i]["img"]%>" alt=""></span>\
							<span class="desc"><b><%=data.list[i]["title"]%></b><em><%=data.list[i]["game_title"]%></em></span>\
						</a>\
					</li>\
				<%}%>\
				</ul>\
			<%}%>\
		</div>',
		subject://专题列表页
		'<%if(data.list.length){%>\
			<div class="subject-list">\
				<ul>\
				<%for(var i=0,j=data.list.length;i<j;i++){%>\
					<li>\
						<a data-id="<%=data.list[i]["id"]%>" <%if(version!="v1"){%> data-type="0" data-viewType="<%=data.list[i]["viewType"]%>" <%}%> data-infpage="<%=data.list[i]["data-infpage"]%>" >\
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
		<%}%>',
		newItem://最新游戏列表
		'<%if(data.list.length){%>\
			<%for(var i=0, ilen=data.list.length; i<ilen; i++){%>\
				<%var d=document.querySelectorAll("h3.time-title");if(data.curpage==1||!(i==0&&d.length&&d[d.length-1].getAttribute("data-date")==data.list[0].date)){%>\
				<h3 class="time-title" data-date="<%=data.list[i].date%>">\
					<%var d=new Date(data.list[i].date);%>\
					<%=d.getMonth()+1%>月<%=d.getDate()%>日\
					</h3>\
				<%}else{%>\
				<%}%>\
				<ul>\
				<%var items=data.list[i].list; while(items.length){%>\
					<li class="<%=document.getElementsByClassName("timeline")%>" >\
						<a <%if(version!="v1"){%> data-type="<%=items[0]["data-type"]%>"  <%}%> data-infpage="<%=items[0]["data-Info"]%>" >\
							<span class="icon"><img data-src="<%=items[0]["img"]%>" src="<%=blankPic%>" alt=""></span>\
							<span class="desc">\
								<em><%=items[0]["name"]%></em>\
								<p><em>大小：<%=items[0]["size"]%>M</em>\
								<%if(version!="v1"){%>\
								<%if(items[0].attach!=""||items[0].device!=0){%>\
									<em class="tips">\
									<%var tipArr=items[0].attach.split(",");while(tipArr.length){%>\
										<%if(tipArr[0]=="礼"){%>\
											<span class="gift"></span>\
										<%}if(tipArr[0]=="评"){%>\
											<span class="comment"></span>\
											<%}tipArr.shift();}%>\
											<%if(items[0].device==1){%>\
												<span class="grip"></span>\
											<%}%>\
									</em>\
								<%}}%>\
								</p>\
								<b><%=items[0]["resume"]%></b>\
							</span>\
						</a>\
					</li>\
				<%items.shift();}%>\
				</ul>\
			<%}%>\
		<%}%>',
		commonItem://通用游戏列表
		'<%if(data.list.length){%>\
			<div class="item-list">\
				<ul>\
					<%if(data.list[0].list&&data.list[0].list.length){%>\
						<%for(var i=0, ilen=data.list.length; i<ilen; i++){%>\
							<%var items=data.list[i].list; while(items.length){%>\
								<li class="<%=document.getElementsByClassName("timeline")%>" >\
									<a <%if(version!="v1"){%> data-type="<%=items[0]["data-type"]%>"  <%}%> data-infpage="<%=items[0]["data-Info"]%>" >\
										<span class="icon"><img data-src="<%=items[0]["img"]%>" src="<%=blankPic%>" alt=""></span>\
										<span class="desc">\
											<em><%=items[0]["name"]%></em>\
											<p><em>大小：<%=items[0]["size"]%>M</em>\
											<%if(version!="v1"){%>\
											<%if(items[0].attach!=""||items[0].device!=0){%>\
												<em class="tips">\
												<%var tipArr=items[0].attach.split(",");while(tipArr.length){%>\
													<%if(tipArr[0]=="礼"){%>\
														<span class="gift"></span>\
													<%}if(tipArr[0]=="评"){%>\
														<span class="comment"></span>\
														<%}tipArr.shift();}%>\
														<%if(items[0].device==1){%>\
															<span class="grip"></span>\
														<%}%>\
												</em>\
											<%}}%>\
											</p>\
											<b><%=items[0]["resume"]%></b>\
										</span>\
									</a>\
								</li>\
							<%items.shift();}%>\
						<%}%>\
					<%}else{%>\
						<%for(var i=0, len=data.list.length; i<len; i++){%>\
							<li>\
								<a <%if(version!="v1"){%> data-type="<%=data.list[i]["data-type"]%>"  <%}%> data-infpage="<%=data.list[i]["data-Info"]%>" >\
									<span class="icon"><img data-src="<%=data.list[i]["img"]%>" src="<%=blankPic%>" alt=""></span>\
									<span class="desc">\
										<em><%=data.list[i]["name"]%></em>\
										<p><em>大小：<%=data.list[i]["size"]%>M</em>\
										<%if(version!="v1"){%>\
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
										<b><%=data.list[i]["resume"]%></b>\
									</span>\
								</a>\
							</li>\
						<%}%>\
					<%}%>\
				</ul>\
			</div>\
		<%}%>',
		eventSection://活动列表
		'<%if(data.list){%>\
			<div class="eventSection" >\
				<ul class="event-list">\
				<%for(var i=0,d=data.list,len=d.length;i<len;i++){%>\
					<li>\
						<i <%if(d[i]["status"]==0){%> class="icon-ac-over" <%} else{%> class="icon-ac-ongoing" <%}%> ></i>\
						<a data-type="0" data-viewType="<%=d[i]["viewType"]%>" data-gameId=<%=d[i]["game_id"]%>  data-id="<%=d[i]["id"]%>" data-infpage="<%=d[i]["title"]%>,<%=d[i]["url"]%>" >\
							<img data-src="<%=d[i]["actImageUrl"]%>" src="<%=blankPic%>" alt="">\
						</a>\
						<div class="info">\
							<span class="title"><%=d[i]["title"]%></span>\
							<span class="date"><%=d[i]["activityTime"]%></span>\
							<span class="desc1"><%=d[i]["content"].substr(0,40)%><%if(d[i]["content"].length>40){%>\
							...\
							<%}else{}%></span>\
						</div>\
					</li>\
				<%}%>\
				</ul>\
			</div>\
		<%}%>',
		gift://礼包列表
		'<%if(data.list.length){%>\
			<div <%if(branch=="v3_1"){%> class="gift-list-v2" <%} else{%> class="gift-list" <%}%>>\
				<ul>\
				<%for(var i=0,j=data.list.length;i<j;i++){%>\
					<li >\
						<a  <%if(data.list[i]["isGrab"]=="true"){%> class="grab" <%} else if(data.list[i]["leftNum"]==0){%> class="over" <%}%> data-type="<%=data.list[i]["data-type"]%>" data-gameid="<%=data.list[i]["game_id"]%>" data-id="<%=data.list[i]["id"]%>" data-viewtype="<%=data.list[i]["viewType"]%>" data-infpage="<%=data.list[i]["data-infpage"]%>" >\
							<span class="icon"><img data-src="<%=data.list[i]["img"]%>" src="<%=blankPic%>" alt=""></span>\
							<span class="desc">\
								<em class="name"><%=data.list[i]["title"]%></em>\
								<%if(branch=="v3_1"){%>\
									<%if(data.list[i]["leftNum"]!=0){%>\
										<div class="bars">\
											<div style="width:<%=data.list[i]["leftNum"]/data.list[i]["totalNum"]*100%>%" >\
												<span></span>\
											</div>\
										</div>\
										<em class="left number">剩余:<%=data.list[i]["leftNum"]%>个</em>\
									<%} else{%>\
										<em class="left">大侠，礼包已经被抢完了</em> \
									<%}%>\
								<%} else{%>\
									<%if(data.list[i]["isGrab"]=="true"){%>\
										<em class="left">已抢到</em>\
									<%} else{%>\
										<%if(data.list[i]["leftNum"]==0){%>\
											<em class="left">大侠，礼包已经被抢完了</em> \
										<%}else{%>\
											<em class="left number">剩余:<%=data.list[i]["leftNum"]%>个</em>\
										<%}%>\
									<%}%>\
								<%}%>\
							</span>\
						</a>\
					</li>\
				<%}%>\
				</ul>\
			</div>\
		<%}%>',
	};
	//view
	var bi = typeof t_bi != 'undefined'? ('t_bi=' + t_bi) : '',
		i = 0;
	GameApk.view = iCat.View.extend({
		tmplData:{
			blankPic:iCat.PathConfig.picPath+'blank.gif',
			blankPic1:iCat.PathConfig.picPath+'blank1.gif',
			version:GameApk.ApiVersion
		}

	});
	GameApk.view.setting = {
		'slidePic':{//首页 大图
				config:{
					tmplId:'slidePic',
					wrap:'.module',
					ajaxUrl:GameApk.ApiVersion!='v1'?'/api/indexi/turn':'/api/index/turn',
				}
		  	},
		'recItem':{
				config:{
					tmplId:'recItem',
					imgSelector:'img[src$="blank1.gif"]',
					ajaxUrl:GameApk.ApiVersion!='v1'?'/api/indexi/channel':'/api/index/channel',
					wrap: '.module'
				}
		},
		'activity':{
				config:{
					tmplId:'activity',
					imgSelector:'img[src$="blank1.gif"]',
					ajaxUrl:'/api/indexi/announce',
					wrap:'.module'
				}
		},
		'recommend':{
			config:{
				tmplId:'recommendlist',
				wrap: '.module',
				ajaxUrl:GameApk.ApiVersion!='v1'?'/api/indexi/recommend':'/api/index/recommend',
				clear:false,
				callback:function(warp,cfg,data){
					GameApk.view.setting.ajaxSucCallback(warp,cfg,data);
				},
				events:[{
					selector:window,type:'scroll',callback:function(el,evt){
						GameApk.view.setting.eventCallback(el,evt,this);
					}
				}]
			},
		},
		'category':{//分类
			config:{
				wrap:'.module',
				tmplId:'category',
				ajaxUrl:'/api/category/index?sp='+GameApk.spParam,
				clear:false,
				callback:function(warp,cfg,data){
					GameApk.view.setting.ajaxSucCallback(warp,cfg,data);
				},
				events:[{
					selector:window,type:'scroll',callback:function(el,evt){
						GameApk.view.setting.eventCallback(el,evt,this);
					}
				}]
			}
		},
		'subject':{//专题
			config:{
				wrap:'.module',
				tmplId:'subject',
				ajaxUrl:'/api/subject/index?sp='+GameApk.spParam,
				clear:false,
				callback:function(warp,cfg,data){
					GameApk.view.setting.ajaxSucCallback(warp,cfg,data);
				},
				events:[{
					selector:window,type:'scroll',callback:function(el,evt){
						GameApk.view.setting.eventCallback(el,evt,this);
					}
				}]
			}
		},
		'newItem':{
			config:{
				wrap:'.module',
				tmplId:'newItem',
				ajaxUrl:'/api/local_rank/webindex'+location.search+"&flag=1",
				clear:false,
				callback:function(warp,cfg,data){
					GameApk.view.setting.ajaxSucCallback(warp,cfg,data);
				},
				events:[{
					selector:window,type:'scroll',callback:function(el,evt){
						GameApk.view.setting.eventCallback(el,evt,this);
					}
				}]
			}
		},
		'commonItem':{
			config:{
				wrap:'.module',
				tmplId:'commonItem',
				ajaxUrl:'/api/local_rank/webindex'+location.search,
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
		'eventSection':{
			config:{
				wrap:'.module',
				tmplId:'eventSection',
				ajaxUrl:'/api/activity/index'+location.search,
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
			var J_loadMore=document.getElementsByClassName('J_loadMore');
			if(!data.data||!data.data.list){
				if(J_loadMore.length){
					J_loadMore[0].style.display='none';
				}
				return;
			} 
			if(data.data&&data.data.list){
				_cfg_.hasnext=data.data.hasnext;
				_cfg_.curIndex=data.data.curpage+1;
			}

			if(_cfg_.hasnext){
				if(J_loadMore.length){
					J_loadMore[0].style.display='none';
				}
			}
			else{
				if(J_loadMore.length){
					J_loadMore[0].children[0].className='bottom';
					J_loadMore[0].children[0].innerHTML='到底了，去其它页面看看吧';
				}
			}
		},
		eventCallback:function(el,evt,that,type){
			var scrollTop = $(window).scrollTop();
		　　var scrollHeight = $(document).height();
		　　var windowHeight = $(window).height();
			if(_cfg_.hasnext){
				if(scrollTop + windowHeight+5>= scrollHeight){
					var url=that.config.ajaxUrl.replace(/[\?\&]page=\d+/, '');
					url=url + (url.indexOf('?')<0? '?' : '&') + 'page=' + (_cfg_.curIndex);
					that.setConfig({
						ajaxUrl:url,
						subkey:_cfg_.curIndex
					});
					if(that.config.__hasnext){
						if(!$("body").find('.J_loadMore').length){
							$("body").append('<div class="loading J_loadMore"><span class="load"></span></div>')
						}
						$('.J_loadMore').show();
					}
				}
			}
		}
	};
})(ICAT);