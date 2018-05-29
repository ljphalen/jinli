/*!
 * Copyright 2011~2013, ICAT JavaScript Library v1.1.4
 * https://github.com/valleykid/icat
 *
 * Copyright (c) 2013 valleykid
 * Licensed under the MIT license.
 *
 * @Author valleykiddy@gmail.com
 * @Time 2013-04-20 08:50:00
 */

/* core.js # */
(function(){
	// Create the root object, 'window' in the browser, or 'global' on the server.
	var root = this, doc = document, iCat = { version: '1.1.4' };
	
	// Export the ICAT object for **Node.js**
	if(typeof exports!=='undefined'){
		if(typeof module!=='undefined' && module.exports){
			exports = module.exports = iCat;
		}
		exports.ICAT = iCat;
	} else {
		root['ICAT'] = iCat;
	}

	// Compatible plugin for PC
	root['SHIM'] = root['SHIM'] || {};

	// Copies all the properties of s to r.
	// w(hite)l(ist):白名单, ov(erwrite):覆盖
	iCat.mix = SHIM.mix || function(r, s, wl, ov){
		if (!s || !r) return r;
		if (!ov) ov = true;
		var i, p, len;

		if (wl && (len = wl.length)) {
			for (i = 0; i < len; i++) {
				p = wl[i];
				if (p in s) {
					if (ov || !(p in r)) {
						r[p] = s[p];
					}
				}
			}
		} else {
			for (p in s) {
				if (ov || !(p in r)) {
					r[p] = s[p];
				}
			}
		}
		return r;
	};

	// expand the built-in Objects' functions.
	iCat.mix(Array.prototype, {
		//数组中是否包含指定元素
		contains: function(item){
			return this.indexOf(item)==-1? false : true;
		},
		//数组去掉指定元素
		remove: function(item){
			var self = this;
			self.forEach(function(v, i){
				if(v===item){ self.splice(i, 1); }
			});
			return self;
		},
		//数组去重
		unique: function(){
			var self = this, hash = {}, r = [];
			self.forEach(function(v){
				if(!hash[v]){
					r.push(v); hash[v] = true;
				}
			});
			return r;
		}
	});

	/*-------------------------------------------*
	 * The core of ICAT's framework
	 *-------------------------------------------*/
	var _href = location.href;

	// Kinds of modes or judgments
	['Function', 'String', 'Object', 'Array', 'Number', 'Boolean', 'Undefined', 'Null'].forEach(function(v){
		iCat['is'+v] = function(obj){
			return Object.prototype.toString.call(obj) === '[object '+v+']';
		};
	});

	iCat.mix(iCat, {

		DebugMode: /debug/i.test(_href),
		DemoMode: /localhost|demo\.|\/{2}\d+(\.\d+){3}|file:/i.test(_href),
		TestMode: /3gtest\./i.test(_href),
		IPMode: /\/{2}\d+(\.\d+){3}/.test(_href),

		isEmptyObject: function(obj){
			for(var name in obj){ return false; }
			return true;
		},

		isjQueryObject: function(obj){
			return typeof $!=='undefined' && obj instanceof $;
		},

		toArray: SHIM.toArray || function(o){
			return Array.prototype.slice.call(o);
		},
		
		// Handles objects with the built-in 'foreach', arrays, and raw objects.
		foreach: function(o, cb, args){
			var name, i = 0, length = o.length,
				isObj = length===undefined || iCat.isString(o) || iCat.isFunction(o);
			
			if(args){
				if(isObj){
					for(name in o){
						if(cb.apply(o[name],args)===false){
							break;
						}
					}
				} else {
					for(  ; i<length; ){
						if(cb.apply(o[i++],args)===false){
							break;
						}
					}
				}
			} else {
				if(isObj){
					for(name in o){
						if(cb.call(o[name], name, o[name])===false){
							break;
						}
					}
				} else {
					for( ; i<length; ){
						if(cb.call(o[i], i, o[i++])===false){
							break;
						}
					}
				}
			}
		},
		
		// Create Class for the kinds of UI
		Class: function(){
			var argus = arguments,
				len = argus.length;
			if(!len) return;

			if(len===1){
				if(!iCat.isObject(argus[0])) return;
				var cfg = argus[0],
					Cla = function(){cfg.Create.apply(this, arguments);}
				iCat.foreach(cfg, function(k, v){
					if(k!='Create') Cla.prototype[k] = v;
				});
				return Cla;
			} else {
				if(!iCat.isString(argus[0]) && !iCat.isObject(argus[1])) return;
				var claName = argus[0], cfg = argus[1],
					context = argus[2] || root,
					Cla = function(){cfg.Create.apply(this, arguments);};
				iCat.foreach(cfg, function(k, v){
					if(k!='Create') Cla.prototype[k] = v;
				});
				return context[claName] = Cla;
			}
		},
		
		widget: function(name, cfg){
			this.Class(name, cfg, iCat.widget);
		},

		util: function(name, fn){
			if(iCat.isString(name)){
				iCat.util[name] = fn;
			} else {
				iCat.mix(iCat.util, name);
			}
		},

		// iCat或app下的namespace，相当于扩展出的对象
		namespace: function(){
			var a = arguments, l = a.length, o = null, i, j, p;

			for(i=0; i<l; ++i){
				p = ('' + a[i]).split('.');
				o = this;
				for(j = (root[p[0]]===o)? 1:0; j<p.length; ++j){
					o = o[p[j]] = o[p[j]] || {};
				}
			}
			return o;
		},
		
		// create a app for some project
		app: function(name, sx){
			var isStr = iCat.isString(name),
				O = isStr? root[name] || {} : name;

			iCat.mix(O, iCat, ['namespace'], true);
			iCat.mix(O, iCat.isFunction(sx) ? sx() : sx);
			isStr && (iCat.app[name] = root[name] = O);

			return O;
		},

		log: function(msg){
			if(doc.all){
				root.console!==undefined && console.log? console.log(msg) : alert(msg);
			} else {
				try{ __$abc_ICAT(); }
				catch(e){
					var fileline = e.stack.split('\n')[2];
					fileline = fileline.replace(/.*[\(\s]|\).*/g, '').replace(/(.*):(\d+):\d+/g, '$1  line $2:\n');
					console.log(fileline, msg);
				}
			}
		}
	});
}).call(this);

