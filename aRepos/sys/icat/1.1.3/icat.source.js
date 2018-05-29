/*!
 * Copyright 2011~2012, ICAT JavaScript Library v1.1.2.b
 * MIT Licensed
 * @Author valleykiddy@gmail.com
 * @Time 2012-09-23 10:00:00
 */

(function(){
	// Create the root object, 'window' in the browser, or 'global' on the server.
	var root = this;
	
	var _ua = navigator.userAgent, isDebug = /debug/i.test(root.location.href),
		ObjProto = Object.prototype;
	
	var toString         = ObjProto.toString,
		nativeIsArray    = Array.isArray;
	
	// Copies all the properties of s to r.
	// w(hite)l(ist):白名单, ov(erwrite):覆盖
	function mix(r, s, wl, ov){
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
	};
	
	// Handles objects with the built-in 'foreach', arrays, and raw objects.
	function foreach(o, cb, args){
		var name, i = 0, length = o.length,
			isObj = length===undefined || iCat.isFunction(o);
		
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
	}
	
	// ICAT内部代理
	var iCat = function(){
		var argus = arguments, len = argus.length;
		
		if(len==0) return null;
		
		if(len==1){
			var cfg = argus[0];
			if(cfg!==Object(cfg)) return null;
			else {
				var Cla = cfg.Create, ClaProto = Cla.prototype;
				foreach(cfg, function(k, v){
					if(k!='Create')
						ClaProto[k] = v;
				});
				return Cla;
			}
		}
		
		if(len>=2){
			var claName = argus[0], cfg = argus[1];
			if(toString.call(claName)!='[object String]' || cfg!==Object(cfg)) return null;
			else {
				root[claName] = cfg.Create;
				var ClaProto = root[claName].prototype;
				foreach(cfg, function(k, v){
					if(k!='Create')
						ClaProto[k] = v;
				});
			}
		}
	};
	
	// Export the ICAT object for **Node.js**
	if (typeof exports !== 'undefined') {
		if (typeof module !== 'undefined' && module.exports) {
		  exports = module.exports = iCat;
		}
		exports.ICAT = iCat;
	} else {
		root['ICAT'] = iCat;
	}
	
	// kinds of browsers
	iCat.browser = {
		uc: /UC/i.test(_ua),
		safari: /webkit/i.test(_ua),
		opera: /opera/i.test(_ua),
		msie: /msie/i.test(_ua) && !/opera/i.test(_ua),
		mozilla: /mozilla/i.test(_ua) && !/(compatible|webkit)/i.test(_ua)
	};
	
	// set the path
	var doc = document, scripts = doc.getElementsByTagName('script'),
		metaAppRef = doc.getElementsByName('appRef')[0],
		_icatsrc, timestamp = '';
	iCat.appRef = metaAppRef? metaAppRef.content : '';//设置app参照路径
	iCat.corecss = metaAppRef? (metaAppRef.getAttribute('corecss')||'') : '';
	
	for(var i=0, el; el = scripts[i++];){
		_icatsrc = !!doc.querySelector ? el.src : el.getAttribute('src',4);
		if(_icatsrc && /icat(\.source)?\.js/i.test(_icatsrc)){
			var icatType = /icat\/\d+\.+/.test(_icatsrc), icatReg;
			if(icatType){
				icatReg = '/icat/';
			} else {
				icatReg = metaAppRef? (metaAppRef.getAttribute('icatreg') || '/icat.') : '/icat.';
			}
			if(/\?(v|t)=\d+/.test(_icatsrc)){
				timestamp = '?'+_icatsrc.replace(/.*\?/,'');
			}
			
			iCat.refFile = el;
			iCat.sysRef = _icatsrc.substr(0, _icatsrc.lastIndexOf(icatReg));//设置sys参照路径
			iCat.libRef = iCat.sysRef + '/lib';//设置lib参照路径
			break;
		}
	}
	
	/** The ICAT System function for organization code */
	mix(iCat, {
		
		// Current version.
		version: '1.1.2.b',
		
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
			
			self.loadStore = {}; //存放已加载过的文件
			self.modStore = {}; //存放已经加载的模块
			self.mods = {}; //模块配置
		},
		
		// commonly used judgment
		isFunction: function(obj){return toString.call(obj) == '[object Function]';},
		isString: isString = function(obj){return toString.call(obj) == '[object String]';},
		isArray: nativeIsArray || function(obj){return toString.call(obj) == '[object Array]';},
		isNull: function(obj){return obj === null;},
		
		// each such as Array, Object
		foreach: foreach,
		
		// mixin
		mix: mix,
		
		// iCat或app下的namespace，相当于扩展出的对象
		namespace: function() {
            var a = arguments, l = a.length, o = null, i, j, p;

            for (i = 0; i < l; ++i) {
                p = ('' + a[i]).split('.');
                o = this;
                for (j = (root[p[0]] === o) ? 1 : 0; j < p.length; ++j) {
                    o = o[p[j]] = o[p[j]] || {};
                }
            }
            return o;
        },
		
		// create a app for some project
		app: function(name, sx) {
            var self = this,
				isStr = self.isString(name),
                O = isStr ? root[name] || {} : name;

            self.mix(O, self, self.__APP_MEMBERS, true);
			self.mix(O, self.isFunction(sx) ? sx() : sx);
			isStr && (root[name] = O);

            return O;
        },
		
		// when debuging, print some msg for unit testing
		log: function(msg) {
            if(!this.isDebug) return;
			
			if(this.isIE){
            	alert(msg);
            } else {
            	if(root.console !== undefined && console.log)
            		console.log(msg);
            }
        }
	});
	
	// 初始化
	iCat.__init();
}).call(this);

