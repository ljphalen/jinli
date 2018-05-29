/*!
 * Copyright 2011~2013, ICAT JavaScript Library v1.1.6
 * https://github.com/valleykid/icat
 *
 * Copyright (c) 2013 valleykid
 * Licensed under the MIT license.
 *
 * @Author valleykiddy@gmail.com
 * @Time 2013-08-22 20:40:00
 */

/* core.js # */
;(function(root, doc, C){
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

	// expand the built-in Objects' functions.
	mix(Array.prototype, {
		contains: function(item){
			return this.indexOf(item) > -1;
		},

		remove: function(item){
			var self = this;
			self.forEach(function(v, i){
				if(v===item){ self.splice(i, 1); }
			});
			return self;
		},

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

	// Kinds of judgments
	foreach(
		['String', 'Boolean', 'Function', 'Array', 'Object', 'Number'],
		function(v){
			iCat['is'+v] = function(obj){
				return (v==='Array' && Array.isArray)?
						Array.isArray(obj) :
							Object.prototype.toString.call(obj) === '[object '+v+']';
			};
		}
	);

	// Kinds of modes
	foreach({
			'DebugMode': /debug/i,
			'DemoMode': /localhost|demo\.|\/{2}\d+(\.\d+){3}|file\:/i,
			'IPMode': /\/{2}\d+(\.\d+){3}/
		},
		function(v, k){ iCat[k] = v.test(location.href); }
	);

	// extend iCat-object
	mix(iCat, {

		mix: mix,

		foreach: foreach,

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

		isjQueryObject: function(obj){
			return !!iCat.$ && (!!obj) && iCat.isFunction(obj.get);
		},

		isEmptyObject: function(obj){
			for(var name in obj){
				return false;
			}
			return true;
		},

		//需重写函数：数据填充模版
		render: function(tmplId, data){
			var cacheTmpls = iCat.__cache_tmpls || (iCat.__cache_tmpls = {}),
				cacheFuns = iCat.__cache_funs || (iCat.__cache_funs = {}),
				sTmpl;

			// cacheTmpls的解析
			if(iCat.isEmptyObject(cacheTmpls)){
				iCat.foreach(iCat.app, function(app, k){
					if(app.template){
						iCat.foreach(app.template, function(v, k){
							cacheTmpls[k] = v.replace(/[\r\t\n]/g, '');
						});
					}
				}, undefined, true);
			}

			// tmplId的解析
			if(/^[\w\-_]+$/.test(tmplId)){
				if(cacheFuns[tmplId]){
					return cacheFuns[tmplId](data);
				}
				else {
					if(cacheTmpls[tmplId]){
						sTmpl = cacheTmpls[tmplId];
					} else {
						var el = UTIL.queryOne('#'+tmplId);
						sTmpl = el?
							el.innerHTML.replace(/[\r\t\n]/g, '').replace(/\s+(<)|\s*$/g, '$1') : '';
					}
					
					cacheTmpls[tmplId] = sTmpl;
					return (cacheFuns[tmplId] = _tmpl(sTmpl))(data);
				}
			} else {
				return _tmpl(tmplId, data);
			}
		},

		contains: function(o, p){
			if(iCat.isArray(o)) return o.contains(p);
			if(iCat.isObject(o)) return p in o;
			if(iCat.isString(o)) return o.indexOf(p)>-1;
			return false;
		},

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
		
		// create a app for some project
		app: function(name, sx){
			var isStr = iCat.isString(name),
				O = isStr? root[name] || {} : name;

			mix(O, iCat, ['namespace'], true);
			mix(O, iCat.isFunction(sx) ? sx() : sx);
			isStr && (iCat.app[name] = root[name] = O);

			return O;
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

		log: function(msg){
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
	iCat.namespace('AMD', 'OO', 'SM');

	/**
     * Copies all the properties of s to r.
     * @param {Object}       r  接收方
     * @param {Object}       s  发出方
     * @param {Array/String} l  Array时表示白名单，String时表示黑名单
     * @param {Boolean}      ov 接收方的同名属性/方法是否被覆盖
     * @return {Object}
     */
	function mix(r, s, l, ov){
		if(!s || !r) return r;
		if(ov===undefined) ov = true;
		var i, p, len,
			white = true;

		if(l && !iCat.isArray(l)){
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
	}

	/**
     * Handles objects with the built-in 'foreach', arrays, and raw objects.
     * @param {Array/Object} o      被遍历的对象/数组
     * @param {Function}     cb     回调方法，返回false则跳出遍历
     * @param {Array}        args   传递给回调方法的参数
     * @param {Boolean}      setObj 设定o是或不是对象
     */
	function foreach(o, cb, args, setObj){
		var name, i = 0, length = o.length,
			isObj = setObj || length===undefined;//Object.prototype.toString.call(o)==='[object Object]'
		
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

	// 模板函数
	function _tmpl(tmpl, data){
		var fnEmpty = function(){return '';},
			fBody, fn;
		if(!tmpl) return fnEmpty;

		tmpl = tmpl.replace(/[\r\t\n]/g, '');
		fBody = "var __p_fun = [], _self = jsonData; with(jsonData){" +
					"__p_fun.push('" + //fixe bug:当json不包含某字段时，整个函数执行异常
						tmpl.replace(/<%=(.*?)%>/g, "',(typeof $1!='undefined'? $1:''),'")
							.replace(/<%(.*?)%>/g, "');$1__p_fun.push('") + 
						"');" +
				"};return __p_fun.join('');";
		
		fn = new Function("jsonData", fBody);
		return data? fn(data) : fn;
	}

	// 实现类jQuery的风格
	var jqStyle = iCat.$$ = function(){ return new jqStyle.fn.init(); };
	jqStyle.fn = jqStyle.prototype = {
		init: function(appName){
			if(iCat.isString(appName)){
				
			}
			return this;
		},

		extend: function(o){
			var self = this;
			if(iCat.isObject(o)){
				foreach(o, function(v, k){
					if(iCat.isFunction(v)){
						self[k] = function(){
							return v.call(self) || self;
						}
					} else {
						self[k] = v;
					}
				});
			}
			return self;
		}
	};
	jqStyle.fn.init.prototype = jqStyle.fn;
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
			
			PC.weinreRef = iCat.IPMode? baseURI.replace(/(^\w+:\/{2,}[^\/]+).*/g, '$1:8080/') : '';
			PC.timestamp = iCat.hasItem(src, '?')? src.replace(/[^?]+\?/g, '?') : '';
			PC.staticRef = src.replace(/(^\w+:\/{2,}[^\/]+).*/g, '$1');
			PC.sysRef = src.replace(/^\w+:\/{2,}[^\/]+|[^\/]+$/g, '');

			if(mainJS){
				mainJS = getRelativePath(mainJS, PC.sysRef)[0];
				PC.appRef = mainJS.replace(/[^\/]+$/g, '');

				mainCSS = mainCSS? getRelativePath(mainCSS)[0] : '../css/main.css';
				PC.mainScript = [mainCSS, mainJS];
			} else {
				PC.appRef = PC.sysRef;
			}

			PC.LoadedGroup = {};// 存放已加载的文件
			PC.ModGroup = {}; //存放已加载的模块
			PC._hasReady = true;
		}

		if(cfg) iCat.mix(PC, cfg);
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
				fnOption(arguments[0]) :
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
		'$MVC': './mvc', '$Event': './event', '$Dom': './dom', '$util': './util',
		'appmvc': ['$MVC', './mvc/view.js', './mvc/model.js', './mvc/controller.js'],
		'$Zepto': '../../lib/zepto/zepto',
		'$jQuery': '../../lib/jquery/jquery'
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

		opt.argus = [];
		iCat.foreach(
			iCat.isArray(opt.files)? opt.files : [opt.files],
			function(key){
				if(/^\$?\w+$/.test(key)) opt.argus.push(key);
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
					unblockLoad({
						file:curJS, modName:opt.modName,
						callback:function(){
							if(!iCat.$ && root.$)
								iCat.$ = root.jQuery || root.Zepto || root.ender || root.$;
							SingleLoad();/*next*/
						}
					});
				else {
					unblockLoad({
						file:curJS,
						callback:function(){
							if(!iCat.$ && root.$)
								iCat.$ = root.jQuery || root.Zepto || root.ender || root.$;
						}
					});
					SingleLoad();/*next*/
				}
			} else {
				unblockLoad({file:curJS,
					callback:function(MG, argus){
						MG = iCat.PathConfig.ModGroup;
						argus = [];
						iCat.foreach(opt.argus, function(key){
							var k = key.charAt(0)==='$'? key.slice(1) : key,
								o = root[k] || iCat[k];
							if(o) argus.push(o);
						});
						if(opt.domReady===false){
							if(!iCat.$ && root.$)
								iCat.$ = root.jQuery || root.Zepto || root.ender || root.$;
							if(opt.callback) opt.callback.apply(iCat, argus);
							if(opt.modName) MG[opt.modName] = true;
						} else {
							iCat.ready(function(){
								if(!iCat.$ && root.$)
									iCat.$ = root.jQuery || root.Zepto || root.ender || root.$;
								if(opt.callback) opt.callback.apply(iCat, argus);
								if(opt.modName) MG[opt.modName] = true;
							});
						}
					}
				});
			}
		})();
	}

	/* 非阻塞式加载文件 */
	function unblockLoad(opt){
		var url = _geturl(opt.file), type,
			f = opt.file.replace(/(\?|#).*$/, ''),
			LG = iCat.PathConfig.LoadedGroup;
		
		if(LG[f]){
			if(opt.callback) opt.callback();
			return;
		}

		type = f.match(/[^\.]+$/)[0];
		/css/i.test(type)?
			cssLoad(opt) : scriptLoad(opt);
	}

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

	/* 获取file和参照路径 */
	function _getScriptRef(s, ref){
		var MC = iCat.ModConfig, PC = iCat.PathConfig,
			one = s.charAt(0),
			s1 = one==='$'? s : '$' + s,
			arr = [], f;
		
		if(/^\$?\w+$/.test(s)){
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
				arr.push([s+'.js']);
			}
		}
		else if(one==='.'){
			arr.push([s, ref || PC.appRef]);
		}
		else if(one==='~'){// 参照静态文件根路径
			arr.push([PC.staticRef+'/'+s.slice(1)]);
		}
		else {
			if(s) arr.push([s]);
		}

		return arr;
	}

	/**
	 * 获取js或css完整的相对路径(相对staticRef/页面/网站)
	 * @param  {Array} item 0简化的文件路径,1参照路径,有以下几种情况：
	 * - ../apps/gou/assets/gionee.js
	 * - ../../apps/js/abc.js
	 * - apps/gou/assets/js/main.js (相对当前页面目录)
	 * - /apps/gou/main.js (相对网站根目录)
	 * @return {String} 完整的相对路径
	 */
	function _getScriptFile(item){
		var f = item[0], ref = item[1] || '',
			keepIt = /!$/.test(f), two = f.charAt(1);

		// ./apps/gou/../a/../assets/js/main.js => ./apps/gou/a/assets/js/main.js
		// ./apps/gou/../../main.js => ./apps/gou/main.js
		f = f.replace(/\s+|[!\?].*$/g, '')
			 .replace(/(\w+)(\/?\.{2,})+/g, '$1')
			 .replace(/(\/[^\.]+)$/, '$1.js');

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
				f : f.replace(/([^.]+)$/, 'source.$1');
		}

		return f;
	}

	/**
	 * 封装文件路径
	 * @param  {String} file 文件完整的绝对路径
	 * @return {String} 带时间戳的绝对路径
	 */
	function _geturl(file){
		var PC = iCat.PathConfig;
		if(PC.timestamp) file = file.replace(/\?([^\?\/]+)$/, '&$1');
		return /#|&/.test(file)?
			file.replace(/(#|&)/, PC.timestamp + '$1') : file + PC.timestamp;
	}

	/* css文件加载 */
	function cssLoad(opt, pNode){
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

	/* js文件加载 */
	function scriptLoad(opt, pNode){
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
})(this, document, ICAT)