/* util.js # */
(function(iCat, root, doc){

	/* common function */
	iCat.util({
		
		// 元素与选择器是否匹配
		matches: SHIM._matches || function(el, selector){
			if(iCat.isjQueryObject(el)){
				return el.closest(selector).length>0;
			} else {
				//fixed bug:冒泡不能到body以上，会报错(Illegal invocation)
				if(!selector || el==null || el==doc.body.parentNode || el==doc) return;
				var match = doc.documentElement.webkitMatchesSelector;

				if(iCat.isString(selector)){
					return match.call(el,selector);
				} else if(iCat.isArray(selector)){
					for(var i=0, len=selector.length; i<len; i++){
						if(match.call(el,selector[i])) return i;
					}
					return false;
				}
			}
		},

		_sizzle: SHIM._sizzle || function(selector, el){
			return el.querySelectorAll(selector);
		},

		_getSelector: function(s){
			if(!s || !iCat.isString(s)) return s;

			if(/\:[\d\*]+/.test(s)){//带有:num/*的进行处理
				s = s.trim()
					 .replace(/\:([\d\*]+)\s*/g, '|$1|')
					 .replace(/\|$/g, '');
				return s.split('|');
			} else {//正常的selector直接返回
				return s;
			}
		},

		_queryDom: function(repSelector, repIndex, selector, index, cx){
			var result,
				repEls = iCat.toArray(
					iCat.util._sizzle(repSelector, cx || doc)
				);
			
			if(!selector)
				result = repIndex=='*'? repEls : repEls[repIndex];
			else {
				if(repIndex=='*'){
					result = [];
					repEls.forEach(function(el){
						index=='*'?
								result = result.concat(iCat.toArray(iCat.util._sizzle(selector, el)))
								:
								result.push(iCat.util._sizzle(selector, el)[index||0]);
					});
				} else {
					result = iCat.toArray(
						iCat.util._sizzle(selector, repEls[repIndex])
					);
					result = index=='*'? result : result[index||0];
				}
			}

			return result;
		},

		queryOne: function(s, cx){
			if(!s) return [doc][0];
			
			if(iCat.isString(s)){
				s = iCat.util._getSelector(s);

				if(iCat.isString(s)){
					return iCat.toArray(iCat.util._sizzle(s, cx || doc))[0];
				} else {
					if(s.length>4) return;
					return iCat.util._queryDom(s[0], s[1], s[2], s[3], cx);
				}
			} else {//若不是字符串，直接返回
				return s;
			}
		},

		queryAll: function(s, cx){
			if(!s) return [doc];
			
			if(iCat.isString(s)){
				s = iCat.util._getSelector(s);

				if(iCat.isString(s)){
					return iCat.toArray(iCat.util._sizzle(s, cx || doc));
				} else {
					if(s.length>4) return;
					if(s[2]) s[3] = s[3] || '*';
					return iCat.util._queryDom(s[0], s[1], s[2], s[3], cx);
				}
			} else {
				return s;
			}
		},

		waitObj: function(callback, delay){
			delay = delay || 100;
			iCat.__cache_timers = iCat.__cache_timers || {};
			var step = 0,
				key = 'icat_timer'+Math.floor(Math.random()*1000000+1);

			(function(){
				var fn = arguments.callee;
				callback(key, step);
				if(step<delay && !iCat.__cache_timers[key]){
					setTimeout(function(){
						step++;
						fn();
					}, 1);
				}
			})();
		}
	});
})(ICAT, this, document);

