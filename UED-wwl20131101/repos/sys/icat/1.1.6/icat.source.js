/*!
 * Copyright 2011~2013, ICAT JavaScript Library 1.1.6
 * https://github.com/valleykid/icat
 *
 * Copyright (c) 2013 valleykid
 * Licensed under the MIT license.
 *
 * @Author valleykiddy@gmail.com
 * @Time 2013-12-31 10:11:16
 */

/* core.js # */
;(function(root, doc, C, undefined){
	var iCat = root[C] || { version: '1.1.6' };

	// Export the ICAT object for **Node.js**
	if(typeof exports!=='undefined'){
		if(typeof module!=='undefined' && module.exports){
			exports = module.exports = iCat;
		}
		exports.ICAT = iCat;
	} else {
		root[C] = iCat;
	}

	// Compatible plugin for PC
	iCat.Shim = root['SHIM'] || {};

	// jQuery/Zepto is coming in
	iCat.$ = root.jQuery || root.Zepto || root.ender || root.$;

	// Kinds of judgments
	foreach(
		['String', 'Boolean', 'Function', 'Array', 'Object', 'Number'],
		function(v){
			iCat['is'+v] = function(obj){
				if(obj===undefined || obj===null) return false;// fixed bug: ie下undefined/null为Object
				return Object.prototype.toString.call(obj).slice(8, -1) === v;
			};
		}
	);

	// Kinds of modes
	foreach({
			'DebugMode': /debug/i,
			'DemoMode': /file\:\/\/|localhost|dev\.|demo\.|\/{2}\d+(\.\d+){3}/i,
			'IPMode': /\/{2}\d+(\.\d+){3}/
		},
		function(v, k){ iCat[k] = v.test(location.href); }
	);

	// extend iCat-object
	mix(iCat, {

		mix: mix,

		foreach: foreach,

		isjQueryObject: function(obj){
			return iCat.$ && (obj instanceof iCat.$);
		},

		isEmptyObject: function(obj){
			for(var name in obj){
				return false;
			}
			return true;
		},

		// 数组(对象/字符串)是否包含某值
		hasItem: function(o, p){
			if(iCat.isArray(o)){
				if('indexOf' in o) return o.indexOf(p)>-1;

				var ret; // for ie8-
				foreach(o, function(v){
					if(v===p){ return !(ret = true); }
				});
				return ret;
			}
			if(iCat.isObject(o)) return p in o;
			if(iCat.isString(o)) return o.indexOf(p)>-1;
			return false;
		},

		// 数组删除指定元素
		delItem: function(arr, item){
			foreach(arr, function(v, i){
				if(v===item){ arr.splice(i, 1); }
			});
		},
		
		unique: _unique,

		// 类数组转化为数组
		toArray: function(oArray){
			var arr = [];
			foreach(oArray, function(v){ arr.push(v); });
			return arr;
		},

		// iCat或app下的namespace，相当于扩展出的对象
		namespace: function(){
			var a = arguments, l = a.length,
				o = null, i, j, p;

			for(i=0; i<l; ++i){
				p = ('' + a[i]).split('.');
				o = this;
				for(j = (root[p[0]]===o)? 1:0; j<p.length; ++j){
					o = o[p[j]] = o[p[j]] || {};
				}
			}
			return o;
		},

		exports: {},
		
		// create a app for some project
		app: function(name, sx){
			var isStr = iCat.isString(name),
				o = isStr? root[name] || {} : name;

			mix(o, iCat, ['namespace'], true);
			mix(o, iCat.isFunction(sx) ? sx() : sx);
			isStr && (iCat.app[name] = root[name] = o);

			return o;
		},

		/**
		 * ICAT's utils/tools
		 * @param  {String/Function}   name 方法名/执行函数
		 * @param  {Function}          fn   方法体
		 */
		util: function(name, fn){
			if(iCat.isString(name)){
				iCat.util[name] = fn;
			}
			else if(iCat.isFunction(name)){
				mix(iCat.util, name());
			}
			else {
				mix(iCat.util, name);
			}
		},

		widget: function(name, fn){
			if(!iCat.isString(name)) return;
			iCat.widget[name] = iCat.isFunction(fn)? fn() : fn;
		},

		/**
		 * 等待某对象生效后执行操作
		 * @param  {Function} cb       [description]
		 * @param  {[type]}   delay    超时临界值
		 * @param  {[type]}   step     每隔多少ms执行一次
		 */
		wait: function(cb, delay, step){
			delay = delay || 100;
			step = step || 10;
			var Cache = iCat.__cache_timers = iCat.__cache_timers || {};
			var steps = 0, fn,
				key = 'icat_timer' + Math.floor(Math.random()*1000000+1);

			(fn = function(){
				cb(key, steps===delay, Cache);
				if(steps<=delay && Cache[key]===false){
					setTimeout(function(){
						steps += step;
						fn();
					}, step);
				}
			})();
		},

		rent: function(name, fnRent, cfg){
			if(iCat.isFunction(fnRent)){
				iCat.util[name] = function(newCfg){
					iCat.mix(newCfg, cfg);
					fnRent(newCfg);
				};
			} else {
				iCat.widget(name, fnRent);
			}
		},

		// 参考：https://github.com/ded/domready
		// http://www.cnblogs.com/zhangziqiu/archive/2011/06/27/DOMReady.html
		ready: function(ready){
			var fns = [], DDE = doc.documentElement,
				hack = DDE.doScroll, handler,
				loaded = (hack? /^loaded|^c/ : /^loaded|c/).test(doc.readyState),
				flush = function(f){
					loaded = 1;
					while(f=fns.shift()) f();
				};

			if(doc.addEventListener){
				doc.addEventListener('DOMContentLoaded', handler = function(){
					doc.removeEventListener('DOMContentLoaded', handler, false);
					flush();
				}, false);
			}

			if(hack){
				doc.attachEvent('onreadystatechange', handler = function(){
					if(/^c/.test(doc.readyState)){
						doc.detachEvent('onreadystatechange', handler);
						flush();
					}
				});
			}

			return (ready = hack?
				function(fn){
					self!=top?
						loaded? fn() : fns.push(fn) :
						function(){
							try{
								DDE.doScroll('left');
							} catch(e) {
								return setTimeout(function(){ ready(fn); }, 50);
							}
							fn();
						}()
				} :
				function(fn){ loaded? fn() : fns.push(fn); }
			);
		}(),

		// 打印消息(ie6下弹出消息框)
		log: function(msg){
			if(!iCat.DebugMode || !iCat.DemoMode) return;
			if(!!doc.all){
				root.console && console.log? console.log(msg) : alert(msg);
			} else {
				try{ __$abc_ICAT(); }
				catch(e){
					var fileline = e.stack // fixed bug:Direction on an input element that cannot have a selection.
									.replace(/\n\s+at\s+<.*>/g, '')
									.split('\n')[2];
					fileline = fileline.replace(/.*[\(\s]|\).*/g, '').replace(/(.*):(\d+):\d+/g, '$1  line $2:\n');
					console.log(fileline, msg);
				}
			}
		}
	});

	// more code styles
	//iCat.namespace('MM', 'AMD', 'OO', 'SM', '$$');

	/**
     * Handles objects with the built-in 'foreach', arrays, and raw objects.
     * @param {Array/Object} o      被遍历的对象/数组
     * @param {Function}     cb     回调方法，返回false则跳出遍历
     * @param {Array}        args   传递给回调方法的参数
     * @param {Boolean}      setObj 设定o是或不是对象
     */
	function foreach(o, cb, args, setObj){
		var name, i = 0, length = o.length,
			isObj = setObj || length===undefined;
		
		if(args){
			if(isObj){
				for(name in o){
					if(cb.apply(o[name], args)===false){
						break;
					}
				}
			} else {
				for(  ; i<length; ){
					if(cb.apply(o[i++], args)===false){
						break;
					}
				}
			}
		} else {
			if(isObj){
				for(name in o){
					if(cb.call(o[name], o[name], name)===false){
						break;
					}
				}
			} else {
				for( ; i<length; i++){
					if(cb.call(o[i], o[i], i)===false){
						break;
					}
				}
			}
		}
	}

	/**
     * Copies all the properties of s to r.
     * @param {Object}       r  接收方
     * @param {Object}       s  发出方
     * @param {Array/String} l  Array时表示白名单，String时表示黑名单
     * @param {Boolean}      ov 接收方的同名属性/方法是否被覆盖，可以是函数
     * @return {Object}
     */
	function mix(r, s, l, ov, deep){
		if(!s || !r) return r;
		if(ov===undefined) ov = true;
		if(deep===undefined) deep = true;
		var i, p, len,
			white = true;

		if(l && iCat.isString(l)){
			l = l.replace(/\s+/g, '').split(',');
			white = false;
		}

		if(l && (len=l.length)){
			if(white){
				for(i=0; i<len; i++){
					p = l[i];
					if(!s[p]) continue;
					if(iCat.isFunction(ov)) ov = ov(r, s, p);
					deep? _mix(r, s, p, ov) : (ov && (r[p] = s[p]));
				}
			} else {
				for(p in s){
					if(!iCat.hasItem(l, p)){
						if(iCat.isFunction(ov)) ov = ov(r, s, p);
						deep? _mix(r, s, p, ov) : (ov && (r[p] = s[p]));
					}
				}
			}
		} else {
			for(p in s) {
				if(iCat.isFunction(ov)) ov = ov(r, s, p);
				deep? _mix(r, s, p, ov) : (ov && (r[p] = s[p]));
			}
		}
		return r;
	}

	/**
	 * for mix: 深拷贝
	 * @param  {[type]} prop 单个属性
	 */
	function _mix(r, s, prop, ov){
		if(!ov || !r) return;

		var itemR = r[prop], itemS = s[prop];
		if(iCat.isObject(itemR)){
			if(iCat.isObject(itemS) && !iCat.isEmptyObject(itemS)){//fixed bug: 空对象时为undefined
				for(var p in itemS){
					_mix(itemR, itemS, p, true);
				}
			} else {
				r[prop] = itemS;
			}
		} else if(iCat.isArray(itemR)){
			r[prop] = _unique(itemR.concat(itemS));
		} else {
			if(iCat.isObject(itemS) && !iCat.isEmptyObject(itemS)){//fixed bug: 对象浅拷贝问题
				for(var p in itemS){
					_mix(r[prop] || (r[prop] = {}), itemS, p, true);
				}
			}
			else if(iCat.isArray(itemS)){
				r[prop] = itemS.concat();
			}
			else {
				r[prop] = itemS;
			}
		}
	}

	/**
	 * 数组去重
	 */
	function _unique(arr){
		var hash = {}, r = [];
		foreach(arr, function(v){
			var k = JSON.stringify(v);
			if(!hash[k]){
				r.push(v); hash[k] = true;
			}
		});
		return r;
	}
})(this, document, 'ICAT')

