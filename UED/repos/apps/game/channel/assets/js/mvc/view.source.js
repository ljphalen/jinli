(function(iCat, root){
	var _cfg_ = {
		'recommend':{
			curIndex:2,
			ajaxUrl: ''
		}
	};
	//template
	GameApk.template =
	{
		slidePic: //首页图片轮播
		'<div><section class="wrap first-grade">\
			<div class="slide-pic ">\
			<%if(data.length){%>\
				<div class="slideWrap">\
					<div class="pic">\
					<%for(var i=0, len=data.length; i<len; i++){%>\
						<a data-infpage="<%=data[i]["data-infpage"]%>" >\
							<img src="<%=blankPic%>" data-src="<%=data[i].img%>">\
						</a>\
						<%}%>\
					</div>\
				</div>\
				<%}%>\
			</div>\
		</section>\
		</div>',
		recItem: //推荐栏目
		'<div><section class="wrap first-grade">\
			<div class="channel-list">\
			<%if(data.length&&data.length>=4){%>\
				<ul>\
				<%for(var i=0, len=data.length; i<len; i++){%>\
					<%if(i>=4){%>\
					<%}else{%>\
						<li>\
							<a data-infpage="<%=data[i]["data-infpage"]%>">\
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
		recommendlist://推荐游戏
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
					<a data-infpage="<%=data.list[i]["data-infpage"]%>">\
						<span class="icon">\
						<img src="<%=blankPic%>" data-src="<%=data.list[i].img%>" alt="">\
						</span>\
						<span class="desc">\
						<em><%=data.list[i].name%></em>\
						<p><em><%=data.list[i].category%></em><em>|</em><em><%=data.list[i].size%></em></p>\
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
		config: {
			// isSave: true,
			// oneChild:false,
			// repeatOverwrite:true
			multiChild: true,
			dataSave: true
		},
		blankPic: iCat.PathConfig.appRef.replace(/assets\/js\//g, 'pic/blank.gif')
	});

	GameApk.view.setting = {
			'slidePic':{//首页 图片轮播
					config:{
						tempId:'slidePic',
						wrap:'#container',
						ajaxUrl:'/api/Channel_Index/turn',
						hooks:{
							'&>1':'.J_slidePic'
						}
					}
			  	},
			'recItem':{
					config:{
						tempId:'recItem',
						ajaxUrl: '/api/Channel_Index/channel',
						wrap: '#container',
						hooks:{
							'&>2':'.J_rotate'
						},
						callback:function(){
							GameApk.view.setting.transition();
						},
					}
			},
			transition:function(){ //首页图片旋转
				var rotateWrap=$(".J_rotate");
				if(!rotateWrap[0]) return this;
				// iCat.include(['./plugin/transitions.js'],function(){
					// var tranArr=new Array(4);
					// for(var i=0;i<4;i++){
					// 	tranArr[i]=new Transition();
					// }
					// tranArr[0].init($(rotateWrap.find('.panel')[0]))
					// tranArr[2].init($(rotateWrap.find('.panel')[2]))
					// setTimeout(function(){
					// tranArr[1].init($(rotateWrap.find('.panel')[1]))
					// tranArr[3].init($(rotateWrap.find('.panel')[3]))
				// 	},4000)
				// },true);
			},
			'recommend':{
				config:{
					tempId:'recommendlist',
					ajaxUrl: 'api/Channel_Index/recommend',
					wrap: '#container',
					callback:function(parent,config,initData){
						GameApk.view.setting.loadingData(parent,config,initData);
					}
				},
			},
			loadingData:function(parent,config,initData){
				var ajaxUrl=config.ajaxUrl;
				_cfg_.recommend.ajaxUrl=ajaxUrl;
				var tempHasnext;
				_cfg_.recommend.split=_cfg_.recommend.ajaxUrl.indexOf('?')>-1? '&page=':'?page=';
				if(_cfg_.recommend.hasnext===undefined){
					_cfg_.recommend.hasnext=initData.data.hasnext;
					tempHasnext=initData.data.hasnext;
					if(_cfg_.recommend.hasnext){
						$(parent).find('.J_loadMore').remove();
						var html='<div class="load-more J_loadMore"></div>';
						$('#container').append(html);
						$(window).scroll(function(evt){
							var boxHeight=document.body.clientHeight,
								visibleHeight=document.documentElement.clientHeight,
								boxScrollTop=document.body.scrollTop;
							if(Math.abs(boxHeight-visibleHeight)<=boxScrollTop+50){
								var btnMore=$(".J_loadMore");
								if(_cfg_.recommend.hasnext==true){
									btnMore.html('<img src="'+GameApk.loadingPic+'" />');
								}
								if(_cfg_.recommend.hasnext===true){
									_cfg_.recommend.hasnext=false;
									iCat.util.ajax({
										url:_cfg_.recommend.ajaxUrl+_cfg_.recommend.split+_cfg_.recommend.curIndex,
										success:function(data){
											if(data.success){
												data.blankPic = iCat.PathConfig.appRef.replace(/assets\/js\//g, 'pic/blank.gif');
												config.dataSave=false;
												setTimeout(function(){
													iCat.View[config.viewId].setData(data,false,false);
													evt.preventDefault();
													if(data.data.hasnext!=true){
														$('#container').find('.J_loadMore').html('<span class="bottom">到底了，去其它页面看看吧</span>');
														$(window).unbind("scroll");
													}
													_cfg_.recommend.curIndex=data.data.curpage+1;
													_cfg_.recommend.hasnext=data.data.hasnext;
													tempHasnext=data.data.hasnext;
												},1000);
											}
										},
										error:function(){
											_cfg_.recommend.hasnext=tempHasnext;
										}
									});
								}
							}
						})
					}
				}
			}
			// loadingData:function(parent,config,initData){
			// 		var ajaxUrl=config.ajaxUrl;
			// 		_cfg_.recommend.ajaxUrl=ajaxUrl;
			// 		var tempHasnext;
			// 		_cfg_.recommend.split=_cfg_.recommend.ajaxUrl.indexOf('?')>-1? '&page=':'?page=';
			// 		if(_cfg_.recommend.hasnext===undefined){
			// 			_cfg_.recommend.hasnext=initData.data.hasnext;
			// 			tempHasnext=initData.data.hasnext;
			// 			if(_cfg_.recommend.hasnext){
			// 				$(parent).find('.J_loadMore').remove();
			// 				var html='<div class="load-more J_loadMore"><span>加载更多</span></div>';
			// 				$(parent).append(html);
			// 				$(".J_loadMore").click(function(){
			// 					var btnMore = $(this);
			// 					btnMore.html('<img src="'+GameApk.loadingPic+'" />');
			// 					if(_cfg_.recommend.hasnext===true){
			// 						_cfg_.recommend.hasnext=false;
			// 						iCat.util.ajax({
			// 							url:_cfg_.recommend.ajaxUrl+_cfg_.recommend.split+_cfg_.recommend.curIndex,
			// 							success:function(data){
			// 								if(data.success){
			// 									data.blankPic = iCat.PathConfig.appRef.replace(/assets\/js\//g, 'pic/blank.gif');
			// 									config.dataSave=false;
			// 									iCat.View[config.viewId].setData(data,false,false);
			// 									btnMore.html('<span>加载更多</span>');
			// 									if(data.data.hasnext!=true){
			// 										$(parent).find('.J_loadMore').remove();
			// 									}
			// 									_cfg_.recommend.curIndex=data.data.curpage+1;
			// 									_cfg_.recommend.hasnext=data.data.hasnext;
			// 									tempHasnext=data.data.hasnext;
			// 								}
			// 							},
			// 							error:function(){
			// 								_cfg_.recommend.hasnext=tempHasnext;
			// 							}
			// 						});
			// 					}
			// 				});
			// 			}
			// 			else{
			// 				$(".J_loadMore").css('display','none');
			// 			}
			// 		}
			// }
	};
})(ICAT, window);