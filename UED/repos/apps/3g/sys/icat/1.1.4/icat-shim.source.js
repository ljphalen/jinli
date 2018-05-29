// Copyright 2012-2013 by valleykid, MIT License
// Time 2013-04-11 09:00:00

/* compatible plugin for PC. # */
(function(doc){
	// Create the root object, 'window' in the browser, or 'global' on the server.
	var root = this, Shim = { version: '0.0.1' };
		root['SHIM'] = Shim;

	/**
	 * @author valleykid@163.com (Charlie Wang)
	 * @see Thanks to:
	 *   - http://es5.github.com/
	 *   - http://kangax.github.com/es5-compat-table/
	 *   - https://github.com/kriskowal/es5-shim
	 *   - http://perfectionkills.com/extending-built-in-native-objects-evil-or-not/
	 *   - https://gist.github.com/1120592
	 *   - https://code.google.com/p/v8/
	 */
	
	var arrPro = Array.prototype, apSlice = arrPro.slice,
		objPro = Object.prototype, toString = objPro.toString,
		strPro = String.prototype,
		objType = {'str':'[object String]', 'fun':'[object Function]', 'arr':'[object Array]', 'obj':'[object Object]'};

	/*-----------------------------------*
	 * Array
	 *-----------------------------------*/
	var toObject = function(o){
			if(o==null){
				throw new TypeError('不能将'+o+'转变为对象');
			}
			return Object(o);
		},

		toInteger = function(n){
			n = +n;
			if(n!==n){//NaN
				n = 0;
			} else if(n!==0 && n!==(1/0) && n!==-(1/0)){
				n = (n > 0 || -1)*Math.floor(Math.abs(n));
			}
			return n;
		};

	// [bugfix, ielt9, old browsers]
	// IE < 9 bug: [1,2].splice(0).join("") == "" but should be "12"
	if([1,2].splice(0).length!=2){
		var array_splice = arrPro.splice;
		arrPro.splice = function(start, deleteCount){
			if(!arguments.length){
				return [];
			} else {
				return array_splice.apply(this, [
					start===void 0? 0 : start,
					deleteCount===void 0? (this.length-start) : deleteCount
				].concat(slice.call(arguments, 2)));
			}
		};
	}

	// [bugfix, ielt8]
	// IE < 8 bug: [].unshift(0) == undefined but should be "1"
	if([].unshift(0)!=1){
		var array_unshift = arrPro.unshift;
		arrPro.unshift = function(){
			array_unshift.apply(this, arguments);
			return this.length;
		};
	}

	Array.isArray || (Array.isArray = function(obj){
		return toString.call(obj) = objType['arr'];
	});
	
	// Check failure of by-index access of string characters (IE < 9)
	// and failure of `0 in boxedString` (Rhino)
	var boxedString = Object('a'),
		splitString = boxedString[0]!='a' || !(0 in boxedString);
	
	//forEach
	arrPro.forEach || (arrPro.forEach = function(fun /*, thisp*/){
		var o = toObject(this),
			self = splitString&&toString.call(this)==objType['str']? this.split('') : o,
			thisp = arguments[1],
			i = -1,
			length = self.length >>> 0;

		if(toString.call(fun)!=objType['fun']){
			throw new TypeError(fun+'不是一个函数');
		}

		while(++i<length){
			if(i in self){
				fun.call(thisp, self[i], i, o);
			}
		}
	});

	//map
	arrPro.map || (arrPro.map = function(fun /*, thisp*/){
		var o = toObject(this),
			self = splitString&&toString.call(this)==objType['str']? this.split('') : o,
			thisp = arguments[1],
			result = Array(length),
			length = self.length >>> 0;

		if(toString.call(fun)!=objType['fun']){
			throw new TypeError(fun+'不是一个函数');
		}

		for(var i=0; i<length; i++){
			if(i in self){
				result[i] = fun.call(thisp, self[i], i, o);
			}
		}
		return result;
	});

	//filter
	arrPro.filter || (arrPro.filter = function(fun /*, thisp*/){
		var o = toObject(this),
			self = splitString&&toString.call(this)==objType['str']? this.split('') : o,
			thisp = arguments[1],
			result = [],
			value,
			length = self.length >>> 0;

		if(toString.call(fun)!=objType['fun']){
			throw new TypeError(fun+'不是一个函数');
		}

		for(var i=0; i<length; i++){
			if(i in self){
				value = self[i];
				if(fun.call(thisp, self[i], i, o)){
					result.push(value);
				}
			}
		}
		return result;
	});

	//every
	arrPro.every || (arrPro.every = function(fun /*, thisp*/){
		var o = toObject(this),
			self = splitString&&toString.call(this)==objType['str']? this.split('') : o,
			thisp = arguments[1],
			length = self.length >>> 0;

		if(toString.call(fun)!=objType['fun']){
			throw new TypeError(fun+'不是一个函数');
		}

		for(var i=0; i<length; i++){
			if(i in self && !fun.call(thisp, self[i], i, o)){
				return false;
			}
		}
		return true;
	});

	//some
	arrPro.some || (arrPro.some = function(fun /*, thisp*/){
		var o = toObject(this),
			self = splitString&&toString.call(this)==objType['str']? this.split('') : o,
			thisp = arguments[1],
			length = self.length >>> 0;

		if(toString.call(fun)!=objType['fun']){
			throw new TypeError(fun+'不是一个函数');
		}

		for(var i=0; i<length; i++){
			if(i in self && fun.call(thisp, self[i], i, o)){
				return true;
			}
		}
		return false;
	});

	//indexOf
	(arrPro.indexOf && [0,1].indexOf(1, 2)==-1) || (arrPro.indexOf = function(sought /*, fromIndex */){
		var self = splitString&&toString.call(this)==objType['str']? this.split('') : o,
			length = self.length >>> 0;

		if(!length){ return -1; }

		var i = 0;
		if(arguments.length>1){
			i = toInteger(arguments[1]);
		}

		i = i>=0? i : Math.max(0, length+i);
		for(; i<length; i++){
			if(i in self && self[i]===sought){
				return i;
			}
		}
		return -1;
	});

	//lastIndexOf
	(arrPro.lastIndexOf && [0,1].lastIndexOf(0, -3)==-1) || (arrPro.lastIndexOf = function(sought /*, fromIndex */){
		var self = splitString&&toString.call(this)==objType['str']? this.split('') : o,
			length = self.length >>> 0;

		if(!length){ return -1; }

		var i = length - 1;
		if(arguments.length>1){
			i = Math.min(i, toInteger(arguments[1]));
		}

		i = i>=0? i : length - Math.abs(i);
		for(; i>=0; i--){
			if(i in self && self[i]===sought){
				return i;
			}
		}
		return -1;
	});


	/*-----------------------------------*
	 * String
	 *-----------------------------------*/
	
	// [bugfix, chrome]
	// "0".split(undefined, 0) -> []
	if('0'.split(void 0, 0).length){
		var string_split = strPro.split;
		strPro.split = function(separator, limit){
			if(separator===void 0 && limit===0) return [];
			return string_split.apply(this, arguments);
		};
	}

	// [bugfix, IE lt 9] IE < 9 substr() with negative value not working in IE
	if(''.substr && '0b'.substr(-1)!=='b'){
		var string_substr = strPro.substr;
		strPro.substr = function(start, length){
			return string_substr.call(
				this,
				start<0? ( (start=this.length+start)<0? 0:start ) : start,
				length
			);
		};
	}

	var ws = '\x09\x0A\x0B\x0C\x0D\x20\xA0\u1680\u180E\u2000\u2001\u2002\u2003' +
			 '\u2004\u2005\u2006\u2007\u2008\u2009\u200A\u202F\u205F\u3000\u2028' +
			 '\u2029\uFEFF';
	strPro.trim || (strPro.trim = function(){
		ws = '[' + ws + ']';
		var trimBeginRegexp = new RegExp('^'+ws+ws+'*'),
			trimEndRegexp = new RegExp(ws+ws+'*$');
		strPro.trim = function(){
			if(this===undefined || this===null){
				throw new TypeError('不能将'+o+'转变为对象');
			}
			return String(this)
				.replace(trimBeginRegexp, '')
				.replace(trimEndRegexp, '');
		};
	});


	/*-----------------------------------*
	 * JSON
	 *-----------------------------------*/

	//@see http://www.JSON.org/json2.js
	if(!root.JSON){
		root.JSON = {};
	
		(function(){
			function f(n){ return n<10? '0'+n : n; }
			if(typeof Date.prototype.toJSON!=='function'){
				Date.prototype.toJSON = function(key){
					return !isFinite(this.valueOf())? null :
							this.getUTCFullYear() + '-' + f(this.getUTCMonth()+1) + '-' + f(this.getUTCDate()) + 'T' +
							f(this.getUTCHours()) + ':' + f(this.getUTCMinutes()) + ':' + f(this.getUTCSeconds()) + 'Z';
				};
				strPro.toJSON = Number.prototype.toJSON = Boolean.prototype.toJSON =
					function(key){
						return this.valueOf();
					};
			}

			var cx = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
				escapable = /[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
				gap, indent, rep,
				meta = {// table of character substitutions
					'\b': '\\b',
					'\t': '\\t',
					'\n': '\\n',
					'\f': '\\f',
					'\r': '\\r',
					'"' : '\\"',
					'\\': '\\\\'
				};

			function quote(string){
				escapable.lastIndex = 0;
				return !escapable.test(string)? '"' + string + '"' :
						'"' + string.replace(escapable, 
							function(a){
								var c = meta[a];
								return typeof c==='string'? c : '\\u'+('0000'+a.charCodeAt(0).toString(16)).slice(-4);
							}) + '"';
			}

			function str(key, holder){
				var i, k, v, length,
					mind = gap,
					partial,
					value = holder[key];

				if(value && typeof value==='object' && typeof value.toJSON==='function'){
					value = value.toJSON(key);
				}

				if(typeof rep==='function'){
					value = rep.call(holder, key, value);
				}

				switch(typeof value){
					case 'string': return quote(value);
					case 'number': return isFinite(value)? String(value) : 'null';
					case 'boolean':
					case 'null': return String(value);
					case 'object':
						if(!value) return 'null';
						gap += indent;
						partial = [];
						if(toString.call(value)===objType['arr']){
							length = value.length;
							for(i=0; i<length; i+=1){
								partial[i] = str(i, value) || 'null';
							}
							v = partial.length===0? '[]' :
								gap? '[\n' + gap + partial.join(',\n' + gap) + '\n' + mind + ']' : '[' + partial.join(',') + ']';
							gap = mind;
							return v;
						}
						if(rep && typeof rep==='object'){
							length = rep.length;
							for(i=0; i<length; i+=1){
								k = rep[i];
								if(typeof k==='string'){
									v = str(k, value);
									if(v) partial.push(quote(k)+(gap? ': ':':')+v);
								}
							}
						} else {
							for(k in value){
								if(Object.hasOwnProperty.call(value, k)){
									v = str(k, value);
									if(v) partial.push(quote(k)+(gap? ': ':':')+v);
								}
							}
						}
						v = partial.length===0? '{}' :
							gap? '{\n' + gap + partial.join(',\n' + gap) + '\n' + mind + '}' : '{' + partial.join(',') + '}';
						gap = mind;
						return v;
				}
			}

			//stringify
			if(typeof JSON.stringify!=='function'){
				JSON.stringify = function(value, replacer, space){
					var i;
					gap = '';
					indent = '';

					if(typeof space==='number'){
						for(i=0; i<space; i+=1){
							indent += ' ';
						}
					} else if(typeof space==='string'){
						indent = space;
					}

					rep = replacer;
					if(replacer && typeof replacer!=='function' && (typeof replacer!=='object' || typeof replacer.length!=='number')){
						throw new Error('JSON.stringify');
					}

					return str('', {'':value});
				};
			}

			//parse
			if(typeof JSON.parse!=='function'){
				JSON.parse = function(text, reviver){
					var j;
					function walk(holder, key){
						var k, v, value = holder[key];
						if(value && typeof value==='object'){
							for(k in value){
								if(Object.hasOwnProperty.call(value, k)){
									v = walk(value, k);
									if(v!==undefined){
										value[k] = v;
									} else {
										delete value[k];
									}
								}
							}
						}
						return reviver.call(holder, key, value);
					}

					cx.lastIndex = 0;

					if(cx.test(text)){
						text = text.replace(cx, function(a){
							return '\\u' + ('0000'+a.charCodeAt(0).toString(16)).slice(-4);
						});
					}

					if(/^[\],:{}\s]*$/.
						test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, '@').
						replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').
						replace(/(?:^|:|,)(?:\s*\[)+/g, ''))
					){
						j = eval('('+text+')');
						return typeof reviver==='function'? walk({'': j}, '') : j;
					}

					throw new SyntaxError('JSON.parse');
				};
			}
		}());
	}


	/*---------------------------------------*
	 * Date
	 *---------------------------------------*/

	Date.now || (Date.now = function () {
		return +new Date;
	});


	/*-------------------------------------------*
	 * util
	 *-------------------------------------------*/
	
	Shim.mix = function(r, s, wl, ov){
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

	// kinds of browsers
	var __UA = navigator.userAgent,
		docElem = doc.documentElement,
		supportTouch = ('createTouch' in doc) || ('ontouchstart' in root);

	Shim.mix(Shim, {
		matchesSelector: docElem.matchesSelector || docElem.mozMatchesSelector ||
			docElem.webkitMatchesSelector || docElem.oMatchesSelector || docElem.msMatchesSelector,
		
		/* browser */
		safari: /webkit/i.test(__UA),
		opera: /opera/i.test(__UA),
		msie: /msie/i.test(__UA) && !/opera/i.test(__UA),
		mozilla: /mozilla/i.test(__UA) && !/(compatible|webkit)/i.test(__UA)
	});

	Shim.mix(Shim, {

		//_sizzle: function(){},
		//_matches: function(){},

		//_ready: function(){},

		_load: function(MG, LG, option, pNode, node, iCat){
			if(SHIM.msie){
				var timer = setInterval(function(){
					try{
						document.documentElement.doScroll('left');//在IE下用能否执行doScroll判断dom是否加载完毕
					}catch(e){
						return;
					}
					
					clearInterval(timer);
					if(node.readyState){
						node.onreadystatechange = function(){
							if(node.readyState == "loaded" || node.readyState == "complete") {
								node.onreadystatechange = null;
								if(option.callback && iCat.isFunction(option.callback))
									option.callback(option.context || iCat);
								
								if(option.modName){
									MG[option.modName] = true;
								}
								LG[option.file] = true;
							}
						};
					}
					pNode.appendChild(node);
				},1);
			} else {
				node.onload = function(){
					if(option.callback && iCat.isFunction(option.callback))
						option.callback(option.context || iCat);
					
					if(option.modName){
						MG[option.modName] = true;
					}
					LG[option.file] = true;
				};
				pNode.appendChild(node);
			}
		}
	});

	Shim.event = {
		_bindEvent: function(el, type, handler){//单个绑定
			el.events = el.events || {};
			el.types = el.types || [];

			el.events[type] = handler;
			if(el.types.indexOf(type)==-1)// 请用click.submit来绑定同一el的同类型事件
				el.types.push(type);

			type = type.replace(/\..*/g, '');
			if(el.addEventListener){
				el.addEventListener(type, el.events[type], false);
			} else if(el.attachEvent) {
				el.attachEvent('on'+type, Event[eventId]);
			} else {
				el['on'+type] = handler;
			}
		},

		_unbindEvent: function(el, type){//单个解绑
			if(!el.events || el.types.indexOf(type)==-1) return;
			
			var handler = el.events[type];
				type = type.replace(/\..*/g, '');
			if(el.removeEventListener){
				el.removeEventListener(type, handler, false);
			} else if(el.detachEvent){
				el.detachEvent('on'+type, handler);
			} else {
				el['on'+type] = null;
			}

			if(iCat.isEmptyObject(el.events) || !el.types.length){
				el.events = null;
				el.types = null;
			}
		},

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

		trigger: function(el, type, bubbles, cancelable){
			if(doc.createEventObject){
				var evt = doc.createEventObject();
				el.fireEvent('on'+type, evt);
			} else {
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

			if(Shim.msie){
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
			} else if(Shim.webkit && doc.readyState){
				(function(){
					if(doc.readyState!=='loading'){
						_do();
					} else {
						setTimeout(arguments.callee, 10);
					}
				})();
			} else if(doc.addEventListener){
				doc.addEventListener('DOMContentLoaded', _do, false);
			} else {
				window.onload = _do;
			}

			return function(fn){
				if(iCat.isFunction(fn)){
					_fn[_fn.length] = fn;
				}
				return fn;
			};
		}()
	};

	Shim.mix(Shim, {});

	Shim.util = {

	};
}).call(this, document);