/** Dynamic loading mechanism */
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
					libFolder = /\d|\./.test(libFolder)? libFolder.replace(/\d(\/)?|\./g,'') : libFolder;
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
	
	_importOneFile = function(file, callback, mod, context){
		
		var	url = file,
				
			//去掉?|#后面的参数，保留纯净的文件
			_url = url.replace(/[\?#].*/, ''),
		
			// 执行callback函数
			_exec = function(){
				if(callback && iCat.isFunction(callback))
					callback(context || iCat);
				
				if(mod){
					MS[mod] = true;
					iCat.mods[mod] = iCat.mods[mod]&&iCat.mods[mod].length? iCat.mods[mod] : [];
					iCat.mods[mod].push(file);
				}
			};
		
		if(LS[_url]){
			_exec();
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
		incfile: function(f, cb){//加载一个或多个文件
			if(!f || !f.length) return;
			if(iCat.isString(f)){
				_importOneFile(_dealUrl(f), cb);
			} else if(iCat.isArray(f)) {
				var max = f.length - 1;
				iCat.foreach(f, function(i, v){
					i==max? _importOneFile(_dealUrl(v), cb) : _importOneFile(_dealUrl(v));
				});
			}
		},
		
		/* 加载文件形式：
		 * - 单个文件，支持字符串或文件数组(length为1)
		 * - 多个文件，必须是文件数组
		 */
		require: function(m, r, cb){//加载有依赖的模块
			if(!m && !r) return;
			if(MS[m]){ if(cb) cb(iCat); } else {
				if(iCat.isString(r)){
					_importOneFile(_dealUrl(r), cb, m);
				} else if(iCat.isArray(r)) {
					var max = r.length - 1;
					iCat.foreach(r, function(i, v){
						i==max? _importOneFile(_dealUrl(v), cb, m) : _importOneFile(_dealUrl(v), undefined, m);
					});
				}
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
		mainJS = iCat.refFile.getAttribute('main') || '', arrInit = [];
	
	if(/,/.test(corelib)){
		corelib = corelib.split(',');
		arrInit = [].concat(arrInit, corelib);
		arrInit = [].concat(arrInit, [iCat.corecss, mainJS]);
	} else {
		arrInit = [].concat(arrInit, [iCat.corecss, corelib, mainJS]);
	}
	iCat.inc(arrInit);
})(ICAT);

/**
 *
 * NOTES:
 *
 * 2012=09-23 13:45:00
 * 抛开underscore.js、json2.js，感觉框架太臃肿了
 * 新增iCat函数，使其成为Class的制造体
 *
 * 2012-09-23 10:00:00
 * 为了更和谐地利用backbone，icat融合了underscore.js、json2.js
 * - 避免冲突，改include方法名为incfile
 * - corelib支持多个文件设置
 *
 */