;(function(iCat,$){
	iCat.app('Gapk', function(){
		iCat.PathConfig();
		function skipPage(data, selfPage){
			if(!data || data.indexOf(',')==-1) return;
			var infpage = data.split(','),
				len = infpage.length;
			if(navigator.gamehall){
				len==2 ?
					navigator.gamehall.startlistactivity(
						function(){},function(){},
						{title:infpage[0], url:infpage[1]}
					)
				:
					navigator.gamehall.startdetailsactivity(
						function(){},function(){},
						{title:infpage[0], url:infpage[1], gameid:infpage[2], downurl:infpage[3], packagename:infpage[4], filesize:infpage[5], sdkinfo:infpage[6], resolution:infpage[7]}
					);
			} else {
				location.href = infpage[1];
			}
		}

		var o = {
			openMore: function(){
				$('.wrap').delegate('.J_openBtnWrap', 'click', function(){
					$(this).toggleClass('open')
						.siblings('p').toggleClass('hidden');
				});
				return this;
			},

			slidePic: function(){
				var slideWrap = $('#J_screenshot');
				if(!slideWrap[0]) return this;
				iCat.include(['lib/jquery/touchSwipe.js','./plugin/slidePic.js'], function(){
					slideWrap.slidePic(
						slideWrap.hasClass('J_slidePic')? {
							circle:true,
							auto:true,
							isTouch: false,
						} : {
							slidePanel: '.pic-wrap',
							slideItem: '.pic-wrap span',
							specialWidth: true,
						}
					);
									
				o.displayImg();
				},true);
				return this;
			},

			loadMore: function(){
				$('#page,#list-page').delegate('.J_loadMore', 'click', function(){
					var btnMore = $(this),
						hn = btnMore.attr('data-hasnext');
					if(hn==0 || hn=='false') return false;
					
					var curpage = parseInt(btnMore.attr('data-curpage'));
					btnMore.html('<img src="'+GameApk.loadingPic+'" />');
					$.post(btnMore.attr('data-ajaxUrl'), {page:curpage+1, token:token}, function(data){
						var pNode = $('.J_gameItem ul'), s = '',

							strTemp = '<li>\
										<a data-infpage="{profile}">\
											<span class="icon"><img src="'+GameApk.blankPic+'" data-src="{img}"></span>\
											<span class="desc">\
												<em>{name}</em>\
												<p><em>大小：{size}</em></p>\
												<b>{resume}</b>\
											</span>\
										</a>\
									</li>',
							
							/* template-data merge */
							tdMerge = function(t,d,r){//r(eserve)表示是否保留
								if(!iCat.isString(t) || !/\{|\}/g.test(t)) return false;
								
								var phs = t.match(/\{\w+\}/g);
								if(!phs.length) return false;
								
								iCat.foreach(phs, function(){
									var key = this.replace(/\{|\}/g,''),
										regKey = new RegExp('\{'+key+'\}'),
										val = d[key];
									
									t = t.replace(regKey, val? val:(r? '{'+key+'}':''));
								});
								return t;
							};
							//游戏分类
						if($('.J_categoryItem')[0]){
							pNode = $('.first-grade ul');
							strTemp = '<li>\
										<a data-infpage="{profile}">\
											<span class="pic"><img src="'+GameApk.blankPic+'" data-src="{img}"></span>\
										</a>\
									</li>';
						}
						//游戏主题
						else if($('.J_subjectItem')[0]){
							pNode = $('.first-grade ul');
							strTemp = '<li>\
										<a data-infpage="{profile}">\
											<span class="pic"><img src="'+GameApk.blankPic+'" data-src="{img}"></span>\
											<span class="desc">{title}</span>\
											<span class="mask"></span>\
										</a>\
									</li>';
						}
						iCat.foreach(data.data.list, function(i, v){
							s += tdMerge(strTemp, v);
						});
						
						pNode.append(s);

						o.lazyLoad(document.body, 100);

						if(data.data.hasnext==0 || data.data.hasnext=='false'){
							btnMore.hide();
						}
						btnMore.attr('data-hasnext', data.data.hasnext)
							.attr('data-curpage', data.data.curpage)
							.html('<span>加载更多</span>');
					}, 'json');
				});
				return this;
			},

			openPage: function(argument){
				$('body').delegate('a[data-infpage]', 'click', function(){
					var data = $(this).attr('data-infpage') || '';
					skipPage(data);
				});
			},
			displayImg:function(){
				var screenshot=$("#J_screenshot"),
					imgs=screenshot.find('img');
				if(!screenshot[0]) return this;
				var srcArr=[];
				imgs.each(function(index){
					srcArr[index]=$(this).attr('data-bigpic');
					$(this).attr('data-index',index);
				});
				imgs.swipe({
					click:function(){
						var currentIndex=$(this).attr('data-index');
						var data=[];
					 	data=data.concat(currentIndex,srcArr);
						if(navigator.gamehall){
							navigator.gamehall.startimagescanactivity(
								function(){
									iCat.log('正在为您跳转页面');
								},
								function(){},
								{url:data.join('|')}
							);
						} 
					}
				})
			},

			lazyLoad: function(pNode, t){
				var imgs = pNode.querySelectorAll('img[src$="blank.gif"]'),
					_fn = function(o){
						var src = o.getAttribute('data-src');
						iCat.__IMAGE_CACHE = iCat.__IMAGE_CACHE || {};
						if(!src) return;

						if(!iCat.__IMAGE_CACHE[src]){
							var oImg = new Image(); oImg.src = src;
							oImg.onload = function(){
								o.src = src;
								iCat.__IMAGE_CACHE[src] = true;
								oImg = null;
							};
						} else {
							o.src = src;
						}
					};
				
				iCat.foreach(imgs, function(i,v){
					t ? setTimeout(function(){ _fn(v); }, i*t) : _fn(v);
				});
			},	
		};
		return {
			init: function(){
				o.slidePic();
				o.openMore().loadMore();
				o.openPage();
				o.lazyLoad(document.body,100);
			}
		};
	});
})(ICAT,jQuery);