/* loader.js # */
;(function(root, doc, iCat){
	/**
	 * 设置默认模块
	 * @param {Object} cfg [description]
	 * 参数形如：
	 * {
	 * 		$zepto: '../../zepto/zepto.js',
	 * 		mvc: ['./mvc/view.js', './mvc/model.js', './mvc/controller.js'],
	 * 		jquery: 'http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'
	 * }
	 */
	iCat.ModConfig = function(cfg){ iCat.mix(iCat.ModConfig, cfg); };

	/**
	 * 设置各种路径(声明函数时立即执行，取得默认的各种参照路径)
	 * - staticRef:前端根路径(/xxx/xx/x)
	 * - sysRef:参照icat.js所在的目录路径(./xxx/xx/x | ../xxx/xx/x)
	 * - appRef:参照main.js所在的目录路径($./xxx/xx/x | $../xxx/xx/x)
	 * - pageRef:参照当前页面所在路径(xxx/xx/x)
	 * - timestamp:时间戳
	 * @param {Object} cfg 自定义各种参照路径
	 */
	(iCat.PathConfig = function(cfg){
		var PC = iCat.PathConfig;

		if(!PC._hasReady){
			var scripts = doc.getElementsByTagName('script'),
				curJS = scripts[scripts.length-1],
				src = curJS.hasAttribute ? curJS.src : curJS.getAttribute('src', 4),
				baseURI = curJS.baseURI || doc.baseURI || doc.URL,
				mainJS = curJS.getAttribute('data-main'), mainCSS = curJS.getAttribute('data-cssfile');
			
			PC.weinreRef = iCat.IPMode? baseURI.replace(/(^\w+:\/{2,}[^\/]+).*/g, '$1:8080') : '';
			PC.timestamp = iCat.hasItem(src, '?')? src.replace(/[^?]+\?/g, '?') : '';
			PC.staticRef = src.replace(/(^\w+:\/{2,}[^\/]+).*/g, '$1');
			PC.sysRef = src.replace(/^\w+:\/{2,}[^\/]+|[^\/]+$/g, '');

			if(mainJS){
				mainJS = getRelativePath(mainJS, PC.sysRef)[0];
				PC.appRef = mainJS.replace(/[^\/]+$/g, '');

				mainCSS = mainCSS? getRelativePath(mainCSS)[0] : '';
				PC.mainScript = [mainCSS, mainJS];
			} else {
				PC.appRef = PC.sysRef;
			}

			PC.picPath = getAbsolutePath(getRelativePath('../../pic/'))[0].replace(/[^\/]+$/g, '');
			PC.LoadedGroup = {};// 存放已加载的文件
			PC.ModGroup = {}; //存放已加载的模块
			PC._hasReady = true;
		}
		
		if(cfg){
			if(cfg.picPath){
				var picPath = cfg.picPath.replace(/(\w)$/, '$1/');
					picPath = getAbsolutePath(getRelativePath(picPath))[0];
				cfg.picPath = picPath.replace(/[^\/]+$/g, '');
			}
			iCat.mix(PC, cfg);
		}
	})();

	// 对外接口
	iCat.mix(iCat, {
		
		/* 阻塞式加载文件 */
		inc: function(files){
			if(!files) return;

			files = getRelativePath(files);
			iCat.foreach(getAbsolutePath(files), function(file){
				if(!file) return;
				blockLoad(file);
			});
		},

		/* 加载文件形式：
		 * - 单个文件，支持字符串或文件数组(length为1)
		 * - 多个文件，必须是文件数组
		 * - 参数：options || files, callback, isDepend, isCombo
		 */
		include: function(){
			if(!arguments.length) return;
			arguments.length==1?
				fnOption(
					iCat.isObject(arguments[0])?
						arguments[0] : {files: arguments[0]}
				) :
				fnOption({
					files: arguments[0], callback: arguments[1],
					isDepend: arguments[2], isCombo: arguments[3]
				});
		},

		/* 加载文件形式：
		 * - 单个文件，支持字符串或文件数组(length为1)
		 * - 多个文件，必须是文件数组
		 * - 参数：options || modName, files, callback, isCombo
		 */
		require: function(){
			if(!arguments.length) return;
			arguments.length==1?
				fnOption(arguments[0], true) :
				fnOption({
					modName: arguments[0], files: arguments[1],
					callback: arguments[2], isCombo: arguments[3]
				}, true);
		},

		//使用已加载后的模块
		//参数：options || modName, callback, delay, step
		use: function(){
			if(!arguments.length) return;
			arguments.length==1?
				fnUse(arguments[0]) :
				fnUse({
					modName: arguments[0], callback: arguments[1],
					delay: arguments[2] || 500, step: arguments[3]
				});
		}
	});

	// 默认
	iCat.ModConfig({
		'$MVC':'./mvc', '$Event':'./event', '$Dom':'./dom', '$util':'./utils',
		'appmvc': ['$MVC', './mvc/view.js', './mvc/model.js', './mvc/controller.js'],
		'$Zepto': '../../lib/zepto/zepto',
		'$jQuery': '../../lib/jquery/jquery',
		'$angular': '../../lib/angular/angular'
	});

	iCat.weinreStart = function(){
		var PC = iCat.PathConfig, weinrejs;
		if(!PC.weinreRef) return;

		weinrejs = PC.weinreRef + '/target/target-script-min.js#anonymous!';
		iCat.include(weinrejs);
	};

	//如果是ip模式，自动调用weinre
	//ie6-8报错：Oops. It seems the page runs in compatibility mode. Please fix it and try again.
	if(!/MSIE\s?[6-8]/.test(navigator.userAgent) && iCat.IPMode){
		iCat.weinreStart();
	}

	iCat.include({
		files: iCat.PathConfig.mainScript,
		domReady: false
	});

	/**
	 * 阻塞式加载文件
	 * @param  {String} file 文件完整的绝对路径
	 */
	function blockLoad(file){
		var url = _geturl(file),
			f = file.replace(/(\?|#).*$/, ''),
			LG = iCat.PathConfig.LoadedGroup;
		if(LG[f]) return;

		var type = f.match(/[^\.]+$/)[0],
			isCSS = /css/i.test(type),
			tag = isCSS? 'link':'script',
			attr = isCSS? ' type="text/css" rel="stylesheet"' : ' type="text/javascript"',
			path = (isCSS? 'href' : 'src') + '="' + url + '"';
		doc.write('<' + tag + attr + path + (isCSS? '/>':'></'+tag+'>'));
		LG[f] = true;
		if(!iCat.$ && root.$){
			iCat.$ = root.jQuery || root.Zepto || root.ender || root.$;
		}
	}
	
	/* Common function for iCat.use */
	function fnUse(opt){
		if(!opt || !iCat.isObject(opt)) return;

		iCat.wait(function(key, isEnd, Cache){
			if(!iCat.PathConfig.ModGroup[opt.modName]){
				Cache[key] = false;
				if(isEnd && iCat.ModConfig[opt.modName]){
					delete Cache[key];
					fnOption({
						modName: opt.modName,
						files: iCat.ModConfig[opt.modName],
						callback: opt.callback
					}, true);
				}
				return;
			}
			
			delete Cache[key];
			if(opt.callback){
				opt.callback.call(iCat, root[opt.modName]);
			}
		}, opt.delay, opt.step);
	}

	/**
	 * Common function for iCat.include/require
	 * @param  {Object}  opt
	 * @param  {Boolean} isRequire 是否模块化
	 */
	function fnOption(opt, isRequire, SingleLoad){
		if(!opt) return;
		if(isRequire){
			if(iCat.isString(opt)) opt = {modName: opt};
			if(!iCat.isObject(opt) || !(opt.files = opt.files || iCat.ModConfig[opt.modName])) return;
			if(!iCat.ModConfig[opt.modName]) iCat.ModConfig[opt.modName] = opt.files;
		} else {
			if(iCat.isString(opt) || iCat.isArray(opt)) opt = {files: opt};
			if(!iCat.isObject(opt) || !opt.files) return;
		}

		opt._arguments = [];
		iCat.foreach(
			iCat.isArray(opt.files)? opt.files : [opt.files],
			function(key){
				if(/^[\$\w]+[\w\.]+\w+$/.test(key)){
					iCat.exports[key] = iCat.exports[key] || key.replace(/^\$/, '');
					opt._arguments.push(key);
				}
			}
		);

		opt.files = getAbsolutePath(getRelativePath(opt.files), opt.isCombo);
		if(isRequire) opt.isDepend = true;
		if(opt.domReady===undefined) opt.domReady = true;

		(SingleLoad = function(){
			if(!opt.files.length) return;
			var curJS = opt.files.shift();
			if(opt.files.length){
				if(opt.isDepend)//文件间有依赖 顺序加载
					nonblockLoad({
						file:curJS, modName:opt.modName,
						callback:function(){
							if(!iCat.$ && root.$)
								iCat.$ = root.jQuery || root.Zepto || root.ender || root.$;
							SingleLoad();/*next*/
						}
					});
				else {
					nonblockLoad({
						file:curJS,
						callback:function(){
							if(!iCat.$ && root.$)
								iCat.$ = root.jQuery || root.Zepto || root.ender || root.$;
						}
					});
					SingleLoad();/*next*/
				}
			} else {
				nonblockLoad({file:curJS,
					callback:function(MG, argus){
						MG = iCat.PathConfig.ModGroup;
						argus = [];
						
						if(!iCat.$ && root.$)
							iCat.$ = root.jQuery || root.Zepto || root.ender || root.$;
						
						iCat.foreach(opt._arguments, function(key){
							if(typeof iCat.exports[key]!=='string'){
								argus.push(iCat.exports[key]);
							} else {
								var ns = iCat.exports[key].split('.'),
									k = ns.shift(),
									obj = o = root[k] || iCat[k];
								if(o && ns.length){
									obj = (function(nsRoot){
										while(ns.length){
											var level = ns.shift();
											if(nsRoot===iCat.$) nsRoot = nsRoot.fn;
											if(nsRoot[level]){
												nsRoot = nsRoot[level];
											} else {
												nsRoot = undefined;
												break;
											}
										}
										return nsRoot;
									})(o);
								}
								argus.push(obj);
								iCat.exports[key] = obj;
							}
						});
						
						if(opt.domReady===false){
							if(opt.callback) opt.callback.apply(iCat, argus);
							if(opt.modName) MG[opt.modName] = true;
						} else {
							iCat.ready(function(){
								if(opt.callback) opt.callback.apply(iCat, argus);
								if(opt.modName) MG[opt.modName] = true;
							});
						}
					}
				});
			}
		})();
	}

	/**
	 * 获得绝对路径
	 * @param  {Array/String} arg 简化的文件路径
	 * @param  {Boolean} isCombo 是否支持合并
	 * @return {Array} 完整的绝对路径
	 */
	function getAbsolutePath(arg, isCombo){
		var PC = iCat.PathConfig, ret = [];

		if(isCombo){
			var cssArr = [], jsArr = [], singleArr = [];

			iCat.foreach(arg, function(v){
				/^\w+/.test(v)?
					singleArr.push(v) :
						/\.css/i.test(v)? cssArr.push(v) : jsArr.push(v);
			});

			if(cssArr.length){
				cssArr = PC.staticRef + (cssArr.length===1? cssArr[0] : '/??'+cssArr.join(','));
				ret.push(cssArr);
			}
			if(jsArr.length){
				jsArr = PC.staticRef + (jsArr.length===1? jsArr[0] : '/??'+jsArr.join(','));
				ret.push(jsArr);
			}
			if(singleArr.length){// combo模式下，单个文件（绝对路径或相对页面的文件）会被最后加载
				ret = ret.concat(singleArr);
			}
		}
		else {
			iCat.foreach(arg, function(v){
				ret.push((/^\w+/.test(v)? '':PC.staticRef) + v);
			});
		}

		return ret;
	}

	/* 获得完整的相对路径 */
	function getRelativePath(s, ref){
		var ret = [];
		if(iCat.isArray(s)){
			iCat.foreach(s, function(item){
				var arrFile = _getScriptRef(item, ref);
				iCat.foreach(arrFile, function(f){
					ret.push(_getScriptFile(f));
				});
			});
		} else {
			var arrFile = _getScriptRef(s, ref);
			iCat.foreach(arrFile, function(f){
				ret.push(_getScriptFile(f));
			});
		}

		return ret;
	}

	/* for getRelativePath: 获取file和参照路径 */
	function _getScriptRef(s, ref){
		var MC = iCat.ModConfig, PC = iCat.PathConfig,
			one = s.charAt(0),
			s1 = one==='$'? s : '$' + s,
			arr = [], f;
		
		if(/^[\$\w]+[\w\.]+\w+$/.test(s)){
			if(MC[s1]){// 参照iCat
				f = MC[s1];
				ref = ref || PC.sysRef;
			}
			else if(MC[s]){// 参照main
				f = MC[s];
				ref = ref || PC.appRef;
			}

			if(f){
				if(iCat.isArray(f)){
					iCat.foreach(f, function(v){
						if(v) arr.push([v, ref]);
					});
				} else {
					arr.push([f, ref]);
				}
			} else {// 参照page
				arr.push([s]);
			}
		}
		else if(one==='.'){
			arr.push([s, ref || PC.appRef]);
		}
		else if(one==='~'){// 参照静态文件根路径
			arr.push([/*PC.staticRef+*/'/'+s.slice(1)]);
		}
		else {
			if(s) arr.push([s]);
		}

		return arr;
	}

	/**
	 * for getRelativePath: 获取js或css完整的相对路径(相对staticRef/页面/网站)
	 * @param  {Array} item 0简化的文件路径,1参照路径,有以下几种情况：
	 * - ../apps/gou/assets/gionee.js
	 * - ../../apps/js/abc.js
	 * - apps/gou/assets/js/main.js (相对当前页面目录)
	 * - /apps/gou/main.js (相对网站根目录)
	 * @return {String} 完整的相对路径
	 */
	function _getScriptFile(item){
		var f = item[0].replace(/\s+/g, ''), ref = item[1] || '',
			keepIt = /!$/.test(f), two = f.charAt(1),
			weburl = /^\w+:\/{2,}.*!$/.test(f);

		// ./apps/gou/../a/../assets/js/main.js => ./apps/gou/a/assets/js/main.js
		// ./apps/gou/../../main.js => ./apps/gou/main.js
		if(!weburl){
			f = f.replace(/([\?\#].*|!)$/g, '')
				 .replace(/(\w+)(\/?\.{2,})+/g, '$1');
			f = /\.(js|css)$/i.test(f)? f : f + '.js';
		}

		if(two==='/'){
			f = f.replace(/^\.\//, ref);
		}
		else if(two==='.'){
			if(!ref){
				f = f.replace(/(\.{2}\/)+/, ref);
			}
			else {
				var levels = f.match(/\.{2}\//g), appNeed;
				if(levels!=null){
					appNeed = ref.replace(/\/$/, '').split('/');
					iCat.foreach(levels, function(){
						if(appNeed.length) appNeed.pop();
						else return false;
					});
					if(appNeed.length){
						appNeed.push('');
						appNeed = appNeed.join('/');
					} else {
						appNeed = '/';
					}
					f = f.replace(/(\.{2}\/)+/, appNeed);
				}
			}
		}

		if(iCat.DebugMode){
			f = keepIt || /\.source\./i.test(f)?
				f : f.replace(/([^\.]+)$/, 'source.$1');
		}

		return f;
	}

	/* 非阻塞式加载文件 */
	function nonblockLoad(opt){
		var url = _geturl(opt.file), type,
			f = opt.file.replace(/(\?|#).*$/, ''),
			LG = iCat.PathConfig.LoadedGroup;
		
		if(LG[f]){
			if(opt.callback) opt.callback();
			return;
		}

		type = f.match(/[^\.]+$/)[0];
		/css/i.test(type)?
			_cssLoad(opt) : _scriptLoad(opt);
	}

	/* for nonblockLoad: css文件加载 */
	function _cssLoad(opt, pNode){
		var url = _geturl(opt.file),
			node = doc.createElement('link'),
			pNode = opt.pNode || doc.head || doc.getElementsByTagName('head')[0],
			LG = iCat.PathConfig.LoadedGroup;
		node.setAttribute('type', 'text/css');
		node.setAttribute('rel', 'stylesheet');
		node.setAttribute('href', url);

		setTimeout(function(){
			if(opt.callback) opt.callback();
			LG[opt.file] = true;
		}, 15);
		pNode.appendChild(node);
	}

	/* for nonblockLoad: js文件加载 */
	function _scriptLoad(opt, pNode){
		var url = _geturl(opt.file), fn,
			node = doc.createElement('script'),
			pNode = opt.pNode || doc.head || doc.getElementsByTagName('head')[0],
			LG = iCat.PathConfig.LoadedGroup;
		node.setAttribute('type', 'text/javascript');
		node.setAttribute('src', url);
		node.setAttribute('async', true);

		if('onload' in node){
			node.addEventListener('load', fn = function(){
				node.removeEventListener('load', fn, false);
				if(opt.callback) opt.callback();
				LG[opt.file] = true;
			}, false);
		}
		else if(node.readyState){
			node.attachEvent('onreadystatechange', fn = function(){
				if(/loaded|complete/i.test(node.readyState)){
					node.detachEvent('onreadystatechange', fn);
					if(opt.callback) opt.callback();
					LG[opt.file] = true;
				}
			});
		}
		pNode.appendChild(node);
	}

	/**
	 * for xxxLoad: 封装文件路径
	 * @param  {String} file 文件完整的绝对路径
	 * @return {String} 带时间戳的绝对路径
	 */
	function _geturl(file){
		if(/^\w+:\/{2,}.*!$/.test(file)) return file.slice(0, -1);

		var PC = iCat.PathConfig;
		if(PC.timestamp)
			file = file.replace(/\?([^\?\/]+)$/, '&$1');
		return /#|&/.test(file)?
			file.replace(/(#|&)/, PC.timestamp + '$1') : file + PC.timestamp;
	}
})(this, document, ICAT)