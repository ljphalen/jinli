(function(iCat){
	iCat.PathConfig();

	iCat.app('Game',function(){
		var _action = {
			
			slidePic: function(){
				var slideWrap = $('.J_slidePic');
				if(!slideWrap[0]) return;
				iCat.include(['lib/zepto/touchSwipe.js', './slidePic.js'], function(){
					slideWrap.slidePic({circle:true, auto:true});
				}, true);
			},

			screenshot: function(){
				var scrollWrap = $('#J_screenshot');
				if(!scrollWrap[0]) return;
				iCat.include(['lib/zepto/touchSwipe.js', './slidePic.js'], function(){
					scrollWrap.slidePic({
						slidePanel: '.pic-wrap',
						slideItem: '.pic-wrap span'
					});
				}, true);
			},

			openClose: function(){
				var btnWrap = $('.J_openBtnWrap');
				if(!btnWrap[0]) return;

				btnWrap.find('span').click(function(){
					this.innerHTML = this.innerHTML=='展开'? '收起':'展开';
					btnWrap.parent('div').find('[data-text]').toggleClass('hidden');
				});
			},

			loadMore: function(){
				var btnMore = $('.J_loadMore[data-ajaxUrl]');
				if(!btnMore[0]) return;
				
				btnMore.on('click', function(){
					var hn = btnMore.attr('data-hasnext');
					if(hn==0 || hn=='false') return false;
					
					var curpage = parseInt(btnMore.attr('data-curpage'));
					btnMore.html('<img src="'+Game.loadingPic+'" />');
					$.post(btnMore.attr('data-ajaxUrl'), {page:curpage+1, token:token}, function(data){
						var pNode = $('.J_gameItem ul, .J_topicItem ul,.J_resultItem ul,.J_classItem ul,.J_evaItem ul,.J_centerItem ul'), s = '', 
						isTopic = !!document.querySelector('.J_topicItem'),
						isSearch=!!document.querySelector('.J_resultItem'),
						isClass=!!document.querySelector('.J_classItem'),
						isEval=!!document.querySelector('.J_evaItem'),
						isNewCenter=!!document.querySelector('.J_centerItem')
						if(isTopic){
							strTemp='<li>\
										<a href="{link}">\
											<div class="pic"><img data-src="{img}" src="'+Game.blankPic+'"></div>\
											<div class="desc">{title}</div>\
											<div class="mask"></div>\
										</a>\
									</li>';
						}
						else if(isClass){
							strTemp='<li>\
										<a href="{link}">\
											<div class="pic"><img data-src="{img}" src="'+Game.blankPic+'"></div>\
										</a>\
									</li>';
						}
						else if(isSearch){
							strTemp='<li>\
										<a class="detail" href="{alink}">\
											<div class="pic"><img  alt="" data-src="{img}" src="'+Game.blankPic+'"></div>\
											<div class="desc">\
												<h3>{name}</h3>\
												<p>{resume}</p>\
											</div>\
										</a>\
										<div class="download"><a class="btn" href="{link}">安装</a></div>\
									</li>';
						}
						else if(isEval){
							strTemp='<li>\
										<a class="detail" href="{link}">\
											<div class="photo"><img  alt="" data-src="{img}" src="'+Game.blankPic+'"></div>\
											<div class="desc">\
												<h3>{title}</h3>\
												<p>\
													{resume}\
												</p>\
											</div>\
										</a>\
									</li>';
						}
						else if(isNewCenter){
							strTemp='<li>\
										<a class="detail" href="{link}">\
											<div class="photo"><img  alt="" data-src="{img}" src="'+Game.blankPic+'"></div>\
											<div class="desc">\
												<h3>{title}</h3>\
												<p>\
													{resume}\
												</p>\
											</div>\
										</a>\
									</li>';
						}
						else
						{
							strTemp='<li>\
										<a class="detail" href="{alink}">\
											<div class="pic"><img  alt="" data-src="{img}" src="'+Game.blankPic+'"></div>\
											<div class="desc">\
												<h3>{name}</h3>\
												<p>{resume}</p>\
											</div>\
										</a>\
										<div class="download"><a class="btn" href="{link}">安装</a></div>\
									</li>';
						}
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
						
						iCat.foreach(data.data.list, function(i, v){
							s += tdMerge(strTemp, v);
						});
						
						pNode.append(s);
						if(data.data.hasnext==0 || data.data.hasnext=='false'){
							btnMore.hide();
						}
						_action.lazyLoad(document.body,100);
						
						btnMore.attr('data-hasnext', data.data.hasnext)
							.attr('data-curpage', data.data.curpage)
							.html('<span>加载更多</span>');
					}, 'json');
				});
			},

			backTop: function(){
				var btnBack = $('.J_gotoTop');
				if(!btnBack[0]) return;
				window.onscroll=function(){
					// alert(1);
					if(document.body.scrollTop>=180){
						btnBack.css('display','block');
					}
					else{
						btnBack.css('display','none');
					}
				}

				btnBack.find('a').removeAttr('href').on('click', function(evt){
					evt.preventDefault();
					window.scrollTo(0, 0);
					evt.stopPropagation();
				});
			},

			backPrevPage: function(){
				var href = location.href;
				window.addEventListener("unload", function(){
					alert(href);
				}, false);
			},
			lazyLoad: function(pNode, t){
				if(!pNode) return;
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

			submitForm: function(){
				var btnSubmit = $('.J_submitBtn');
				if(!btnSubmit[0]) return;

				var form = btnSubmit.closest('form'),
					formSelector = form.find('input, textarea'),
					showTip = function(msg){
						var tip = $('.J_tipBox'), dw = $(document).width(), bw = tip.width();
						tip.find('p').html(msg);
						tip.css('left', (dw-bw)/2+'px').removeClass('invisible');
						
						setTimeout(function(){
							tip.addClass('invisible');
							tip.find('p').html('');
						},3000);
					},
					checkSubmit = function(){
						var argus = {}, url = form.attr('data-ajaxUrl') || form.attr('action');
						for(var i=0, len=formSelector.length; i<len; i++){
							if(formSelector.eq(i).attr('rqd') && formSelector[i].value==''){
								showTip(formSelector.eq(i).attr('data-null'));
								return;
							}
							if(formSelector[i].value!='' && formSelector.eq(i).attr('pattern')){
								var reg = new RegExp(formSelector.eq(i).attr('pattern'));
								if(reg && !reg.test(formSelector[i].value)){
									showTip(formSelector.eq(i).attr('data-error'));
									return;
								}
							}
							
							argus[formSelector.eq(i).attr('name')] = formSelector[i].value;
						}

						if(!btnSubmit.hasClass('disabled')){
							btnSubmit.addClass('disabled');
							btime = +new Date();
						} else {
							ctime = +new Date();
							if(ctime-btime>=5000){
								btnSubmit.removeClass('disabled');
							} else {
								showTip('请在5秒后提交意见');
								return;
							}
						}

						$.post(url, argus, function(data){
							
							var oData = JSON.parse(data); //$.parseJSON()
							showTip(oData.msg);
							
							setTimeout(function(){
								if(oData.data.type=='redirect')
									location.href = oData.data.url;
							}, 3000);
						});
					};
				
				btnSubmit.on('click', function(evt){
					evt.preventDefault();
					checkSubmit();
				});
			},
			//search result
			search:function(){
				var btnSearch=$('.search-bar .search');
				if(!btnSearch[0])return;
				var form = btnSearch.closest('form'),
					formSelector = form.find('input, textarea'),
					showTip = function(msg){
						var tip = $('.J_tipBox'), dw = $(document).width(), bw = tip.width();
						tip.find('p').html(msg);
						tip.css('left', (dw-bw)/2+'px').removeClass('invisible');
						
						setTimeout(function(){
							tip.addClass('invisible');
							tip.find('p').html('');
						},3000);
					},
					checkSearch = function(){
						var  url = form.attr('action');
						for(var i=0, len=formSelector.length; i<len; i++){
							if(formSelector.eq(i).attr('rqd')){
								if(formSelector[i].value==''&&formSelector[i].placeholder=='')
								{
									formSelector[i].blur();
									showTip(formSelector.eq(i).attr('data-null'));
								}
								else if(formSelector[i].value!=''||formSelector[i].placeholder!='')
								{
									if(formSelector[i].value==''&&formSelector[i].placeholder!='')
									{
										formSelector[i].value=formSelector[i].placeholder;
									}
									form.submit();
								}
							}
						}
						};
					btnSearch.on('click',function(evt){
							evt.preventDefault();
							checkSearch();
						});
				},
			}
			return {
				version:1.3,
				blankPic:iCat.PathConfig.appRef.replace(/assets\/js\//g, 'pic/blank.gif'),
				loadingPic:iCat.PathConfig.appRef.replace(/assets\/js\//g,'pic/loading.gif'),
				init: function(){
					_action.slidePic();
					_action.loadMore();
					_action.screenshot();
					_action.openClose();
					_action.backTop();
					_action.submitForm();
					_action.search();
					_action.lazyLoad(document.body,100);
				}
			}
		});
	iCat.include(['lib/zepto/zepto.js'],function(){
		Game.init();	
	},true);
})(ICAT);