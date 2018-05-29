;(function(iCat,$){
	iCat.app('Gapk', function(){
		iCat.PathConfig();
		function skipPage(data, type){
			if(!data || data.indexOf(',')==-1) return;
			var infpage = data.split(','),
				len = infpage.length;
			if(navigator.gamehall){
				if(GameApk.ApiVersion=='v1'){
					len==2?
						navigator.gamehall.startlistactivity(
							function(){},function(){},
							{title:infpage[0],url:infpage[1],newArgs:{title:infpage[0],url:infpage[1]}}
						):
						navigator.gamehall.startdetailsactivity(
							function(){},function(){},
							{
								title:infpage[0], url:infpage[1], gameid:infpage[2], downurl:infpage[3], packagename:infpage[4], filesize:infpage[5], sdkinfo:infpage[6], resolution:infpage[7],
								newArgs:{
									title:infpage[0],
									url:infpage[1],
									downloadInfo:[{
										gameId:infpage[2],
										downUrl:infpage[3],
										packageName:infpage[4],
										fileSize:infpage[5],
										sdkVersion:infpage[6],
										resolution:infpage[7]
									}]
								}
							}
						);
				}
				else{ 
					if(type==0){
						if(len==2){
							navigator.gamehall.startlistactivity(
								function(){},function(){},
								{title:"",url:"",newArgs:{title:infpage[0],url:infpage[1]}}
							);
						}
						else{
							var items=[];
							for(var i=0;i<len;i+=2){
								var obj={};
								if(i==0){
									obj.title='热门';
								}
								else{
									obj.title=infpage[i];
								}
								obj.viewType='Webview';
								obj.url=infpage[i+1];
								items.push(obj);
							}
							navigator.gamehall.startlistactivity(
								function(){},function(){},
								{
									title:"",url:"",
									newArgs:{
										title:infpage[0],
										items:items
									}
								}
							);
						}		
					}
					else if(type==1){
						if(len==3){
							navigator.gamehall.startdetailsactivity(
								function(){},function(){},
								{
									title:"", url:"", gameid:"", downurl:"", packagename:"", filesize:"", sdkinfo:"", resolution:"",
									newArgs:{
										title:infpage[0],
										url:infpage[1],
										gameId:infpage[2]
									}
								}
							);
						}
						else if(len==2){
							navigator.gamehall.startdetailsactivity(
								function(){},function(){},
								{
									title:"", url:"", gameid:"", downurl:"", packagename:"", filesize:"", sdkinfo:"", resolution:"",
									newArgs:{
										title:infpage[0],
										url:infpage[1]
									}
								}
							);
						}
						else{
							navigator.gamehall.startdetailsactivity(
								function(){},function(){},
								{
									title:"", url:"", gameid:"", downurl:"", packagename:"", filesize:"", sdkinfo:"", resolution:"",
									newArgs:{
										title:infpage[0],
										url:infpage[1],
										gameId:infpage[2]
									}
								}
							);
						}
					}
					else if(type==2){
						navigator.gamehall.startdetailsactivity(
							function(){},function(){},
							{
								title:'', url:'', gameid:'', downurl:'', packagename:'', filesize:'', sdkinfo:'', resolution:'',
								newArgs:{
									title:'礼包详情',
									url:infpage[1],
									viewType:'GiftDetailView',
									gameId:infpage[2],
									giftId:infpage[3]
								}
							}
						);
					}
					else{
						if(len==2){
							navigator.gamehall.startlistactivity(
								function(){},function(){},
								{	
									title:infpage[0],
									url:infpage[0]
								}
							);
						}
					}
				}
			}
			else{
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
				var panel=$(".pic-wrap");
				if(!slideWrap[0]) return this;
				// if(panel.children('span').length>2){
				// 	$(".next").show();
				// }
				iCat.include(['touchSwipe','./plugin/slidePic.js'], function(){
					slideWrap.slidePic(
						slideWrap.children().children().hasClass('J_arrow')? {
							slidePanel: '.pic-wrap',
							slideItem: '.pic-wrap span',
							specialWidth: true,
							isTouch:false,
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
						data_type="";
						if(GameApk.ApiVersion!="v1"){
							data_type='data-type="{data-type}"';
						}
							strTemp = '<li>\
										<a '+data_type+' data-infpage="{profile}">\
											<span class="icon"><img src="'+GameApk.blankPic+'" data-src="{img}"></span>\
											<span class="desc">\
												<em>{name}</em>\
												<p><em>大小：{size}</em><em class="tips1"></em></p>\
												<b>{resume}</b>\
											</span>\
										</a>\
									</li>',
							
							/* template-data merge */
							tdMerge = function(t,d,r){
								if(!iCat.isString(t) || !/\{|\}/g.test(t)) return false;
								var phs = t.match(/(\{[a-zA-Z]+-[a-zA-Z]+\})|(\{[a-zA-Z]+[a-zA-Z]+\})/g);//fixed bug 判断{字符-字符}
								if(!phs.length) return false;
								iCat.foreach(phs, function(){
									var key = this.replace(/\{|\}/g,''),
										regKey = new RegExp('\{'+key+'\}'),
										val = d[key];
									t = t.replace(regKey, val!==undefined? val:(r? '{'+key+'}':''));
								});
								//游戏列表上的礼包和评测信息
								if(GameApk.ApiVersion!="v1"&&((d.attach&&d.attach!="")||(d.device&&d.device!=0))){
									var tempStr='<em class="tips1">';
									if(d.attach){
										var attachs=d.attach.split(',');
										for(var i=0;i<attachs.length;i++){
											if(attachs[i]=='礼'){
												tempStr+='<span class="gift"></span>';
											}
											if(attachs[i]=='评'){
												tempStr+='<span class="comment"></span>';
											}
										}
										if(d.device==1){
											tempStr+='<span class="grip"></span>';
										}
									}
									else if(d.device==1){
										tempStr+='<span class="grip"></span>';
									}
									tempStr+="</em>";
									t=t.replace('<em class="tips1"></em>',tempStr);
								}
								return t;
							};
						if($('.J_categoryItem')[0]){
							pNode = $('.first-grade ul');
							strTemp = '<li>\
										<a '+data_type+' data-infpage="{profile}">\
											<span class="pic"><img src="'+GameApk.blankPic+'" data-src="{img}"></span>\
										</a>\
									</li>';
						}
						else if($('.J_subjectItem')[0]){
							pNode = $('.first-grade ul');
							strTemp = '<li>\
										<a '+data_type+' data-infpage="{profile}">\
											<span class="pic"><img src="'+GameApk.blankPic+'" data-src="{img}"></span>\
											<span class="desc">{title}</span>\
											<span class="mask"></span>\
										</a>\
									</li>';
						}
						else if($('.J_giftItem')[0]){
							pNode=$('.J_giftItem ul');
							strTemp='<li>\
										<a '+data_type+' data-infpage="{data-infpage}">\
											<span class="icon"><img src="'+GameApk.blankPic+'" data-src="{img}"></span>\
											<span class="desc">\
												<em>{title}</em>\
												<p><em >{giftNum}</em></p>\
											</span>\
										</a>\
									</li>';
						}
						iCat.foreach(data.data.list, function(v, i){
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
					if(GameApk.ApiVersion=="v1"){
						skipPage(data);
					} else{
						skipPage(data,parseInt($(this).attr('data-type'),10));
					}	
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
					t ? setTimeout(function(){ _fn(i); }, v*t) : _fn(v);
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
