/*
 * @module My ui-framework
 * @author Wang.wenlong
 * @date 2011-10-08
 */
 
(function(iCat, win){
	
	// If ICAT is already defined, the existing ICAT object will not
    // be overwritten so that defined namespaces are preserved.
    if (win[iCat] === undefined) win[iCat] = {};
    iCat = win[iCat]; // shortcut
	
	var AP = Array.prototype,
        indexOf = AP.indexOf,
		toString = Object.prototype.toString,
		
		/**
		 * Copies all the properties of s to r.
		 * @return {Object} the augmented object
		 */
		mix = function(r, s, ov, wl) {
			if (!s || !r) return r;
			if (ov === undefined) ov = true;
			var i, p, len;

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
		},
		_mix = function(p, r, s, ov) {
            if (ov || !(p in r)) {
                r[p] = s[p];
            }
        },
		
		// [[Class]] -> type pairs
        class2type = {};
		
	mix(iCat, {
		
		/**
         * The version of the library.
         * @type {string}
         */
        version: '1.0.0',
		
		// iCat.app() with these members.
        __APP_MEMBERS: ['namespace'],
        __APP_INIT_METHODS: ['__init'],

        /**
         * Initializes ICAT object.
         * @private
         */
        __init: function() {
            this.Env = {//this指向iCat
                mods: {}
            };

            this.Config = {
                debug: true
            };
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
		
		/**
         * Copies all the properties of s to r.
         * @return {object} the augmented object
         */
        mix: mix,
		
		/**
         * Applies prototype properties from the supplier to the receiver.
         * @param r {Function} the object to receive the augmentation
         * @param s {Object|Function} the object that supplies the properties to augment
         * @param wl {String[]} a whitelist
         * @return {Object} the augmented object
         */
        augment: function(r, s, ov, wl) {
            mix(r.prototype, s.prototype || s, ov, wl);
            return r;
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
        add: function(name, fn) {
            var self = this;

            // override mode
            self.Env.mods[name] = {
                name: name,
                fn: fn
            };

            // call entry point immediately
            fn(self);

            // chain support
            return self;
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
		
	iCat.namespace('widget');
	iCat.__init();
	
	return iCat;
	
})('ICAT', this);//this指向window

/**
* @module  jQuery-methods
* @author  jn_dream@163.com
* @description Common methods from jQuery
*/
(function(iCat, $){
	
	iCat.mix(iCat, $);
	
	iCat.extend({
		
		/**
         * the jQuery's Object is or isn't exist.
         */
		detect: function(o){
			return o.length>0;
		},
		
		/**
		 * put jQuery's selector into the framework
		 */
		cQuery: function(){
			return $.apply(this,arguments);
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
		dealArgs: function(arr){
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
			var args = this.dealArgs(arguments);
			$(args[0]).bind.apply($(args[0]), args[1]);
		},
		
		/**
		 * 借用jQuery的live函数
		 * @param event arguments list
         * @description like jQuery's live
		 */
		live: function(){
			var args = this.dealArgs(arguments);
			$(args[0]).live.apply($(args[0]), args[1]);
		}
	}
})(ICAT, jQuery);

/**
* @module  loader
* @author  jn_dream@163.com
* @description
*/
(function (iCat) {
	var win = this,
        doc = win['document'],
        //head = doc.getElementsByTagName('head')[0] || doc.documentElement,
		mix = iCat.mix, C = iCat.cQuery,
		loader;
	
	loader = {
		
		/**
         * all kinds of path.
         */
		/*hostPath: function(){
			//var url = document.location.pathname.substr(1);
			//url = '/' + url.substr(0, url.indexOf('/'));
			return win['location'].protocol+'//'+win['location'].host+'/plugin/';
		},*/
		sysPath: C('#J_filePath').attr('sys-hostPath'),
		sysPlugin: C('#J_filePath').attr('sys-hostPath')+'/sys/icat/'+iCat.version+'/plugin/',
		appPlugin: C('#J_filePath').attr('app-pluginPath'),
		
		/**
         * Load a JavaScript/css file from the server using add script tag.
         * <code>
         *  ICAT.include([url]);//缺省路径是公用插件路径
         * </code>
         */
		impFileRec: {},//动态加载记录器
        include: function (file,path) {
            var files = typeof file == 'string' ? [file] : file/*,
				_addfile = function(isCSS,url){
					if(isCSS){
						var cssfile = document.createElement('link');
						cssfile.rel = 'stylesheet';
						cssfile.type = 'text/css';
						cssfile.href = url;
						head.appendChild(cssfile);
					} else {
						var jsfile = document.createElement('script');
						jsfile.type = 'text/javascript';
						jsfile.src = url;
						head.appendChild(jsfile);
					}
				}*/;
			
			/* 取得插件目录的三种方式：
			 -	1.参数传入
			 -	2.dom伪属性设定
			 -	3.js取得host地址/应用js中用iCat.hostPath指定
			
			hostPath = hostPath || (C('#J_hostPath').attr('data-pluginPath') ||
									(iCat.isFunction(this.hostPath) ? this.hostPath() : this.hostPath));*/
            path = path || this.sysPlugin;
			for (var i=0,ilen=files.length; i<ilen; i++){
                var name = files[i].replace(/^\s|\s$/g, ''),
					att = name.split('.'),
					ext = att[att.length - 1].toLowerCase(),
					isCSS = ext=='css',
					url = path + name;
                var tag = isCSS ? 'link' : 'script',
					attr = isCSS ? ' type="text/css" rel="stylesheet" ' : ' type="text/javascript" ',
					link = (isCSS ? 'href' : 'src') + '="' + url + '"';
                if(!this.impFileRec[url]){
					document.write('<' + tag + attr + link + '></' + tag + '>');
					this.impFileRec[url] = true;
				}
				/*if(!this.impFileRec[url]){
					_addfile(isCSS,url);
					this.impFileRec[url] = true;
				}*/
            }
        },
		
		use: function(mod, callback){
			//iCat.getScript(iCat.plugin[mod],callback);
		}
	};
	
	mix(iCat, loader);
	
})(ICAT);

/**
* @module  Util
* @author  jn_dream@163.com
* @description
*/
ICAT.add('util', function(iCat){
	var Dom = iCat.DOM, Event = iCat.Event, C = $ = iCat.cQuery;
	iCat.namespace('util');
	
	iCat.mix(iCat.util, {
		
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
         * 全选效果.
         * @param iptCla {String} Selector's style
         * @param tipCla {String} Selector's style
         */
		checkAll: function(objAll,oneCla,context){
			if(context){
				var arrCheck = oneCla;
			} else {
				var arrCheck = Dom.query('.'+oneCla)
			}
			var len1 = arrCheck.length, i;
			var arrControl = Dom.query('.'+objAll.className),
				len2 = arrControl.length, j;
			
			if(objAll.checked){
				for(i=0; i<len1; i++){
					arrCheck[i].checked = true;
				}
				for(j=0; j<len2; j++){
					arrControl[j].checked = true;
				}
			} else {
				for(i=0; i<len1; i++){
					arrCheck[i].checked = false;
				}
				for(j=0; j<len2; j++){
					arrControl[j].checked = false;
				}
			}
		},
		
		/**
         * 未全选效果.
         * @param iptCla {String} Selector's style
         * @param tipCla {String} Selector's style
         */
		checkOne: function(objOne,allCla){
			var arrControl = Dom.query('.'+allCla),
				len = arrControl.length, i, isAll;
			
			if(!objOne.checked){
				for(i=0; i<len; i++){
					arrControl[i].checked = false;
				}
				return;
			}
			for(var j=0, arrOne=Dom.query('.'+objOne.className), l=arrOne.length; j<l; j++){
				if(!arrOne[j].checked){
					isAll = false;
					break;
				} else {
					isAll = true;
				}
			}
			if(isAll){
				for(i=0; i<len; i++){
					arrControl[i].checked = true;
				}
			}
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
			    if (sPW.length <= 4)
			        return 0; //密码太短 
			    Modes = 0;
			    for (i = 0; i < sPW.length; i++) {
			        //测试每一个字符的类别并统计一共有多少种模式. 
			        Modes |= _CharMode(sPW.charCodeAt(i));
			    }
			    return _bitTotal(Modes);
			},

			_pwStrength = function (o, pwd) {
			    var strCla = ['pwd0', 'pwd1', 'pwd2'],
					$pwdSafe = o.next('.J_pwdSafe');
			    if (pwd == null || pwd == '') {
			        var oldCla = $pwdSafe.attr('data-class');
			        $pwdSafe.removeClass(oldCla);
			    } else {
			        var S_level = _checkStrong(pwd);
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
        }
	});
});

/**
 *
 * NOTES:
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