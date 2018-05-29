/*!
 * Copyright 2011~2013, ICAT JavaScript Library v1.1.5
 * https://github.com/valleykid/icat
 *
 * Copyright (c) 2013 valleykid
 * Licensed under the MIT license.
 *
 * @Author valleykiddy@gmail.com
 * @Time 2013-06-02 09:00:00
 */

/* core.js # */
(function(){
	// Create the root object, 'root' in the browser, or 'global' on the server.
	var root = this, doc = document, iCat = { version: '1.1.5' };
	
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
	iCat.Shim = root['SHIM'] || {};

	// jQuery/Zepto coming in
	iCat.$ = root['jQuery'] || root['Zepto'];
	iCat.embedjQuery = function(){
		var $ = iCat.$,
			hasiCatEvent = !!iCat.Event;
		if(!$) return;

		if(!($.Event || $.event)){
			$.Event = function(a){ return a; };
			$.fn.bind = $.fn.on = function(type, callback){
				if(hasiCatEvent) iCat.Event.on(this, type, callback);
			};
			$.fn.unbind = $.fn.off = function(type){
				if(hasiCatEvent) iCat.Event.off(this, type);
			};
			$.fn.trigger = function(type, data){//data目前还没有妥善处理方式
				if(iCat.Event) iCat.Event.trigger(this, type);
			};
		}
	};

	// expand the built-in Objects' functions.
	var ArrPro = Array.prototype;
	ArrPro.contains = function(item){ return this.indexOf(item)<0? false : true; };
	ArrPro.remove = function(item){
		var self = this;
		self.forEach(function(v, i){
			if(v===item){ self.splice(i, 1); }
		});
		return self;
	};
	ArrPro.unique = function(){
		var self = this, hash = {}, r = [];
		self.forEach(function(v){
			if(!hash[v]){
				r.push(v); hash[v] = true;
			}
		});
		return r;
	};

	// Kinds of judgments
	['String', 'Boolean', 'Function', 'Array', 'Object'].forEach(function(v){
		iCat['is'+v] = function(obj){
			if(v==='Array' && Array.isArray) return Array.isArray(obj);
			return Object.prototype.toString.call(obj) === '[object '+v+']';
		};
	});
	iCat.isNull = function(obj){ return obj===null; };
	iCat.isNumber = function(obj){ return !/\D/.test(obj); };
	iCat.isUndefined = function(obj){ return obj===undefined; };
	iCat.isjQueryObject = function(obj){ return !!iCat.$ && (!!obj) && iCat.isFunction(obj.get);/*obj instanceof iCat.$*/ };
	iCat.isEmptyObject = function(obj){ for(var name in obj){return false;} return true; };

	// Kinds of modes
	(function(){
		var href = location.href,
			keyRegs = {
				'DebugMode': /debug/i,
				'DemoMode': /localhost|demo\.|\/{2}\d+(\.\d+){3}|file\:/i,
				'IPMode': /\/{2}\d+(\.\d+){3}/
			};
		for(var k in keyRegs){ iCat[k] = keyRegs[k].test(href); }
	})();

	// Get icat-js and set pageRef & weinreRef
	(function(){
		var scripts = doc.getElementsByTagName('script'),
			curJs = scripts[scripts.length-1],
			pc = iCat.PathConfig = {};

		var baseURI = curJs.baseURI || doc.baseURI || doc.URL,
			refSlipt = curJs.getAttribute('refSlipt') || '';
		//fixed bug:分隔符在字符串里不存在时
		if(refSlipt && baseURI.indexOf(refSlipt)==-1) refSlipt = false;

		var strExp = iCat.DemoMode?
					(refSlipt? '('+refSlipt+'/).*' : '(/)([\\w\\.]+)?\\?.*') : '(//[\\w\\.]+/).*',
			regExp = new RegExp(strExp, 'g');

		baseURI = (iCat.DemoMode && !refSlipt)? baseURI+'?' : baseURI;//fixed bug:加?为了匹配类似/index.php的情况
		pc.pageRef = baseURI.replace(regExp, '$1');
		pc.weinreRef = iCat.IPMode? baseURI.replace(/(\d+(\.\d+){3}).*/g, '$1:8080/') : '';
	})();

	// Copies all the properties of s to r.
	// l(ist):黑/白名单, ov(erwrite):覆盖
	iCat.mix = function(r, s, l, ov){
		if(!s || !r) return r;
		if(iCat.isUndefined(ov)) ov = true;
		var i, p, len, white = true;

		if(l && !Array.isArray(l)){
			l = l.replace(/\s+/g, '').split(',');
			white = false;
		}

		if(l && (len=l.length)){
			if(white){
				for(i=0; i<len; i++){
					p = l[i];
					if(p in s){
						if(ov || !(p in r)){
							r[p] = s[p];
						}
					}
				}
			} else {
				for(p in s){
					if(l.indexOf(p)<0 && (ov || !(p in r))){
						r[p] = s[p];
					}
				}
			}
		} else {
			for(p in s) {
				if(ov || !(p in r)){
					r[p] = s[p];
				}
			}
		}
		return r;
	};

	/*-------------------------------------------*
	 * The core of ICAT's framework
	 *-------------------------------------------*/
	iCat.mix(iCat,
	{
		contains: function(o, p){
			if(iCat.isArray(o)){
				return o.contains(p);
			}
			else if(iCat.isObject(o)){
				return p in o;
			}
			return false;
		},

		toArray: function(oArr){
			var arr = [];
			iCat.foreach(oArr, function(i,v){ arr.push(v); });
			return arr;
		},
		
		// Handles objects with the built-in 'foreach', arrays, and raw objects.
		foreach: function(o, cb, args){
			var name, i = 0, length = o.length,
				isObj = iCat.isUndefined(length) || iCat.isString(o) || iCat.isFunction(o);
			
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
				len = argus.length, cfg, Cla,
				claName, context;
			if(!len) return;

			cfg = argus[1] || argus[0];
			if(!iCat.isObject(cfg)) return;
			Cla = function(){
				if(cfg.Create) cfg.Create.apply(this, arguments);
			};

			if(len>1){
				claName = argus[0];
				context = argus[2] || root;
				if(!iCat.isString(claName)) return;	
			}
			iCat.mix(Cla.prototype, cfg, 'Create');
			if(Cla.prototype.constructor==Object.prototype.constructor){
				Cla.prototype.constructor = Cla;
			}
			return len===1? Cla : (context[claName] = Cla);
		},
		
		widget: function(name, cfg){
			this.Class(name, cfg, iCat.widget);
		},

		util: function(name, fn){
			if(iCat.isString(name)){
				iCat.util[name] = fn;
			} else if(iCat.isFunction(name)){
				iCat.mix(iCat.util, name());
			} else {
				iCat.mix(iCat.util, name);
			}
		},

		rentAjax: function(fnAjax, cfg){
			if(!fnAjax || !iCat.isFunction(fnAjax)) return function(){};
			iCat.util.ajax = function(cfgAjax){
				cfg = cfg || {};
				var _cfg = iCat.mix({}, cfgAjax, 'success, error');
					_cfg = iCat.mix(_cfg, cfg, 'success, error', false);
				_cfg.prevSuccess = cfg.success;
				_cfg.nextSuccess = cfgAjax.success;
				_cfg.prevError = cfg.error;
				_cfg.nextError = cfgAjax.error;
				_cfg.success = function(data){
					if(!data) return;
					data = iCat.isObject(data) || iCat.isArray(data)? data : JSON.parse(data);
					var ret = _cfg.prevSuccess? _cfg.prevSuccess(data) : '';
					if(_cfg.nextSuccess) _cfg.nextSuccess(ret || data);
				};
				_cfg.error = function(){
					var ret = _cfg.prevError? _cfg.prevError() : '';
					if(_cfg.nextError) _cfg.nextError(ret);
				};
				fnAjax(_cfg);
			};
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
					var fileline = e.stack.replace(/\n\s+at\s+<.*>/g, '').split('\n')[2];// fixed bug:Direction on an input element that cannot have a selection.
					fileline = fileline.replace(/.*[\(\s]|\).*/g, '').replace(/(.*):(\d+):\d+/g, '$1  line $2:\n');
					console.log(fileline, msg);
				}
			}
		}
	});

	/*-------------------------------------------*
	 * The common tools of ICAT
	 *-------------------------------------------*/
	iCat.namespace('Once');
	var Sutil = iCat.Shim.util || {};

	//base
	iCat.util(
	{
		/*
		 * t: 多少毫秒执行取图片
		 * imgSelector: 选择器
		 */
		lazyLoad: function(pNode, t, imgSelector){
			if(!pNode) return;

			t = t || 500;
			pNode = iCat.util.queryOne(pNode);
			imgSelector = imgSelector || 'img[src$="blank.gif"]';

			setTimeout(function(){
				iCat.foreach(iCat.util.queryAll(imgSelector, pNode),
					function(k, o){
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
					}
				);
			}, t);
		},

		/*
		 * delay: 停顿多长时间开始加载
		 * step: 每隔多少ms请求一次
		 */
		wait: function(callback, delay, step){
			delay = delay || 100;
			step = step || 1;
			var cacheTimer = iCat.__cache_timers = iCat.__cache_timers || {};
			var steps = 0, fn,
				key = 'icat_timer' + Math.floor(Math.random()*1000000+1);

			(fn = function(){
				callback(key, steps);
				if(steps<delay && cacheTimer[key]===false){
					setTimeout(function(){
						steps = steps + step;
						fn();
					}, step);
				}
			})();
		},

		recurse: function(arr, callback){//递归
			var fn = function(a, cb){
				while(a.length){
					if(cb(a[0])===false) break;
					a.shift(); fn(a, cb);
				}
			};

			iCat.isArray(arr)?
				fn(arr.concat()/*保护原数组*/, callback) : callback(arr);
		},

		fullUrl: function(url, argu){//isAjax|bi
			var url = url || '',
				bi = iCat.isString(argu)? argu : '',
				isAjax = iCat.isBoolean(argu)? argu : false,
				isUrl = /^\w+:\/\//.test(url);

			url = url.replace(/^\//g, '');

			if(iCat.DemoMode && url!=='' && !isUrl){
				url = /[\?#]/.test(url)?
					url.replace(/(\/\w+)([\?#])/g, '$1.php$2') :
						/(\.\w+)$/.test(url)? url : url.replace(/([^\.]\w+)$/g, '$1.php');
			}
			if(!isAjax && bi){
				url = url + (url.indexOf('?')<0? '?':'&') + bi.replace(/[\?&]+/g, '');
			}

			return (isUrl? '' : iCat.PathConfig.pageRef) + url;
		},

		_jsonCompare: function(json1, json2){
			if(!json1 || !json2) return false;
			var _toString = function(json){
				json = iCat.isString(json)? json : JSON.stringify(json);
				json = json.replace(/[\r\t\n\s'"]/g, '');
				return json;
			};
			return _toString(json1) === _toString(json2);
		},

		_str2Hooks: function(str){
			if(!str) return [];

			var s, sid, arrCla = [];
			s = str.match(/(\#[\w\-\d]+)|(\.[\w\-\d]+)/g);
			if(s!=null){
				s.forEach(function(me){
					/^\./.test(me)?
						arrCla.push(me.substring(1)) : (sid = sid || me.substring(1));
				});
			}
			return [sid, arrCla.unique()];
		},

		scroll: Sutil.scroll || function(box, callback){
			var me = iCat.isString(box)? iCat.util.queryOne(box, iCat.elCurWrap) : box,
				o, nodes, isBody;
			if(!me) return;

			me = me.nodeType!==1? doc.body : me;
			nodes = me.children;
			isBody = me===doc.body;
			/*if(nodes.length!==1){
				o = doc.createElement('div');
				while(nodes.length){ o.appendChild(nodes[0]); }
				me.appendChild(o);
			} else {*/
				o = nodes[0];
			//}

			if(!me.bindScroll){
				(isBody? root : me).addEventListener('scroll', function(){
					var boxHeight = iCat.util.outerHeight(isBody? root : me),
						boxScrollTop = me.scrollTop,
						panelHeight = iCat.util.outerHeight(o);
					callback(boxHeight, boxScrollTop, panelHeight);
				}, false);
				me.bindScroll = true;
			}
		}
	});

	iCat.util(
	{
		_matches: Sutil.matches || function(el, selector){
			if(!el || !selector) return false;
			if(el.nodeType!==1 || el==doc.body) return false;//fixed bug:冒泡不能到body以上，会报错(Illegal invocation)

			if(iCat.isjQueryObject(el)){
				return el.closest(selector).length>0;
			} else {
				var match = doc.documentElement.webkitMatchesSelector;
				if(iCat.isString(selector)){
					return match.call(el, selector);
				} else if(iCat.isArray(selector)){
					for(var i=0, len=selector.length; i<len; i++){
						if(match.call(el, selector[i])) return i;
					}
					return false;
				}
			}
		},

		queryAll: Sutil.queryAll || function(selector, context){
			if(!selector) return [];
			return iCat.isString(selector)?
					(context || iCat.elBodyWrap || doc).querySelectorAll(selector) : selector;
		},

		queryOne: Sutil.queryOne || function(selector, context){
			if(iCat.isUndefined(selector)) return;
			if(selector==='') return iCat.elCurWrap;
			if(iCat.isString(selector)){
				selector = /\:[\d]+/.test(selector)?
					selector.replace(/(\:[\d]+).*/g, '$1').split(':') : [selector];
				return iCat.util.queryAll(selector[0], context)[ selector[1] || 0 ];
			} else
				return selector;
		},

		addClass: function(el, cla){
			if(!el) return;
			var arr = el.className? el.className.split(/\s+/) : [];
			if(!arr.contains(cla)) arr.push(cla);
			el.className = arr.join(' ');
		},

		removeClass: function(el, cla){
			if(!el) return;
			var arr = (el.className || '').split(/\s+/);
			if(arr.contains(cla)) arr.remove(cla);
			el.className = arr.join(' ');
		},

		hasClass: function(el, cla){
			if(!el) return false;
			var arr = (el.className || '').split(/\s+/);
			return arr.contains(cla.trim());
		}
	});
	
	iCat.util(
	{
		/*
		 * 一个参数时表示取数据(同规则：storage, cookie)
		 * 两个及以上的参数时表示存数据
		 */
		storage: function(){
			if(!arguments.length || !root.localStorage || !root.sessionStorage) return;
			
			var ls = root.localStorage,
				ss = root.sessionStorage;
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
			if(!dname || !root.localStorage || !root.sessionStorage) return;

			var ls = root.localStorage,
				ss = root.sessionStorage;
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
		}
	});

	// kinds of width & height
	iCat.foreach({Height:'height', Width:'width'}, function(name, type){
		iCat.foreach(
			{padding:'inner'+name, content:type, '':'outer'+name},
			function(defaultExtra, funName){
				iCat.util[funName] = function(elem){
					if(!elem) return 0;
					return (function(el, type){
						var doc;
						if(el===root){
							return el.document.documentElement['client'+name];
						}
						if(el.nodeType===9){
							doc = el.documentElement;
							return Math.max(
								el.body['scroll'+name], doc['scroll'+name],
								el.body['offset'+name], doc['offset'+name],
								doc['client'+name]
							)
						}
						return el['client'+name];
					})(elem);
				};
			}
		);
	});

	//html engine
	iCat.util(function(tools){
		iCat.Class('Tools',
		{
			init: function(){
				var oSelf = this;
				return {
					zenCoding: function(s){
						if(!s) return '';
						if(/(\<[^\>]+\>)/.test(s)) return s;
						return oSelf._bracket(s.replace(/\s*/g, '')).replace(/\&nbsp;/g, '');
					},

					/*
					 * items = 'header#iHeader.hd + div#iScroll'
					 * items = {'module:3': 'header#iHeader.hd + div#iScroll'}
					 * items = {
							'module:2': 'header#iHeader.hd + div#iScroll + div.aaa.bbb + div.ccc'
							'module:3': 'span.aaa.bbb.ccc*9'
						}
					 */
					makeHtml: function(items, pNode, clear, mustAppend){
						if(!items) return;

						if(iCat.isObject(items)){
							iCat.foreach(items, function(k, item){
								oSelf._makeHtml(item, iCat.util.queryOne(k.trim()), clear, mustAppend);
							});
						}
						else if(iCat.isString(items)){
							oSelf._makeHtml(items, iCat.util.queryOne(pNode), clear, mustAppend);
						}
					},

					unwrap: function(el){
						if(!el) return;

						var p = el.parentNode,
							uncla = el.className,
							nodes = el.childNodes;
						while(nodes.length>0){
							if(uncla){
								nodes[0].setAttribute('data-unclass', uncla);
							}
							p.insertBefore(nodes[0], el);
						}
						p.removeChild(el);
					},

					clearHtml: function(pNode){
						var nodes = pNode.childNodes;
						while(nodes.length){
							pNode.removeChild(nodes[0]);
						}
					}
				};
			},

			_tag: function(t){
				if(!t) return '';
				var s = iCat.util._str2Hooks(t),
					sid, arrCla, rpStr;
				
				arrCla = s[1].length? (' class="'+s[1].join(' ')+'"') : '';
				sid = s[0]? (' id="'+s[0]+'"') : '';
				rpStr = /img|input|br|hr/i.test(t)? ('<$1'+sid+arrCla+' />') : ('<$1'+sid+arrCla+'>&nbsp;</$1>');
				return t = t.replace(/^(\w+).*/g, rpStr);
			},

			_repeat: function(s){
				if(!s) return '';

				var oSelf = this;
				if(s.indexOf('*')<0) return oSelf._tag(s);

				s = s.split('*');
				var str = '';
				for(var i=0; i<s[1]; i++){
					str += oSelf._tag(s[0]);
				}
				return str;
			},

			_sibling: function(s){
				if(!s) return '';

				var oSelf = this;
				if(s.indexOf('+')<0){
					if(s.indexOf('*')!=-1)
						return oSelf._repeat(s);
					else if(s.indexOf('>')!=-1)
						return oSelf._stack(s);
					else
						return oSelf._tag(s);
				}

				s = s.split('+');
				var str = '';
				s.forEach(function(v){
					if(v.indexOf('*')!=-1)
						str += oSelf._repeat(v);
					else if(v.indexOf('>')!=-1)
						str += oSelf._stack(v);
					else
						str += oSelf._tag(v);
				});
				return str;
			},

			_stack: function(s){
				if(!s) return '';

				var oSelf = this;
				if(s.indexOf('>')<0){
					if(s.indexOf('*')!=-1)
						return oSelf._repeat(s);
					else if(s.indexOf('+')!=-1)
						return oSelf._sibling(s);
					else
						return oSelf._tag(s);
				}

				s = s.split('>');
				var str = '&nbsp;';
				s.forEach(function(v){
					if(v.indexOf('*')!=-1)
						str = str.replace(/\&nbsp;/g, oSelf._repeat(v));
					else if(v.indexOf('+')!=-1)
						str = str.replace(/\&nbsp;/g, oSelf._sibling(v));
					else
						str = str.replace(/\&nbsp;/g, oSelf._tag(v));
				});

				return str;
			},

			_bracket: function(s){
				if(!s) return '';

				var oSelf = this,
					singlefn = function(sExp){
						if(!/\(|\)/.test(sExp))
							return sExp.indexOf('+')? oSelf._sibling(sExp) : oSelf._stack(sExp);
						
						if(/\+\([^\)]+/.test(sExp)){
							var str = '';
							sExp = sExp.replace(/\+\(([^\)]+)/g, ',$1,');
							sExp = sExp.split(',');
							sExp.forEach(function(v){
								v = v.replace(/\(|\)\+?/g, '');
								str += oSelf._stack(v);
							});
							return str;
						}
					};

				if(/\>\(/.test(s)){
					s = s.replace(/(\>)(\()/g, '$1,$2').split('>,');
					return oSelf._stack(s[0]).replace(/\&nbsp;/g, singlefn(s[1]));
				} else {
					return singlefn(s);
				}
			},

			_makeHtml: function(items, pNode, clear, mustAppend){
				var p = pNode, o, shtml;
				if(!p) return;

				if(clear)
					iCat.util.clearHtml(p);
				if(!p.childNodes.length || mustAppend){//拒绝重复
					shtml = iCat.util.zenCoding(items);
					o = doc.createElement('wrap');
					o.innerHTML = shtml || '';
					itemNodes = o.childNodes;
					while(itemNodes.length>0){ p.appendChild(itemNodes[0]); }
					o = null;
				}
			}
		}, iCat.Once);
		
		tools = new iCat.Once.Tools();
		delete iCat.Once.Tools; 
		return tools.init();
	});
	
	//template engine
	iCat.util(function(tools){
		iCat.Class('Tools',
		{
			init: function(){
				var oSelf = this;
				return {
					/*
					 * cfg = {
					 *     wrap: 父层，没有设置则返回html
					 *     tempId: 模板ID（规则同_fnTmpl）
					 *     hooks: js-hooks，也可以设置伪属性
					 *     multiChild: 父层可非空渲染
					 *     callback: 渲染完成后执行回调函数
					 *     delayTime: 惰性加载img，推迟时间点
					 *     blankPic: 占位图片选择器
					 *     loadCallback: (内部使用)当页面模块化加载时，此为控制函数
					 * }
					 *
					 * before: 是否在旧元素前渲染
					 * clear: 是否先清空再渲染
					 */
					render: function(cfg, data, before, clear){
						if(cfg && data){
							var pWrap = iCat.util.queryOne(cfg.wrap, iCat.elCurWrap);
							iCat.isjQueryObject(pWrap) && (pWrap = pWrap[0]);

							var	o = doc.createElement('wrap'),
								uncla = (cfg.viewId || (iCat.isString(cfg.tempId)? cfg.tempId:'wrap')) + '-loaded',
								oldNodes = iCat.util.queryAll('*[data-unclass='+uncla+']', pWrap),
								isFirst = !oldNodes.length,
								curNode, html = '';

							try {// fixed bug:如果json不符合模版，报错(此问题已解)
								html = oSelf._fnTmpl(cfg.tempId)(data);
							} catch(e){}

							o.style.display = 'block';
							o.className = uncla;
							o.innerHTML = html;
							
							if(cfg.hooks){//js钩子
								iCat.foreach(cfg.hooks, function(k, arrHook){
									k = oSelf._getWalker(k);
									if(!k) oSelf._joinHook(arrHook, pWrap);
									else {
										var nodes = oSelf._walker(k, [o, pWrap]);
										if(!nodes) return;
										nodes.length===undefined?
											oSelf._joinHook(arrHook, nodes) : 
											nodes.forEach(function(node){
												oSelf._joinHook(arrHook, node);
											});
									}
								});
							}
							html = o.innerHTML;
						} else {
							// 如果没有数据，返回模板函数
							return oSelf._fnTmpl(cfg.tempId);
						}

						// 如果没有父层，返回html字符串
						if(!pWrap) return html;
						
						//辞旧
						if(clear || !cfg.multiChild){
							iCat.util.clearHtml(pWrap);
						}

						//迎新
						if(isFirst){
							before?
								pWrap.insertBefore(o, pWrap.firstChild) : pWrap.appendChild(o);
						}
						else {
							if(!pWrap.childNodes.length){
								pWrap.appendChild(o);
							} else {
								pWrap.insertBefore(o, oldNodes[0]);
								for(var i=oldNodes.length-1; i>=0; i--){
									if(!before){
										pWrap.removeChild(oldNodes[i]);
										o.insertBefore(oldNodes[i], o.firstChild);
									}
								}
							}
						}
						curNode = iCat.util.queryOne('.'+uncla, pWrap);
						cfg.loadCallback?
							// fixed bug:当模块html为空时，滑动加载有卡的感觉(加上!html)
							cfg.loadCallback(curNode, !html) : iCat.util.unwrap(curNode);

						// 图片默认惰性加载
						iCat.util.lazyLoad(pWrap, cfg.delayTime, cfg.blankPic);
						o = null;

						// 回调函数
						if(cfg.callback) cfg.callback(pWrap, cfg, data);

						// 包含表单
						var form = /form/i.test(pWrap.tagName) ?
								pWrap : iCat.util.queryOne('form', pWrap);
						if(!form) return;

						return function(format){
							format = format || 'string';
							var jsonFormat = /json/i.test(format),
								argus = jsonFormat? {} : '';

							iCat.foreach(form.elements, function(i, el){
								var key = el.getAttribute('name'), value = el.value;
								if(key){
									jsonFormat?
										argus[key] = value : argus += '&' + key + '=' + value;
								}
							});
							return jsonFormat? argus : argus.replace(/^&/, '');
						};
					},

					/*
					 * cfg = {
					 *      viewId: mvc模式下，每一个view的id
					 *      dataSave: 是否存储数据，默认存入localStorage
					 *      dataKey: 可选，与viewId一起组成keyStorage，没有则只有viewId成为keyStorage
					 *      ajaxUrl: ajax请求地址
					 *      globalKey: 单页面时，全局数据的key
					 *      overwrite: 数据重复时是否覆盖，不覆盖则转为repeatData
					 * }
					 */
					fetch: function(cfg, callback){
						if(!cfg) return;

						var keyStorage,
							IMData = cfg.viewId? iCat.Model.ViewData(cfg.viewId) : {},
							ownData = IMData.ownData,
							online = navigator.onLine==true,
							hasGData = !!cfg.globalKey && !!iCat.Model.GlobalData(cfg.globalKey);

						//兼容old-api
						cfg.dataSave = cfg.isSave!==undefined? cfg.isSave : cfg.dataSave;
						cfg.dataKey = cfg.key!==undefined? cfg.key : cfg.dataKey;
						cfg.overwrite = cfg.repeatOverwrite!==undefined? cfg.repeatOverwrite : cfg.overwrite;

						if(cfg.dataSave){
							cfg.dataKey = cfg.dataKey || '';
							keyStorage = (cfg.viewId || cfg.tempId) + cfg.dataKey;
						}

						if(online && cfg.ajaxUrl && !hasGData){
							oSelf._ajaxFetch(cfg, callback, ownData, keyStorage);
						}
						else {
							hasGData?
								oSelf._globalFetch(cfg, callback, ownData) :
								oSelf._storageFetch(cfg, callback, ownData, keyStorage);
						}
					},

					save: function(cfg, data){//overwrite是否覆盖
						if(iCat.isString(cfg))
							return oSelf._dataSave(cfg, data);

						//兼容old-api
						cfg.dataSave = cfg.isSave!==undefined? cfg.isSave : cfg.dataSave;
						cfg.dataKey = cfg.key!==undefined? cfg.key : cfg.dataKey;
						cfg.overwrite = cfg.repeatOverwrite!==undefined? cfg.repeatOverwrite : cfg.overwrite;

						if(cfg.dataSave){
							cfg.dataKey = cfg.dataKey || '';
							var keyStorage = (cfg.viewId || cfg.tempId) + cfg.dataKey;
							return oSelf._dataSave(keyStorage, data, cfg.overwrite);
						}
					},

					remove: function(key){
						if(!key) return;
						iCat.isArray(key)?
							key.forEach(function(k){ oSelf._dataRemove(k); }) : oSelf._dataRemove(key);
					}
				};
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
					fBody = "var __p_fun = [], _self = jsonData;with(jsonData){" +//typeof $1!='undefined' fixe bug:当json不包含某字段时，整个函数执行异常
								"__p_fun.push('" + strTmpl.replace(/<%=(.*?)%>/g, "',(typeof $1!='undefined'? $1:''),'").replace(/<%(.*?)%>/g, "');$1__p_fun.push('") + "');" +
							"};return __p_fun.join('');";
					
					cacheFuns[tempId] = new Function("jsonData", fBody);
					return data? cacheFuns[tempId](data) : cacheFuns[tempId];
				}
			},

			/*
			 * 根据tempId获得模板函数
			 * tempId可以是字符串ID，jquery对象，dom对象
			 */
			_fnTmpl: function(tempId){
				tempId = iCat.isString(tempId)? tempId.trim() : tempId;

				var cacheTmpls = iCat.__cache_tmpls = iCat.__cache_tmpls || {};
				var cacheFuns = iCat.__cache_funs = iCat.__cache_funs || {};
				var _fn, sTmpl, oSelf = this;

				// cacheTmpls的解析
				if(iCat.isEmptyObject(cacheTmpls)){
					iCat.foreach(iCat.app, function(k, app){
						if(app.template){
							iCat.foreach(app.template, function(k, v){
								cacheTmpls[k] = v.replace(/[\r\t\n]/g, '');
							});
						}
					});
				}

				// tempId的解析
				if(cacheFuns[tempId]){// 已有模板函数
					_fn = cacheFuns[tempId];
				} else if(cacheTmpls[tempId]) {// 已有模板字符串
					_fn = oSelf._tmpl( tempId, undefined, cacheTmpls[tempId] );
					cacheFuns[tempId] = _fn;
				} else if(iCat.isjQueryObject(tempId)){// jquery对象
					_fn = oSelf._tmpl( tempId, undefined, tempId.html() );
					cacheFuns[tempId] = _fn;
				} else if(iCat.isString(tempId) || iCat.isObject(tempId)){// dom/选择器/id
					var el = iCat.isObject(tempId)?
							tempId : /\.|#|\[[\w\$\^\*\=]+\]/.test(tempId)?
								iCat.util.queryOne(tempId) : doc.getElementById(tempId);
					sTmpl = el? el.innerHTML.replace(/[\r\t\n]/g, '').replace(/\s+(<)|\s*$/g, '$1') : '';
					cacheFuns[tempId] = _fn = oSelf._tmpl(tempId, undefined, sTmpl);
					cacheTmpls[tempId] = sTmpl;
				}

				return _fn;
			},

			/*
			 * hooks = '.xxx#aaa.yyy.zzz'
			 * hooks = ['.aaa.bbb#ccc', 'data-ajaxUrl~http://www.baidu.com']
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
						v = iCat.util._str2Hooks(v);
						var oldClass = el.className? el.className.trim().split(/\s+/) : [],
							newClass = v[1].concat(oldClass).unique();
						if(v[0]) el.id = v[0];
						if(newClass.length) el.className = newClass.join(' ');
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
			_walker: Sutil._walker || function(o, ref){
				if(o.c){
					var a = parseInt(o.c[0]), b = o.c[1] || '*',
						isSelector = !iCat.isNumber(b),// fixed bug:非数字即是选择器
						filter = null;
					b = isSelector? b : parseInt(b);
					ref = ref[0];//指向创建的div
					if(!ref) return;

					if(a==0){ 
						if(isSelector){
							var arrNode = [];
							iCat.foreach(ref.children, function(i, v){
								if(iCat.util._matches(v, b)) arrNode.push(v);
							});
							return arrNode;
						} else
							return ref.children[b];
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
									if(iCat.util._matches(v, b)){
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

			_ajaxFetch: function(cfg, callback, ownData, keyStorage){
				if(!iCat.util.ajax && iCat.$) iCat.rentAjax(iCat.$.ajax);
				var oSelf = this;

				cfg.ajaxUrl = iCat.util.fullUrl(cfg.ajaxUrl, true);
				iCat.util.ajax({
					type: 'POST', timeout:10000,
					url: cfg.ajaxUrl,
					cache: false,
					success: function(data){
						var _data = JSON.stringify(data), ret;

						if(cfg.globalKey)
							iCat.Model.GlobalData(cfg.globalKey, data);
						if(keyStorage)
							ret = oSelf._dataSave(keyStorage, _data, cfg.overwrite);
						if(!!ret && iCat.isArray(ret))
							data = { repeatData: ret };
						iCat.mix(data, ownData);
						if(callback) callback(data);
					},
					error: function(){
						oSelf._storageFetch(cfg, callback, ownData, keyStorage);
					}
				});
			},

			_globalFetch: function(cfg, callback, ownData){
				iCat.util.wait(function(k, t){
					var gData = iCat.Model.GlobalData(cfg.globalKey);
					if(!gData){
						iCat.__cache_timers[k] = false;
						return;
					}
					delete iCat.__cache_timers[k];

					iCat.mix(gData, ownData);
					if(callback) callback(gData);
				}, 1000, 10);
			},

			_storageFetch: function(cfg, callback, ownData, keyStorage){
				var data = {}, arr = [];
				if(keyStorage){
					data = iCat.util.storage(keyStorage) || iCat.util.storage(keyStorage+'Repeat') || {};
				}

				if(iCat.isString(data)){
					if(/Repeat_\d+/.test(data)){
						data = data.split(',');
						data.forEach(function(k){
							var item = JSON.parse(iCat.util.storage(k));
							arr.push(item);
						});
						data = { repeatData: arr };
					} else {
						data = JSON.parse(data);
					}
				}

				if(cfg.globalKey){
					if(iCat.isEmptyObject(data)){
						this._globalFetch(cfg, callback, ownData);
						return;
					} else {
						iCat.Model.GlobalData(cfg.globalKey, data);
					}
				}

				iCat.mix(data, ownData);
				if(callback) callback(data);
			},

			_dataSave: function(key, data, overwrite){
				if(!key || !data) return;
				if(iCat.isUndefined(overwrite) || /(Repeat)_\d+/.test(key)) overwrite = true;

				var firstData = iCat.util.storage(key),
					arrKeys = iCat.util.storage(key+'Repeat'),//索引
					repeatKeys = iCat.util.storage('repeatKeys'), _key,
					_repeatStore = function(d, arr){
						if(iCat.isArray(d)){
							iCat.util.clearStorage(key);
							repeatKeys[key] = true;
							iCat.util.storage('repeatKeys', repeatKeys);
							d.forEach(function(v){ _repeatStore(v, arr); });
						} else {
							var prevData = arr[0]? iCat.util.storage(arr[0]) : '';
							if(iCat.util._jsonCompare(prevData, d)) return;//拒绝重复
							var k = key + 'Repeat_' + arr.length + '_' + Math.floor(Math.random()*1000+1);
							arr.unshift(k);
							iCat.util.storage(key+'Repeat', arr.join(','));
							d.rkey = k;
							iCat.util.storage(k, d);
						}
					};
				
				repeatKeys = repeatKeys? JSON.parse(repeatKeys) : {};
				_key = key.replace(/repeat_\d+.*/gi, '');
				if(iCat.isUndefined(repeatKeys[_key])//第一次
					|| (repeatKeys[_key]===false && iCat.isNull(iCat.util.storage(key)))//已被删除
						|| overwrite)//可以覆盖
				{
					iCat.util.storage(key, data);
					if(iCat.isUndefined(repeatKeys[_key])){
						repeatKeys[_key] = false;
						iCat.util.storage('repeatKeys', repeatKeys);
					}
					return;
				}

				if(iCat.util._jsonCompare(firstData, data)) return;//拒绝重复

				data = firstData? [JSON.parse(firstData), data] : data;
				arrKeys = arrKeys? arrKeys.split(',') : [];
				_repeatStore(data, arrKeys);
				return data;
			},

			_dataRemove: function(key){
				if(key.indexOf('Repeat_')>0){
					var indexKey = key.replace(/(Repeat)_\d+.*/g, '$1'),
						arrKeys = iCat.util.storage(indexKey).split(',');
					arrKeys.remove(key);
					iCat.util.storage(indexKey, arrKeys.join(','));
				}
				iCat.util.clearStorage(key);
			}
		}, iCat.Once);

		tools = new iCat.Once.Tools();
		delete iCat.Once.Tools; 
		return tools.init();
	});
	
	// Game over
	delete iCat.Once;
}).call(this);

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

/* mvc.js # */
(function(iCat, root, doc){
	// 创建mvc命名空间
	iCat.namespace('mvc');

	iCat.Class('Tools',
	{
		init: function(){
			var oSelf = this;
			return {
				/*
				 * Class: 父类
				 * option: 继承时被共用的配置
				 */
				inherit: function(Class, option){
					var Cla = function(){
						var argus = iCat.toArray(arguments);
						if(option){
							argus[1] = argus[1] || {};// fixed bug:如果没有第二个参数时，会合并不了
							argus[1].config = argus[1].config || {};
							iCat.mix(argus[1].config, option['config'], undefined, false); //此处false防止公用的config覆盖实例化的config
							iCat.foreach(option, function(k, v){
								// fixed bug:取或(||)时，最后一个config覆盖了前面的config
								if(k!='config' && !iCat.isFunction(v)) argus[1][k] = v;
							});
						}
						var ret = Class.apply(this, argus);
						if(ret) return ret;// fixed bug: 当已有某实例化对象时，应返回它
					};
					iCat.mix(Cla.prototype, Class.prototype);
					if(Cla.prototype.constructor==Object.prototype.constructor){
						Cla.prototype.constructor = Cla;
					}
					if(option){
						iCat.foreach(option, function(k, v){
							if(iCat.isFunction(v)) Cla.prototype[k] = v;
						});
					}
					Cla.__super__ = Class;
					return Cla;
				},

				/*
				 * objHash = {
					'help'                 : 'help',
					'search/:query'        : 'search\/(\w+)',
					'search/:query/p:page' : 'search\/(\w+)\/p(\w+)''
				   }
				 *
				 * return [pid, argus]
				*/
				dealHash: function(s, objHash){
					if(!s) return [''];

					s = s.replace(/\s+/g, '').match(/[^\#]+/g)[0];
					if(s.indexOf('/')<0)
						return [s];
					else {
						if(!objHash) return;
						var _s;
						iCat.foreach(objHash, function(k, fn){
							var _exp = new RegExp('^'+k+'$', 'i'),
								argus = k.match(/\([^\)]+\)/g),
								querys = '', len;
							if(argus && (len=argus.length)){
								argus.forEach(function(v, i){
									querys += '$' + (i+1) + (i==len-1? '':',');
								});
							}
							if(_exp.test(s)){
								s = s.replace(_exp, querys);
								s = s.split(',');
								s.unshift(k);
								_s = s;
								return false;
							}
						});

						return _s || [''];
					}
				},

				addItem: function(vmGroups, curView, model, init){
					var key = curView.viewId;
					if(!vmGroups[key]){
						if(init){//(伪)初始化时
							if(curView.model){
								delete curView.model;
							} else {
								if(!model){
									model = iCat.Model['__page_emptyModel'] || new Model('__page_emptyModel');
								}
							}
							vmGroups[key] = model.modelId;
						}
						else if(model){
							curView.setModel(model);
							vmGroups[key] = model.modelId;
						}
						else {
							vmGroups[key] = '__page_emptyModel';
						}
					}
				},

				removeItem: function(vmGroups, vid){
					if(!iCat.isString(vid) || vid.indexOf(',')>0) return;
					
					if(vmGroups[vid]){
						var events = iCat.Model.ViewData(vid).config.events;
						if(events){
							iCat.util.recurse(events, function(e){
								if(iCat.Event)
									iCat.Event.undelegate(e);
								else if(iCat.$ && iCat.$.event){
									e.type = e.type.replace(/!/g, '')
												   .replace(/(long|single)?tap/gi, 'click');
									iCat.$(iCat.elCurWrap).undelegate(e.selector, e.type);
								}
							});
						}
						delete vmGroups[vid];
					}
				},

				regEvents: function(view, cfg){// bind-events
					if(cfg.events){
						iCat.util.recurse(cfg.events, function(e){
							//此处如果直接e.callback=f，e.callback已被替换，无法找到函数
							var fn = e.callback;
							if(iCat.isString(fn)) fn = view[e.callback];
							e.callback = function(){
								var argus = iCat.toArray(arguments); //step2
									argus.unshift(view, view.model, cfg);//普通方法追加view, model, config
								fn.apply(this, argus);
							};
							if(iCat.Event)
								iCat.Event.delegate(e);
							else if(iCat.$ && iCat.$.event)
								iCat.$(iCat.elCurWrap).delegate(e.selector, e.type.replace(/!/g, ''), e.callback);
						});
					}
				},

				/*
				 * 页面初始化分三种情况：
				 * - 多页面(multiPage)切换：此种情况下，每次都是“真实初始化”
				 * - 单页面单层(singleLayer)：此种情况下，第一次是“真实初始化”，以后是“半伪初始化”
				 * - 单页面多层(multiLayer)切换：此种情况下，第一次是“真实初始化”，以后是“伪初始化”
				 * 
				 * #真实初始化：直接实例化view和model，绑定events，进行页面渲染，不用进行数据比较
				 * #半伪初始化：清空上次的dom和events，进行真实初始化
				 * #伪初始化：清空上次的events，如果是空白层则进行真实初始化，如果非空则选择性渲染
				 */
				multiPage: function(){
					oSelf._baseInit.apply(oSelf, arguments);
				},

				singleLayer: function(c, o){
					// clear
					if(c.modsLoad_mode) delete c.modsLoad_mode;
					if(c.routes.scrollBox) delete c.routes.scrollBox;
					c.vmClear();

					oSelf._baseInit(c, o);
				},

				multiLayer: function(c, o){
					var cfg = c.config,
						wraps = c.pageWraps,
						curGroup = c.wrapGroup, curPid = c.hashArgus[0],
						curCla = cfg.currentCla || 'icat-current-wrap',
						curWrap, page1, page2;
					
					// clear
					if(c.modsLoad_mode) delete c.modsLoad_mode;
					if(c.routes.scrollBox) delete c.routes.scrollBox;
					if(c.endVid) delete c.endVid;
					if(iCat.hashChange) delete iCat.hashChange;
					if(iCat.__back_history) delete iCat.__back_history;

					c.vmClear();

					if(iCat.elCurWrap){
						iCat.util.addClass(iCat.elCurWrap, '__prev_baseBed');
						iCat.util.removeClass(iCat.elCurWrap, curCla);
						delete iCat.elCurWrap;
					}

					// 设置操作层
					if(!curGroup.contains(curPid)){
						if(wraps.length==curGroup.length){
							page1 = iCat.util.queryOne('.__prev_baseBed');
							iCat.util.removeClass(page1, '__prev_baseBed');
							iCat.util.addClass(page1, curCla);
							console.log('The beds are not enough.');
							return;
						}
						iCat.hashChange = true;
						curWrap = iCat.elCurWrap = wraps[curGroup.length];
						curGroup.push(curPid);
					} else {
						curWrap = iCat.elCurWrap = wraps[curGroup.indexOf(curPid)];
						iCat.__back_history = true;
					}
					iCat.util.addClass(curWrap, curCla);

					// 操作层切换动画接口
					page1 = iCat.util.queryOne('.__prev_baseBed');
					page2 = iCat.elCurWrap;
					if(o.switchPage) o.switchPage(page1, page2);
					if(page1) iCat.util.removeClass(page1, '__prev_baseBed');

					oSelf._baseInit(c, o);
				},

				pageLoad: function(c){
					c.pageMods && c.pageMods.length?
						oSelf._modsLoad(c) : oSelf._commLoad(c);
				}
			};
		},

		_baseInit: function(c, o){
			var cfg = c.config,
				curWrap = iCat.elCurWrap;
			if(!curWrap) return;

			if(o.adjustLayout){
				iCat.util.makeHtml(o.adjustLayout, curWrap, iCat.mode_singleLayer);
				delete o.adjustLayout;
			}
			else if(cfg.adjustLayout){
				iCat.util.makeHtml(cfg.adjustLayout, curWrap, iCat.mode_singleLayer);
			}

			// page set
			if(o.baseBed) delete o.baseBed;
			if(cfg.scrollBox) c.routes.scrollBox = cfg.scrollBox;

			if(o.scrollBox){
				c.routes.scrollBox = o.scrollBox;
				delete o.scrollBox;
			}
			if(o.setAjax){
				delete iCat.util.ajax;
				iCat.rentAjax(o.setAjax[0], o.setAjax[1]);
			}
			if(o.modules){
				c.pageMods = o.modules.replace(/\s+/g, '').split(',');// fixed bug:前后有空格，模块加载失败
				delete o.modules;
			}

			if(o.vmGroups) o = o.vmGroups;

			// add init-vm
			c.vmAdd(o, true);
		},

		_modsLoad: function(c){// 模块化加载
			var self = this;

			// type: 0=common, 1=height-load, 2=scroll-load
			var fn = function(type){
				if(!c.pageMods.length || !c.pageMods[0]) return;

				var vid = c.pageMods[0],
					curView = iCat.View[vid], modelId = c.vmGroups[vid],
					IMData = iCat.Model.ViewData(vid),
					cfg = (IMData || {}).config;
				if(!curView || !IMData){// fixed bug:某个模块请求失败，影响后续加载
					c.pageMods.shift();
					fn(c, arguments);
					return;
				}

				if(iCat.__back_history && !c.endVid){
					var els = iCat.util.queryAll('*[data-unclass$=-loaded]', iCat.elCurWrap),
						el = els[els.length-1],
						attr = el.getAttribute('data-unclass');
					c.endVid = attr.replace(/\-loaded/g, '');
				}

				switch(type){
					case 0:
						if(iCat.__back_history){
							curView.setModel(iCat.Model[modelId]);
							c.pageMods.shift();
							if(vid!==c.endVid) fn(0);
						} else {
							cfg.loadCallback = function(node){
								c.pageMods.shift();
								if(node) iCat.util.unwrap(node);
								fn(0);
							};
							curView.setModel(iCat.Model[modelId]);
						}
					break;

					case 1:
						if(iCat.__back_history){
							curView.setModel(iCat.Model[modelId]);
							c.pageMods.shift();
							if(vid!==c.endVid) fn(1);
						} else {
							cfg.loadCallback = function(node){
								c.pageMods.shift();
								if(node) iCat.util.unwrap(node);
								
								var box = iCat.util.queryOne(c.routes.scrollBox);
								box = box.nodeType!==1? doc.body : box;

								var	panel = box.children[0],
									modh = iCat.util.outerHeight(panel),
									boxh = iCat.util.outerHeight(box===doc.body? window : box);
								if(modh<=boxh+50 && c.pageMods.length){
									fn(1);
								} else if(c.pageMods.length){
									iCat.util.scroll(
										c.routes.scrollBox,
										function(slHeight, slTop, spHeight){
											if(!c.pageMods.length) return;
											if(slTop+slHeight+50>=spHeight){
												fn(2);
											}
										}
									);
								}
							};
							curView.setModel(iCat.Model[modelId]);
						}
					break;

					case 2:
						if(curView.loaded==undefined){//拒绝重复
							curView.loaded = false;
							cfg.loadCallback = function(node, blankHtml){
								c.pageMods.shift();
								curView.loaded = true;
								if(node) iCat.util.unwrap(node);
								if(blankHtml) fn(2);
							};
							curView.setModel(iCat.Model[modelId]);
						}
					break;
				}
			};

			if(c.routes.scrollBox){// 滚动加载
				fn(1);
			} else {
				fn(0);
			}
		},

		_commLoad: function(c){// 普通加载
			iCat.foreach(c.vmGroups, function(vid, mid){
				var curView = iCat.View[vid],
					curModel = iCat.Model[mid];
				curView.setModel(curModel);
			});
		}
	}, iCat.mvc);

	var tools = new iCat.mvc.Tools().init();
	delete iCat.mvc.Tools;
	delete iCat.mvc;

	/*
	 * view-module职责：ui中心
	 * - 设置与ui相关的参数
	 * - 设置与ui相关的函数(events挂钩)，调用其model的逻辑
	 * - 每次extend都会生成一个新的view-Class
	 */
	var View = function(viewId, option){
		this.viewId = viewId;//必须
		this._init(option, viewId);

		if(!iCat.View[viewId]){//copy
			iCat.View[viewId] = this;
		} else {
			return iCat.View[viewId];
		}
	};
	View.prototype = {
		_init: function(opt, vid){
			var self = this, IMData;

			IMData = iCat.Model.__pageData[vid] = iCat.Model.ViewData(vid) || {};
			IMData.ownData = {};

			iCat.foreach(opt, function(k, v){
				if(iCat.isFunction(v)){//option中的方法
					self[k] = v;
				}
				else if(k=='config'){//option中的配置数据
					IMData['config'] = v;
					IMData['config'].viewId = vid;
				}
				else {//option中的静态数据
					self[k] = v;
					IMData.ownData[k] = v;
				}
			});

			if(self.model)
				self._htmlRender();
		},

		_render: function(data, before, clear, outData){
			var self = this, vid = self.viewId, ret1, ret2,
				IMData = iCat.Model.ViewData(vid),
				ownData = IMData.ownData,
				curCfg = IMData.config;
			
			if(self.model.dataChange(vid, data)//数据发生变化
				|| iCat.hashChange//hash变化(fixed bug: 同init函数不同hash无法渲染)
					|| self.forcedChage//当调用update、改变wrap或tempId时，数据变化与否都要渲染
						|| iCat.mode_singleLayer)//单层切换
					
			{
				if(self.forcedChage) delete self.forcedChage;

				if(outData){//设置setData得到的数据
					ret1 = self.model.save(curCfg, data);
					if(!!ret1 && iCat.isArray(ret1))
						data = { repeatData: ret1 };
					iCat.mix(data, ownData);
				}
				if(data.repeatData){
					data.repeatData.forEach(function(d, i){
						iCat.util.render(curCfg, d, before, i==0);
					});
				} else {
					ret2 = iCat.util.render(curCfg, data, before, clear);
					if(ret2 && iCat.isFunction(ret2)){
						self.getFormData = ret2;
					}
				}
			}
		},

		_htmlRender: function(data, before, clear){
			var self = this,
				IMData = iCat.Model.ViewData(self.viewId),
				cfg = IMData.config;

			if(!data && self.model){
				self.model.fetch(cfg, function(servData){
					if(cfg.globalKey && self.model.DataOutput){
						cfg.globalArgus = cfg.globalArgus || [];
						cfg.globalArgus.unshift(servData);
						servData = self.model.DataOutput.apply(self, cfg.globalArgus);
					}
					if(cfg.getBIData){
						servData = cfg.getBIData(servData);
					}
					self._render(servData, before, clear);
				});
			} else if(data) {
				self._render(data, before, clear, true);
			}
		},

		setModel: function(m, data, before, clear){
			var self = this;
			if(!m || (iCat.isObject(m) && m.constructor.__super__!==Model)){
				m = iCat.Model['__page_emptyModel'] || new Model('__page_emptyModel');
			}
			if(!self.model){//第一次
				self.model = m;
				if(self.init)//自定义初始化
					data = self.init(self, m, true);
				self._htmlRender(data, before, clear);
			} else {
				self.model = m;
			}
		},

		setConfig: function(cfg, before, clear){
			var self = this,
				ret = self.model.cfgChange(self.viewId, cfg);
			if(ret[0]){
				if(ret[1]) self.forcedChage = true;
				self._htmlRender(null, before, clear);
			}
		},

		setAjaxUrl: function(url, before, clear){ this.setConfig({ajaxUrl:url}, before, clear); },
		setTempId: function(tid, before, clear){ this.setConfig({tempId:tid}, before, clear); },
		setWrap: function(wrap, before, clear){ this.setConfig({wrap:wrap}, before, clear); },
		setData: function(data, before, clear){ this._htmlRender(data, before, clear); },
		update: function(before){ this._htmlRender(null, before, true); this.forcedChage = true; }
	};

	/*
	 * model-module职责：数据和逻辑处理中心
	 * - 设置与数据/逻辑处理相关的函数
	 * - 处理view发过来的指令，处理后返回相应结果
	 * - 每次extend都会生成一个新的model-Class
	 */
	var Model = function(modelId){
		this.modelId = modelId;//必须

		if(!iCat.Model[modelId]){//copy
			iCat.Model[modelId] = this;
		} else {
			return iCat.Model[modelId];
		}
	};
	Model.prototype = {
		cfgChange: function(vid, cfg){ return iCat.Model.cfgChange(vid, cfg); },
		dataChange: function(vid, data){ return iCat.Model.dataChange(vid, data); },

		fetch: function(){ iCat.util.fetch.apply(this, arguments); },
		save: function(){ return iCat.util.save.apply(this, arguments); },
		remove: function(){ iCat.util.remove.apply(this, arguments); }
	};

	/*
	 * controller-module职责：响应中心
	 * - 响应用户动作，调用对应的View处理函数
	 * - 每次extend都会生成一个新的controller-Class
	 */
	var	Controller = function(ctrlId, option){
		option = option || {};

		var self = this;
		self.ctrlId = ctrlId;//必须
		self.config = option.config || {};
		self.routes = option.routes || {};

		self.vmGroups = {};// key=viewId, value=modelId
		self.wrapGroup = [];// value=modHash

		self._init(ctrlId, option, self.config);
	};
	Controller.prototype = {
		_init: function(cId, opt, cfg){
			var self = this,
				elBody, bodyId;

			if(!iCat.Controller[cId])//copy
				iCat.Controller[cId] = self;

			//处理routes
			iCat.foreach(self.routes, function(k, v){
				var _k = k.replace(/\s+/g, '')
					 .replace(/\:num/gi, '(\\d+)')
					 .replace(/\:\w+/g, '(\\w+)');
				self.routes[_k] = iCat.isFunction(v)? v : self[v];
				if(_k!==k) delete self.routes[k];
			});
			
			//把option合并到self
			iCat.mix(self, opt, 'config, routes');
			
			iCat.util.wait(function(k, t){
				var wraps = iCat.util.queryAll(cfg.baseBed);
				if(!wraps || !wraps.length){
					iCat.__cache_timers[k] = false;
					if(t==1400){
						elBody = iCat.elBodyWrap || iCat.util.queryOne('*[data-pagerole=body]') || doc.body;
						iCat.util.makeHtml(/^\w+/.test(cfg.baseBed)? cfg.baseBed : 'div'+cfg.baseBed, elBody, false, true);
					}
					else return;
				}

				delete iCat.__cache_timers[k];
				iCat.elBodyWrap = iCat.elBodyWrap || elBody || iCat.util.queryOne('*[data-pagerole=body]') || doc.body;
				
				bodyId = iCat.elBodyWrap.getAttribute('id');
				var fnInit = function(id){
					var hash = tools.dealHash(id, self.routes);
					self.hashArgus = hash;
					try{
						self.routes[hash[0]].call(self);
					}
					catch(e){}
					tools.pageLoad(self);
				};

				if(iCat.isNull(bodyId)){//页面里没有id属性，则为锚点hash
					fnInit(location.hash);
					root['onhashchange'] = function(){ fnInit(location.hash); };
				} else{
					iCat.mode_multiPage = true;
					fnInit(bodyId);
				}
			}, 1500, 10);
		},

		init: function(o){
			var self = this,
				cfg = self.config;

			if(iCat.mode_multiPage){
				iCat.elCurWrap = iCat.util.queryOne(cfg.baseBed);
				tools.multiPage(self, o);
			} else {
				if(iCat.mode_singleLayer){
					tools.singleLayer(self, o);
				} else {
					if(iCat.mode_multiLayer){
						tools.multiLayer(self, o);
					} else {
						var wraps = self.pageWraps = iCat.util.queryAll(cfg.baseBed),
							len = wraps.length;
						if(!len) return;

						if(len==1){
							iCat.mode_singleLayer = true;
							iCat.elCurWrap = wraps[0];
							tools.singleLayer(self, o);
						} else {
							iCat.mode_multiLayer = true;
							tools.multiLayer(self, o);
						}
					}
				}
			}
		},

		vmAdd: function(vm, init){
			if(!vm) return;
			vm = iCat.isArray(vm)? vm : [vm];

			var self = this,
				vmGroups = self.vmGroups;
			iCat.util.recurse(vm, function(item){//instanceof => false
				//view必须有，且是View的实例化 
				if(!item.view ||
					(iCat.isObject(item.view) && item.view.constructor.__super__!==View)) return;
				
				if(iCat.isFunction(item.model) && item.model.__super__==Model){
					iCat.foreach(
						item.model.setting || {'__page_mainModel': {}},// default= '__page_mainModel'
						function(key, setItem){
							item.model = iCat.Model[key] || new item.model(key, setItem);
						}
					);
				}

				if(iCat.isFunction(item.view) && item.view.__super__==View){
					iCat.foreach(
						item.view.setting || {'__page_mainView': {config:{}}},// default= '__page_mainView'
						function(key, setItem){
							if(init && self.pageMods && !self.pageMods.contains(key)) return;
							var curView = iCat.View[key] || new item.view(key, setItem),
								cfg = setItem.config;
							tools.addItem(vmGroups, curView, item.model, init);
							tools.regEvents(curView, cfg);
						}
					);
				} else {
					var curView = item.view,
						cfg = iCat.Model.ViewData(curView.viewId).config;
					tools.addItem(vmGroups, curView, item.model, init);
					tools.regEvents(curView, cfg);
				}
			});
		},

		vmRemove: function(vid){
			if(!vid) return;
			var vmGroups = this.vmGroups;
			vid = iCat.isString(vid)? vid.split(',') : vid;
			vid.forEach(function(k){
				tools.removeItem(vmGroups, k);
			});
		},

		vmClear: function(){
			var vmGroups = this.vmGroups;
			iCat.foreach(vmGroups, function(k){
				tools.removeItem(vmGroups, k);
			});
		},

		gotopage: function(url){ location.href = iCat.util.fullUrl(url); }
	};

	// 对外接口
	iCat.namespace('View', 'Model', 'Controller');
	iCat.View.extend       = function(opt){ return tools.inherit(View, opt); };
	iCat.Model.extend      = function(opt){ return tools.inherit(Model, opt); };
	iCat.Controller.extend = function(opt){ return tools.inherit(Controller, opt); };

	iCat.Model['__pageData'] = iCat.Model['__pageData'] || {};
	iCat.mix(iCat.Model, {
		cfgChange: function(vid, d){
			var oldCfg = iCat.Model.ViewData(vid).config,
				retAjax = d.ajaxUrl && oldCfg.ajaxUrl!=d.ajaxUrl,
				retForce = (d.tempId && oldCfg.tempId!=d.tempId) || (d.wrap && oldCfg.wrap!=d.wrap),
				ret = retAjax || retForce;
			if(ret) iCat.mix(oldCfg, d);
			return [ret, retForce];
		},

		dataChange: function(vid, d){
			var IMData = iCat.Model.ViewData(vid),
				prevData = IMData.prevData,
				ret = !iCat.util._jsonCompare(d, prevData);
			if(ret) IMData.prevData = JSON.stringify(d);
			return ret;
		},

		GlobalData: function(key, d){
			var GD = iCat.Model.__globalData = iCat.Model.__globalData || {};
			if(!d) return GD[key];
			if(!GD[key]) GD[key] = d;
		},

		ViewData: function(vid, d){
			var PD = iCat.Model.__pageData = iCat.Model.__pageData || {};
			if(!d) return PD[vid];
			if(!PD[vid]) PD[vid] = d;
		}
	});
})(ICAT, this, document);

/* loader.js # */
(function(iCat, root, doc){
	// 创建Loader命名空间
	iCat.namespace('Loader');
	
	var fnPathConfig,
		Sloader = iCat.Shim.loader || {};//keep Compatible

	iCat.Class('Tools',
	{
		init: function(){
			var oSelf = this;
			return {
				getCurrentJS: function(){
					var scripts = doc.getElementsByTagName('script');
					return scripts[scripts.length-1];
				},

				getConcatUrl: function(src, o){
					var arrsrc = src.replace(/(\?{2}|\.js(?=\?))/g, '$1|').split('|'),
					_webRoot = arrsrc[0].replace(/\?+/g,'');
					o._webRoot = arrsrc[0];
					o.timestamp = arrsrc[2] || '';//fixed bug:时间戳没设置时，会有undefined

					arrsrc[1].split(',').forEach(function(v){
						if(/\/sys\//i.test(v))
							o._sysRef = v.replace(/(\/sys\/).*/ig, '$1');
						if(/\/apps\//i.test(v))
							o._appRef = v.replace(/(\/)\w+(\.\w+)?\.js/g, '$1');
					});

					o.sysRef = (_webRoot+o._sysRef).replace(/([^:])\/{2,}/g,'$1/');//fixed bug:把http://变成了http:/
					o.appRef = (_webRoot+o._appRef).replace(/([^:])\/{2,}/g,'$1/');
				},

				getCommonUrl: function(src, o, init){
					if(init){//初始化设置pageRef,sysRef
						o.sysRef = /\/sys\//i.test(src)?
							src.replace(/(\/sys\/).*/ig, '$1') : src.replace(/(\/)\w+(\.\w+)?\.js(.*)?/g, '$1');
						o.timestamp = src.replace(/.*\.js(\?)?/g, '$1');
					} else {//设置appRef
						o.appRef = src.replace(/(\/)\w+(\.\w+)?\.js(.*)?/g, '$1');
						if(!o.timestamp)
							o.timestamp = src.replace(/.*\.js(\?)?/g, '$1');
					}
				},

				fnInc: function(files){
					files = iCat.isString(files)? [files] : files;
					oSelf.getURL(files).forEach(function(v){
						if(!v) return;
						oSelf.blockLoad(v);
					});
				},

				fnOption: function(option, isRequire){
					oSelf._fnOption.apply(oSelf, arguments);
				},

				fnInclude: function(files, callback, isDepend, isSingle, context){
					oSelf._fnOption({
						files: files, callback: callback,
						isDepend: isDepend, isSingle: isSingle,
						context: context
					});
				},

				fnRequire: function(modName, files, callback, isSingle, context){
					oSelf._fnOption({
						modName: modName,
						files: files, callback: callback,
						isSingle: isSingle, context: context
					});
				},

				fnUse: function(){
					var opt = arguments[0];
					if(!iCat.isObject(opt)) return;

					iCat.util.wait(function(k, t){
						if(!oSelf._modGroup[opt.modName]){
							iCat.__cache_timers[k] = false;
							if(t==50 && iCat.ModsConfig[opt.modName]){
								delete iCat.__cache_timers[k];
								iCat.require({
									modName: opt.modName,
									files: iCat.ModsConfig[opt.modName],
									callback: opt.callback, context: opt.context
								});
							}
							return;
						}

						delete iCat.__cache_timers[k];
						if(opt.callback)
							opt.callback(opt.context);
					}, 60, 10);
				}
			}
		},
		
		/*
		 * type1: 参照sys目录
		 * type2: 参照页面根目录
		 * type3: 参照main.js目录
		 * type4: 网址
		 */
		_fullUrl: function(strPath, isConcat){
			var _line = isConcat? '_' : '',
				appRef = iCat.PathConfig[_line+'appRef'],
				sysRef = iCat.PathConfig[_line+'sysRef'],
				pageRef = iCat.PathConfig.pageRef;
			strPath = strPath.replace(/\?.*/, '');

			if(/^\.{1,2}\//.test(strPath)){//type3
				strPath = /^\.\//.test(strPath) ?
					strPath.replace(/^\.\//g, appRef) : strPath.replace(/^\.\.\//g, appRef.replace(/\w+\/$/g,''));
			} else {//type1
				strPath = sysRef + strPath;
			}

			return strPath;
		},

		_loadedGroup: {},//loaded-js

		_modGroup: {},//loaded-module

		_fnLoad: Sloader._fnLoad || function(option, pNode, node){
			var oSelf = this;
			node.onload = function(){
				oSelf._loadedGroup[option.file] = true;
				if(option.callback && iCat.isFunction(option.callback))
					option.callback(option.context || iCat);
				if(option.modName)
					oSelf._modGroup[option.modName] = true;
				oSelf._loadedGroup[option.file] = true;
				if(!iCat.$ && (root['jQuery'] || root['Zepto'])){
					iCat.$ = root['jQuery'] || root['Zepto'];
					iCat.embedjQuery();
				}
			};
		},

		getURL: function(arr, isSingle){//isSingle表示强制单个加载
			if(!iCat.isArray(arr) || !arr.length) return;
			if(arr.length===1) isSingle = true;

			var oSelf = this,
				singleArr = [], newArr = [],
				isConcat = iCat.PathConfig._isConcat && !isSingle;
			arr.forEach(function(v){
				v = v.replace(/([^\:])\/+/g, '$1/');
				if(iCat.DebugMode){
					v = v.indexOf('!')>=0 ?
							v.replace(/\!/g,'') : v.replace(/(\.source)?(\.(js|css))/g, '.source$2');
				} else {
					v = v.replace(/\!/g,'');
				}

				if(/^\w+:\/\/|^\/\w+/.test(v)){//type4|type2
					v = /^\/\w+/.test(v)?
						v.replace(/^\//g, iCat.PathConfig.pageRef) : v;
					singleArr.push(v);
				} else {
					v = oSelf._fullUrl(v, isConcat);
					newArr.push(v);
				}
			});
			newArr = isConcat? [iCat.PathConfig._webRoot + newArr.join(',')] : newArr;
			return newArr.concat(singleArr);
		},

		blockLoad: function(file){
			var oSelf = this,
				_url = file.indexOf('#')>0 ?
					file.replace(/(#.*)/, iCat.PathConfig.timestamp+'$1') : (file + iCat.PathConfig.timestamp);
			if(oSelf._loadedGroup[_url]) return;
			
			var type = file.replace(/.*\.(css|js)/g,'$1'),
				isCSS = type=='css',
				tag = isCSS? 'link':'script',
				attr = isCSS? ' type="text/css" rel="stylesheet"' : ' type="text/javascript"',
				path = (isCSS? 'href':'src') + '="' + _url + '"';
			doc.write('<'+tag+attr+path+(isCSS? '/>':'></'+tag+'>'));
			oSelf._loadedGroup[file] = true;
			if(!iCat.$ && (root['jQuery'] || root['Zepto'])) iCat.$ = root['jQuery'] || root['Zepto'];
		},

		unblockLoad: function(option){
			//增加时间戳
			var	oSelf = this,
				_url = option.file.indexOf('#')>0?
					option.file.replace(/(#.*)/, iCat.PathConfig.timestamp+'$1') : (option.file + iCat.PathConfig.timestamp);
			option.file = option.file.replace(/(#.*)/g, '');
			
			if(oSelf._loadedGroup[option.file]){
				if(option.callback && iCat.isFunction(option.callback))
					option.callback(option.context || iCat);
				
				if(option.modName){
					oSelf._modGroup[option.modName] = true;
				}
				return;
			}
			
			var node, type = option.file.replace(/.*\.(css|js)/g,'$1');
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
			
			iCat.util.wait(function(k){
				var nodeReady = doc.body || doc.getElementsByTagName('body')[0],
					pNode;
				if(!nodeReady){
					iCat.__cache_timers[k] = false;
					return;
				}

				delete iCat.__cache_timers[k];
				pNode = doc.head || doc.getElementsByTagName('head')[0];
				
				/* 监听加载完成 */
				if(type==='js'){
					oSelf._fnLoad(option, pNode, node);
				}
				
				/* css不需要监听加载完成*/
				if(type==='css'){
					setTimeout(function(){
						if(option.callback && iCat.isFunction(option.callback))
							option.callback(option.context || iCat);
						
						if(option.modName){
							oSelf._modGroup[option.modName] = true;
						}
					},5);
					oSelf._loadedGroup[option.file] = true;
				}

				pNode.appendChild(node);
			});
		},

		_fnOption: function(option, isRequire){
			var oSelf = this,
				opt = option, fn;
			if(isRequire){
				if(iCat.isString(opt)) opt = {modName:opt};
				if(!iCat.isObject(opt) || !(opt.files = opt.files || iCat.ModsConfig[opt.modName])) return;
			} else {
				if(iCat.isString(opt) || iCat.isArray(opt)) opt = {files:opt};
				if(!iCat.isObject(opt) || !opt.files) return;
			}

			opt.files = iCat.isString(opt.files) ?
					oSelf.getURL([opt.files]) : oSelf.getURL(opt.files, opt.isSingle);

			if(isRequire && oSelf._modGroup[opt.modName] && opt.callback){
				opt.callback();
				return;
			}

			if(isRequire) opt.isDepend = true;

			(fn = function(){
				if(!opt.files.length) return;
				var curJS = opt.files.shift();
				if(opt.files.length){
					if(opt.isDepend)//文件间有依赖 顺序加载
						oSelf.unblockLoad({
							file:curJS, modName:opt.modName,
							callback:function(){fn(opt.files);/*next*/}, context:opt.context
						});
					else {
						oSelf.unblockLoad({file:curJS, context:opt.context});
						fn(opt.files);/*next*/
					}
				} else {
					oSelf.unblockLoad({
						file:curJS, modName:opt.modName,
						callback:opt.callback, context:opt.context
					});
				}
			})();
		}
	}, iCat.Loader);

	var loader = new iCat.Loader.Tools().init();
	delete iCat.Loader.Tools;
	delete iCat.Loader;

	/*
	 * pageRef:参照页面路径
	 * sysRef:参照icat.js所在的sys目录路径
	 * appRef:参照main.js所在的目录路径
	 * timestamp:时间戳
	 */
	(fnPathConfig = function(cfg){
		var pc = iCat.PathConfig,
			_curScript = loader.getCurrentJS(), src = _curScript.src;
		pc._isConcat = src.indexOf('??')>=0;

		if(!pc.appRef){
			pc._isConcat?
				loader.getConcatUrl(src, pc) : loader.getCommonUrl(src, pc, cfg===true);
		}

		if(iCat.isObject(cfg)){
			iCat.mix(pc, cfg);
		}
	})(true);

	//对外接口
	iCat.mix(iCat, {

		PathConfig: iCat.mix(fnPathConfig, iCat.PathConfig),

		// support user's config
		ModsConfig: function(cfg){
			var mc = iCat.ModsConfig;
			if(iCat.isArray(cfg)){
				iCat.foreach(cfg, function(k, v){
					mc[v.modName] = (mc[v.modName]||[]).concat(v.paths);
				});
			} else {
				if(cfg.modName && cfg.paths){
					mc[cfg.modName] = (mc[cfg.modName]||[]).concat(cfg.paths);
				} else {
					iCat.foreach(cfg, function(k, v){
						mc[k] = (mc[k]||[]).concat(v);
					});
				}
			}
		},

		/* 阻塞式加载文件 */
		inc: function(){
			if(!arguments.length) return;
			loader.fnInc(arguments[0]);
		},

		/* 加载文件形式：
		 * - 单个文件，支持字符串或文件数组(length为1)
		 * - 多个文件，必须是文件数组
		 * - 参数：options || files, callback, isDepend, isSingle, context
		 */
		include: function(){//加载一个或多个文件
			if(!arguments.length) return;
			arguments.length==1?
				loader.fnOption(arguments[0]) :
				loader.fnInclude(arguments[0], arguments[1], arguments[2], arguments[3], arguments[4]);
		},
		
		/* 加载文件形式：
		 * - 单个文件，支持字符串或文件数组(length为1)
		 * - 多个文件，必须是文件数组
		 * - 参数：options || modName, files, callback, isSingle, context
		 */
		require: function(){//加载有依赖的模块
			if(!arguments.length) return;
			arguments.length==1?
				loader.fnOption(arguments[0], true) :
				loader.fnRequire(arguments[0], arguments[1], arguments[2], arguments[3], arguments[4]);
		},
		
		//使用已加载后的模块
		//参数：options || modName, callback, delay, context
		use: function(opt){
			if(!arguments.length) return;
			if(arguments.length==1){
				loader.fnUse(arguments[0]);
			} else {
				loader.fnUse({
					modName: arguments[0], callback: arguments[1],
					delay: arguments[2], context: arguments[3]
				});
			}
		}
	});

	//默认模块
	iCat.ModsConfig({
		'zeptoAjax': ['lib/zepto/src/zepto.js', 'lib/zepto/src/ajax.js'],
		'zeptoAnim': ['lib/zepto/src/zepto.js', 'lib/zepto/src/fx.js', 'lib/zepto/src/fx_methods.js'],
		'zAjaxAnim': ['lib/zepto/src/zepto.js', 'lib/zepto/src/ajax.js', 'lib/zepto/src/fx.js', 'lib/zepto/src/fx_methods.js'],
		'appMVC': ['./mvc/view.js', './mvc/model.js', './mvc/controller.js']
	});

	iCat.weinreStart = function(){
		if(!iCat.PathConfig.weinreRef) return;

		var whash = iCat.util.cookie('__w_hash') || '#anonymous',
			curHash = location.hash;
		if(curHash && /^#[^\W]+$/.test(curHash) && !whash){//忽略单页面中的hash
			iCat.util.cookie('__w_hash', curHash, 3600);
		}
		var weinrejs = iCat.PathConfig.weinreRef + 'target/target-script-min.js!' + whash;
		iCat.include(weinrejs);// fixed bug:用inc当js无法加载时，会阻碍页面渲染
	};

	//如果是ip模式，自动调用weinre
	if(iCat.IPMode) iCat.weinreStart();
})(ICAT, this, document);