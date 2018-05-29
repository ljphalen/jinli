/*!
 * Copyright 2011~2012, ICAT JavaScript Library v1.1.3
 * https://github.com/valleykid/icat
 *
 * Copyright (c) 2012 valleykid
 * Licensed under the MIT license.
 *
 * @Author valleykiddy@gmail.com
 * @Time 2012-11-29 19:52:00
 */

/** core.js */
(function(){
	// Create the root object, 'window' in the browser, or 'global' on the server.
	var root = this, iCat = {};
	
	// Export the ICAT object for **Node.js**
	if(typeof exports!=='undefined'){
		if(typeof module!=='undefined' && module.exports){
			exports = module.exports = iCat;
		}
		exports.ICAT = iCat;
	} else {
		root['ICAT'] = iCat;
	}
	
	var _ua = navigator.userAgent, ObjProto = Object.prototype,
		toString = ObjProto.toString,
		ArrProto = Array.prototype,

		// iCat.app() with these members.
		__APP_MEMBERS = ['namespace'];

	// expand the built-in Objects' functions.
	String.prototype.trim = function(){
		return this.replace(/(^\s*)|(\s*$)/g, '');
	};

	ArrProto.hasItem = function(item){
		for(var i=0, self=this, len=self.length; i<len; i++){
			if(self[i]==item){
				return true;
			}
		}
		return false;
	};

	ArrProto.removeItem = function(item){
		for(var i=0, self=this, len=self.length; i<len; i++){
			if(self[i]==item){
				self.splice(i, 1);
			}
		}
		return self;
	};

	ArrProto.unique = function(){
		var self = this, hash = {}, r = [];
		for(var i=0, len=self.length; i<len; i++){
			if(!hash[self[i]]){
				r.push(self[i]);
				hash[self[i]] = true;
			}
		}
		return r;
	};
	
	// Copies all the properties of s to r.
	// w(hite)l(ist):白名单, ov(erwrite):覆盖
	iCat.mix = function(r, s, wl, ov){
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
	
	iCat.mix(iCat, {
		// Current version.
		version: '1.1.3',
		
		// debug or not
		isDebug: /debug/i.test(root.location.href),
		
		// kinds of browsers
		browser: {
			safari: /webkit/i.test(_ua),
			opera: /opera/i.test(_ua),
			msie: /msie/i.test(_ua) && !/opera/i.test(_ua),
			mozilla: /mozilla/i.test(_ua) && !/(compatible|webkit)/i.test(_ua)
		},
		
		// Commonly used judgment
		isFunction: function(obj){
			return toString.call(obj) == '[object Function]';
		},
		
		isString: function(obj){
			return toString.call(obj) == '[object String]';
		},
		
		isArray: Array.isArray ||
			function(obj){
				return toString.call(obj) == '[object Array]';
			},
		
		isObject: function(obj){
			return toString.call(obj) == '[object Object]';//obj === Object(obj);
		},
		
		isNull: function(obj){
			return obj === null;
		},

		isEmptyObject: function(obj){
			for(var name in obj){
				return false;
			}
			return true;
		},

		// function throttle
		throttle: function(opt){
			var timer = null, t_start;

			var fn = opt.fn,
				context = opt.context,
				delay = opt.delay || 100,
				mustRunDelay = opt.mustRunDelay;
			
			return function(){
				var args = arguments, t_curr = +new Date();
				context = context || this;
				
				clearTimeout(timer);
				if(!t_start){
					t_start = t_curr;
				}
				if(mustRunDelay && t_curr - t_start >= mustRunDelay){
					fn.apply(context, args);
					t_start = t_curr;
				}
				else {
					timer = setTimeout(function(){
						fn.apply(context, args);
					}, delay);
				}
			};
		},
		
		// Handles objects with the built-in 'foreach', arrays, and raw objects.
		foreach: function(o, cb, args){
			var name, i = 0, length = o.length,
				isObj = length===undefined || iCat.isString(o);
			
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
			
			if(len==0) return null;
			
			else if(len==1){
				var cfg = argus[0];
				if(!iCat.isObject(cfg))
					return null;
				else {
					function Cla(){cfg.Create.apply(this, arguments);}
					iCat.foreach(cfg, function(k, v){
						if(k!='Create')
							Cla.prototype[k] = v;
					});
					
					return Cla;
				}
			}
			
			else if(len>=2){
				var claName = argus[0],
					cfg = argus[1],
					context = argus[2] || root;
				
				if(!iCat.isString(claName) || !iCat.isObject(cfg))
					return null;
				else {
					function Cla(){cfg.Create.apply(this, arguments);}
					iCat.foreach(cfg, function(k, v){
						if(k!='Create')
							Cla.prototype[k] = v;
					});
					
					context[claName] = Cla;
				}
			}
		},
		
		widget: function(name, cfg){
			this.Class(name, cfg, iCat.widget);
		},

		util: function(name, fn){
			iCat.util[name] = fn;
		},
		
		// iCat或app下的namespace，相当于扩展出的对象
		namespace: function(){
			var a = arguments, l = a.length, o = null, i, j, p;

			for (i=0; i<l; ++i){
				p = ('' + a[i]).split('.');
				o = this;
				for (j = (root[p[0]]===o)? 1:0; j<p.length; ++j){
					o = o[p[j]] = o[p[j]] || {};
				}
			}
			return o;
		 },
		
		// create a app for some project
		app: function(name, sx){
			var self = this,
				isStr = self.isString(name),
				O = isStr? root[name] || {} : name;

				self.mix(O, self, __APP_MEMBERS, true);
				self.mix(O, self.isFunction(sx) ? sx() : sx);
				isStr && (root[name] = O);

				return O;
		 },
		
		// print some msg for unit testing
		log: function(msg) {
			root.console!==undefined && console.log ? console.log(msg) : alert(msg);
		 }
	});
}).call(this);

/** load.js */
(function(iCat){
	var doc = document,
		ohead = doc.head || doc.getElementsByTagName('head')[0],
		_metaAppRef = doc.getElementsByName('appRef')[0],
		_curScript, _curUrl, _timestamp,
		_hasSysOZ, _hasAppOZ, _sysPlugin, _appPlugin,
		_corecss, _corelib, _asynCorelib, _mainJS, _asynMainJS,
		_loadedGroup = {}, _modGroup = {};
	
	// get the current js-file
	(function(scripts){
		_curScript   =  scripts[scripts.length-1];
		_curUrl      =  _curScript.hasAttribute ?  _curScript.src : _curScript.getAttribute('src',4);
		_corelib     =  _curScript.getAttribute('corelib') || '';
		_asynCorelib =  _curScript.getAttribute('asyn-corelib') || '';
		_mainJS      =  _curScript.getAttribute('main') || '';
		_asynMainJS  =  _curScript.getAttribute('asyn-main') || '';
		_hasAppOZ    =  /^\.{2}\//.test(_mainJS || _asynMainJS);

		if(_curUrl.indexOf(',')<0){
			
			if(/\?[vt]=\d+/.test(_curUrl)){
				_timestamp = _curUrl.replace(/.*\?/,'?');
				_curUrl = _curUrl.replace(/\?.*/,'');
			}
		} else {
			var arrUrl = _curUrl.replace(/\/\?{2}/,'?').split('?'),
				arrJS = arrUrl[1].split(','),
				len = arrJS.length, i = 0;

			do {
				if(/icat\//i.test(arrJS[i])){
					_curUrl = arrUrl[0]+arrJS[i];
				}

				if(/apps\//i.test(arrJS[i])){
					_hasAppOZ = /assets\//i.test(arrJS[i]);
				}
				i++;
			} while(i<=len-1);

			if(arrUrl[2])
				_timestamp = '?'+arrUrl[2];	
		}
	})(doc.getElementsByTagName('script'));

	_hasSysOZ =  /\/sys\/icat\//i.test(_curUrl);
	
	// set the path
	iCat.modsConfig = {};
	iCat.sysRef = _hasSysOZ? _curUrl.replace(/\/sys\/.*/, '/sys') : _curUrl.replace(/\/\w*\.js/, '');
	iCat.appRef = _metaAppRef? _metaAppRef.content : iCat.sysRef;
	iCat.libRef = iCat.sysRef + '/lib';
	_corecss    = _metaAppRef? _metaAppRef.getAttribute('corecss') : '';
	_sysPlugin  = _hasSysOZ? _curUrl.replace(/icat\..*/,'plugin/') : iCat.sysRef+'plugin/';
	_appPlugin  = iCat.appRef + (_hasAppOZ? '/assets':'') + '/plugin/';
	
	// support user's config
	iCat.config = function(cfg){
		iCat.modsConfig[cfg.modName] = [];
		
		iCat.foreach(cfg.paths, function(k, v){
			iCat.modsConfig[cfg.modName].push((cfg.baseUrl||'')+v);
		});
	}
	
	// type1( ): 指向sys根目录(sys级) ~/指向icat下的plugin目录
	// type2(/): 指向lib根目录(lib级) //库文件夹和库名相同
	// type3(./): 指向app根目录(app级) ../指向assets下的css或js目录 .~/指向assets下的plugin目录
	// type4(网址形式): 外链网址
	var _dealUrl = function(s){
		if(!s) return;
		
		//step1: 清理空格及?|#后缀参数
		var url = s.replace(/\s|[\?#].*/g,''), type = url.replace(/.*\./g,''),
			isCSS = type=='css';
		if(!url) return;
		
		//step2: 是否开启debug
		if(iCat.isDebug){
			url = /\.source/i.test(url)? url :
				(isCSS? url.replace(/\.css/g, '.source.css') : url.replace(/\.js/g, '.source.js'));
		}

		if(/^(http|ftp|https):\/\/.*/i.test(url)){//type4，直接输出
			return url;
		} else {
			if(/^\.{1,2}(~)?\//.test(url)){//type3 ##千万不能带g了
				if(/^\.\//.test(url))
					url = url.replace(/^\./, iCat.appRef);
				if(/^\.{2}\//.test(url))
					url = url.replace(/^\.{2}/, iCat.appRef+(isCSS? '/assets/css':'/assets/js'));
				if(/^\.~\//.test(url))
					url = url.replace(/^\.~\//, _appPlugin);
			} else if(/^\/{1,2}/.test(url)){//type2	
				if(/^\/{2}/.test(url)){
					var libFolder = url.replace(/^\/{2}|.source|.css|.js/ig, '');
					libFolder = /\d|\./.test(libFolder)? libFolder.replace(/\d(\/)?|\./g,'') : libFolder;
					url = url.replace(/^\//, iCat.libRef+'/'+libFolder);
				} else {
					url = url.replace(/^\//, iCat.libRef+'/');
				}
			} else {//type1
				if(/^~\//.test(url)){
					url = url.replace(/^~\//, _sysPlugin);
				} else {
					url = iCat.sysRef + '/' + url;
				}
			}
		}
		
		return url + (_timestamp || '');
	},
	
	_blockImport = function(loadFile){
		var url = loadFile, _url = url.replace(/[\?#].*/, '');
		if(_loadedGroup[_url]) return;
		
		var type = _url.replace(/.*\./g,''),
			isCSS = type=='css', tag = isCSS? 'link':'script',
			attr = isCSS? ' type="text/css" rel="stylesheet"' : ' type="text/javascript"',
			path = (isCSS? 'href':'src') + '="'+url+'"';
		doc.write('<'+tag+attr+path+(isCSS? '/>':'></'+tag+'>'));
		_loadedGroup[_url] = true;
	},
	
	// 执行callback函数
	_exec = function(f, cb, mod, ct){
		if(cb && iCat.isFunction(cb))
			cb(ct || iCat);
		
		if(mod){
			_modGroup[mod] = true;
			
			iCat.modsConfig[mod] = iCat.modsConfig[mod]? iCat.modsConfig[mod] : [];
			iCat.modsConfig[mod].push(f);
		}
	},
	
	_unblockImport = function(file, callback, mod, context){
		
		var	url = file,
		
			pNode = _curScript.parentNode || ohead,
			
			//去掉?|#后面的参数，保留纯净的文件
			_url = url.replace(/[\?#].*/, '');
		
		if(_loadedGroup[_url]){
			_exec(file, callback, mod, context);
			return;
		}
		
		var node, type = _url.replace(/.*\./g,'');
		if(type==='css'){
			node = doc.createElement('link');
			node.setAttribute('type', 'text/css');
			node.setAttribute('rel', 'stylesheet');
			node.setAttribute('href', url);
		} else if(type==='js'){
			node = doc.createElement('script');
			node.setAttribute('type', 'text/javascript');
			node.setAttribute('src', url);
			node.setAttribute('async', true);
		}
		
		if(!node) return;
			
		if(iCat.browser.msie){
			var timer = setInterval(function(){
				try{
					document.documentElement.doScroll('left');//在IE下用能否执行doScroll判断dom是否加载完毕
				}catch(e){
					return;
				}
				
				clearInterval(timer);
				if(type==='js' && node.readyState){
					node.onreadystatechange = function(){
						if(node.readyState == "loaded" || node.readyState == "complete") {
							node.onreadystatechange = null;
							_exec(file, callback, mod, context);
							_loadedGroup[_url] = true;
						}
					};
				}
				pNode.appendChild(node);
			},1);
		} else {
			if(type==='js'){
				node.onload = function(){
					_exec(file, callback, mod, context);
					_loadedGroup[_url] = true;
				};
			}
			pNode.appendChild(node);
		}
		
		/* css不需要监听加载完成*/
		if(type==='css'){
			setTimeout(function(){
				_exec(file, callback, mod, context);
			},5);
			_loadedGroup[_url] = true;
			pNode.appendChild(node);
		}
	};
	
	//对外接口
	iCat.mix(iCat, {
		
		/* 阻塞式加载文件 */
		inc: function(f){
			if(!f) return;
			f = iCat.isString(f)? [f] : f;
			
			iCat.foreach(f, function(i, v){
				if(!v) return;
				_blockImport(_dealUrl(v));
			});
			
		},
		
		/* 加载文件形式：
		 * - 单个文件，支持字符串或文件数组(length为1)
		 * - 多个文件，必须是文件数组
		 */
		incfile: function(f, cb, isDepend, pNode, context){//加载一个或多个文件
			if(!f) return;
			f = iCat.isString(f)? [f] : f;
			
			(function(){
				if(f.length){
					var curJS = f.shift();
					curJS = _dealUrl(curJS);
				} else
					return;

				if(f.length){
					var fn = arguments.callee;

					if(isDepend)//文件间有依赖 顺序加载
						_unblockImport(curJS, function(){fn(f);}, undefined, context);
					else {
						_unblockImport(curJS, undefined, undefined, context);
						fn(f);
					}
				} else {
					_unblockImport(curJS, cb, undefined, context);
				}
			})();
		},
		
		/* 加载文件形式：
		 * - 单个文件，支持字符串或文件数组(length为1)
		 * - 多个文件，必须是文件数组
		 */
		require: function(m, f, cb, pNode, context){//加载有依赖的模块
			if(!f) return;
			f = iCat.isString(f)? [f] : f;
			
			if(_modGroup[m]){
				if(cb) cb(context);
			} else {
				(function(){
					if(f.length){
						var curJS = f.shift();
						curJS = _dealUrl(curJS);
					} else
						return;

					if(f.length){
						var fn = arguments.callee;
						_unblockImport(curJS, function(){fn(f);}, m, context);
					} else {
						_unblockImport(curJS, cb, m, context);
					}
				})();
			}
		},
		
		//使用已加载后的模块
		use: function(m, cb, t, context){
			var i = 0, t = t || 100, timer;
			
			if(_modGroup[m]){
				if(cb) cb(context);
			} else if(iCat.modsConfig[m]){
				timer = setInterval(function(){
					i += 5;
					if(_modGroup[m]){
						clearInterval(timer);
						if(cb) cb(context);
					} else if(i>=t){
						clearInterval(timer);
						iCat.require(m, iCat.modsConfig[m], cb, context);
					}
				},5);
			}
			
		}
	});
	
	/* 加载js库和关键js */
	if(_corecss){
		iCat.inc(_corecss);
	}
	
	if(_corelib){
		_corelib = _corelib.split(',');
		iCat.inc(_corelib);
	}else if(_asynCorelib){
		_asynCorelib = _asynCorelib.split(',');
		iCat.incfile(_asynCorelib, undefined, true);
	}
	
	if(_mainJS)
		iCat.inc(_mainJS);
	else if(_asynMainJS)
		iCat.incfile(_asynMainJS);
})(ICAT);

/** dom.js */
(function(iCat, doc){

	// 创建Dom命名空间
	iCat.namespace('Dom');

	var Dom = iCat.Dom,
		doc = document, apSlice = Array.prototype.slice,
		testStyle = doc.createElement('div').style,

		vendor = function(){//检测css3支持前缀
			var style = testStyle,
				vendors = ['t','WebkitT','MozT','msT','OT'],
				t;
			for(var i = 0, l = vendors.length; i<l; i++){
				t = vendors[i]+'ransform';
				if(t in style){
					return vendors[i].slice(0,-1);
				}
			}
			return false;
		}(),
		
		cssVendor = vendor? '-'+vendor.toLowerCase()+'-' : '',
		hasTransform = !!vendor,//是否支持css3 transform
		hasTransition = vendor+'Transition' in testStyle,//是否支持css3 transition
		hasBorderImage = vendor+'BorderImage' in testStyle;//是否支持css3 transition

	function _matches(el, selector){
		var docElem = doc.documentElement,
			match = docElem.matchesSelector || docElem.mozMatchesSelector || docElem.webkitMatchesSelector ||
				docElem.oMatchesSelector || docElem.msMatchesSelector;
		return match.call(el,selector);
	}

	// base
	iCat.mix(Dom, {

		one: function(s, cx){
			return !s? doc : (cx||doc).querySelector(s);
		},

		all: function(s, cx){
			return !s? [doc] :
				apSlice.call(
					(cx || doc).querySelectorAll(s)
				);
		},

		filter: function(els, s){
			if(!els.length || !s)
				return els;
			
			var newEls = [];
			els.forEach(function(el){
				if(iCat.isString(s) && _matches(el, s))
					newEls.push(el);
				if(s.nodeType && el==s)
					newEls.push(el);
			});

			return newEls;
		},

		not: function(els, s){
			if(!els.length || !s)
				return els;
			
			var newEls = [];
			els.forEach(function(el){
				if(iCat.isString(s) && !_matches(el, s))
					newEls.push(el);
				if(s.nodeType && el!=s)
					newEls.push(el);
			});

			return newEls;
		},

		index: function(el, els){}
	});

	// related nodes
	iCat.mix(Dom, {

		parent: function(el){
			if(!el) return null;

			if(!iCat.isArray(el)){
				return el.parentNode;
			} else {
				if(!el.length) return null;

				var arr = [];
				for(var i=0, l=el.length; i<l; i++){
					var item = arguments.callee(el[i]);
					if(!arr.hasItem(item))
						arr.push(item);
				}
				return arr;
			}
		},

		parents: function(el, s){
			if(!el) return null;

			if(!iCat.isArray(el)){
				if(!s || typeof s=='number'){
					s = s || 1;
					for(var i=0; i<s; i++){
						if(!iCat.isNull(el))
							el = el.parentNode;
					}
				} else {
					(function(){
						el = el.parentNode;
						if(iCat.isNull(el)) return;
						if(_matches(el,s)){
							return;
						} else {
							arguments.callee();
						}
					})();
				}
				return el;
			} else {
				if(!el.length) return null;

				var arr = [];
				for(var i=0, l=el.length; i<l; i++){
					var item = arguments.callee(el[i], s);
					if(!arr.hasItem(item))
						arr.push(item);
				}
				return arr;
			}
		},

		children: function(el, s){
			if(!el) return null;

			if(!iCat.isArray(el)){
				var c_els = el.childNodes,
					l = c_els.length,
					arr = [];
				for(var i=0; i<l; i++){
					var e = c_els[i];
					if(!s){
						if(e.nodeType==1 && !arr.hasItem(e))
							arr.push(e);
					} else {
						if(e.nodeType==1 && _matches(e,s) && !arr.hasItem(e))
							arr.push(e);
					}
				}
				return arr;
			} else {
				if(!el.length) return null;

				var _arr = [];
				for(var i=0, l=el.length; i<l; i++){
					var item = arguments.callee(el[i], s);
					_arr = _arr.concat(item);
				}
				return _arr;//.unique()
			}
		},

		siblings: function(el, s){
			if(!el) return null;

			if(!iCat.isArray(el)){
				var p = el.parentNode,
					c = Dom.children(p, s);
				return Dom.not(c, el);
			} else {
				if(!el.length) return null;

				var _arr = [];
				for(var i=0, l=el.length; i<l; i++){
					var item = arguments.callee(el[i], s);
					_arr = _arr.concat(item);
				}
				return _arr;
			}
		},

		prev: function(el, s){
			if(!el) return null;

			if(!iCat.isArray(el)){
				if(!s){
					do {
						el = el.previousSibling;
					} while (el && el.nodeType!=1);
				} else {
					(function(){
						el = el.previousSibling;

						if(iCat.isNull(el)) return;
						if(el.nodeType==1 && _matches(el,s)){
							return;
						} else {
							arguments.callee();
						}
					})();
				}
				return el;
			} else {
				if(!el.length) return null;

				var arr = [];
				for(var i=0, l=el.length; i<l; i++){
					var item = arguments.callee(el[i], s);
					if(!arr.hasItem(item))
						arr.push(item);
				}
				return arr;
			}
		},

		next: function(el, s){
			if(!el) return null;

			if(!iCat.isArray(el)){
				if(!s){
					do {
						el = el.nextSibling;
					} while (el && el.nodeType!=1);
				} else {
					(function(){
						el = el.nextSibling;

						if(iCat.isNull(el)) return;
						if(el.nodeType==1 && _matches(el,s)){
							return;
						}
						else {
							arguments.callee();
						}
					})();
				}
				return el;
			} else {
				if(!el.length) return null;

				var arr = [];
				for(var i=0, l=el.length; i<l; i++){
					var item = arguments.callee(el[i], s);
					if(!arr.hasItem(item))
						arr.push(item);
				}
				return arr;
			}
		},

		first: function(el){
			if(!el) return null;

			if(!iCat.isArray(el)){
				el = el.firstChild;
				return el && el.nodeType!=1? Dom.next(el) : el;
			} else {
				if(!el.length) return null;

				var arr = [];
				for(var i=0, l=el.length; i<l; i++){
					var item = arguments.callee(el[i]);
					if(!arr.hasItem(item))
						arr.push(item);
				}
				return arr;
			}
		},

		last: function(el){
			if(!el) return null;

			if(!iCat.isArray(el)){
				el = el.lastChild;
				return el && el.nodeType!=1? Dom.prev(el) : el;
			} else {
				if(!el.length) return null;

				var arr = [];
				for(var i=0, l=el.length; i<l; i++){
					var item = arguments.callee(el[i]);
					if(!arr.hasItem(item))
						arr.push(item);
				}
				return arr;
			}
		},

		closest: function(el, s){

		}
	});

	// css & attribute & position & size
	iCat.mix(Dom, {

		hasClass: function(el, cla){
			if(!el) return null;

			if(!iCat.isArray(el)){
				el = iCat.isArray(el)? el[0] : el;
				return new RegExp('(?:^|\\s+)'+cla+'(?:\\s+|$)').test(el.className);
			} else {
				if(!el.length) return null;

				var arr = [];
				for(var i=0, l=el.length; i<l; i++){
					arr.push(arguments.callee(el[i]));
				}
				return arr;
			}
		},

		addClass: function(el, cla){
			if(!el) return;

			if(!iCat.isArray(el)){
				if(!Dom.hasClass(el,cla)){
					var cn = el.className;
					cn = !cn? cla : [cn, cla].join(' ');
					el.className = cn.trim();
				}
			} else {
				if(!el.length) return;
				for(var i=0, l=el.length; i<l; i++){
					arguments.callee(el[i], cla);
				}
			}
		},

		removeClass: function(el, cla){
			if(!el) return;

			if(!iCat.isArray(el)){
				if(Dom.hasClass(el,cla)){
					var cn = el.className;
					cn = cn.replace(new RegExp('(?:^|\\s+)'+cla+'(?:\\s+|$)', 'g'), ' ');
					el.className = cn.trim();
				}
			} else {
				if(!el.length) return;
				for(var i=0, l=el.length; i<l; i++){
					arguments.callee(el[i], cla);
				}
			}
		},

		replaceClass: function(el, oldcla, newcla){
			if(!el) return;

			if(!iCat.isArray(el)){
				if(Dom.hasClass(el,oldcla)){
					var cn = el.className;
					cn = cn.replace(new RegExp('(?:^|\\s+)'+oldcla+'(?:\\s+|$)','g'), ' '+newcla+' ');
					el.className = cn.trim();
				}
			} else {
				if(!el.length) return;
				for(var i=0, l=el.length; i<l; i++){
					arguments.callee(el[i], oldcla, newcla);
				}
			}
		},

		toggleClass: function(el, cla){
			if(!el) return;

			if(!iCat.isArray(el)){
				Dom[Dom.hasClass(el,cla)? 'removeClass' : 'addClass'](el, cla);
			} else {
				if(!el.length) return;
				for(var i=0, l=el.length; i<l; i++){
					arguments.callee(el[i], cla);
				}
			}
		},

		attr: function(){},

		removeAttr: function(){},

		// 样式 设置时必须有单位
		css: function(){

			function styleFilter(p){
				switch(p){
					case 'float':
						return ('cssFloat' in doc.body.style)? 'cssFloat' : 'styleFloat';
						break;
					
					case 'opacity':
						return ('opacity' in doc.body.style)? 'opacity' :
							{
								get: function(el,style){
									var ft = style.filter;
									return ft && ft.indexOf('opacity')>=0 && parseFloat(ft.match(/opacity=([^)]*)/i)[1])/100+'' || '1';
								},
								set: function(el,va){
									el.style.filter = 'alpha(opacity='+va*100+')';
									el.style.zoom = 1;
								}
							}
						break;
					
					default:
						if(p.indexOf('-')>-1){
							var arr = p.split('-');
							for(var i=0, l=arr.length; i<l; i++){
								if(arr[i]=='webkit' || arr[i]=='ms' || arr[i]=='moz' || arr[i]=='o') continue;
								arr[i] = arr[i].substring(0,1).toUpperCase()+arr[i].substring(1);
							}
							p = arr.join('');
						}
						return p;
						break;
				}
			}

			function getStyle(el, p){
				if(!el) return null;

				if(!iCat.isArray(el)){
					p = styleFilter(p);
					var val = el.style[p];
					if(!val){
						var style = doc.defaultView && doc.defaultView.getComputedStyle && getComputedStyle(el, null) || el.currentStyle || el.style;
						val = iCat.isString(p)? style[p] : p.get(el, style);
					}
					return val=='auto'? '' : val;
				} else {
					if(!el.length) return null;

					var arr = [];
					for(var i=0, l=el.length; i<l; i++){
						var item = arguments.callee(el[i], p);
						arr.push(item);
					}
					return arr;
				}
			}

			function setStyle(el, o){
				if(!el || !iCat.isObject(o)) return;

				if(!iCat.isArray(el)){
					var attr;
					iCat.foreach(o, function(k, v){
						attr = styleFilter(k);
						iCat.isString(attr)? el.style[attr] = v : attr.set(el, v);
					});
				} else {
					if(!el.length) return;
					for(var i=0, l=el.length; i<l; i++){
						arguments.callee(el[i], o);
					}
				}
			}

			return function(el, styleCss){
				if(iCat.isString(styleCss))
					return getStyle(el,styleCss);
				else
					setStyle(el, styleCss);
			}
		}(),

		// 位置 设置时必须有单位
		position: function(){

			function getPos(el){
				if(!el) return null;

				if(!iCat.isArray(el)){
					var x = 0, y = 0;
					do {
						x += el.offsetLeft || 0;
						y += el.offsetTop || 0;
						el = el.offsetParent;
					} while(el);
					return {'left':x, 'top':y};
				} else {
					if(!el.length) return null;

					var arr = [];
					for(var i=0, l=el.length; i<l; i++){
						var item = arguments.callee(el[i]);
						arr.push(item);
					}
					return arr;
				}
			}

			function setPos(el, pos){
				if(!el) return;

				if(!iCat.isArray(el)){
					pos = typeof pos=='number'? pos+'px' : pos;
					if(iCat.isString(pos))
						return setPos(el, {left:pos});
					var st = {},
						isX = typeof pos.left!='undefined',
						isY = typeof pos.top!='undefined',
						isPosistion = /absolute|relative/i.test(Dom.css(el, 'position'));
					if(hasTransition && !isPosistion){
						if(isX && isY){
							st[cssVendor+'transform'] = 'translate('+pos.left+', '+pos.top+')';
						}else{
							if(isX)
								st[cssVendor+'transform'] = 'translateX('+pos.left+')';
							if(isY)
								st[cssVendor+'transform'] = 'translateY('+pos.top+')';
						}
					} else {
						if(isX)
							st['left'] = pos.left;
						if(isY)
							st['top'] = pos.top;
					}

					Dom.css(el, st);
				} else {
					if(!el.length) return;
					for(var i=0, l=el.length; i<l; i++){
						arguments.callee(el[i], pos);
					}
				}
			}

			return function(el, pos){
				if(typeof pos=='undefined')
					return getPos(el);
				else
					setPos(el, pos);
			}
		}(),

		// 偏移量 设置时必须有单位
		offset: function(){
			function getOffset(el){
				if(!el) return null;

				if(!iCat.isArray(el)){
					var x = el.offsetLeft || 0,
						y = el.offsetTop || 0;
					return {'left':x, 'top':y};
				} else {
					if(!el.length) return null;

					var arr = [];
					for(var i=0, l=el.length; i<l; i++){
						var item = arguments.callee(el[i]);
						arr.push(item);
					}
					return arr;
				}
			}

			return function(el, pos){
				return typeof pos=='undefined'? getOffset(el) : Dom.position(el, pos);
			}
		}(),

		width: function(el, w){
			return Dom.css(el, w? {width:w} : 'width');
		},

		height: function(el, w){
			return Dom.css(el, w? {height:w} : 'height');
		}
	});

	// join dom
	iCat.mix(Dom, {
		html: function(el, shtml){},
		before: function(){},
		after: function(){}
	});

	// iCat.$ as jQuery
	var $ = iCat.$ = function(s, cx){return new $.fn.init(s, cx);};
	$.fn = $.prototype = {
		constructor: $,
		init: function(s, cx){
			if(!s) return this;

			if(s.nodeType){
				this.selector = [s];
				return this;
			}

			if(iCat.isString(s)){
				this.selector = Dom.all(s, cx);
				return this;
			} else if(iCat.isArray(s)){
				this.selector = s.length? s : [doc];
				return this;
			}
		},

		size: function(){
			return this.selector.length;
		}
	};
	$.fn.init.prototype = $.fn;

	iCat.foreach(Dom, function(k, v){
		if(k=='one' || k=='all') return;
		$.fn[k] = function(){
			var arr = apSlice.call(arguments),
				els = this.selector, _arr, isNode;
			if(!els.length) return this;

			arr.unshift(els);
			var rv = v.apply(els||this, arr);
			if(rv){
				_arr = [];
				for(var i=0, l=rv.length; i<l; i++){
					if(!iCat.isNull(rv[i]) && rv[i].nodeType){
						_arr.push(rv[i]);
						isNode = true;
					}
				}
				_arr = isNode? $(_arr) : rv;
			} else {
				_arr = this;
			}
			
			return _arr;
		}
	});

	$.extend = $.fn.extend = function(o){
		if(!iCat.isObject(o)) return this;

		var _self = this;
		iCat.foreach(o, function(k, v){
			if(iCat.isFunction(v)){
				_self[k] = function(){
					return v.apply(this.selector||_self, arguments);
				};
			} else {
				_self[k] = v;
			}
		});
		return _self;
	};

	// extend jquery's funs
	$.fn.extend({
		get: function(num){
			return num==null? apSlice.call(this) :
				(num<0? this[this.length+num] : this[num]);
		}
	});
})(ICAT, document);

/** event.js */
(function(iCat){
	
	// 创建Event命名空间
	iCat.namespace('Event');

	function _matches(el, selector){
		var docElem = document.documentElement,
			match = docElem.matchesSelector || docElem.mozMatchesSelector || docElem.webkitMatchesSelector ||
				docElem.oMatchesSelector || docElem.msMatchesSelector;
		return match.call(el,selector);
	}

	function _parentIfText(node){
		return 'tagName' in node ? node : node.parentNode;
	}

	// 创建Observer类
	iCat.Class('Observer', {
		Create: function(pageid){
			this.selectors = [];
			this.events = {};
			this.pageid = pageid;
		},

		/*
		 * argus可以是<b>单个对象</b>或<b>对象数组</b>
		 * o = {el:'.cla', eType:'click', callback: function(){}, stopDefault: true, stopBubble:false}
		 */
		subscribe: function(o){
			var self = this;
			if(!o) return self;

			o = iCat.isArray(o)? o : [o];
			iCat.foreach(o, function(i,v){
				if(!self.selectors.hasItem(v.el))
					self.selectors.push(v.el);

				var key = v.el.trim()+'|'+(v.stopDefault? 1:0)+'|'+(v.stopBubble? 1:0),
					eType = v.eType.trim();

				switch(eType) {
					case 'click':
						eType = 'tap';
						break;
					case 'longClick':
						eType = 'longTap';
						break;
					case 'doubleClick':
						eType = 'doubleTap';
						break;
					case 'singleClick':
						eType = 'singleTap';
						break;
					case 'moving':
						eType = 'swiping';
						break;
				}

				if(!self.events[eType])
					self.events[eType] = {}; //{'click':{}, 'longTap':{}}

				if(!self.events[eType][key])
					self.events[eType][key] = v.callback; // {'click':{'li|0|1':function, '.test a|1|1':function}, 'longTap':{}}
			});

			return self;
		},

		unsubscribe: function(key){
			var self = this;
			if(!key){
				self.events = {};
			} else {
				key = iCat.isArray(key)? key : [key];
				key.forEach(function(v){
					if(v.indexOf('|')>0){
						v = v.split('|');
						delete self.events[v[1].trim()][v[0].trim()];
					} else {
						delete self.events[v.trim()];
					}
				});
			}

			return self;
		},

		execute: function(eType, el, argus){
			var self = this, key,
				cbs = self.events[eType];
			if(!cbs) return;

			for(key in cbs){
				var k = key.split('|'),
					iamhere = false;
				(function(node, cb){
					if(_matches(node, k[0])){
						cb.apply(node, argus);
						if(k[1]==0)//值为0，不阻止默认事件
							iCat.Event.triggerEvent(node, 'click', false, true);
						iamhere = true;
					} else {
						if(node.parentNode!==doc.body){
							arguments.callee(node.parentNode, cb);
						}
					}
				})(el, cbs[key]);
				
				if(iamhere && k[2]==1)//值为1，阻止冒泡
					return;
			}
		},

		setCurrent: function(){
			iCat.__OBSERVER_PAGEID = this.pageid;
			return this;
		},

		on: function(selector, eType, callback, stopDefault, stopBubble){
			return this.subscribe({
				el: selector,
				eType: eType,
				callback: callback,
				stopDefault: stopDefault,
				stopBubble: stopBubble
			});
		},

		off: function(selector, eType){
			return this.unsubscribe(selector+'|'+eType);
		}
	});

	// iCat创建观察者
	iCat.obsCreate = function(pid){
		if(!iCat.obsCreate[pid])
			iCat.obsCreate[pid] = new Observer(pid);
		return iCat.obsCreate[pid];
	};

	// iCat删除观察者
	iCat.obsDestroy = function(pid){
		iCat.obsCreate[pid] = null;
		iCat.__OBSERVER_PAGEID = '__PAGE_EVENT';
	};

	// 默认观察者
	var Event = iCat.Event = iCat.obsCreate('__PAGE_EVENT'),
		doc = document,

		__bindEvent = //单个绑定
			doc.addEventListener ?
				function(el, type, handler){
					if(_matches(el, '*[data-pagerole=body]')){
						el.addEventListener(type, handler, false);
					} else {
						var eventId = 'icat_event'+Math.floor(Math.random()*1000000+1);
						el.setAttribute(type+'-eventId', eventId);
						Event[eventId] = handler;
						el.addEventListener(type, Event[eventId], false);
					}
				}
				: doc.attachEvent ?
					function(el, type, handler){
						if(_matches(el, '*[data-pagerole=body]')){
							el.attachEvent('on'+type, handler);
						} else {
							var eventId = 'icatevent'+Math.floor(Math.random()*1000000+1);
							el.setAttribute('eventId', eventId);
							Event[eventId] = handler;
							el.attachEvent('on'+type, Event[eventId]);
						}
					}
					: function(el, type, handler){el['on'+type] = handler;},
		
		__removeEvent = //单个解绑
			doc.removeEventListener ?
				function(el, type, handler){
					var eventId = el.getAttribute(type+'-eventId');
					if(!eventId) return;
					el.removeEventListener(type, Event[eventId], false);
					delete Event[eventId];
				}
				: doc.detachEvent ?
					function(el, type, handler){
						var eventId = el.getAttribute(type+'-eventId');
						if(!eventId) return;
						el.detachEvent('on'+type, Event[eventId]);
						delete Event[eventId];
					}
					: function(el, type, handler){ el['on'+type] = null; };
	
	iCat.mix(Event, {
		preventDefault: function(evt){
			if(evt && evt.preventDefault)
				evt.preventDefault();
			else
				window.event.returnValue = false;
		},

		stopPropagation: function(evt){
			if(window.event){
				window.event.cancelBubble = true;
			} else {
				evt.stopPropagation();
			}
		},

		bindEvent: function(el, type, handler){
			if(!el) return;
			!el.length ?
				__bindEvent(el, type, handler)
				:
				iCat.foreach(el, function(i,v){
					__bindEvent(v, type, handler);
				});
		},

		removeEvent: function(el, type, handler){
			if(!el) return;
			!el.length ?
				__removeEvent(el, type, handler)
				:
				iCat.foreach(el, function(i,v){
					__removeEvent(v, type, handler);
				});
		},

		triggerEvent: function(element, type, bubbles, cancelable){
			if(doc.createEventObject){
				var evt = doc.createEventObject();
				element.fireEvent('on'+type, evt);
			} else {
				var ev = doc.createEvent('Event');
				ev.initEvent(type, bubbles, cancelable);
				element.dispatchEvent(ev);
			}
		},

		ready: function(){
			var _fn = [];
			var _do = function(){
				if(!arguments.callee.done){
					arguments.callee.done = true;
					for(var i=0; i<_fn.length; i++){
						_fn[i]();
					}
				}
			};

			if(doc.addEventListener){
				doc.addEventListener('DOMContentLoaded', _do, false);
			}

			if(iCat.browser.msie){
				(function(){
					try{
						doc.documentElement.doScroll('left');
					} catch(e) {
						setTimeout(arguments.callee, 50);
						return;
					}
					doc.onreadystatechange = function(){
						if(doc.readyState==='complete'){
							doc.onreadystatechange = null;
							_do();
						}
					};
				})();
			}

			if(iCat.browser.webkit && doc.readyState){
				(function(){
					if(doc.readyState!=='loading'){
						_do();
					} else {
						setTimeout(arguments.callee, 10);
					}
				})();
			}

			window.onload = _do;

			return function(fn){
				if(iCat.isFunction(fn)){
					_fn[_fn.length] = fn;
				}
				return fn;
			};
		}()
	});

	// 所有事件的实现都绑定在body上
	(function(){
		var touch = {}, touchTimeout,
			supportTouch = 'ontouchstart' in window;

		var start_evt = supportTouch ? 'touchstart' : 'mousedown',
			move_evt = supportTouch ? 'touchmove' : 'mousemove',
			end_evt = supportTouch ? 'touchend' : 'mouseup',
			cancel_evt = 'touchcancel';

		// common functions
		function swipeDirection(x1, x2, y1, y2){
			var xDelta = Math.abs(x1 - x2), yDelta = Math.abs(y1 - y2);
			return xDelta >= yDelta ? (x1 - x2 > 0 ? 'Left' : 'Right') : (y1 - y2 > 0 ? 'Up' : 'Down');
		}

		var longTapDelay = 750, longTapTimeout;
		function cancelLongTap(){
			if(longTapTimeout)
				clearTimeout(longTapTimeout);
			longTapTimeout = null;
		}

		Event.ready(function(){

			if(!iCat.__OBSERVER_PAGEID || iCat.obsCreate[iCat.__OBSERVER_PAGEID]==null) iCat.__OBSERVER_PAGEID = '__PAGE_EVENT';
			
			var bodyNode = doc.querySelector('*[data-pagerole=body]'),
				now, delta;
			if(!bodyNode) return;

			// start
			Event.bindEvent(bodyNode, start_evt, function(evt){
				var objObs = iCat.obsCreate[iCat.__OBSERVER_PAGEID],
					page = supportTouch? evt.touches[0] : evt;
				now = Date.now();
				delta = now - (touch.last || now);
				touch.el = _parentIfText(evt.target);
				touchTimeout && clearTimeout(touchTimeout);

				touch.x1 = page.pageX;
				touch.y1 = page.pageY;
				touch.isScrolling = undefined;

				if(delta>0 && delta<=250) touch.isDoubleTap = true;
				touch.last = now;
				longTapTimeout = setTimeout(function(){
						longTapTimeout = null;
						if(touch.last){
							objObs.execute('longTap', touch.el);
							touch = {};
						}
					}, longTapDelay);
				Event.stopPropagation(evt);
			});

			// doing
			Event.bindEvent(bodyNode, move_evt, function(evt){
				cancelLongTap();
				var objObs = iCat.obsCreate[iCat.__OBSERVER_PAGEID],
					page = supportTouch? evt.touches[0] : evt;
				touch.x2 = page.pageX;
				touch.y2 = page.pageY;
				var distanceX = touch.x2 - touch.x1,
					distanceY = touch.y2 - touch.y1;
				if(typeof touch.isScrolling=='undefined'){
					touch.isScrolling = !!(touch.isScrolling || Math.abs(distanceX)<Math.abs(distanceY));
				}
				if(!touch.isScrolling){
					Event.preventDefault(evt);
					objObs.execute('swiping', touch.el, [touch.x1, touch.x2, touch.y1, touch.y2]);
					Event.stopPropagation(evt);
				}
			});

			// end
			Event.bindEvent(bodyNode, end_evt, function(evt){
				cancelLongTap();
				var objObs = iCat.obsCreate[iCat.__OBSERVER_PAGEID];

				if(!touch.isScrolling){
					// double tap (tapped twice within 250ms)
					if(touch.isDoubleTap){
						objObs.execute('doubleTap', touch.el);
						touch = {};
					} else if('last' in touch) {
						objObs.execute('tap', touch.el);

						touchTimeout = setTimeout(function(){
							touchTimeout = null;
							objObs.execute('singleTap', touch.el);
							touch = {};
						}, 250);
					} else if((touch.x2&&Math.abs(touch.x1-touch.x2)>30) || (touch.y2&&Math.abs(touch.y1-touch.y2)>30)){
						var swipe = 'swipe' + swipeDirection(touch.x1, touch.x2, touch.y1, touch.y2);
						objObs.execute(swipe, touch.el);
						objObs.execute(swipe, touch.el);
						touch = {};
					}
				} else {
					touch = {};
				}
				Event.stopPropagation(evt);
			});

			// cancel
			Event.bindEvent(bodyNode, cancel_evt, function(evt){
				Event.preventDefault(evt);
				Event.stopPropagation(evt);

				if(touchTimeout) clearTimeout(touchTimeout);
				if(longTapTimeout) clearTimeout(longTapTimeout);
				longTapTimeout = touchTimeout = null;
				touch = {};
			});

			// Stops the default click event
			Event.bindEvent(bodyNode, 'click', function(evt){
				var objObs = iCat.obsCreate[iCat.__OBSERVER_PAGEID],
					el = _parentIfText(evt.target),
					selectors = objObs.selectors;
				if(!el || el==doc.body) return;

				for(var i=0; i<selectors.length; i++){
					(function(){
						if(_matches(el, selectors[i])){
							Event.preventDefault(evt);
							Event.stopPropagation(evt);
						} else {
							if(el.parentNode!==doc.body){
								el = el.parentNode;
								arguments.callee();
							}
						}
					})();
				}
			});
		});
	})();
})(ICAT);

/**
 *
 * NOTES:
 *
 * 2013-01-31 13:00:00
 * - 新增dom模块，支持YUI写法
 * - 增加对jQuery写法的支持
 *
 * 2013-01-21 11:00:00
 * - 模拟事件冒泡，不过跟队列有关
 * - 优化阻止冒泡
 *
 * 2013-01-12 12:48:00
 * - 优化了阻止默认事件的逻辑
 *
 * 2013-01-09 12:00:00
 * - 增加event-observer模块
 *
 * 2012-12-04 21:03:00
 * - 改善了widget方法，使其生成的构造函数在iCat.Widget对象之下
 * - 新增了util方法，使其生成的方法在iCat.Util对象之下
 * - 新增了节流函数
 *
 * 2012-11-29 19:52:00
 * - 重构了代码，提升了性能，使其无多余代码，仅仅裸露ICAT
 * - 增加了Class、iCat.widget，用于扩展UI组件
 *
 * 2012-10-31 09:14:30
 * - 增加插入script节点的父层
 * - 增加mainjs、corelib异步加载的支持
 *
 * 2012-09-23 13:45:00
 * - 抛开underscore.js、json2.js，感觉框架太臃肿了
 * - 新增iCat函数，使其成为Class的制造体
 *
 * 2012-09-23 10:00:00
 * 为了更和谐地利用backbone，icat融合了underscore.js、json2.js
 * - 避免冲突，改include方法名为incfile
 * - corelib支持多个文件设置
 *
 */