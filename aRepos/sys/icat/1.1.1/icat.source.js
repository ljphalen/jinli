/*!
 * Copyright 2011~2012, ICAT JavaScript Library v1.1.1
 * MIT Licensed
 * @Author valleykiddy@gmail.com
 * @Time 2012-08-06 14:00:00
 */

(function(iCat, win){
	
	// shortcut for defined ICAT
	if(win[iCat] === undefined) win[iCat] = {};
    iCat = win[iCat];
	
	var _ua = navigator.userAgent, isDebug = /debug/i.test(window.location.href),
		
		_isfn = function(o){
			return !o? false : o.constructor === Function;
		},
		
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
	delete pith;
	
	// set the path
	var doc = document, scripts = doc.getElementsByTagName('script'),
		ar = doc.getElementsByName('appRef')[0],
		corelib, main, src, timestamp = '';
	iCat.appRef = ar? ar.content : '';
	for(var i=0, el; el = scripts[i++];){
		src = !!doc.querySelector ? el.src : el.getAttribute('src',4);
		if(src && /icat(\.source)?\.js/i.test(src)){
			if(/\?(v|t)=\d+/.test(src)){
				timestamp = '?'+src.replace(/.*\?/,'');
			}
			iCat.sysRef = src.substr(0, src.lastIndexOf('/icat/'));
			corelib = el.getAttribute('corelib');
			main = el.getAttribute('main');
			break;
		}
	}
	
	/** The ICAT System function for organization code */
	iCat.mix(iCat, {
		
		// The version of the library.
		version: '1.1.1',
		
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
			var self = this;
			
			self.widget = {};
			self.util = {};
			
			self.sysPlugin = self.sysRef + '/icat/'+self.version+'/plugin/';
			self.appPlugin = self.appRef + '/assets/plugin/';
			self.jqPath = self.sysRef + '/lib/jquery/1.8.0/';
			self.timestamp = timestamp;
			
			// 加载js库和关键js
			if(corelib){
				corelib = (isDebug && !/\.source/.test(corelib)? corelib.replace(/\.js/, '.source.js') : corelib)+timestamp;
				corelib = /^\//.test(corelib)? corelib : '/lib/'+corelib;
				corelib = corelib.replace(/\?.*/,'')+timestamp;
				corelib = iCat.sysRef+corelib;
				doc.write('<script src="'+corelib+'"><'+'/script>');
				iCat.loadStore[corelib.replace(/\?.*/,'')] = true;
			};
			if(main){
				main = (isDebug && !/\.source/.test(main)? main.replace(/\.js/, '.source.js') : main)+timestamp;
				main = /^\//.test(main)? main : '/assets/js/'+main;
				main = main.replace(/\?.*/,'')+timestamp;
				main = iCat.appRef+main;
				doc.write('<script src="'+main+'"><'+'/script>');
				iCat.loadStore[main.replace(/\?.*/,'')] = true;
			}
		},
		
		// commonly used judgment
		isFunction: _isfn,
		isString: function(o){
			return !o? false : typeof o === 'string';
		},
		isArray: function(o){
			return !o? false : o.constructor === Array;
		},
		
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
		
		// type1: 如 jquery, jquery.js, ~linkage/core.js, app/test/core.js等裸奔者(sys级)
		// type2: 如 ./core.js, ./~linkage/core.js, ./app/test.core.js等带有./者(app级)
		// type3: 如 http://www.baidu.com/core.js等外链网址
		// 对于省略扩展名的，默认为.js
		_dealUrl = function(s){
			if(!s) return;
			
			var url = s.replace(/\s/g,'');//第一步，清理空格
			if(!url) return;
			
			if(iCat.isDebug){
				url = /\.source/i.test(url)? url:url.replace(/\.js|\.css/g, /\.css/i.test(url)? '.source.css':'.source.js');
			}
			if(/\w?:\/\//.test(url)){//类型3，直接输出
				return url;
			} else {
				if(/\.\/(~)?/.test(url)){//类型2
					if(/\.\/\w+/.test(url))
						url = url.replace(/\.\//g, iCat.appRef+'/');//+(/\.css/i.test(url)? '/assets/css/':'/assets/js/')
					if(/\.\/~/.test(url))
						url = url.replace(/\.\/~/g, iCat.appPlugin);
				} else {//类型1
					if(/~\w+/.test(url))
						url = url.replace(/~/g, iCat.sysPlugin);
					else
						url = iCat.sysRef + '/' + url;
				}
			}
			
			return url+iCat.timestamp;
		};
		
	//加载构造函数
	function load(loadQueue, callback, mod){
		this.loadQueue = loadQueue;
		this.callback = callback || function(){};
		this.mod = mod;
	}
	
	//加载方法
	load.prototype = {
		
		start: function(){
			var self = this;
			if(self.loadQueue.length==0) return;
			
			var	url = self.loadQueue[0],
				
				//去掉?|#后面的参数，保留纯净的文件
				_url = url.replace(/[\?#].*/, ''),
				
				// 执行callback函数
				_exec = function(o, cx){
					if(o.loadQueue.length>0){
						o.next();
					} else {
						if(o.callback && iCat.isFunction(o.callback))
							o.callback(cx);
						if(o.mod)
							MS[o.mod] = true;
					}
				};
			
			self.loadQueue.shift();
			
			if(LS[_url]){
				_exec(self, iCat);
				return this;
			}
			
			var node, type = type || _url.replace(/.*\./g,'');
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
			//if(self.charset) node.charset = self.charset;
			
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
								_exec(self, iCat);
								LS[_url] = true;
							}
						};
					}
					ohead.appendChild(node);
				},1);
			} else {
				if(type==='js'){
					node.onload = function(){
						_exec(self, iCat);
						LS[_url] = true;
					};
				}
				ohead.appendChild(node);
			}
			
			/* css不需要监听加载完成*/
			if(type==='css'){
				_exec(self, iCat);
				LS[_url] = true;
			}
		},
		
		next: function(){
			var self = this;
			if(self.loadQueue.length>0){
				self.start(self.loadQueue[0]);
			}
		}
	};
	
	//对外接口
	iCat.mix(iCat, {
		
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
				iCat.foreach(f, function(){
					queue.push(_dealUrl(this));
				});
			} else {
				return;
			}
			
			var loader = new load(queue, cb).start();
			delete loader;
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
					iCat.foreach(r, function(){
						queue.push(_dealUrl(this));
					});
				}
				var loader = new load(queue, cb, m).start();
				delete loader;
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
})(ICAT);