/* event.js # */
(function(iCat, root, doc){

	/* 本模块公用方法 */
	iCat.util({
		parentIfText: function(node){
			return 'tagName' in node ? node : node.parentNode;
		},

		bubble: function(node, cb){
			if(!node) return;
			while(node!==doc.body){
				if(cb && iCat.isFunction(cb)){
					if(cb(node)==false) break;
				}
				node = node.parentNode;
			}
		}
	});

	// 创建Event命名空间
	var Event = iCat.namespace('Event');

	iCat.mix(Event, {
		_bindEvent: function(el, type, handler){
			el.events = el.events || {};
			el.types = el.types || [];
			el.events[type] = function(evt){
				evt = evt || window.event;
				evt.target = evt.target || evt.srcElement;
				evt.preventDefault = evt.preventDefault || function(){evt.returnValue = false;};
				evt.stopPropagation = evt.stopPropagation || function(){evt.cancelBubble = true;};
				handler(evt);
			};

			//绑定同el的同type事件，请用type.xxx方式
			!el.types.contains(type) && el.types.push(type);

			if(el.addEventListener)
				el.addEventListener(type.replace(/\..*/g, ''), el.events[type], false);
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

		_execute: function(eType, el, argus){
			iCat.util.bubble(el, function(node, index){
				index = iCat.util.matches(node, Event.__event_selectors);
				var _stopBubble = false;
				if(iCat.isNumber(index)){
					var el = Event.items[Event.__event_selectors[index]];
					eType = eType.replace(/click|tap/gi, 'tap').replace(/moving|swiping/gi, 'swiping');
					if(el.types.contains(eType)){
						iCat.foreach(el.events, function(k, v){
							k = k.replace(/\.\w+/g, '').split('|');
							if(k[0]==eType){
								if(eType=='hover'){
									v[argus].apply(node);
								} else { v.apply(node, argus); }
							}
							if(k[1]==0){//preventDefault - false
								Event.trigger(node, 'click', false, true);
							}
							if(k[2]==1){//stopPropagation - true
								_stopBubble = true;
								return false;
							}
						});
					}
				}
				if(_stopBubble) return false;
			});
		},

		bind: function(el, type, handler){
			if(!el) return;
			el = iCat.util.queryAll(el);
			if(iCat.isjQueryObject(el)){//兼容jquery
				el.bind? el.bind(type, handler) :
							arguments.callee(iCat.util.queryAll(el.selector), type, handler);
			} else {
				el.length===undefined ?
					Event._bindEvent(el, type, handler)
					:
					iCat.foreach(el, function(i,v){
						Event._bindEvent(v, type, handler);
					});
			}
		},

		unbind: function(el, type){
			if(!el) return;
			el = iCat.util.queryAll(el);
			if(iCat.isjQueryObject(el)){//兼容jquery
				el.unbind? el.unbind(type) :
							arguments.callee(iCat.util.queryAll(el.selector), type);
			} else {
				el.length===undefined ?
					Event._unbindEvent(el, type)
					:
					iCat.foreach(el, function(i,v){
						Event._unbindEvent(v, type);
					});
			}
		},

		trigger: function(el, type, bubbles, cancelable){

			if(iCat.isObject(el) && !iCat.isjQueryObject(el)){// 普通对象
				el[type] && el[type].apply(el, bubbles);
				return;
			}

			if(iCat.isjQueryObject(el)) {// jquery对象
				if(el.trigger){
					el.trigger(type);
					return;
				} else
					el = el.get(0);
			}

			if(/\:dg$/i.test(type)){// 事件代理
				type = type.replace(/\:dg$/i, '');
				el = iCat.util.queryOne(el);
				Event._execute(type, el);
			}  else { // 普通元素
				if(!doc.createEvent) return;
				var ev = doc.createEvent('Event');
				ev.initEvent(type, bubbles, cancelable);
				el.dispatchEvent(ev);
			}
		},

		ready: function(){
			var _fn = [],
				_do = function(){
					if(!arguments.callee.done){
						arguments.callee.done = true;
						for(var i=0; i<_fn.length; i++){
							_fn[i]();
						}
					}
				};

			if(doc.readyState){
				(function(){
					if(doc.readyState!=='loading'){
						_do();
					} else {
						setTimeout(arguments.callee, 10);
					}
				})();
			}

			return function(fn){
				if(iCat.isFunction(fn)){
					_fn[_fn.length] = fn;
				}
				return fn;
			};
		}(),

		//存放代理元素的选择器
		__event_selectors: [],

		/*
		 * o可以是<b>单个对象</b>或<b>对象数组</b>
		 * o = {selector:'.cla', type:'click', callback:function(){}, preventDefault:true, stopPropagation:false}
		 * disabled是否不起作用
		 */
		delegate: function(o, disabled){
			if(!o || iCat.isEmptyObject(o) || o==[]) return;
			var arrSele = Event.__event_selectors;
			var objItem = Event.items = Event.items || {};

			if(iCat.isObject(o)){
				if(!arrSele.contains(o.selector) && !disabled) arrSele.push(o.selector);
				o.type = o.type.replace(/click|tap/gi, 'tap').replace(/moving|swiping/gi, 'swiping');

				var el = objItem[o.selector] = objItem[o.selector] || {},
					key = o.type + '|' + (o.preventDefault? 1:0) + '|' + (o.stopPropagation? 1:0);
				el.events = el.events || {};
				el.types = el.types || [];

				el.events[key] = o.callback;
				o.type = o.type.replace(/\..*/g, '');
				!el.types.contains(o.type) && el.types.push(o.type);
				
			} else if(iCat.isArray(o)){
				while(o.length){
					arguments.callee(o[0]);
					o.shift();
				}
			}
		},

		//o = {selector:'#page', type:'click'}
		undelegate: function(o){
			if(!o || iCat.isEmptyObject(o) || o==[]) return;
			var arrSele = Event.__event_selectors;
			var objItem = Event.items = Event.items || {};

			if(iCat.isObject(o)){
				if(!arrSele.contains(o.selector) || !objItem[o.selector]) return;

				arrSele.remove(o.selector);
				var el = objItem[o.selector];
				o.type = o.type.replace(/click|tap/gi, 'tap').replace(/moving|swiping/gi, 'swiping');

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
			} else if(iCat.isArray(o)){
				while(o.length){
					arguments.callee(o[0]);
					o.shift();
				}
			}
		},

		on: function(el, type, handler, pd, sp){
			if(iCat.isString(el) && /\:dg$/i.test(type)){
				type = type.replace(/\:dg$/i, '');
				Event.delegate({selector:el, type:type, callback:handler, preventDefault:pd, stopPropagation:sp});
			} else {
				Event.bind(el, type, handler);
			}
		},

		off: function(el, type){
			if(iCat.isString(el) && /\:dg$/i.test(type)){
				type = type.replace(/\:dg$/i, '');
				Event.undelegate({selector:el, type:type});
			} else {
				Event.unbind(el, type);
			}
		}
	});
	
	Event.ready(function(){
		var touch = {}, touchTimeout,
			supportTouch = 'ontouchstart' in root;

		var start_evt = supportTouch ? 'touchstart' : 'mousedown',
			move_evt = supportTouch ? 'touchmove' : 'mousemove',
			end_evt = supportTouch ? 'touchend' : 'mouseup',
			cancel_evt = supportTouch ? 'touchcancel' : 'mouseout';

		var bodyNode = iCat.pageBody = iCat.util.queryOne('*[data-pagerole=body]'),
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

		if(!bodyNode) return;

		// start
		Event.on(bodyNode, start_evt, function(evt){
			//evt.preventDefault(); //fixed bug: 以下事件加上阻止默认，会引起无法滑动滚动条
			evt.stopPropagation();
			if(evt.button && evt.button===2) return;

			var page = supportTouch? evt.touches[0] : evt;
			now = Date.now();
			delta = now - (touch.last || now);
			touch.el = iCat.util.parentIfText(evt.target);
			touchTimeout && clearTimeout(touchTimeout);

			touch.x1 = page.pageX;
			touch.y1 = page.pageY;
			touch.isScrolling = undefined;

			if(delta>0 && delta<=250) touch.isDoubleTap = true;
			touch.last = now;
			Event._execute('hover', touch.el, 0);

			longTapTimeout = setTimeout(function(){
					longTapTimeout = null;
					if(touch.last){
						Event._execute('longTap', touch.el);
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
				Event._execute('swiping', touch.el, [touch.x1, touch.x2, touch.y1, touch.y2]);
			}
		});

		// end
		Event.on(bodyNode, end_evt, function(evt){
			evt.stopPropagation();
			if(evt.button && evt.button===2) return;
			Event._execute('hover', touch.el, 1);
			cancelLongTap();
			
			if(!touch.isScrolling){
				if(touch.isDoubleTap){// double tap (tapped twice within 250ms)
					Event._execute('doubleTap', touch.el);
					touch = {};
				} else if('last' in touch){
					if((touch.x2&&Math.abs(touch.x1-touch.x2)<20) || (touch.y2&&Math.abs(touch.y1-touch.y2)<20)){
						Event._execute('tap', touch.el);
					}

					touchTimeout = setTimeout(function(){
						touchTimeout = null;
						Event._execute('singleTap', touch.el);
						touch = {};
					}, 250);
				} else if((touch.x2&&Math.abs(touch.x1-touch.x2)>30) || (touch.y2&&Math.abs(touch.y1-touch.y2)>30)){
					var swipe = 'swipe' + swipeDirection(touch.x1, touch.x2, touch.y1, touch.y2);
					Event._execute(swipe, touch.el);
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

		// Stops the default click event
		Event.on(bodyNode, 'click', function(evt){
			var el = iCat.util.parentIfText(evt.target);
			if(!el || el==doc.body) return;

			iCat.util.bubble(el, function(node, ret){
				ret = iCat.util.matches(node, Event.__event_selectors);
				if(iCat.isNumber(ret)){
					evt.preventDefault();
					//evt.stopPropagation();
				}
			});
		});
	});
})(ICAT, this, document);

/* mvc.js # */
(function(iCat, root, doc){

	/* 本模块公用方法 */
	iCat.util({
		lazyLoad: function(pNode, t){
			if(!pNode) return;
			pNode = iCat.util.queryOne(pNode);
			var imgs = iCat.toArray(
				iCat.util.queryAll('img[src$="blank.gif"]', pNode)
			);
			
			t = t || 500;
			setTimeout(function(){
				imgs.forEach(function(o){
					var src = o.getAttribute('data-src');
					iCat.__cache_images = iCat.__cache_images || {};
					if(!src) return;

					if(!iCat.__cache_images[src]){
						var oImg = new Image(); oImg.src = src;
						oImg.onload = function(){
							o.src = src;
							iCat.__cache_images[src] = true;
							oImg = null;
						};
					} else {
						o.src = src;
					}
					o.removeAttribute('data-src');
				});
			}, t);
		},

		//根据tempId获得模板函数
		_fnTmpl: function(tempId){
			tempId = iCat.isString(tempId)? tempId.trim() : tempId;

			var cacheTmpls = iCat.__cache_tmpls = iCat.__cache_tmpls || {};
			var cacheFuns = iCat.__cache_funs = iCat.__cache_funs || {};
			var _fn, sTmpl;

			// cacheTmpls的解析
			if(iCat.isEmptyObject(cacheTmpls)){
				iCat.foreach(iCat.app, function(k, v){
					if(this.template){
						iCat.foreach(this.template, function(k, v){
							cacheTmpls[k] = v.replace(/[\r\t\n]/g, '');
						});
					}
				});
			}

			// tempId的解析
			if(cacheFuns[tempId]){// 已有模板函数
				_fn = cacheFuns[tempId];
			} else if(cacheTmpls[tempId]) {// 已有模板字符串
				_fn = iCat.util._tmpl( tempId, undefined, cacheTmpls[tempId] );
				cacheFuns[tempId] = _fn;
			} else if(iCat.isjQueryObject(tempId)){// jquery对象
				_fn = iCat.util._tmpl( tempId, undefined, tempId.html() );
				cacheFuns[tempId] = _fn;
			} else if(iCat.isString(tempId) || iCat.isObject(tempId)){// dom/选择器/id
				var el = iCat.isObject(tempId)?
						tempId : /[\.#]/.test(tempId)?
							iCat.util.queryOne(tempId) : doc.getElementById(tempId);
				_fn = iCat.util._tmpl(
					tempId, undefined, el? el.innerHTML : ''
				);
				cacheFuns[tempId] = _fn;
				cacheTmpls[tempId] = sTmpl;
			}

			return _fn;
		},

		_tmpl: function(tempId, data, strTmpl){
			if(!tempId) return;

			var cacheFuns = iCat.__cache_funs = iCat.__cache_funs || {},
				fnEmpty = function(){return '';},
				fBody;
			if(cacheFuns[tempId]){
				return data? cacheFuns[tempId](data) : cacheFuns[tempId];
			} else {
				if(!strTmpl) return fnEmpty;
				strTmpl = strTmpl.replace(/[\r\t\n]/g, '');
				fBody = "var __p_fun = [];with(jsonData){" +
							"__p_fun.push('" + strTmpl.replace(/<%=(.*?)%>/g, "',$1,'").replace(/<%(.*?)%>/g, "');$1__p_fun.push('") + "');" +
						"};return __p_fun.join('');";
				
				cacheFuns[tempId] = new Function("jsonData", fBody);
				return data? cacheFuns[tempId](data) : cacheFuns[tempId];
			}
		},

		/*
		 * hooks = '.xxx#aaa.yyy.zzz' | ['.aaa.bbb#ccc', 'data-ajaxUrl~http://www.baidu.com']
		 */
		_joinHook: function(hooks, el){
			if(!hooks || !el) return;
			hooks = iCat.isArray(hooks)? hooks : [hooks];
			hooks.forEach(function(v){
				if(!v) return;

				if(/\w*~.*/.test(v)){
					v = v.split('~');
					el.setAttribute(v[0]/*.replace(/^(\s|data-)?/, 'data-')*/, v[1]);
				} else {
					if(!/[#\.]/.test(v)) return;
					v = v.replace(/\s+/g, '')
						 .replace(/#(\w+)/g, ',$1,').replace(/,+/g, ',')
						 .replace(/^,|,$/g, '')
						 .split(',');

					var oldClass = (el.className || '').trim().split(/\s+/);
					v.forEach(function(s){
						if(s.indexOf('.')==-1){
							el.id = s;
						} else {
							el.className = s.split('.').concat(oldClass).unique().join(' ').trim();
						}
					});
				}
			});
		},

		/*
		 * & : 本父层
		 * &>2(:*) : 第三层<所有子元素>
		 * &>1:.item : 第二层<.item子元素>
		 * &>1:3 : 第二层<子元素>的第四个元素
		 * &<0 : 第一层<父元素>
		 */
		_getWalker: function(s){
			if(!/^&[<>]\d+/.test(s)) return;
			return s.indexOf('>')!=-1?
				{'c': s.replace(/^&>(\d+\:?[\d\w\.\*#]*).*/, '$1').split(':')} : {'p': s.replace(/^&<(\d+).*/, '$1')};
		},

		/*
		 * o = {'c': ['0','1']}
		 * o = {'c': ['1', '*']} = {'c': ['1']}
		 * o = {'p': '1'}
		 */
		_walker: function(o, ref){
			if(o.c){
				var a = parseInt(o.c[0]), b = o.c[1] || '*',
					isSelector = /[#\.\*]/.test(b),
					filter = null;
				b = isSelector? b : parseInt(b);
				ref = ref[0];//指向创建的div
				if(!ref) return;

				if(a==0){
					return isSelector? iCat.toArray(ref.children).filter(function(v){
						return iCat.util.matches(v, b);
					}) : ref.children[b];
				} else {
					var walker = doc.createTreeWalker(ref, NodeFilter.SHOW_ELEMENT, filter, false),
						cp, cnodes;
					for(var i=0; i<a; i++){
						if(i==a-1){
							cp = walker.nextNode();
						} else {
							walker.nextNode();
						}
					}
					if(isSelector){
						cnodes = [];
						while(cp){
							iCat.foreach(cp.children, function(i, v){
								if(iCat.util.matches(v, b)){
									cnodes.push(v);
								}
							});
							cp = walker.nextSibling();
						}
					} else {
						cnodes = cp.children;
					}
					return isSelector? cnodes : cnodes[b];
				}

				
			} else {
				var num = parseInt(o.p) + 1;
				return function(){
					var px = ref[1];//指向父层
					if(!px) return;

					for(var i=0; i<num; i++){
						px = px.parentNode;
						if(px===doc.body) break;
					}
					return px;
				}();
			}
		},

		/*
		 * tempId可以是字符串，jquery对象/dom对象
		 * clear表示是否先清空再渲染
		 */
		render: function(tempId, data, clear){
			if(iCat.isString(tempId))
				tempId = tempId.trim();

			if(data){
				var parentNode = data.ICwrap,
					html = iCat.util._fnTmpl(tempId)(data),
					o = doc.createElement('div'),
					itemNodes;
				
				o.innerHTML = html;
				if(data.IChooks){//js钩子
					iCat.foreach(data.IChooks, function(k, arrHook){
						k = iCat.util._getWalker(k);
						if(!k) iCat.util._joinHook(arrHook, parentNode);
						else {
							var nodes = iCat.util._walker(k, [o, parentNode]);
							if(!nodes) return;
							nodes.length===undefined?
								iCat.util._joinHook(arrHook, nodes) : 
								nodes.forEach(function(node){
									iCat.util._joinHook(arrHook, node);
								});
						}
					});
				}
				html = o.innerHTML;
			} else {
				// 如果没有数据，返回模板函数
				return iCat.util._fnTmpl(tempId);
			}

			// 如果没有父层，返回html字符串
			if(!parentNode) return html;
			
			if(clear){//辞旧
				var oldNodes = parentNode.childNodes;
				while(oldNodes.length>0){
					parentNode.removeChild(oldNodes[0]);
				}
			}

			itemNodes = o.childNodes;
			while(itemNodes.length>0){//迎新
				parentNode.appendChild(itemNodes[0]);
			}

			// 图片默认惰性加载
			iCat.util.lazyLoad(parentNode);
			o = null;

			// 回调函数
			if(data.callback)
				data.callback(parentNode);
		},

		/*
		 * 一个参数时表示取数据(同规则：storage, cookie)
		 * 两个及以上的参数时表示存数据
		 */
		storage: function(){
			if(!arguments.length || !window.localStorage || !window.sessionStorage) return;
			
			var ls = window.localStorage,
				ss = window.sessionStorage;
			if(arguments.length==1){
				var dname = arguments[0];
				return iCat.isString(dname)? ( ls.getItem(dname) || ss.getItem(dname) ) : '';
			} else {
				var dname = arguments[0],
					data  = arguments[1],
					shorttime = arguments[2];
				if(iCat.isString(dname)){
					var s = shorttime? ss : ls;
					s.removeItem(dname);
					s.setItem(dname, iCat.isObject(data)? JSON.stringify(data) : data);
				}
			}
		},

		clearStorage: function(dname){
			if(!dname || !window.localStorage || !window.sessionStorage) return;

			var ls = window.localStorage,
				ss = window.sessionStorage;
			if(dname==ls || dname==ss){
				dname.clear();
			} else {
				if(ls[dname]) ls.removeItem(dname);
				if(ss[dname]) ss.removeItem(dname);
			}
		},

		cookie: function(){
			if(!arguments.length) return;

			if(arguments.length==1){
				var dCookie = doc.cookie;
				if(dCookie.length<=0) return;

				var cname = arguments[0],
					cStart = dCookie.indexOf(cname+'=');
				if(cStart!=-1){
					cStart = cStart + cname.length + 1;
					cEnd   = dCookie.indexOf(';', cStart);
					if(cEnd==-1) cEnd = dCookie.length;
					return unescape(dCookie.substring(cStart,cEnd));
				}
			} else {
				var cname = arguments[0], val = arguments[1], seconds = arguments[2] || 60,
					exdate = new Date(), expires = '';
				exdate.setTime( exdate.getTime()+(seconds*1000) );
				expires = '; expires='+exdate.toGMTString();
				doc.cookie = cname + '=' + escape(val) + expires + '; path=/';
			}
		},

		clearCookie: function(cname){
			iCat.View.cookie(cname, '', -1);
		},

		makeWrap: function(s, pNode){
			if(!s) return;

			if(iCat.isString(s)){
				var o = doc.createElement('div'),
					exp;
				if(s.indexOf('~')>=0){
					s = s.split('~');
					pNode = iCat.util.queryOne(s[0]);
					exp = s[1];
				} else {
					pNode = pNode || doc.body;
					exp = s;
				}
				
				var c = exp.trim().split('*'),
					cSelector = c[0],
					num = c[1] || 1,
					shtml = '',

					strHtml = cSelector
								.replace(/(\w+)([\.\#\w\-\d]+)/, '<$1$2></$1>')
								.replace(/\.([\.\w\-\d]+)/g, ' class="$1"').replace(/\./g, ' ')
								.replace(/\#([\w\-\d]+)/g, ' id="$1"');

				for(var i=0; i<num; i++){ shtml += strHtml; }
				o.innerHTML = shtml;
				itemNodes = o.childNodes;
				while(itemNodes.length>0){ pNode.appendChild(itemNodes[0]); }
			} else {
				var fn = arguments.callee;
				s.forEach(function(v){
					fn(v);
				});
			}
		},

		fullUrl: function(url, argu){//isAjax/bi
			var url = url || '',
				bi = iCat.isString(argu)? argu : '',
				isAjax = iCat.isBoolean(argu)? argu : false;

			url = url.replace(/^\//g, '');

			if(iCat.DemoMode && url!==''){
				url = url.indexOf('?')<0? (url+'.php') : url.replace(/(\?)/g, '.php$1');
			}
			if(!isAjax && bi){
				url = url + (url.indexOf('?')<0? '?':'&') + bi;
			}

			return iCat.PathConfig.pageRef + url;
		}
	});

	/*
	 * view-module职责：
	 * - 初始化页面刚进入时的模板函数（及数据），渲染模块
	 * - 接收controller传递过来的数据，并更新渲染模块
	 * - 获取用户‘输入的表单数据’，传递给controller
	 * - 扩展实例化后对象的方法
	 */
	
	function View(tempId, initData){
		var _self = this;
		
		_self.tempId = tempId;

		_self._render = function(data, clear){
			iCat.util.waitObj(function(k){
				var pNode = iCat.util.queryOne(data.ICwrap);
				if(!pNode){
					iCat.__cache_timers[k] = false;
					return;
				}

				iCat.__cache_timers[k] = true;
				data.ICwrap = pNode;
				iCat.View.render(_self.tempId, data);

				// 包含表单
				var form = /form/i.test(pNode.tagName)?
						pNode : iCat.util.queryOne('form', pNode);
				if(form){
					_self.getData = function(format){
						format = format || 'string';
						var jsonFormat = /json/i.test(format),
							argus = jsonFormat? {} : '';

						iCat.toArray(form.elements).forEach(function(el){
							var key = el.getAttribute('name'), value = el.value;
							if(key){
								jsonFormat?
									argus[key] = value : argus += '&' + key + '=' + value;
							}
						});
						return jsonFormat? argus : argus.replace(/^&/, '');
					}
				}
			});
		};

		if(initData){
			_self._render(initData);
		}
	}
	View.prototype = {

		addItem: function(d){
			this._render(d);
		},

		setData: function(d){
			this._render(d, true);
		},

		extend: function(o){
			iCat.mix(this, o);
		}
	};

	//对外接口
	iCat.View = function(module, data){
		if(!module) return;

		if(!iCat.View[module]){
			iCat.View[module] = new View(module);
		}
		if(data){
			iCat.View[module].setData(data);
		}
		return iCat.View[module];
	};

	iCat.mix(iCat.View, {
		render: iCat.util.render,
		destroy: function(modules){
			if(!modules) return;

			modules = iCat.isString(modules) ? [modules] : modules;
			modules.forEach(function(v){
				delete iCat.View[v];
			});
		}
	});

	/*
	 * model-module职责：
	 * - 处理controller传递过来的数据，进行封装返回
	 * - 处理数据层面的业务逻辑，进行封装返回
	 * - 按需存取数据
	 * - 扩展实例化后对象的方法
	 */
	function Model(module, initData){
		this.module = module;
		this.initData = initData;
	}
	Model.prototype = {

		getInitData: function(dname){
			return this.initData[dname || ''];
		},

		fetchData: function(){},

		storeData: function(){},
		
		extend: function(o){
			iCat.mix(this, o);
		}
	};

	//对外接口
	iCat.Model = function(module, data){
		if(!module) return;

		if(!iCat.Model[module]){
			iCat.Model[module] = new Model(module, data);
		} else {
			iCat.Model[module].initData = data || iCat.Model[module].initData;
		}
		return iCat.Model[module];
	};
	
	iCat.mix(iCat.Model, {
		
		storage: iCat.util.storage,

		clearStorage: iCat.util.clearStorage,

		cookie: iCat.util.cookie,

		clearCookie: iCat.util.clearCookie,

		destroy: function(modules){
			if(!modules) return;
			modules = iCat.isString(modules) ? [modules] : modules;
			modules.forEach(function(v){
				delete iCat.Model[v];
			});
		}
	});

	/*
	 * controller-module职责：
	 * - 响应用户动作，调用对应的View和Model
	 * - 在View/Model之间传递数据
	 * - 如果是apk，添加或调用硬件接口
	 * - 扩展实例化后对象的方法
	 */
	
	var Event = iCat.Event;

	// 创建Observer-Controller类
	function Controller(module){
		this.selectors = [];
		this.module = module;
	}
	Controller.prototype = {
		subscribe: function(o){//o同Event.delegate
			var _self = this;
			o = iCat.isArray(o)? o : [o];
			o.forEach(function(item){
				Event.delegate(item, true);
				if(!_self.selectors.contains(item.selector)){
					_self.selectors.push(item.selector);
				}
			});
		},

		unsubscribe: function(o){//o同Event.undelegate
			var _self = this;
			o = iCat.isArray(o)? o : [o];
			o.forEach(function(item){
				Event.undelegate(item);
				if(_self.selectors.contains(item.selector)){
					_self.selectors.remove(item.selector);
				}
			});
		},

		addEvents: function(events){
			this.subscribe(events);
		},

		removeEvents: function(events){
			this.unsubscribe(events);
		},

		extend: function(o){
			iCat.mix(this, o);
		}
	};

	//对外接口
	iCat.Controller = function(module, events){
		if(!module) return;

		if(!iCat.Controller[module]){
			iCat.Controller[module] = new Controller(module);
		}
		if(iCat.isFunction(events)){
			events(iCat.Controller[module]);
		} else {
			iCat.Controller[module].subscribe(events);
		}
		return iCat.Controller[module];
	};

	//销毁实例化对象
	iCat.mix(iCat.Controller, {
		addCurrent: function(modules, callback){
			if(!modules) return;
			modules = iCat.isString(modules) ? [modules] : modules;
			modules.forEach(function(v){
				if(iCat.Controller[v]){
					Event.__event_selectors = Event.__event_selectors.concat(iCat.Controller[v].selectors);
				}
			});
			if(callback && iCat.isFunction(callback)){
				callback();
			}
		},

		destroy: function(modules){
			if(!modules) return;
			modules = iCat.isString(modules) ? [modules] : modules;
			modules.forEach(function(v){
				if(iCat.Controller[v]){
					iCat.Controller[v].selectors.forEach(function(v){
						delete Event.items[v];
						Event.__event_selectors.remove(v);
					});
				}
				delete iCat.Controller[v];
			});
		}
	});
})(ICAT, this, document);

/* load.js # */
(function(iCat, root, doc){

	// 本模块公用方法
	var _loadedGroup = {}, _modGroup = {}, _fnLoad;
	iCat.util({
		getCurrentJS: function(){
			var scripts = doc.getElementsByTagName('script');
			return scripts[scripts.length-1];
		},

		/*
		 * type1: 参照sys目录
		 * type2: 参照页面根目录
		 * type3: 参照main.js目录
		 * type4: 网址
		 */
		_dealURL: function(arr, isSingle){//isSingle表示强制单个加载
			if(!arr.length) return;
			if(arr.length===1) isSingle = true;

			var newArr, urlArr = [],
				_notUrl = function(s){
					var v = s,
						isConcat = iCat.PathConfig._isConcat && !isSingle ? '_' : '';
					if(/^\.{1,}\//.test(v)){//type3
						v = /^\.\//.test(v) ?
								v.replace(/^\.\//g, iCat.PathConfig[isConcat+'appRef']) :
								v.replace(/^\.{2}\//g, iCat.PathConfig[isConcat+'appRef'].replace(/\w+\/$/g,''));
					}
					else if(/^\//.test(v)){//type2
						v = v.replace(/^\//g, iCat.PathConfig.pageRef);
					} else {//type1
						v = iCat.PathConfig[isConcat+'sysRef'] + v;
					}

					return v;
				};
			
			if(iCat.PathConfig._isConcat && !isSingle){
				newArr = '';
				iCat.DebugMode ?
					arr.forEach(function(v){
						v = v.replace(/\?.*/, '');
						if(/^(http|ftp|https):\/\//i.test(v))//type4
							urlArr.push(
								v.indexOf('!')>=0 ?
									v.replace(/\!/g,'') :
									v.replace(/(\.source)?(\.(js|css))/g,'.source$2')
							);
						else {
							if(/^\//.test(v)){
								v = _notUrl(v);
								urlArr.push(
									v.indexOf('!')>=0 ?
										v.replace(/\!/g,'') :
										v.replace(/(\.source)?(\.(js|css))/g,'.source$2')
								);
							} else {
								v = _notUrl(v);
								newArr += (v.indexOf('!')>=0? v.replace(/\!/g,'') : v.replace(/(\.source)?(\.(js|css))/g,'.source$2')) + ',';
							}
						}
					})
					:
					arr.forEach(function(v){
						v = v.replace(/\?.*/, '');
						v = v.replace(/\!/g,'');
						if(/^(http|ftp|https):\/\//i.test(v))//type4
							urlArr.push(v);
						else {
							if(/^\//.test(v)){
								urlArr.push(_notUrl(v));
							} else {
								newArr += _notUrl(v) + ',';
							}
						}
					});

				newArr = iCat.PathConfig._webRoot + newArr.replace(/,$/g, '');
				return [newArr].concat(urlArr);
			} else {
				newArr = [];
				iCat.DebugMode ?
					arr.forEach(function(v){
						v = v.replace(/\?.*/, '');
						if(/^(http|ftp|https):\/\//i.test(v))//type4：网址
							newArr.push(
								v.indexOf('!')>=0 ?
									v.replace(/\!/g,'') :
									v.replace(/(\.source)?(\.(js|css))/g,'.source$2')
							);
						else {
							v = _notUrl(v);
							newArr.push(
								v.indexOf('!')>=0 ?
									v.replace(/\!/g,'') :
									v.replace(/(\.source)?(\.(js|css))/g,'.source$2')
							);
						}
					})
					:
					arr.forEach(function(v){
						v = v.replace(/\?.*/, '');
						v = v.replace(/\!/g,'');
						if(/^(http|ftp|https):\/\//i.test(v))//type4：网址
							newArr.push(v);
						else {
							newArr.push(_notUrl(v));
						}
					});

				return newArr;
			}
		},

		_blockImport: function(file){
			var url = file,
				_url = url.indexOf('#')>0?
					url.replace(/(#.*)/, iCat.PathConfig.timestamp+'$1') : (url + iCat.PathConfig.timestamp);

			if(_loadedGroup[_url]) return;
			
			var type = url.replace(/.*\./g,''),
				isCSS = type=='css', tag = isCSS? 'link':'script',
				attr = isCSS? ' type="text/css" rel="stylesheet"' : ' type="text/javascript"',
				path = (isCSS? 'href':'src') + '="'+_url+'"';
			doc.write('<'+tag+attr+path+(isCSS? '/>':'></'+tag+'>'));
			_loadedGroup[url] = true;
		},

		_unblockImport: function(option){
			//增加时间戳
			var	_url = option.file.indexOf('#')>0?
					option.file.replace(/(#.*)/, iCat.PathConfig.timestamp+'$1') : (option.file + iCat.PathConfig.timestamp);
			
			if(_loadedGroup[option.file]){
				if(option.callback && iCat.isFunction(option.callback))
					option.callback(option.context || iCat);
				
				if(option.modName){
					_modGroup[option.modName] = true;
				}
				return;
			}
			
			var node, type = option.file.replace(/.*\./g,'');
			if(type==='css'){
				node = doc.createElement('link');
				node.setAttribute('type', 'text/css');
				node.setAttribute('rel', 'stylesheet');
				node.setAttribute('href', _url);
			} else if(type==='js'){
				node = doc.createElement('script');
				node.setAttribute('type', 'text/javascript');
				node.setAttribute('src', _url);
				node.setAttribute('async', true);
			}
			
			if(!node) return;
			
			iCat.util.waitObj(function(k){
				var pNode = doc.body || doc.getElementsByTagName('body')[0];
				if(!pNode){
					iCat.__cache_timers[k] = false;
					return;
				}

				iCat.__cache_timers[k] = true;
				
				/* 监听加载完成 */
				if(type==='js'){
					_fnLoad = SHIM._load || function(MG, LG, option, pNode, node, _icat){
						node.onload = function(){
							if(option.callback && _icat.isFunction(option.callback))
								option.callback(option.context || _icat);
							
							if(option.modName){
								MG[option.modName] = true;
							}
							LG[option.file] = true;
						};
						pNode.appendChild(node);
					};
					_fnLoad(_modGroup, _loadedGroup, option, pNode, node, iCat);
				}
				
				/* css不需要监听加载完成*/
				if(type==='css'){
					setTimeout(function(){
						if(option.callback && _icat.isFunction(option.callback))
							option.callback(option.context || _icat);
						
						if(option.modName){
							_modGroup[option.modName] = true;
						}
					},5);
					_loadedGroup[option.file] = true;
					pNode.appendChild(node);
				}
			});
		}
	});

	/*
	 * pageRef:参照页面路径
	 * sysRef:参照icat.js所在的sys目录路径
	 * appRef:参照main.js所在的目录路径
	 * timestamp:时间戳
	 */
	iCat.PathConfig = function(cfg){
		var _curScript = iCat.util.getCurrentJS(),
			src = _curScript.src,
			refSlipt = _curScript.getAttribute('refSlipt') || '';
		
		iCat.PathConfig._isConcat = src.indexOf('??')>=0;
		if(refSlipt && _curScript.baseURI.indexOf(refSlipt)==-1) refSlipt = false;//fixed bug:分隔符在字符串里不存在时

		if(!iCat.PathConfig.appRef){
			var baseURI = (iCat.DemoMode && !refSlipt)?//fixed bug:为了匹配类似/index.php的情况
					_curScript.baseURI+'?' : _curScript.baseURI,
				strExp = iCat.DemoMode? (refSlipt? '('+refSlipt+'/).*' : '(/)([\\w\\.]+)?\\?.*') : '(//[\\w\\.]+/).*',
				regExp = new RegExp(strExp, 'g');
			iCat.PathConfig.pageRef = iCat.PathConfig.pageRef || baseURI.replace(regExp, '$1');
			iCat.PathConfig.weinreRef = iCat.IPMode? baseURI.replace(/(\d+(\.\d+){3}).*/g, '$1:8080/') : '';

			if(iCat.PathConfig._isConcat){
				var arrsrc = src.replace(/(\?{2}|\.js(?=\?))/g, '$1|').split('|'),
					_webRoot = arrsrc[0].replace(/\?+/g,'');
				iCat.PathConfig._webRoot = arrsrc[0];
				iCat.PathConfig.timestamp = arrsrc[2] || '';//fixed bug:时间戳没设置时，会有undefined

				arrsrc[1].split(',').forEach(function(v){
					if(/\/sys\//i.test(v))
						iCat.PathConfig._sysRef = v.replace(/(\/sys\/).*/ig, '$1');
					if(/\/apps\//i.test(v))
						iCat.PathConfig._appRef = v.replace(/(\/)\w+(\.\w+)?\.js/g, '$1');
				});

				iCat.PathConfig.sysRef = (_webRoot+iCat.PathConfig._sysRef).replace(/([^:])\/{2,}/g,'$1/');//fixed bug:把http://变成了http:/
				iCat.PathConfig.appRef = (_webRoot+iCat.PathConfig._appRef).replace(/([^:])\/{2,}/g,'$1/');
			} else {
				if(cfg===true){//初始化设置pageRef,sysRef
					iCat.PathConfig.sysRef = /\/sys\//i.test(src)? src.replace(/(\/sys\/).*/ig, '$1') : src.replace(/(\/)\w+(\.\w+)?\.js(.*)?/g, '$1');
					iCat.PathConfig.timestamp = src.replace(/.*\.js(\?)?/g, '$1');
				} else {//设置appRef
					iCat.PathConfig.appRef = src.replace(/(\/)\w+(\.\w+)?\.js(.*)?/g, '$1');
					if(!iCat.PathConfig.timestamp)
						iCat.PathConfig.timestamp = src.replace(/.*\.js(\?)?/g, '$1');
				}
			}
		}

		if(iCat.isObject(cfg)){
			iCat.mix(iCat.PathConfig, cfg);
		}
	};

	// The first execution 
	iCat.PathConfig(true);

	// support user's config
	iCat.ModsConfig = function(cfg){
		if(iCat.isArray(cfg)){
			iCat.foreach(cfg, function(k, v){
				iCat.ModsConfig[v.modName] = (iCat.ModsConfig[v.modName]||[]).concat(v.paths);
			});
		} else {
			if(cfg.modName && cfg.paths){
				iCat.ModsConfig[cfg.modName] = (iCat.ModsConfig[cfg.modName]||[]).concat(cfg.paths);
			} else {
				iCat.foreach(cfg, function(k, v){
					iCat.ModsConfig[k] = (iCat.ModsConfig[k]||[]).concat(v);
				});
			}
		}
	};

	//对外接口
	iCat.mix(iCat, {

		/* 阻塞式加载文件 */
		inc: function(files){
			if(!files) return;
			files = iCat.isString(files)? [files] : files;
			
			iCat.foreach(iCat.util._dealURL(files), function(i, v){
				if(!v) return;
				iCat.util._blockImport(v);
			});
		},

		/* 加载文件形式：
		 * - 单个文件，支持字符串或文件数组(length为1)
		 * - 多个文件，必须是文件数组
		 * - 参数：options || files, callback, isDepend, isSingle, context
		 */
		include: function(){//加载一个或多个文件
			if(!arguments.length) return;

			if(arguments.length==1){
				var opt = arguments[0];
				if(iCat.isString(opt)) opt = {files:opt};
				if(!iCat.isObject(opt) || !opt.files) return;

				opt.files = iCat.isString(opt.files) ?
								iCat.util._dealURL([opt.files]) : iCat.util._dealURL(opt.files, opt.isSingle);
				
				(function(){
					if(!opt.files.length) return;

					var curJS = opt.files.shift(),
						fn = arguments.callee;
					if(opt.files.length){
						if(opt.isDepend)//文件间有依赖 顺序加载
							iCat.util._unblockImport({
								file: curJS,
								callback: function(){
									fn(opt.files);//next
								},
								context: opt.context
							});
						else {
							iCat.util._unblockImport({
								file: curJS,
								context: opt.context
							});
							fn(opt.files);//next
						}
					} else {
						iCat.util._unblockImport({
							file: curJS,
							callback: opt.callback,
							context: opt.context
						});
					}
				})();
			} else {
				arguments.callee({
					files: arguments[0],
					callback: arguments[1],
					isDepend: arguments[2],
					isSingle: arguments[3],
					context: arguments[4]
				});
			}
		},
		
		/* 加载文件形式：
		 * - 单个文件，支持字符串或文件数组(length为1)
		 * - 多个文件，必须是文件数组
		 * - 参数：options || modName, files, callback, isSingle, context
		 */
		require: function(){//加载有依赖的模块
			if(!arguments.length) return;

			if(arguments.length==1){
				var opt = arguments[0];
				if(!iCat.isObject(opt) || !(opt.files = opt.files || iCat.ModsConfig[opt.modName])) return;

				opt.files = iCat.isString(opt.files) ?
								iCat.util._dealURL([opt.files]) : iCat.util._dealURL(opt.files, opt.isSingle);
			
				if(_modGroup[opt.modName]){
					if(opt.callback)
						opt.callback(opt.context);
				} else {
					(function(){
						if(!opt.files.length) return;

						var curJS = opt.files.shift(),
							fn = arguments.callee;
						if(opt.files.length){
							iCat.util._unblockImport({
								file: curJS,
								callback: function(){fn(opt.files);},
								context: opt.context,
								modName: opt.modName
							});
						} else {
							iCat.util._unblockImport({
								file: curJS,
								callback: opt.callback,
								context: opt.context,
								modName: opt.modName
							});
						}
					})();
				}
			} else {
				arguments.callee({
					modName: arguments[0],
					files: arguments[1],
					callback: arguments[2],
					isSingle: arguments[3],
					context: arguments[4]
				});
			}
		},
		
		//使用已加载后的模块
		//参数：options || modName, callback, delay, context
		use: function(opt){
			if(!arguments.length) return;

			var i = 0, timer;
			if(arguments.length==1){
				var opt = arguments[0];
				if(!iCat.isObject(opt)) return;

				iCat.util.waitObj(function(k, t){
					if(!_modGroup[opt.modName]){
						iCat.__cache_timers[k] = false;
						if(t==50 && iCat.ModsConfig[opt.modName]){
							iCat.require({
								modName: opt.modName,
								files: iCat.ModsConfig[opt.modName],
								context: opt.context
							});
						}
						return;
					}

					iCat.__cache_timers[k] = true;
					if(opt.callback)
						opt.callback(opt.context);
				});
			} else {
				arguments.callee({
					modName: arguments[0],
					callback: arguments[1],
					delay: arguments[2],
					context: arguments[3]
				});
			}
		}
	});

	//默认模块
	iCat.ModsConfig([
		{
			modName: 'zepto_core',
			paths: ['lib/zepto/src/zepto.js', 'lib/zepto/src/event.js', 'lib/zepto/src/ajax.js', 'lib/zepto/src/fx.js']
		},{
			modName: 'app_mvcBase',
			paths: ['./mvc/template.js', './mvc/initdata.js', './mvc/view.js', './mvc/model.js', './mvc/controller.js']
		}
	]);

	iCat.weinreStart = function(){
		if(!iCat.PathConfig.weinreRef) return;
		var weinrejs = iCat.PathConfig.weinreRef + 'target/target-script-min.js!' + (location.hash || '');
		iCat.include(weinrejs);
	};

	//如果是ip模式，自动调用weinre
	if(iCat.IPMode){
		iCat.weinreStart();
	}
})(ICAT, this, document);