/*!
 * Copyright 2011~2012, ICAT JavaScript Library v1.1.2.a
 * MIT Licensed
 * @Author valleykiddy@gmail.com
 * @Time 2012-08-20 19:10:00
 */

(function(iCat, win){
	
	// shortcut for defined ICAT
	if(win[iCat] === undefined) win[iCat] = {};
    iCat = win[iCat];
	
	var _ua = navigator.userAgent, isDebug = /debug/i.test(window.location.href),
		
		_isfn = function(o){return !o? false : o.constructor === Function;},
		
		// the properties & functions of the pith
		pith = {
			// 存放已加载过的文件
			loadStore: {},
			
			// 存放已经加载的模块
			modStore: {},
			
			// 模块配置
			mods: {},
			
			// kinds of browsers
			browser: {
				uc: /UC/i.test(_ua),
				safari: /webkit/i.test(_ua),
				opera: /opera/i.test(_ua),
				msie: /msie/i.test(_ua) && !/opera/i.test(_ua),
				mozilla: /mozilla/i.test(_ua) && !/(compatible|webkit)/i.test(_ua)
			},
			
			// enumerate the Array or Object
			foreach: function(o, cb, args){
				var name, i = 0, length = o.length,
					isObj = length===undefined || _isfn(o);
				
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
			
			// Copies all the properties of s to r.
			// w(hite)l(ist):白名单, ov(erwrite):覆盖
			mix: function(r, s, wl, ov){
				if (!s || !r) return r;
                if (ov === undefined) ov = true;
                var i, p, len,
					_mix = function(p, r, s, ov) {
						if (ov || !(p in r)) {
							r[p] = s[p];
						}
					};

                if (wl && (len = wl.length)) {
                    for (i = 0; i < len; i++) {
                        p = wl[i];
                        if (p in s) {
                            _mix(p, r, s, ov);
                        }
                    }
                } else {
                    for (p in s) {
                        _mix(p, r, s, ov);
                    }
                }
                return r;
			}
		};
	
	// put pith into the iCat
    pith.mix(iCat, pith);
	pith = null;
	
	// set the path
	var doc = document, scripts = doc.getElementsByTagName('script'),
		ar = doc.getElementsByName('appRef')[0],
		src, timestamp = '';
	iCat.appRef = ar? ar.content : '';//设置app参照路径
	iCat.corecss = ar? (ar.getAttribute('corecss')||'') : '';
	/*wdpi = ar? (ar.getAttribute('uc-wdpi')||'480|360') : '480|360';
	if(iCat.browser.uc){
		var viewport = doc.getElementsByName('viewport')[0],
			wdpi = wdpi.split('|'),
			vct = viewport.content;
		viewport.content = vct.replace(/device-width/i, wdpi[0]).replace(/device-dpi/i, wdpi[1]);
	}*/
	for(var i=0, el; el = scripts[i++];){
		src = !!doc.querySelector ? el.src : el.getAttribute('src',4);
		if(src && /icat(\.source)?\.js/i.test(src)){
			var icatType = /icat\/\d+\.+/.test(src), icatReg;
			if(icatType){
				icatReg = '/icat/';
			} else {
				icatReg = ar? (ar.getAttribute('icatreg') || '/icat.') : '/icat.';
			}
			if(/\?(v|t)=\d+/.test(src)){
				timestamp = '?'+src.replace(/.*\?/,'');
			}
			
			iCat.refFile = el;
			iCat.sysRef = src.substr(0, src.lastIndexOf(icatReg));//设置sys参照路径
			iCat.libRef = iCat.sysRef + '/lib';//设置lib参照路径
			break;
		}
	}
	
	
	/** The ICAT System function for organization code */
	iCat.mix(iCat, {
		
		// The version of the library.
		version: '1.1.2.a',
		
		// common browser
		isIE: iCat.browser.msie,
		ieVersion: iCat.browser.msie? _ua.match(/MSIE(\s)?\d+/i)[0].replace(/MSIE(\s)?/i,'') : -1,
		
		// debug or not
		isDebug: isDebug,
		
		// iCat.app() with these members.
        __APP_MEMBERS: ['namespace'],
		__APP_INIT_METHODS: ['__init'],
		
		// init function
		__init: function(){
			var self = this, hasAssets = /^\.{2}\//.test(iCat.refFile.getAttribute('main')||'');
			
			self.widget = {};
			self.util = {};
			
			self.sysPlugin = self.refFile.src.replace(/icat\..*/,'')+'plugin/';
			self.appPlugin = self.appRef + (hasAssets? '/assets':'') + '/plugin/';
			self.timestamp = timestamp;
		},
		
		// commonly used judgment
		isFunction: _isfn,
		isString: function(o){return !o? false : typeof o === 'string';},
		isArray: function(o){return !o? false : o.constructor === Array;},
		
		// iCat或app下的namespace，相当于扩展出的对象
		namespace: function() {
            var a = arguments, l = a.length, o = null, i, j, p;

            for (i = 0; i < l; ++i) {
                p = ('' + a[i]).split('.');
                o = this;
                for (j = (win[p[0]] === o) ? 1 : 0; j < p.length; ++j) {
                    o = o[p[j]] = o[p[j]] || {};
                }
            }
            return o;
        },
		
		// create a app for some project
		app: function(name, sx) {
            var self = this,
				isStr = self.isString(name),
                O = isStr ? win[name] || {} : name;

            self.mix(O, self, self.__APP_MEMBERS, true);
			self.mix(O, self.isFunction(sx) ? sx() : sx);
			isStr && (win[name] = O);

            return O;
        },
		
		// 扩展icat公用方法，绑在util上
		extend: function(name, fn){
			self.mix(self, {name: fn});
		},
		
		// 扩展icat公用组件，绑在widget上
		// o对象里必须有init，option用来传递组件参数
		register: function(name, o, opt){
			iCat.mix(o.option, opt);
			self.widget[name] = o.init;
		},
		
		// when debuging, print some msg for unit testing
		log: function(msg) {
            if(!this.isDebug) return;
			
			if(this.isIE){
            	alert(msg);
            } else {
            	if(win.console !== undefined && console.log)
            		console.log(msg);
            }
        }
	});
	
	iCat.__init();
})('ICAT', this);

/** loader */
(function(iCat){
	var doc = document, ohead = doc.head || doc.getElementsByTagName('head')[0],
		
		LS = iCat.loadStore, MS = iCat.modStore,
		
	// type1( ): 指向sys根目录(sys级) ~/指向icat下的plugin目录
	// type2(/): 指向lib根目录(lib级) //库文件夹和库名相同
	// type3(./): 指向app根目录(app级) ../指向assets下的css或js目录 .~/指向assets下的plugin目录
	// type4(网址形式): 外链网址
	_dealUrl = function(s){
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
			if(/^\.(\.|~)?\//g.test(url)){//type3
				if(/^\.\//.test(url))
					url = url.replace(/^\./, iCat.appRef);
				if(/^\.{2}\//.test(url))
					url = url.replace(/^\.{2}/, iCat.appRef+(isCSS? '/assets/css':'/assets/js'));
				if(/^\.~\//.test(url))
					url = url.replace(/^\.~\//, iCat.appPlugin);
			} else if(/^\/{1,2}/.test(url)){//type2	
				if(/^\/{2}/.test(url)){
					var libFolder = url.replace(/^\/{2}|.source|.css|.js/ig, '');
					url = url.replace(/^\//, iCat.libRef+'/'+libFolder);
				} else {
					url = url.replace(/^\//, iCat.libRef+'/');
				}
			} else {//type1
				if(/^~\//.test(url)){
					url = url.replace(/^~\//, iCat.sysPlugin);
				} else {
					url = iCat.sysRef + '/' + url;
				}
			}
		}
		
		return url+iCat.timestamp;
	},
	
	_inc = function(loadFile){//console.log(loadFile);
		var url = loadFile, _url = url.replace(/[\?#].*/, '');
		if(LS[_url]) return;
		
		var type = _url.replace(/.*\./g,''),
			isCSS = type=='css', tag = isCSS? 'link':'script',
			attr = isCSS? ' type="text/css" rel="stylesheet"' : ' type="text/javascript"',
			path = (isCSS? 'href':'src') + '="'+url+'"';
		doc.write('<'+tag+attr+path+(isCSS? '/>':'></'+tag+'>'));
		LS[_url] = true;
	},
	
	_start = function(loadQueue, config){
		if(loadQueue.length==0) return;
		
		var cfg = {};
		iCat.mix(cfg, config);
		
		var	url = loadQueue[0],
				
			//去掉?|#后面的参数，保留纯净的文件
			_url = url.replace(/[\?#].*/, ''),
		
			// 执行callback函数
			_exec = function(){
				if(loadQueue.length>0){
					_start(loadQueue, config);
				} else {
					if(cfg.callback && iCat.isFunction(cfg.callback))
						cfg.callback(cfg.context || iCat);
					if(cfg.mod)
						MS[cfg.mod] = true;
				}
			};
		
		if(LS[_url]){
			_exec();
			return;
		}
		
		loadQueue.shift();
		
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
			
		if(iCat.isIE){
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
							_exec();
							LS[_url] = true;
						}
					};
				}
				ohead.appendChild(node);
			},1);
		} else {
			if(type==='js'){
				node.onload = function(){
					_exec();
					LS[_url] = true;
				};
			}
			ohead.appendChild(node);
		}
		
		/* css不需要监听加载完成*/
		if(type==='css'){
			_exec();
			LS[_url] = true;
			ohead.appendChild(node);
		}
	};
	
	//对外接口
	iCat.mix(iCat, {
		
		/* 阻塞式加载文件 */
		inc: function(f){
			if(!f) return;
			var files = iCat.isString(f)? [f]:f;
			
			iCat.foreach(files, function(i, v){
				if(!v) return;
				_inc(_dealUrl(v));
			});
			
		},
		
		/* 加载文件形式：
		 * - 单个文件，支持字符串或文件数组(length为1)
		 * - 多个文件，必须是文件数组
		 */
		include: function(f, cb){//加载一个或多个文件
			if(!f || !f.length) return;
			var queue = [];
			
			if(iCat.isString(f)){
				queue.push(_dealUrl(f));
			} else if(iCat.isArray(f)) {
				iCat.foreach(f, function(i, v){
					queue.push(_dealUrl(v));
				});
			} else {
				return;
			}
			
			_start(queue, {callback:cb});
		},
		
		/* 加载文件形式：
		 * - 单个文件，支持字符串或文件数组(length为1)
		 * - 多个文件，必须是文件数组
		 */
		require: function(m, r, cb){//加载有依赖的模块
			if(!m && !r) return;
			if(MS[m]){ if(cb) cb(iCat); } else {
				var queue = [];
				if(iCat.isString(r)){
					queue.push(_dealUrl(r));
				} else if(iCat.isArray(r)) {
					iCat.foreach(r, function(i, v){
						queue.push(_dealUrl(v));
					});
				}
				_start(queue, {callback:cb, mod:m});
			}
		},
		
		//使用已加载后的模块
		use: function(m, cb, t){
			var i = 0, t = t || 500,
			timer = setInterval(function(){
				i += 5;
				
				if(i>=t){
					clearInterval(timer);
					iCat.require(m, iCat.mods[m], cb);
				} else {
					if(MS[m]){
						clearInterval(timer);
						cb(iCat);
					}
				}
			},5);
		}
	});
	
	/* 加载css、js库和关键js */
	var corelib = iCat.refFile.getAttribute('corelib') || '',
		mainJS = iCat.refFile.getAttribute('main') || '';
	
	iCat.inc([iCat.corecss, corelib, mainJS]);
})(ICAT);

/** icat-mvc */
(function(iCat){
	
	iCat.tempCache = {};
	
	iCat.mix(iCat, {
		
		template: function(str, data){
			var cache = iCat.tempCache,
			
				fn = !/\W/.test(str)?
					cache[str] = cache[str] || this.temp(document.getElementById(str).innerHTML) :
					new Function("obj",
						"var p=[],print=function(){p.push.apply(p,arguments);};" +
						"with(obj){p.push('" +
						str.replace(/[\r\t\n]/g, "")
							.split("<%").join("\t")
							.replace(/((^|%>)[^\t]*)'/g, "$1\r")
							.replace(/\t=(.*?)%>/g, "',$1,'")
							.split("\t").join("');")
							.split("%>").join("p.push('")
							.split("\r").join("\\'")
						+"');}return p.join('');"
					);
			
			return data ? fn(data) : fn;
		},
		
		mergeTemp: function(t,d,r){//r(eserve)表示是否保留
			if(!iCat.isString(t) || !/\{|\}/g.test(t)) return;
			
			var phs = t.match(/\{\w+\}/g);
			if(!phs.length) return;
			
			iCat.foreach(phs, function(){
				var key = this.replace(/\{|\}/g,''),
					regKey = new RegExp('\{'+key+'\}'),
					val = d[key];
				
				t = t.replace(regKey, val? val:(r? '{'+key+'}':''));
			});
			
			return t;
		},
		
		// 封装表单元素 -> 字符串
		form2str: function(form){
			if(!form) return;
			var strArgus = '';
			for(var i=0, els=form.elements, ilen=els.length; i<ilen; i++){
				var oMe = $(els[i]);
				if(oMe.attr('name')){
					strArgus += '&' + oMe.attr('name') + '=' + els[i].value;
				}
			}
			strArgus = strArgus.replace('&','?');
			return strArgus;
		},
		
		// 封装表单元素 -> json
		form2json: function(form){
			if(!form) return;
			var jsonArgus = {};
			for(var i=0, els=form.elements, ilen=els.length; i<ilen; i++){
				var oMe = $(els[i]);
				if(oMe.attr('name')){
					jsonArgus[oMe.attr('name')] = els[i].value;
				}
			}
			return jsonArgus;
		}
	});
})(ICAT);

/**
 *
 * NOTES:
 *
 * 2012-09-16 12:00:00
 * - 新增extend、register
 * - 进一步丰富MVC模块
 *
 * 2012-08-27 23:53:00
 * - 更改cssCore为corecss，与corelib保持一致
 * - 新增MVC，以适应发展。
 *
 * 2012-08-20 15:00:00
 * - 优化路径解释器，新增/指向库路径
 * - 增加inc方法，类似java里面的import，阻塞式加载文件
 *
 */