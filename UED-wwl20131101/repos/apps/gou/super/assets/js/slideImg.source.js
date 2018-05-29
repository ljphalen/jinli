(function($){
	$.fn.extend({
		SlideImg: function(options){
			var defOptions = {
				scroll: '.ui-slide-scroll',
				scrollItem: '.ui-slide-item',
				tabs: '.ui-slide-tabs>.ui-slide-tab',
				loop: false,
				auto: false,
				circleTime: 500,
				css3: false,
				prev: '.ui-slide-prev',
				next: '.ui-slide-next',
				curTabClass: 'ui-slide-tabcur',
				beforeSlide: function(){},
				afterSlide: function(){}
			},
			opt = $.extend(defOptions, options);
			
			var me = $(this),
				scroll = me.find(opt.scroll),
				scrollItem = me.find(opt.scrollItem),
				tabs = me.find(opt.tabs),
				prev = me.find(opt.prev),
				next = me.find(opt.next);
			if(!me[0]) return;

			if(tabs.size() > 1){
				tabs.css('display','inline-block');
			}
			
			var timer = null, itemW = scrollItem.width(),
				curIndex = 0, numIndex = scrollItem.length,
				
			o = {
				autoplay: function(){
					timer = setTimeout(function(){
						curIndex = curIndex>=numIndex? 0 : curIndex+1;
						o.slide(curIndex);
						o.autoplay();
					}, 5000);
				},
				
				slide: function(index){
					opt.beforeSlide();
					if(opt.css3){
						scroll.css('-webkit-transition', 'all '+opt.circleTime/1000+'s')
							.get(0).style.webkitTransform = 'translateX('+(-index*scrollItem.width())+'px)';
						if(index>=numIndex){
							curIndex = 0;
							scroll.get(0).style.webkitTransform = 'translateX(0px)';
						} else {
							curIndex = index;
						}
						o.reset(curIndex);
						opt.afterSlide();
					} else {
						scroll.animate({
							left: -index*scrollItem.width() + 'px'
						}, opt.circleTime, function(){
							if(index>=numIndex){
								curIndex = 0;
								scroll.css({left:'0px'});
							} else {
								curIndex = index;
							}
							o.reset(curIndex);
							opt.afterSlide();
						});
					}
					
					if(index<=0){
						prev.addClass('ui-slide-prevdis');
					} else if(0<index<numIndex){
						prev.removeClass('ui-slide-prevdis');
					}
					
					if(index==numIndex-1){
						next.addClass('ui-slide-nextdis');
					} else if(index<numIndex-1){
						next.removeClass('ui-slide-nextdis');
					}
					
					if(opt.auto && timer==null){
						o.autoplay();
					}
				},
				
				reset: function(index){
					tabs.removeClass(opt.curTabClass).eq(index).addClass(opt.curTabClass);
				}
			};
			
			scroll.width(itemW*numIndex*(opt.css3? 1:2));
			if(opt.loop && !opt.css3){
				scroll.append(scroll.html());
			}
			if(opt.auto){
				o.autoplay();
			}
			if(prev[0]){
				prev.bind('click', function(){
					if(curIndex>0){
						o.slide(curIndex-1);
					}
				});
			}
			if(next[0]){
				next.bind('click', function(){
					if(curIndex<(numIndex-1)){
						o.slide(curIndex+1);
					}
				});
			}
			if(tabs[0]){
				tabs.bind('click', function(){
					var self = this, index = tabs.index(self);
					if(timer){
						clearTimeout(timer);
						timer = null;
					}
					o.slide(index);
				});
			}
			
			scroll.swipe({swipeLeft:swipe1, swipeRight:swipe2});
			function swipe1(evt, direction, distance, duration, fingerCount){
				//scroll.css({left: -distance+'px'});
				if(timer){
					clearTimeout(timer);
					timer = null;
				}
				if(distance>10){
					if(curIndex==numIndex-1){
						opt.loop? o.slide(curIndex+1) : o.slide(curIndex);
					} else {
						o.slide(curIndex+1)
					}
				}
			}
			function swipe2(evt, direction, distance, duration, fingerCount){
				//scroll.css({left: distance+'px'});
				if(timer){
					clearTimeout(timer);
					timer = null;
				}
				if(distance>10){
					curIndex<=0? o.slide(curIndex) : o.slide(curIndex-1);
					/*if(curIndex<=0){
						opt.loop? o.slide(numIndex-1) : o.slide(curIndex);
					} else {
						o.slide(curIndex-1);
					}*/
				}
			}
			
			/*窗口翻转 */
			//var supportsOrientationChange = "onorientationchange" in window,  
			//	orientationEvent = supportsOrientationChange? "orientationchange" : "resize";
			//window.addEventListener('resize', function(){
			if(opt.css3){
				setInterval(function(){
					var w = scrollItem.width();
					scroll.width(w*numIndex*(opt.css3? 1:2));
					scroll.get(0).style.webkitTransform = 'translateX('+(-curIndex*w)+'px)';
				},100);
			} else {
				$(window).bind('resize', function(){
					var w = scrollItem.width();
					scroll.width(w*numIndex*(opt.css3? 1:2));
					scroll.css({left: -curIndex*w + 'px'});
				});
			}
		}
	});
})(jQuery);