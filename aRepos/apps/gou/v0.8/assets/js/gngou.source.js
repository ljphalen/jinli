(function(iCat, $){
	
	iCat.inc(['.~/tempcore.js','.~/jtouchSwipe.js']);
	
	iCat.app('GNG', function(){
		return {
			version: '0.8'
		};
	});
	
	iCat.mix(GNG, {
		
		init: function(){
			var self = this;
			
			self.slideAjax().dialog();
			
			self.slideImg().tabType();
			
			self.linkage();
			self.skipPage();
			self.scrollAjax();
			
			$('.J_showPwd input[type=checkbox]').click(function(){
				var oIptText = $('.JS-showPwd'), oIptPwd = oIptText.prev('input');
				
				oIptText.val(oIptPwd.val());
				if(this.checked){
					oIptText.show();
					oIptPwd.hide();
				} else {
					oIptText.hide();
					oIptPwd.show();
				}
				
				oIptText.change(function(){
					oIptPwd.val(this.value);
				});
				oIptPwd.change(function(){
					oIptText.val(this.value);
				});
			});
		},
		
		// select级联
		linkage: function(){
			var areaWrap = $('.J_areaWrap'), oS = areaWrap.find('select');
			if(!areaWrap[0]) return this;
			
			iCat.include(['.~/dataArea.js','.~/linkage.js'],function(){
				iCat.widget.linkage({
					isArea: true,
					areaWrap: areaWrap,
					s1: oS.eq(0),
					s2: oS.eq(1),
					s3: oS.eq(2),
					aNode: areaWrap.attr('old-aNode') || ''
				});
			});
			
			return this;
		},
		
		// 轮播banner
		slideImg: function(){
			if(!$('.isTouch')[0]) return this;
			iCat.include(['../slideImg.js'], function(){
				$('#mainfocus').SlideImg({
					auto: true,
					loop: true
				});
				
				$('.J_scrollPro').SlideImg({css3:true});
			});
			return this;
		},
		
		// slide ajax
		slideAjax: function(){
			var slideBox = $('.J_slideAjax[data-ajaxUrl]'), maxpage;
			if(!slideBox[0]) return this;
			
			function itemSwipe(o, max){
				var w = o.width();
				o.swipe({
					swipeStatus: function(evt, phase, direction, distance, fingerCount){
						var me = $(this), theItem = $(evt.target).parents('.J_slideItem'),
							page = parseInt(theItem.attr('pid')), hasnext = theItem.attr('hasnext');
						
						/*if(hasnext=='true' && !me.find('.J_slideItem').eq(page)[0]){
							me.append('<div class="J_slideItem loading" style="left:100%;"><img src="'+iCat.appRef+'/assets/img/loading.gif" alt="" /></div>');
							ajaxSwipe(page+1);
						}*/
						/*for(var i=2; i<=max; i++){
							ajaxSwipe(i);console.log(i);
						}*/
						
						if(phase=='start'){
							var jsDate = new Date();
							s_time = jsDate.getTime();
						}
						else if(phase=='move'){
							var item = me.find('.J_slideItem'),
								thePercent = Math.round(100*distance/w), sibPercent = 100-thePercent;
							if(direction=='left'){
								if(hasnext=='true'){
									item.eq(page-1).css('left', -thePercent+'%');
									item.eq(page).css('left', sibPercent+'%');
								}
							} else if(direction=='right'){
								if(page!=1){
									item.eq(page-1).css('left', thePercent+'%');
									item.eq(page-2).css('left', -sibPercent+'%');
								}
							}
						}
						else if(phase=='cancel'){
							var jsDate = new Date();
							e_time = jsDate.getTime();
							
							var item = me.find('.J_slideItem'),
								thePercent = Math.round(100*distance/w);
							
							if(direction=='right'){
								item.eq(page-1).animate({left: '0%'}, 500);
								item.eq(page-2).animate({left: '-100%'}, 500);
							} else {
								if(hasnext=='true'){
									item.eq(page-1).animate({left: '0%'}, 500);
									item.eq(page).animate({left: '100%'}, 500);
								}
							}
							/*if(e_time-s_time<500 && distance<10){
								if(direction=='right'){
									item.eq(page-1).animate({left: '100%'}, 500);
									item.eq(page-2).animate({left: '0%'}, 500);
								} else {
									item.eq(page-1).animate({left: '-100%'}, 500);
									item.eq(page).animate({left: '0%'}, 500);
								}
							} else {
								if(direction=='right'){
									item.eq(page-1).animate({left: '0%'}, 500);
									item.eq(page-2).animate({left: '-100%'}, 500);
								} else {
									item.eq(page-1).animate({left: '0%'}, 500);
									item.eq(page).animate({left: '100%'}, 500);
								}
							}*/
						}
						else if(phase=='end'){
							var item = me.find('.J_slideItem'),
								thePercent = Math.round(100*distance/w);
							
							if(page!=1 && direction=='right'){
								if(thePercent>=25){
									item.eq(page-1).animate({left: '100%'}, 300);
									item.eq(page-2).animate({left: '0%'}, 300);
									
									var span = o.next('.J_slideSpan').find('span');
									span.removeClass('selected');
									span.eq(page-2).addClass('selected');
								}/* else {
									item.eq(page-1).animate({left: '0%'}, 300);
									item.eq(page-2).animate({left: '-100%'}, 300);
								}*/
							} else if(hasnext=='true' && direction=='left'){
								if(thePercent>=25){
									item.eq(page-1).animate({left: '-100%'}, 300);
									item.eq(page).animate({left: '0%'}, 300);
									
									var span = o.next('.J_slideSpan').find('span');
									span.removeClass('selected');
									span.eq(page).addClass('selected');
								}/* else {
									item.eq(page-1).animate({left: '0%'}, 300);
									item.eq(page).animate({left: '100%'}, 300);
								}*/
							}
						}
					},
					threshold: w/4,
				  	triggerOnTouchEnd: true,
					allowPageScroll: 'vertical'
				});
			}
			
			function ajaxSwipe(page){
				$.post(url, {token:token, page:page}, function(data){
					var d = data;//$.parseJSON()
					//$('.J_slideItem.loading').replaceWith(template('J_itemView', d));
					slideBox.append(template('J_itemView', d));
					
					if(d.data.hasnext) ajaxSwipe(page+1);
				}, 'json');
			}
			function ajaxClick(me, page, dir){
				if(maxpage && page>maxpage) return;
				if(!dir) dir = 'left';
				
				var item = me.find('.J_slideItem');
				if(dir=='left'){
					if(item.eq(page-1)[0]){
						var curItem = item.eq(page-1);
						curItem.animate({left: '0%'}, 500);
						item.eq(page-2).animate({left: '-100%'}, 500);
						me.height(curItem.attr('wrapHeight'));
					} else {
						$.post(url, {token:token, page:page}, function(data){
							var d = $.parseJSON(data);
							if(!d.data.hasnext) maxpage = page;
							
							me.append('<div class="J_slideItem loading" style="left:100%;"><img src="'+iCat.appRef+'/assets/img/loading.gif" alt="" /></div>');
							$('.J_slideItem.loading').replaceWith(template('J_itemView', d));
							
							var item = me.find('.J_slideItem'), h = item.eq(page-1).height();
							item.eq(page-1).animate({left: '0%'}, 500).attr('wrapHeight', h);
							item.eq(page-2).animate({left: '-100%'}, 500);
							me.height(h);
							
							if(!d.data.hasnext)
								item.eq(page-1).find('.r').addClass('disable');
						});
					}
				} else if(page>0 && dir=='right'){
					var curItem = item.eq(page-1);
					item.eq(page).animate({left: '100%'}, 500);
					curItem.animate({left: '0%'}, 500);
					me.height(curItem.attr('wrapHeight'));
				}
			}
			
			// 初始化
			var url = slideBox.attr('data-ajaxUrl');
			$.post(url,{token:token, page:1}, function(data){
				var d = $.parseJSON(data);
				
				slideBox.append(template('J_itemView', d));
				var firstItem = slideBox.find('.J_slideItem'), wraph = firstItem.height();
				slideBox.find('.J_slideItem').css('left','0%').attr('wrapHeight', wraph);
				slideBox.height(wraph);
				
				if(slideBox.find('.handle')[0]){
					if(!d.data.hasnext){
						slideBox.find('.handle').addClass('disable');
						return;
					}
					slideBox.find('.handle').live('click', function(){
						var curpage = parseInt($(this).parents('.J_slideItem').attr('pid'));
						
						$(this).hasClass('r')? ajaxClick(slideBox, curpage+1) : ajaxClick(slideBox, curpage-1, 'right');
					})
						.eq(0).addClass('disable');
					return;
				}
				if(d.data.hasnext){
					ajaxSwipe(2);
					itemSwipe(slideBox);
					
					if(slideBox.next('.J_slideSpan')[0]){
						var str = '';
						for(var i=0; i<d.data.total; i++){
							str += '<span'+(i==0? ' class="selected"':'')+'></span>';
						}
						slideBox.next('.J_slideSpan').html(str);
					}
				}
			});
			
			window.onorientationchange = window.onresize = window.onorientation = function(){
				var item = slideBox.find('.J_slideItem'), w = slideBox.width();
				item.each(function(){
					this.style.width = w+'px';
				});
			}
			
			return this;
		},
		
		// skip page
		skipPage: function(){
			$('.J_skipLeft, .J_skipRight').swipe({
				swipe: function(evt, direction, distance, duration, fingerCount){
					var me = $(this), url = me.attr('data-toUrl');
					if(me.hasClass('J_skipLeft')){
						if(direction=='left') location.href = url;
					}
					if(me.hasClass('J_skipRight')){
						if(direction=='right') location.href = url;
					}
				},
				allowPageScroll: 'vertical'
			});
		},
		
		// show dialog
		dialog: function(){
			var dw = $(document).width(), dh = $(document).height(),
				box = $('.J_dialogBox'), w = box.width(), x_pos = (dw-w)/2;
			
			$('.J_showDialog').click(function(evt){
				evt.preventDefault();
				
				if(this.getAttribute('data-ajaxUrl')){
					var ajaxurl = this.getAttribute('data-ajaxUrl');
					$.post(ajaxurl, {token:token}, function(data){
						var d = $.parseJSON(data);
						box.find('p').html(d.msg);
						if(d.success) box.find('.btn a').removeAttr('href');
						box.css('left', x_pos+'px').show()
							.find('.btn a').live('click', function(){
								$('.JS-dbMask').hide();
								box.hide();
							});;
						$('.JS-dbMask').height(dh).show()
							.live('click', function(){
								this.style.display = 'none';
								box.hide();
							});
					});
				} else {
					box.css('left', x_pos+'px').show()
						.find('.btn span').bind('click', function(){
							$('.JS-dbMask').hide();
							box.hide();
						});;
					$('.JS-dbMask').height(dh).show()
						.bind('click', function(){
							this.style.display = 'none';
							box.hide();
						});
				}
			});
			
			$('.form-box form').submit(function(evt){
				evt.preventDefault();
				
				var form = $(this), url = form.attr('action'),
					els = this.elements, json = {};
				iCat.foreach(els, function(i, v){
					var oMe = $(v);
					if(oMe.attr('name')){
						if(oMe.attr('type')=='radio'){
							if(v.checked){
								json[oMe.attr('name')] = v.value;
							}
						} else {
							json[oMe.attr('name')] = v.value;
						}
					}
				});
				
				iCat.mix(json, {token: token});
				
				$.post(url, json, function(data){
					var d = $.parseJSON(data);
					if(d.success){
						if(d.data.type=='redirect' && d.data.url){
							location.href = d.data.url;
						}
						return;
					};
					
					box.find('p').html(d.msg);
					form.find('input').blur();
					box.css('left', x_pos+'px').show()
						.find('.btn span').live('click', function(){
							$('.JS-dbMask').hide();
							box.hide();
						});
					$('.JS-dbMask').height(dh).show()
						.live('click', function(){
							this.style.display = 'none';
							box.hide();
						});
				});
			});
			
			return this;
		},
		
		// tab
		tabType: function(){
			var tabCtrl = $('.JS-tabItem');
			
			tabCtrl.each(function(){
				var _self = $(this), tabLi = _self.find('li'),
					url = _self.attr('data-ajaxUrl'),
					tabMain = _self.next('.JS-tabMain');
				
				tabLi.click(function(){
					var me = $(this), type = me.attr('data-type');
					
					me.addClass('selected').siblings('li').removeClass('selected');
					if(tabMain.find('.pictext[type='+type+']')[0]){
						tabMain.find('.pictext[type='+type+']').show()
							.siblings('.pictext').hide();
					} else {
						$.post(url, {token:token, type:type}, function(data){
							var d = $.parseJSON(data); d = iCat.mix(d, {type:type});
							tabMain.append(template('J_itemView', d));
							tabMain.find('.pictext').css({'position':'static'}).hide();
							tabMain.find('.pictext[type='+type+']').show();
						});
					}
				});
				
				//初始化
				var type1 = tabLi.eq(0).attr('data-type');
				$.post(url, {token:token, type:type1}, function(data){
					var d = $.parseJSON(data); d = iCat.mix(d, {type:type1});
					tabMain.append(template('J_itemView', d));
					tabMain.find('.pictext').css({'position':'static'});
				});
			});
		},
		
		// scrollajax
		scrollAjax: function(){
			if(!$('.JS-loadMore')[0]) return this;
			
			var pnode = $('#page'), url = pnode.attr('data-ajaxUrl'),
				btnMore = pnode.find('.JS-loadMore');
			
			function _ajaxMore(){
				var page = parseInt(pnode.attr('data-curPage'))+1,
					keyword = pnode.find('input[name=keyword]').val();
				if(btnMore.hasClass('disabled')) return;
				
				btnMore.html('<a><img src="'+iCat.appRef+'/assets/img/loading.gif" alt="" /></a>');
				$.post(url,{token:token, page:page, keyword:keyword}, function(data){
					var d = $.parseJSON(data);
					
					pnode.attr('data-curPage', page)
						.find('.item-list ul, .item-grid ul').append(template('J_itemView', d));
					btnMore.html('<a href="">点击加载更多</a>');
					if(!d.data.hasnext){
						btnMore.addClass('disabled');
						pnode.addClass('disabled');
					}
				});
			}
			
			pnode.swipe({
				swipe: function(evt, direction, distance, duration, fingerCount){
					if(pnode.hasClass('disabled')) return;
					if(direction=='up' && distance>=30){
						_ajaxMore();
					}
				},
				allowPageScroll: 'auto'
			});
			
			btnMore.bind('click', function(evt){
				evt.preventDefault();
				_ajaxMore();
			});
			
			$(window).bind('scroll', function(){
				var lazyheight = parseFloat($(window).height()) + parseFloat($(window).scrollTop());
				if($(document).height()<= lazyheight){
					_ajaxMore();
				}
			});
		}
	});
	
	$(function(){
		GNG.init();
	});
})(ICAT,jQuery);