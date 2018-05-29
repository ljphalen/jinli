/* event.js # */
(function(iCat, root, doc){
	// 创建Event命名空间
	var Event = iCat.namespace('Event');

	iCat.mix(Event,
	{
		_parentIfText: function(node){
			return node.nodeType===1? node : node.parentNode;
		},

		_bubble: function(node, callback){//冒泡
			if(!node || node.nodeType!==1) return;
			while(node!==doc.body){
				if(callback && callback(node)==false) break;
				if(node.parentNode)
					node = node.parentNode;
				else break;
			}
		},

		_bindEvent: function(el, type, handler){
			el.events = el.events || {};
			el.types = el.types || [];
			var _fn = el.events[type] = handler;

			//绑定同el的同type事件，请用type.xxx方式
			!el.types.contains(type) && el.types.push(type);

			if(el.addEventListener)
				el.addEventListener(type.replace(/\..*/g, ''), _fn, false);
		},

		_unbindEvent: function(el, type){
			if(!el.events || !el.types.contains(type)) return;
			
			var handler = el.events[type];
				type = type.replace(/\..*/g, '');
			if(el.removeEventListener)
				el.removeEventListener(type, handler, false);

			if(iCat.isEmptyObject(el.events) || !el.types.length){
				el.events = null;
				el.types = null;
			}
		},

		_execute: function(eType, el, evt, argus){
			eType = eType.toLocaleLowerCase();
			if(argus==='refuseSham') argus = true;
			Event._bubble(el, function(node, index){
				index = iCat.util._matches(node, Event.__event_selectors);
				var _preventDef = false, _stopBubble = false;
				if(iCat.isNumber(index)){
					var el = Event.items[Event.__event_selectors[index]];
					if(el && el.types.contains(eType)){// fixed bug:el有时候找不到
						iCat.foreach(el.events, function(k, v){
							k = k.replace(/\.\w+/g, '').split('|');
							if(k[0]==eType){
								argus = argus!==undefined? argus : [];// fixed bug:当argus是0时，没有传0
								iCat.isArray(argus)?
									argus.push(evt) : argus = [evt,argus]; //step1
								v.apply(node, argus);
							}
							
							_preventDef = k[1]==0? false : true;
							_stopBubble = k[2]==0? false : true;
							//return false; fixed bug:阻止了本元素其他事件的执行
						});
						if(_preventDef){//阻止默认事件
							if(argus===true){
								evt.preventDefault();
							} else {
								Event.on(node, 'click.prevent', function(evt){ evt.preventDefault(); });
							}
						}
					}
				}

				if(_stopBubble){//阻止冒泡
					if(argus)
						evt.stopPropagation();
					return false;
				}
			});
		},

		bind: function(el, type, handler){
			if(!el) return;
			el = iCat.util.queryAll(el);
			el.length===undefined ?
				Event._bindEvent(el, type, handler) :
				iCat.foreach(el, function(i,v){
					Event._bindEvent(v, type, handler);
				});
		},

		unbind: function(el, type){
			if(!el) return;
			el = iCat.util.queryAll(el);
			el.length===undefined ?
				Event._unbindEvent(el, type) :
				iCat.foreach(el, function(i,v){
					Event._unbindEvent(v, type);
				});
		},

		trigger: function(el, type, bubbles, cancelable){
			if(iCat.isObject(el) && !iCat.isjQueryObject(el)){// 普通对象
				el[type] && el[type].apply(el, bubbles);
				return;
			}

			if(iCat.isjQueryObject(el)) {// jquery对象
				el = el.get(0);
			}

			if(/^@\w+/i.test(type)){// 事件代理
				type = type.replace(/^@/i, '');
				el = iCat.util.queryOne(el);
				Event._execute(type, el);
			}
			else { // 普通元素
				var ev = doc.createEvent('Event');
				ev.initEvent(type.replace(/\.\w+/g, ''), bubbles, cancelable);
				el.dispatchEvent(ev);
			}
		},

		ready: function(f){
			var domReady = Event.ready,
				_ready = function(){
					if(domReady.done) return false;
					if(doc && doc.getElementsByTagName && doc.getElementById && doc.body){
						clearInterval(domReady.timer);
						domReady.timer = null;

						domReady.arrFuns.forEach(function(fn){ fn(); });
						domReady.arrFuns = null;
						domReady.done = true;
					}
				};

			if(domReady.done) return f();
			if(domReady.timer){
				domReady.arrFuns.push(f);
			} else {
				Event.on(root, 'load', _ready);
			}

			domReady.arrFuns = [f];
			domReady.timer = setInterval(_ready, 15);
		},

		//存放代理元素的选择器
		__event_selectors: [],

		/*
		 * arrObj可以是<b>单个对象</b>或<b>对象数组</b>
		 * arrObj = {selector:'.cla', type:'click', callback:function(){}, preventDefault:true, stopPropagation:false}
		 */
		delegate: function(arrObj){
			if(!arrObj) return;
			var arrSele = Event.__event_selectors;
			var objItem = Event.items = Event.items || {};
			
			iCat.util.recurse(arrObj, function(o){
				if(iCat.isEmptyObject(o)) return;

				var _refuseSham = /!$/.test(o.type);
				o.type = o.type.toLocaleLowerCase();
				o.type = _refuseSham? o.type.replace(/!/g,'') : o.type.replace(/click/g,'tap').replace(/mov/g,'swip');

				if(/blur|focus|load|unload|change/i.test(o.type)){//不适合代理的事件
					iCat.util.wait(function(k){
						var node = iCat.util.queryAll(o.selector);
						if(!node || !node.length){
							iCat.__cache_timers[k] = false;
							return;
						}
						delete iCat.__cache_timers[k];
						Event.on(node, o.type, o.callback);
					}, 500, 10);
				}
				else {
					o.selector = o.selector.trim().replace(/\s+/g, ' ');
					if(!arrSele.contains(o.selector)) arrSele.push(o.selector);
					var el = objItem[o.selector] = objItem[o.selector] || {},
						key = o.type + '|' + (o.preventDefault? 1:0) + '|' + (o.stopPropagation? 1:0);
					el.events = el.events || {};
					el.types = el.types || [];

					o.type = o.type.replace(/\..*/g, '');
					el.types.indexOf(o.type)<0 && el.types.push(o.type);
					el.events[key] = o.callback;

					if(!/tap|swip|hover/i.test(o.type) || _refuseSham){//非模拟事件
						iCat.util.wait(function(k){
							if(!iCat.elBodyWrap){
								iCat.__cache_timers[k] = false;
								return;
							}

							delete iCat.__cache_timers[k];
							
							if(!iCat.elBodyWrap.events[o.type]){
								Event.on(iCat.elBodyWrap, o.type, function(evt){
									Event._execute(o.type, evt.target, evt, 'refuseSham');
								});
							}
						});
					}
				}
			});
		},

		//arrObj = {selector:'#page', type:'click'}
		undelegate: function(arrObj){
			if(!arrObj) return;
			var arrSele = Event.__event_selectors;
			var objItem = Event.items = Event.items || {};

			iCat.util.recurse(arrObj, function(o){
				if(iCat.isEmptyObject(o)) return;

				var _refuseSham = /!$/.test(o.type),
					node = iCat.util.queryAll(o.selector);
				o.type = o.type.toLocaleLowerCase();
				o.type = _refuseSham? o.type.replace(/!/g,'') : o.type.replace(/click/g,'tap').replace(/mov/g,'swip');
				Event.off(node, 'click.prevent');

				if(/blur|focus|load|unload|change/i.test(o.type)){
					Event.off(node, o.type);
				} else {
					o.selector = o.selector.trim().replace(/\s+/g, ' ');
					if(!arrSele.contains(o.selector) || !objItem[o.selector]) return;
					arrSele.remove(o.selector);
					var el = objItem[o.selector];

					if(el.types.contains(o.type)){
						el.types.remove(o.type);
						iCat.foreach(el.events, function(k, v){
							if(k.indexOf(o.type)!=-1)
								delete el.events[k];
						});
					}

					//事件为空时去掉
					if(!el.types.length && iCat.isEmptyObject(el.events)){
						delete objItem[o.selector];
					}
				}
			});
		},

		on: function(el, type, handler, pd, sp){
			if(iCat.isString(el) && /^@\w+/i.test(type)){
				type = type.replace(/^@/i, '');
				Event.delegate({
					selector:el, type:type, callback:handler,
					preventDefault:pd, stopPropagation:sp
				});
			} else {
				Event.bind(el, type, handler);
			}
		},

		off: function(el, type){
			if(iCat.isString(el) && /^@\w+/i.test(type)){
				type = type.replace(/^@/i, '');
				Event.undelegate({selector:el, type:type});
			} else {
				Event.unbind(el, type);
			}
		}
	});
	
	if(iCat.Shim.Event){//keep Compatible
		iCat.mix(Event, iCat.Shim.Event);
	}

	iCat.embedjQuery();// for jQuery/Zepto
	
	Event.ready(function(){
		var touch = {}, touchTimeout,
			supportTouch = 'ontouchstart' in root;

		var start_evt = supportTouch ? 'touchstart' : 'mousedown',
			move_evt = supportTouch ? 'touchmove' : 'mousemove',
			end_evt = supportTouch ? 'touchend' : 'mouseup',
			cancel_evt = supportTouch ? 'touchcancel' : 'mouseout';

		var bodyNode = iCat.elBodyWrap = iCat.util.queryOne('*[data-pagerole=body]'),
			Event = iCat.Event, now, delta,
			longTapDelay = 750, longTapTimeout,

			cancelLongTap = function(){
				if(longTapTimeout)
					clearTimeout(longTapTimeout);
				longTapTimeout = null;
			},

			swipeDirection = function(x1, x2, y1, y2){
				var xDelta = Math.abs(x1 - x2), yDelta = Math.abs(y1 - y2);
				return xDelta >= yDelta ? (x1 - x2 > 0 ? 'Left' : 'Right') : (y1 - y2 > 0 ? 'Up' : 'Down');
			};

		if(!bodyNode){
			iCat.elBodyWrap = doc.body;
			return;
		}

		// start
		Event.on(bodyNode, start_evt, function(evt){
			//evt.preventDefault(); //fixed bug: 以下事件加上阻止默认，会引起无法滑动滚动条
			evt.stopPropagation();
			if(evt.button && evt.button===2) return;

			var page = supportTouch? evt.touches[0] : evt;
			now = Date.now();
			delta = now - (touch.last || now);
			touch.el = Event._parentIfText(evt.target);
			touchTimeout && clearTimeout(touchTimeout);

			touch.x1 = page.pageX;
			touch.y1 = page.pageY;
			touch.isScrolling = undefined;

			if(delta>0 && delta<=250) touch.isDoubleTap = true;
			touch.last = now;
			Event._execute('hover', touch.el, evt, 0);

			longTapTimeout = setTimeout(function(){
					longTapTimeout = null;
					if(touch.last){
						Event._execute('hover', touch.el, evt, 1);
						Event._execute('longtap', touch.el, evt);
						touch = {};
					}
				}, longTapDelay);
		});

		// doing
		Event.on(bodyNode, move_evt, function(evt){
			evt.stopPropagation();
			if(evt.button && evt.button===2) return;

			cancelLongTap();
			var page = supportTouch? evt.touches[0] : evt;
			touch.x2 = page.pageX;
			touch.y2 = page.pageY;
			var distanceX = touch.x2 - touch.x1,
				distanceY = touch.y2 - touch.y1;
			if(typeof touch.isScrolling=='undefined'){
				touch.isScrolling = !!(touch.isScrolling || Math.abs(distanceX)<Math.abs(distanceY));
			}
			if(!touch.isScrolling){
				Event._execute('swiping', touch.el, evt, [touch.x1, touch.x2, touch.y1, touch.y2]);
			}
		});

		// end
		Event.on(bodyNode, end_evt, function(evt){
			evt.stopPropagation();
			if(evt.button && evt.button===2) return;
			Event._execute('hover', touch.el, evt, 1);
			cancelLongTap();
			
			if(!touch.isScrolling){
				if(touch.isDoubleTap){// double tap (tapped twice within 250ms)
					Event._execute('dbltap', touch.el, evt);
					touch = {};
				} else if('last' in touch){
					if((touch.x2&&Math.abs(touch.x1-touch.x2)<20) || (touch.y2&&Math.abs(touch.y1-touch.y2)<20)){
						Event._execute('tap', touch.el, evt);
					}

					touchTimeout = setTimeout(function(){
						touchTimeout = null;
						Event._execute('singletap', touch.el, evt);
						touch = {};
					}, 250);
				} else if((touch.x2&&Math.abs(touch.x1-touch.x2)>30) || (touch.y2&&Math.abs(touch.y1-touch.y2)>30)){
					var swipe = 'swipe' + swipeDirection(touch.x1, touch.x2, touch.y1, touch.y2);
					Event._execute(swipe, touch.el, evt);
					touch = {};
				}
			} else {
				touch = {};
			}
		});

		// cancel
		Event.on(bodyNode, cancel_evt, function(evt){
			evt.stopPropagation();
			if(touchTimeout) clearTimeout(touchTimeout);
			if(longTapTimeout) clearTimeout(longTapTimeout);
			longTapTimeout = touchTimeout = null;
			touch = {};
		});
	});
})(ICAT, this, document);