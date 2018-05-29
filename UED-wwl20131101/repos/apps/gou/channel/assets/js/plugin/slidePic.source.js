;(function($){
	$.extend($.fn, {
		slidePic: function(options){
			var defOptions = {
				slideWrap: '.slideWrap',
				slidePanel: '.pic',
				slideItem: '.pic a',
				handlePanel: '.handle',
				handleItem: '.handle>span',
				circle: false,
				auto: false,
				speed: 500,
				circleTime: 3000,
				prev: '.prev',
				next: '.next',
				curHandleCla: 'on',
				isBounce: false,
				isTouch: true
			},
			opt = $.extend(defOptions, options);
			
			var me = $(this), playTimer,
				slideWrap = me.find(opt.slideWrap),
				slidePanel = me.find(opt.slidePanel),
				slideItem = me.find(opt.slideItem), steps = slideItem.length, iWidth = slideItem.width(), pnWidth = iWidth*steps, boxWidth = me.width(),
				siWidth = opt.itemWidth || iWidth, threshold = Math.round(siWidth/4), noRule = siWidth!=iWidth,
				handlePanel = $(opt.handlePanel),
				handleItem = $(opt.handleItem),
				prev = $(opt.prev),
				next = $(opt.next);
			if(!me[0]) return;

			if(steps<=1){
				handlePanel.hide();
				return;
			}

			slidePanel.width(pnWidth);
			steps = noRule? Math.ceil(iWidth*steps/siWidth) : steps;
			me.data('curIndex', opt.fixCurrent? opt.fixCurrent() : 0);
			
			var style = slidePanel[0].style,
				def_curIdx = parseInt(me.data('curIndex')), def_curPos = def_curIdx*siWidth;
			if(opt.specialWidth){
				var specialWidth = iWidth-(boxWidth-iWidth)/2;
				def_curPos = def_curIdx==0? 0 : (def_curIdx-1)*iWidth+specialWidth;
			}
			style.MozTransform = style.webkitTransform = 'translate3d(' + (-def_curPos) + 'px,0,0)';
			style.msTransform = style.OTransform = 'translateX(' + (-def_curPos) + 'px)';
			me.data('curPosition', def_curPos);
			handleItem.removeClass(opt.curHandleCla).eq(def_curIdx).addClass(opt.curHandleCla);
			
			prev.on('click', function(){
				var curIndex = me.data('curIndex'),
					index = parseInt(curIndex)-1;
				slide(index, 'right');
			});
			next.on('click', function(){
				var curIndex = me.data('curIndex'),
					index = parseInt(curIndex)+1;
				slide(index, 'left');
			});
			
			if(opt.auto||opt.circle){autoplay();}
			
			if(opt.isTouch == true){
				slidePanel.swipe({
					swipeStatus: function(evt, phase, direction, distance, duration, fingerCount){
						if(phase=='start' || phase=='move'){
							if(playTimer) clearTimeout(playTimer);
							if(direction=='up' || direction=='down') return;
							tmove(direction, distance);
						}else {
							if(opt.auto) autoplay();
							if(direction=='up' || direction=='down') return;
							slide(parseInt(me.data('curIndex')), direction, distance);
						}
					},
					threshold: threshold,
					maxTimeThreshold: siWidth,
					triggerOnTouchEnd: true,
					allowPageScroll: 'vertical'
				});
			}

			function autoplay(){
				playTimer = setTimeout(function(){
					curIndex = parseInt(me.data('curIndex')) + 1;
					slide(curIndex, 'left');
					autoplay();
				}, opt.circleTime);
			}
			
			function slide(index, dir, distance, duration){
				
				var style = slidePanel[0].style,
					curPos, curIdx, timer;
				if(!duration){
					duration = opt.speed;
				}
				if(distance){
					index = distance<threshold? index : (dir=='left'? index+1 : index-1);
				}
				style.MozTransitionDuration = style.msTransitionDuration
					= style.OTransitionDuration = style.webkitTransitionDuration
						= style.transitionDuration = duration + 'ms';
				//ICAT.log(index+' '+steps+' '+dir);
				if(index>=steps && dir=='left'){
					var idx = steps-1;

					if(opt.afterLast){
						opt.afterLast();
					}

					if(opt.circle){
						curIdx = 0; curPos = 0;
						style.MozTransitionDuration = style.msTransitionDuration
							= style.OTransitionDuration = style.webkitTransitionDuration
								= style.transitionDuration = '1ms';
						style.MozTransform = style.webkitTransform = 'translate3d('+curPos+'px,0,0)';
						style.msTransform = style.OTransform = 'translateX('+curPos+'px)';
					} else if(opt.isBounce){
						curIdx = idx;
						curPos = curIdx*siWidth;

						style.MozTransform = style.webkitTransform = 'translate3d(' + (-(curPos+siWidth/2)) + 'px,0,0)';
						style.msTransform = style.OTransform = 'translateX(' + (-(curPos+siWidth/2)) + 'px)';

						timer = setTimeout(function(){
							style.MozTransform = style.webkitTransform = 'translate3d(' + (-curPos) + 'px,0,0)';
							style.msTransform = style.OTransform = 'translateX(' + (-curPos) + 'px)';
							
							me.data('curPosition', curPos).data('curIndex', curIdx);
							handleItem.removeClass(opt.curHandleCla).eq(curIdx).addClass(opt.curHandleCla);
						}, opt.speed);
					} else{
						curIdx = idx;
						curPos = noRule||opt.specialWidth? pnWidth-boxWidth : curIdx*siWidth;
						style.MozTransform = style.webkitTransform = 'translate3d(' + (-curPos) + 'px,0,0)';
						style.msTransform = style.OTransform = 'translateX(' + (-curPos) + 'px)';
					}
				}
				else if(index<0 && dir=='right'){
					
					if(opt.beforeFirst){
						opt.beforeFirst();
					}

					curIdx = 0;
					curPos = 0;

					if(opt.isBounce){
						style.MozTransform = style.webkitTransform = 'translate3d(' + siWidth/2 + 'px,0,0)';
						style.msTransform = style.OTransform = 'translateX(' + siWidth/2 + 'px)';
						timer = setTimeout(function(){
							style.MozTransform = style.webkitTransform = 'translate3d('+curPos+'px,0,0)';
							style.msTransform = style.OTransform = 'translateX('+curPos+'px)';

							me.data('curPosition', curPos).data('curIndex', curIdx);
							handleItem.removeClass(opt.curHandleCla).eq(curIdx).addClass(opt.curHandleCla);
						}, opt.speed);
					} else {
						style.MozTransform = style.webkitTransform = 'translate3d('+curPos+'px,0,0)';
						style.msTransform = style.OTransform = 'translateX('+curPos+'px)';
					}
				} else {
					curIdx = index;
					if(pnWidth-curIdx*siWidth<boxWidth){
						steps = curIdx+1;
						curPos = pnWidth-boxWidth;
						style.MozTransform = style.webkitTransform = 'translate3d(' + (-curPos) + 'px,0,0)';
						style.msTransform = style.OTransform = 'translateX(' + (-curPos) + 'px)';
					} else {
						if(opt.specialWidth){
							var specialWidth = iWidth-(boxWidth-iWidth)/2,
								i = curIdx-1;
							
							curPos = i==-1? 0 : i*iWidth+specialWidth;
							style.MozTransform = style.webkitTransform = 'translate3d(' + (-curPos) + 'px,0,0)';
							style.msTransform = style.OTransform = 'translateX(' + (-curPos) + 'px)';
						} else {
							curPos = curIdx*siWidth;
							style.MozTransform = style.webkitTransform = 'translate3d(' + (-curPos) + 'px,0,0)';
							style.msTransform = style.OTransform = 'translateX(' + (-curPos) + 'px)';
							
						}
					}
				}

				if(!timer){
					me.data('curPosition', curPos).data('curIndex', curIdx);
					handleItem.removeClass(opt.curHandleCla).eq(curIdx).addClass(opt.curHandleCla);
				}
			}
			
			function tmove(dir, distance, duration){
				var style = slidePanel[0].style,
					index = me.data('curIndex') || 0,
					pos = parseInt(me.data('curPosition')||0);

				if(!duration) duration = 0;//Math.floor(opt.speed*distance/boxWidth)

				if(!opt.isBounce&&index==0&&dir=='right' || !opt.isBounce&&index==steps-1&&dir=='left') return;

				style.MozTransitionDuration = style.msTransitionDuration
					= style.OTransitionDuration = style.webkitTransitionDuration
						= style.transitionDuration = duration+'ms';
				
				var dis = dir=='left'? distance : -distance;
				if(pnWidth-index*siWidth<boxWidth){
					var w = pnWidth-boxWidth;
					style.MozTransform = style.webkitTransform = 'translate3d(' + (-(w+dis)) + 'px,0,0)';
					style.msTransform = style.OTransform = 'translateX(' + (-(w+dis)) + 'px)';
				} else {
					style.MozTransform = style.webkitTransform = 'translate3d(' + (-(pos+dis)) + 'px,0,0)';
					style.msTransform = style.OTransform = 'translateX(' + (-(pos+dis)) + 'px)';
				}
				
			}
			
			/*(function(){
				var style = slidePanel[0].style,
					index = me.data('curIndex');
				setInterval(function(){
					style.MozTransform = style.webkitTransform = 'translate3d(' + (-index*siWidth) + 'px,0,0)';
					style.msTransform = style.OTransform = 'translateX(' + (-index*siWidth) + 'px)';
				}, 2*opt.speed);
			})()*/
		}
	});
})(jQuery);