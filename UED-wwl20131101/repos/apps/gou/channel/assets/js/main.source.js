(function(iCat){
	
	//调试配置开启
	/*iCat.PathConfig({ weinreRef: 'http://18.8.2.34:8080/'});
	iCat.weinreStart();*/

	iCat.app('Gou', function(){
		iCat.PathConfig();
		return {
			version: '0.9.6',
			platform: function() {
				var src = location.host, cfg;
				if(/channel\./i.test(src)){
					cfg = {
						prefix: 'channel_'
					};
				} else if(/market\./i.test(src)) {
					cfg = {
						prefix: 'market_'
					};
				} else if(/ios\./i.test(src)) {
					cfg = {
						prefix: 'ios_',
						head: 'header, ',
						layout: 'header#iHeader.hd + div#iScroll'
					};
				} else {
					cfg = {
						prefix: ''
					};
				}
				return cfg;
			}()
		};
	});

	Gou.namespace('view', 'model', 'controller');

	iCat.mix(Gou, {
		init: function(){
			var Event = iCat.Event,
				self = this, token = window['token'] || '';
			
			//设置ajax接口
			iCat.rentAjax($.ajax, {
				data: {token:token},
				success: function(){ /*iCat.log('我执行了...');*/ },
				error: function(){}
			});

			//事件绑定
			Event.on('.J_unfoldWrap h2', '@singleTap', self.unfold);
			Event.on('.J_loadMore', '@singleTap', self.loadmore);

			//普通执行
			self.dialog().linkage().waterFall().scrollNav().refresh().scrollPic().lazyLoad(document.body,100);

			//prize
			if(document.body.id==='prize'){
				Event.on('#J_createLink button', '@singleTap', function(){
					var input = $('#J_createLink input'),
						val = input.val(), ajaxUrl = input.attr('data-ajaxurl');
					if(!val){
						alert('手机号码不能为空！');
						return;
					}

					iCat.util.ajax({
						url: iCat.util.fullUrl(ajaxUrl, true), data: {phone: val},
						success: function(d){
							if(d.success){
								var url = d.data.url || d.data[0].ajax_url;
								$('#J_linkResult').removeClass('hidden')
										.find('span').html(url);
								$('.share-list').hide();
								$('.bshare-custom').css('display', '-webkit-box');
								bShare.addEntry({
									title: "#邀请好友送话费#",
									summary: "小伙伴们，点击下载购物大厅，从此免装淘宝天猫京东，购物更轻松，另有话费、实物多重大奖等你来拿！",
									url: url
								});
							} else {
								alert(d.msg);
							}
						}
					});
				});
			}
		},

		unfold: function(){
			var _self = $(this), ajaxUrl = _self.attr('data-ajaxUrl'),
				_desc = _self.next('.desc'), _itemWrap = _desc.find('ul');

			if(ajaxUrl && !_self.hasClass('done')){
				_itemWrap.addClass('items-loading');
				iCat.util.ajax({
					url: iCat.util.fullUrl(ajaxUrl, true),
					success: function(data){
						if(data.success){
							iCat.util.render({
								tempId: $('.J_unfoldWrap script'),
								wrap: _itemWrap,
								multiChild: true
							}, data);
							_itemWrap.removeClass('items-loading');
							if(data.data.hasnext==true && !_desc.find('.J_loadMore')[0])
								_itemWrap.after('<div class="btn items J_loadMore"><span class="rount-rect gray">加载更多...</span></div>');
							_self.attr('data-hasnext', data.data.hasnext)
								 .attr('data-curpage', data.data.curpage);
						}
					}
				});
				_self.addClass('done');
			}

			_self.hasClass('up')?
				_self.removeClass('up').addClass('down') :
				_self.removeClass('down').addClass('up');
			_desc.toggleClass('hidden');
		},

		loadmore: function(){
			var _self = $(this), _itemWrap = _self.prev('ul'),
				infWrap, ajaxUrl, elTemp;

			infWrap = _self.hasClass('items')?
						_self.parent('.desc').prev('h2') : _self;
			ajaxUrl = infWrap.attr('data-ajaxurl');
			
			if(infWrap.attr('data-hasnext')=='true'){
				var curPage = infWrap.attr('data-curpage'),
					_page = curPage? (ajaxUrl.indexOf('?')<0? '?':'&')+'page=' + (parseInt(curPage)+1) : '';

				_self.removeClass('J_loadMore');
				_itemWrap.addClass('items-loading');
				iCat.util.ajax({
					url: iCat.util.fullUrl(ajaxUrl+_page, true),
					success: function(data){
						if(data.success){
							iCat.util.render({
								tempId: 'templete',
								wrap: _itemWrap,
								multiChild: true
							}, data);
							_self.addClass('J_loadMore');
							_itemWrap.removeClass('items-loading');
							if(data.data.hasnext==false) _self.remove();
							infWrap.attr('data-hasnext', data.data.hasnext)
									  .attr('data-curpage', data.data.curpage);
						}
					}
				});
			}
		},

		// show dialog
		dialog: function(){
			var dw = $(document).width(), dh = $(document).height()+1000,
				box = $('.J_dialogBox'), w = box.width(), x_pos = (dw-w)/2;
			
			$('.J_showDialog').click(function(evt){
				evt.preventDefault();
				
				$('html').css('overflow','hidden');
				dh = $('.J_showDialog').parent().hasClass('favor')? $(window).height() : dh;
				if(this.getAttribute('data-ajaxUrl')){
					var ajaxurl = this.getAttribute('data-ajaxUrl');
					$.post(ajaxurl, {token:token}, function(data){
						var d = $.parseJSON(data);
						box.find('p').html(d.msg);
						if(d.success){
							box.find('.btn a').removeAttr('href');
							if($('.J_showDialog').parent().hasClass('favor')){
								$('.J_showDialog').addClass('selected');
							}
						}
						box.css('left', x_pos+'px').show()
							.find('.btn a').live('click', function(){
								$('.JS-dbMask').hide();
								box.hide();
								$('html').css('overflow','auto');
							});
						$('.JS-dbMask').height(dh).show()
							.live('click', function(){
								this.style.display = 'none';
								box.hide();
								$('html').css('overflow','auto');
							});
					});
				} else {
					box.css('left', x_pos+'px').show()
						.find('.btn span').live('click', function(){
							$('.JS-dbMask').hide();
							box.hide();
							$('html').css('overflow','auto');
						});;
					$('.JS-dbMask').height(dh).show()
						.live('click', function(){
							this.style.display = 'none';
							box.hide();
							$('html').css('overflow','auto');
						});
				}
			});
			return this;
		},

		//省市级联
		linkage: function(){
			var areaWrap = $('.J_areaWrap'), oS = areaWrap.find('select');
			if(areaWrap[0]){
				iCat.include(['./plugin/dataArea.js','./plugin/linkage.js'],function(){
						iCat.widget.linkage({
							isArea: true,
							areaWrap: areaWrap,
							s1: oS.eq(0),
							s2: oS.eq(1),
							s3: oS.eq(2),
							aNode: areaWrap.attr('old-aNode') || ''
						});
				}, true);
			}

			return this;
		},

		waterFall: function(){
			var wfWrap = $('.J_waterFall'), scrollPannel = $(window),
				slHeight = 0, slTop = 0, spHeight,
				blankPic = iCat.PathConfig.appRef.replace(/assets\/js\//g, 'pic/blank.gif'),
				curData = [];
			if(!wfWrap[0]) return this;

			var colums = wfWrap.find('li'),
				ajaxUrl = wfWrap.attr('data-ajaxUrl'), st = scrollPannel.scrollTop(),
				
				_appendDetect = function(d){
					if(!d.length){
						wfWrap.addClass('done');
						return;
					}

					var start = 0;
					for(start; start<3; start++){
						var colum = colums.eq(start),
							cHeight = colum.height(), cTop = colum.scrollTop(),
							spTop = scrollPannel.scrollTop(), spHeight = scrollPannel.height();
						//console.log(cHeight, cTop, spTop, spHeight);
						if(colum[0] && !wfWrap.hasClass('done')){
							if(cHeight+cTop<spTop+spHeight){
								_append(colum, d);
								
							}
						}
					}
				},

				_append = function(wrap, d){
					wrap.append(
						'<a href="'+d[0].url+'">'
						+	'<img src="'+blankPic+'" data-src="'+d[0].img+'">'
						+	'<span>￥'+d[0].price+'</span>'
						+'</a>'
					);
					iCat.util.lazyLoad(wrap[0]);
					d.shift();
				};

			$.ajax({
				type: 'POST', timeout:10000,
				url: ajaxUrl,
				data: {token:token},
				cache: false,
				success: function(d){
					var data = JSON.parse(d);
					if(data.success){
						data.data.list.forEach(function(v, i){
							if(i<15){
								if(i%3==0)
									_append(colums.eq(0), [v]);
								if((i-1)%3==0)
									_append(colums.eq(1), [v]);
								if((i-2)%3==0)
									_append(colums.eq(2), [v]);
							} else {
								curData.push(v);
							}
						});

						if(curData.length){
							scrollPannel.scroll(function(){
								var pl = this===window? document.body : this;
								slHeight = pl.scrollHeight;
								slTop = pl.scrollTop;
								spHeight = scrollPannel.height();
								 
								if(Math.abs(st-slTop)>40){
									st = slTop;
									_appendDetect(curData);
								}
							});
						}
						
						wfWrap.attr('data-hasnext', data.data.hasnext)
							  .attr('data-curpage', data.data.curpage);
					}
				},
				error: function(){}
			});

			return this;
		},

		scrollNav: function(){
			var navWrap = $('.scroll-nav .wrap');
			if(!navWrap[0]) return this;

			var seleNav = navWrap.find('span').parent();
			navWrap.scrollLeft(seleNav.position().left);

			return this;
		},


		refresh: function(){
			var refresh = $('.J_refresh'),
				url = location.href;
			if(!refresh[0]) return this;

			refresh.click(function(){
				location.href = url;
			});

			return this;
		},
		//详情页图片轮播
		scrollPic:function(){
			var wrap=$('.J_scrollBanner'),
			sPannel = wrap.find('.pic-box ul');
			if(!sPannel[0]) return this;
			if(sPannel.find('img').length<=1){
				$('.handle').css('display','none');
				return this;
			}
			iCat.include(['lib/jquery/touchSwipe.js','./plugin/slidePic.js'],function(){
				wrap.slidePic({
					slidePanel:'.pic-box>ul',
					slideItem:'.pic-box>ul>li',
					auto:true,
					circle:true,
					isTouch:true
				}
				);
			},true);
			return this;
		},
		lazyLoad: function(pNode, t){
			if(!pNode) return;
				var imgs = pNode.querySelectorAll('img[src$="blank.gif"]'),
					_fn = function(o){
						var src = o.getAttribute('data-original');
						iCat.__IMAGE_CACHE = iCat.__IMAGE_CACHE || {};
						if(!src) return;

						if(!iCat.__IMAGE_CACHE[src]){
							var oImg = new Image(); oImg.src = src;
							oImg.onload = function(){
								if($(o).hasClass('lazy')){
									$(o).css('width','auto');
								}
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
	});

	iCat.include('lib/jquery/jquery.js', function(){	
		iCat.require({modName:'appMVC'});//mvc
		Gou.init();//other
	}, true, false);
})(ICAT);