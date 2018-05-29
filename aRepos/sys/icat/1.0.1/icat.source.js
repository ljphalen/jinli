/*!
 * Copyright 2011, ICAT JavaScript Library v1.0.1
 * MIT Licensed
 * @Author jn_dream@163.com
 * @Time 2012-01-05 13:52:30
 */

(function(iCat, win){
	
	// shortcut for defined ICAT
	if(win[iCat] === undefined) win[iCat] = {};
    iCat = win[iCat];
	
	var oBase = document.getElementById('J_hostPath'),
        AP = Array.prototype, indexOf = AP.indexOf,
		toString = Object.prototype.toString,
		_inArray = function(arr, item){
			for (var i = 0, len = arr.length; i < len; ++i) {
				if (arr[i] === item) {
					return i;
				}
			}
			return -1;
		},
		
		/**
		 * Copies all the properties of s to r.
		 * @param w(hite)l(ist):白名单, ov(erwrite):覆盖
		 * @return {Object} the augmented object
		 */
		mix = function(r, s, wl, ov){
			if(!s || !r) return r;
			if(ov===undefined) ov = true;
			
			if(wl && (len = wl.length)){
				if(typeof wl==='string' && /(\^)/g.test(wl)){
					var wl = wl.replace(/(\^)/g,'').split('|');
					for(var p in s){
						if(_inArray(wl,p)<0){
							if (ov || !(p in r)) r[p] = s[p];
						}
					}
				} else {
					for(var i=0; i<len; i++){
						p = wl[i];
						if(p in s){
							if (ov || !(p in r)) r[p] = s[p];
						}
					}
				}
			} else {
				for(var p in s){
					if (ov || !(p in r)) r[p] = s[p];
				}
			}
			return r;
		},
		
		// [[Class]] -> type pairs
        class2type = {};
	
	mix(iCat, {
		
		/**
         * The version of the library.
         * @type {string}
         */
		version: '1.0.1',
		
		/** The files path of the Application*/
		__SYS_PATH: oBase.getAttribute('sys-hostPath'),
		__APP_PATH: oBase.getAttribute('app-filePath'),
		
		/** iCat.app() with these members.*/
        __APP_MEMBERS: ['namespace'],
		__APP_INIT_METHODS: ['__init'],
		
		__init: function(){
			this.Mods = {};
		},
		
		isNull: function(o) {
            return o === null;
        },

        isUndefined: function(o) {
            return o === undefined;
        },
		
		/**
         * Determines whether or not the provided object is a boolean.
         */
        isBoolean: function(o) {
            return typeof o === 'boolean';
        },

        /**
         * Determines whether or not the provided object is a string.
         */
        isString: function(o) {
            return typeof o === 'string';
        },

        /**
         * Determines whether or not the provided item is a legal number.
         * NOTICE: Infinity and NaN return false.
         */
        isNumber: function(o) {
            return typeof o === 'number' && isFinite(o);
        },
		
		/**
         * Determines whether or not the provided object is a function.
         */
		isFunction: function(o) {
			return o.constructor === Function;
		},
		
		/**
         * Determines whether or not the provided object is a array.
         */
		isArray: function(o) {
			return o.constructor === Array;
		},
		
		inArray: _inArray,
		
		/**
         * Search for a specified value within an array.
         */
        indexOf: indexOf ?
            function(elem, arr) {
                return indexOf.call(arr, elem);
            } :
            function(elem, arr) {
                for (var i = 0, len = arr.length; i < len; ++i) {
                    if (arr[i] === elem) {
                        return i;
                    }
                }
                return -1;
            },
		
		foreach: function(arr, cb){
			for(var i=0, ilen=arr.length; i<ilen; i++){
				cb.call(arr[i]);
			}
		},
		
		/**
         * Copies all the properties of s to r.
         * @return {object} the augmented object
         */
		mix: function(){
			var args = arguments, self = this;
			if(args.length==1) return mix(self, args[0]);
			if(this.isArray(args[1])){
				var s = {}, as = args[1], i=0, ilen=as.length, argus=[], j=0, jlen=args.length;
				for(; i<ilen; i++){
					mix(s, as[i], undefined, true);
				}
				for(; j<jlen; j++){
					j===1? argus.push(s) : argus.push(args[j]);
				}
				return mix.apply(self, argus);
			} else {
				return mix.apply(self, args);
			}
		},
		
		/**
         * Registers a module.
         * @param {string} name module name
         * @param {function} fn entry point into the module that is used to bind module to ICAT
         * <pre>
         * ICAT.add('module-name', function(iCat){ });
         * </pre>
         * @return {ICAT}
         */
		add: function(name, fn){
            var self = this;
			
			self.Mods[name] = {
                name: name,
                func: fn
            };
			
            fn(self);
		},
		
		/**
         * Returns the namespace specified and creates it if it doesn't exist. Be careful
         * when naming packages. Reserved words may work in some browsers and not others.
         * <pre>
         * iCat.namespace('AC.app'); // returns AC.app
         * iCat.namespace('app.Shop'); // returns AC.app.Shop
         *
         * AC.namespace('AC.app'); // returns AC.app
         * AC.namespace('app.Shop'); // returns AC.app.Shop
         * </pre>
         * @return {object}  A reference to the last namespace object created
         */
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
		
		/**
         * create app based on ICAT.
         * @param name {String} the app name
         * @param sx {Object} static properties to add/override
         * <code>
         * iCat.app('AC');
         * AC.namespace('app'); // returns AC.app
         * </code>
         * @return {Object}  A reference to the app global object
         */
        app: function(name, sx) {
            var self = this,
				isStr = iCat.isString(name),
                O = isStr ? win[name] || {} : name,
                i = 0,
                len = self.__APP_INIT_METHODS.length;

            mix(O, self, true, self.__APP_MEMBERS);
            for (; i < len; i++) self[self.__APP_INIT_METHODS[i]].call(O);
			
			mix(O, iCat.isFunction(sx) ? sx() : sx);
			isStr && (win[name] = O);

            return O;
        },
		
        /**
         * Prints debug info.
         * @param {string} msg The message to log.
         * @param {string} sign The log category for the message. Default
         * categories are "info", "warn", "error", time" etc.
         * @param {string} src The source of the the message (opt)
         * @return {ICAT}
         */
        log: function(msg, sign, src) {
            var c = this.Config;

            if (c.debug) {
                src && (msg = src + ': ' + msg);
                if (win.console !== undefined && console.log) {
                    console[sign && console[sign] ? sign : 'log'](msg);
                }
            }
        },
		
		/**
         * Throws error message.
         */
        error: function(msg) {
            if(this.Config.debug) {
                throw msg;
            }
        }
	});
	
	iCat.__init();
	
})('ICAT', this);


/**
* @module  loader
* @author  jn_dream@163.com
* @description
*/
(function(iCat){
	
	var path = {
		sysPlugin: iCat.__SYS_PATH+'/sys/icat/'+iCat.version+'/plugin/',
		appPic: iCat.__APP_PATH+'/pic/',
		appPlugin: iCat.__APP_PATH+'/assets/plugin/',
		appImg: iCat.__APP_PATH+'/assets/img/',
		appCss: iCat.__APP_PATH+'/assets/css/',
		appJS: iCat.__APP_PATH+'/assets/js/'
	},
	
	_doc = document, _win = window,
	_loaded = {},//已加载
	_loading = {},//正在加载
	_jsFiles = _doc.getElementsByTagName('script'),
	_cssFiles = _doc.getElementsByTagName('link'),
		
	//加载文件函数
	_import = function(url, type, isPrev, callback, charset, context){
	
		if(!url) return;
		
		if(_loaded[url]){
			_loading[url] = false;
			if(callback) callback(url, context);
			return;
		}
		
		// 加载中的文件有可能是太大，有可能是404
		// 当加载队列中再次出现此模块会再次加载，理论上会出现重复加载
		if(_loading[url]){
			setTimeout(function() {
				_import(url, type, charset, callback, context, isPrev);
			}, 1);
			return;
		}
		_loading[url] = true;
		
		var node, fileType = type || url.toLowerCase().split(/\./).pop().replace(/[\?#].*/, '');
		if (fileType==='js') {
			node = _doc.createElement('script');
			node.setAttribute('type', 'text/javascript');
			node.setAttribute('src', url);
			//node.setAttribute('async', true);
		} else if (fileType==='css') {
			node = _doc.createElement('link');
			node.setAttribute('type', 'text/css');
			node.setAttribute('rel', 'stylesheet');
			node.setAttribute('href', url);
			_loaded[url] = true;
		}
		if(!node) return;
		if(charset) node.charset = charset;
		
		
		var refFile = _jsFiles[0], head = _doc.getElementsByTagName('head')[0];
		
		// CSS无必要监听是否加载完毕
		if (fileType==='css') {
		  isPrev ? refFile.parentNode.insertBefore(node, refFile) : head.appendChild(node);
		  if (callback) callback();//url, context
		  return;
		}
		
		node.onload = node.onreadystatechange = function() {
			if (!this.readyState || this.readyState==='loaded' || this.readyState==='complete') {
				_loaded[this.getAttribute('src')] = true;
				if (callback) callback();//this.getAttribute('src'), context
				node.onload = node.onreadystatechange = null;
			}
		};
		isPrev ? refFile.parentNode.insertBefore(node, refFile) : head.appendChild(node);
	},
		
	loader = {
		
		//调用普通js，或注册Mods模块的js文件
		include: function(file,fpath,callback){//path缺省值为sys插件的路径
			var files = typeof file == 'string' ? [file] : file,
				fpath = fpath || path.sysPlugin;
			
			iCat.foreach(files, function(){
				if(this.indexOf('/')!=-1){
					var type = 'js',
						url = fpath + this.replace(/^\s|\s$/g, '')+'index.js',
						tag = 'script',
						attr = ' type="text/javascript" ',
						link = 'src="'+url+'"';
				} else {
					var theFile = this.replace(/^\s|\s$/g, ''),
						arrFile = theFile.split('.'),
						type = arrFile[arrFile.length-1].toLowerCase(),
						isCSS = type==='css',
						url = fpath + theFile,
						tag = isCSS ? 'link' : 'script',
						attr = isCSS ? ' type="text/css" rel="stylesheet" ' : ' type="text/javascript" ',
						link = (isCSS ? 'href' : 'src') + '="' + url + '"';
				}
				//_import(url, type, undefined, callback);
				
                if(!_loaded[url]){
					document.write('<' + tag + attr + link + '></' + tag + '>');
					_loaded[url] = true;
				}
			});
		},
		
		//调用在Mods中注册过js的模块，如果暂时无法找到该模块，延时1秒
		use: function(mod, callback){
			var name = mod.replace(/^\s|\s$/g, ''),
				i = 0,
				si = setInterval(function(){
					i++;
					if(i>1000){
						clearInterval(si);
						return;
					}
					if(iCat.Mods[name]){
						clearInterval(si);
						callback.call(iCat.Mods[name]);
					}
				},1);
		}
	};
		
	//初始化页面上调用的css、js文件
	iCat.foreach(_jsFiles, function(){ _loaded[this.src] = true;});
	iCat.foreach(_cssFiles, function(){ _loaded[this.href] = true;});
	
	iCat.mix(iCat, [path,loader]);
	
})(ICAT);


/**
* @module  Util
* @author  jn_dream@163.com
* @description
*/
ICAT.add('Util', function(iCat){
	var util = {
		
		//模板填充数据成为html
		tdMerge: function(t,d,r){//r(eserve)表示是否保留
			if(!iCat.isString(t) || t.indexOf('}')==-1) return;
			
			var arr, s = '';
			arr = t.split('{');
			for(var i=0, ilen=arr.length; i<ilen; i++){
				if(arr[i].indexOf('}')==-1){
					s += arr[i];
				} else {
					var _arr = arr[i].split('}'),
						key = d[_arr[0]];
					s += (key? key:(r? '{'+_arr[0]+'}':''))+_arr[1];
				}
			}
			
			return s;
		},
		
		//返回一个实例化对象
		New: function(aClass, aParams){
			
			//定义临时的中转函数壳
			function new_(){
				aClass.init.apply(this, aParams);
			};
			
			iCat.mix(new_.prototype, aClass, '^init');
			return new new_();
		},
		
		//延迟1秒使用
		delay: function(o, callback){
			var i = 0;
			if(typeof o=='undefined'){
				var si = setInterval(function(){
					i++;
					if(i>1000){
						clearInterval(si);
						return;
					}
					if(o){
						clearInterval(si);
						callback.call(this);
					}
				},1000);
			}
		},
		
		/**
         * 聚焦时提示语为空，失焦时提示语出现.
         * @param iptCla {String} Selector's style
         * @param strMsg {String} tips
         */
		focusBox: function(iptCla, strMsg){
			var $tInput = $(iptCla), msg = strMsg || $tInput.val();
			if(!$tInput.length) return;
			
			$tInput.focus(function(){
				if(this.value==msg) this.value = '';
			}).blur(function(){
				if(this.value=='') this.value = msg;
			});
		},
		
		/**
         * 提示输入多少字，还剩多少字.
         * @param iptCla {String} Selector's style
         * @param tipCla {String} Selector's style
         */
		tipNum: function(iptCla,tipCla){
			var $tInput = $(iptCla),
				maxLen = $tInput.attr('maxlength'),
				$tip = $tInput.siblings(tipCla),
				eChange = iCat.browser.msie ? 'propertychange':'input focus blur',
				_tipNum = function(o){
					var tValue = o.val(),
						tLen = tValue.replace(/[^\x00-\xFF]/g,'**').length;
					$tip.html('您已经输入了:'+tLen+'个字符，还剩'+(maxLen-tLen)+'个');
					
					if(maxLen-tLen<=0){
						o.val(tValue.substring(0,maxLen));
					}
				};
			if(!$tInput.length && !$tip.length) return;
			
			//初始化
			_tipNum($tInput);
			$tInput.bind(eChange, function(){_tipNum($tInput);});
		},
		
		/**
         * 文本提示效果.
         * @param aimCla {String} Selector's style
         * @param tipCla {String} Selector's style
         */
		tipShow: function(config){
			var dConfig = {aimCla:'.J_aimObject', tipId:'#J_tipBox', offsetX:0, offsetY:0},
				cfg = iCat.extend(dConfig,config),
				oAim = C(cfg.aimCla),
				aimPos = oAim.offset(),
				tId = cfg.tipId.replace(/(#)/g,'');
			if(!oAim.length) return;
			if(!C(cfg.tipId).length){
				C('body').append('<div id="'+tId+'" class="tip-box"></div>');
				var oTip = C('body').find(cfg.tipId);
			} else {
				var oTip = C('body').find(cfg.tipId);
			}
			
			oAim.hover(function(){
				oTip.css({
					'left':(aimPos.left+cfg.offsetX)+'px',
					'top':(aimPos.top+cfg.offsetY)+'px'
				}).html(oAim.attr('data-tipText')).show();
			}, function(){
				oTip.hide();
			});
		},
		
		/**
         * 密码强度.
         */
        pwdStrength: function () {
            var _CharMode = function (iN) {
                if (iN >= 48 && iN <= 57) //数字 
                    return 1;
                if (iN >= 65 && iN <= 90) //大写字母 
                    return 2;
                if (iN >= 97 && iN <= 122) //小写 
                    return 4;
                else
                    return 8; //特殊字符 
            },

			_bitTotal = function (num) {
			    modes = 0;
			    for (i = 0; i < 4; i++) {
			        if (num & 1) modes++;
			        num >>>= 1;
			    }
			    return modes;
			},

			_checkStrong = function (sPW) {
				if (sPW.length <= 4 || /^\d*$/g.test(sPW))
			        return 0; //密码太短或纯数字
			    Modes = 0;
			    for (i = 0; i < sPW.length; i++) {
			        //测试每一个字符的类别并统计一共有多少种模式. 
			        Modes |= _CharMode(sPW.charCodeAt(i));
			    }
			    return _bitTotal(Modes);
			},

			_pwStrength = function (o, pwd) {
			    var strCla = ['pwd0', 'pwd1', 'pwd2'],
					$pwdSafe = o.siblings('.J_pwdSafe');
			    if (pwd == null || pwd == '') {
			        var oldCla = $pwdSafe.attr('data-class');
			        $pwdSafe.removeClass(oldCla);
					$pwdSafe.siblings('span').show();
			    } else {
			        var S_level = _checkStrong(pwd);
					$pwdSafe.siblings('span').hide();
			        switch (S_level) {
			            case 0:
			                var oldCla = $pwdSafe.attr('data-class');
			                $pwdSafe.removeClass(oldCla).addClass(strCla[0]);
			                $pwdSafe.attr('data-class', strCla[0]);
			                break;
			            case 1:
			                var oldCla = $pwdSafe.attr('data-class');
			                $pwdSafe.removeClass(oldCla).addClass(strCla[1]);
			                $pwdSafe.attr('data-class', strCla[1]);
			                break;
			            case 2:
			                var oldCla = $pwdSafe.attr('data-class');
			                $pwdSafe.removeClass(oldCla).addClass(strCla[2]);
			                $pwdSafe.attr('data-class', strCla[2]);
			                break;
			        }
			    }
			    return;
			};

            $('.J_pwdStrength').bind('blur keyup', function () {
                _pwStrength($(this), this.value);
            });
        },
		
		/*封装表单元素=》字符串 */
		form2str: function(form){
			if(!form) return;
			var strArguments = '';
			for(var i=0, els=form.elements, ilen=els.length; i<ilen; i++){
				var oMe = $(els[i]);
				if(oMe.attr('name')){
					strArguments += '&' + oMe.attr('name') + '=' + els[i].value;
				}
			}
			strArguments = strArguments.replace('&','?');
			return strArguments;
		},
		
		/*封装表单元素=》json */
		form2json: function(form){
			if(!form) return;
			var jsonArguments = {};
			for(var i=0, els=form.elements, ilen=els.length; i<ilen; i++){
				var oMe = $(els[i]);
				if(oMe.attr('name')){
					jsonArguments[oMe.attr('name')] = els[i].value;
				}
			}
			return jsonArguments;
		}
	}
	
	iCat.mix(util);
});


/**
* @module  jQuery-methods
* @author  jn_dream@163.com
* @description Common methods from jQuery
*/
(function(iCat,$){
	
	iCat.mix({
		cQuery: function(){
			return $.apply(this,arguments);
		},
		
		getScript: function(file,fpath,callback){
			var files = iCat.isString(file) ? [file] : file,
				fpath = fpath || iCat.sysPlugin;
			
			iCat.foreach(files, function(){
				var url = fpath + this.replace(/^\s|\s$/g, '') + (this.indexOf('/')!=-1? 'index.js':'');
				$.get(url, undefined, callback, 'script');
			});
		}
	});
	
	iCat.namespace('DOM','Event');
	iCat.DOM = {
		/**
		 * 利用jQuery选择器，获得原生的dom对象数组
		 * @param arr {String[]} arguments list
		 * @return {dom[]} the dom Object
		 */
		query: function(){
			var arrNodes = [], nodes = $.apply(this,arguments);
			
			nodes.each(function(){
				arrNodes.push(this);
			});
			return arrNodes;
		},
		
		/**
		 * 利用jQuery选择器，获得第一个原生的dom对象
		 * @param arr {String[]} arguments list
		 * @return {dom[]} the first dom Object
		 */
		get: function(){
			return $.apply(this,arguments).get(0);
		}
	};
	
	iCat.Event = {
		/**
		 * 处理传入事件的参数
		 * @param arr {String[]} arguments list
		 * @return {Object} the augmented object
		 */
		_args: function(arr){
			var args = [], evtArgs = [];
			iCat.each(arr, function(i,v){
				i===0 ? evtArgs.push(v) : args.push(v);
			});
			evtArgs.push(args);
			
			return evtArgs;
		},
		
		/**
		 * 借用jQuery的bind函数
		 * @param event arguments list
		 * @description like jQuery's bind
		 */
		on: function(){
			var args = this._args(arguments);
			$(args[0]).bind.apply($(args[0]), args[1]);
		},
		
		/**
		 * 借用jQuery的live函数
		 * @param event arguments list
		 * @description like jQuery's live
		 */
		live: function(){
			var args = this._args(arguments);
			$(args[0]).live.apply($(args[0]), args[1]);
		}
	}
})(ICAT,jQuery);

/**
 *
 * NOTES:
 *
 *2012.2.14
 * - 增加loader模块，include、use和getScript方法
 * - 优化mix方法
 *
 *2012.1.5
 * - 分离出iCat最核心的方法，作为core，名称依然叫做icat
 * - 牵涉到路径不止是插件，还有其他css和js，甚至是pic，再次优化路径
 * - 改写mix方法
 *
 *2011.11.8
 * - 感觉loader里面的hostPath有点混乱，梳理了一下，js用到路径无非是两种路径：sys级别的公用插件和app级别的插件
 * - 针对sys插件，定了hostPath就能确定插件位置；针对app插件，可以用伪属性去指定。
 *
 * 2011.10.16
 * - 既然把jQuery当跑车的轮子，就放心的去用，不再有普通轮子了。此框架只是为了组织代码，形成标准框架。至于dom、event等，让jQuery去做吧。
 * - 利用jQuery转化DOM模块、Event模块
 *
 * 2011.10.11
 * - 开辟iCat与jQuery融合的独立模块（匿名函数中），把jQuery上的方法“直接嫁接到”iCat上
 * - 增加DOM模块，以后丰富
 * - 增加Event模块，以后丰富
 * - 删除与jQuery重复的方法：isFunciton、merge、extend等
 *
 * 2011.9.10
 * - add 方法决定内部代码的基本组织方式（用 module 和 submodule 来组织代码）。
 * - mix, merge, augment, extend 方法，决定了类库代码的基本实现方式，充分利用 mixin 特性和 prototype 方式来实现代码。
 * - namespace, app 方法，决定子库的实现和代码的整体组织。
 * - log, error 方法，简单的调试工具和报错机制。
 *
 * 2011.8.10
 * - 创建js库，宗旨是把jquery当汽车轮子，架构及代码组织依照本库。
 * - 此库包含基本的几个方面：
	1、ICAT全局变量的定义；
	2、版本号及公共方法mix；
	3、namespace、app及其他方法；
	4、动态加载模块；
	5、加载js的方法；
 */