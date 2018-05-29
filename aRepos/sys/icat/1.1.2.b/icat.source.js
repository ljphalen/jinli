/*!
 * Copyright 2011~2012, ICAT JavaScript Library v1.1.2.b
 * MIT Licensed
 * @Author valleykiddy@gmail.com
 * @Time 2012-09-23 10:00:00
 */

(function(){
	// Create the root object, 'window' in the browser, or 'global' on the server.
	var root = this;
	
	var previcat = root.ICAT = root._;
	
	// Create the object that gets returned to break out of a loop iteration.
	var breaker = {};
	
	var _ua = navigator.userAgent, isDebug = /debug/i.test(root.location.href);
	
	var ArrayProto = Array.prototype, ObjProto = Object.prototype, FuncProto = Function.prototype;
	
	var slice            = ArrayProto.slice,
		unshift          = ArrayProto.unshift,
		toString         = ObjProto.toString,
		hasOwnProperty   = ObjProto.hasOwnProperty;
	
	var
		nativeForEach      = ArrayProto.forEach,
		nativeMap          = ArrayProto.map,
		nativeReduce       = ArrayProto.reduce,
		nativeReduceRight  = ArrayProto.reduceRight,
		nativeFilter       = ArrayProto.filter,
		nativeEvery        = ArrayProto.every,
		nativeSome         = ArrayProto.some,
		nativeIndexOf      = ArrayProto.indexOf,
		nativeLastIndexOf  = ArrayProto.lastIndexOf,
		nativeIsArray      = Array.isArray,
		nativeKeys         = Object.keys,
		nativeBind         = FuncProto.bind;
	
	var iCat = function(obj){return new wrapper(obj);};
	
	// Export the ICAT object for **Node.js**
	if (typeof exports !== 'undefined') {
		if (typeof module !== 'undefined' && module.exports) {
		  exports = module.exports = iCat;
		}
		exports._ = exports.ICAT = iCat;
	} else {
		root['_'] = root['ICAT'] = iCat;
	}
	
	// Current version.
	iCat.VERSION = '1.1.2.b';
	
	// Copies all the properties of s to r.
	// w(hite)l(ist):白名单, ov(erwrite):覆盖
	iCat.mix = function(r, s, wl, ov){
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
	iCat.mix(iCat, {
		
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
		
		// Handles objects with the built-in 'foreach', arrays, and raw objects.
		foreach: function(o, cb, args){
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
		},
		
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
	
	// Collection Functions
	// --------------------
	var each = iCat.each = iCat.forEach = function(obj, iterator, context) {
		if (obj == null) return;
		if (nativeForEach && obj.forEach === nativeForEach) {
			obj.forEach(iterator, context);
		} else if (obj.length === +obj.length) {
			for (var i = 0, l = obj.length; i < l; i++) {
				if (i in obj && iterator.call(context, obj[i], i, obj) === breaker) return;
			}
		} else {
			for (var key in obj) {
				if (iCat.has(obj, key)) {
					if (iterator.call(context, obj[key], key, obj) === breaker) return;
				}
			}
		}
	};
	
	iCat.map = iCat.collect = function(obj, iterator, context) {
		var results = [];
		if (obj == null) return results;
		if (nativeMap && obj.map === nativeMap) return obj.map(iterator, context);
		each(obj, function(value, index, list) {
			results[results.length] = iterator.call(context, value, index, list);
		});
		if (obj.length === +obj.length) results.length = obj.length;
		return results;
	};
	
	  iCat.reduce = iCat.foldl = iCat.inject = function(obj, iterator, memo, context) {
		var initial = arguments.length > 2;
		if (obj == null) obj = [];
		if (nativeReduce && obj.reduce === nativeReduce) {
			if (context) iterator = iCat.bind(iterator, context);
			return initial ? obj.reduce(iterator, memo) : obj.reduce(iterator);
		}
		each(obj, function(value, index, list) {
		  if (!initial) {
			memo = value;
			initial = true;
		  } else {
			memo = iterator.call(context, memo, value, index, list);
		  }
		});
		if (!initial) throw new TypeError('Reduce of empty array with no initial value');
		return memo;
	};

  iCat.reduceRight = iCat.foldr = function(obj, iterator, memo, context) {
    var initial = arguments.length > 2;
    if (obj == null) obj = [];
    if (nativeReduceRight && obj.reduceRight === nativeReduceRight) {
      if (context) iterator = iCat.bind(iterator, context);
      return initial ? obj.reduceRight(iterator, memo) : obj.reduceRight(iterator);
    }
    var reversed = iCat.toArray(obj).reverse();
    if (context && !initial) iterator = iCat.bind(iterator, context);
    return initial ? iCat.reduce(reversed, iterator, memo, context) : iCat.reduce(reversed, iterator);
  };
  
  iCat.find = iCat.detect = function(obj, iterator, context) {
    var result;
    any(obj, function(value, index, list) {
      if (iterator.call(context, value, index, list)) {
        result = value;
        return true;
      }
    });
    return result;
  };
  
  iCat.filter = iCat.select = function(obj, iterator, context) {
    var results = [];
    if (obj == null) return results;
    if (nativeFilter && obj.filter === nativeFilter) return obj.filter(iterator, context);
    each(obj, function(value, index, list) {
      if (iterator.call(context, value, index, list)) results[results.length] = value;
    });
    return results;
  };
  
  iCat.reject = function(obj, iterator, context) {
    var results = [];
    if (obj == null) return results;
    each(obj, function(value, index, list) {
      if (!iterator.call(context, value, index, list)) results[results.length] = value;
    });
    return results;
  };
  
  iCat.every = iCat.all = function(obj, iterator, context) {
    var result = true;
    if (obj == null) return result;
    if (nativeEvery && obj.every === nativeEvery) return obj.every(iterator, context);
    each(obj, function(value, index, list) {
      if (!(result = result && iterator.call(context, value, index, list))) return breaker;
    });
    return !!result;
  };
  
  var any = iCat.some = iCat.any = function(obj, iterator, context) {
    iterator || (iterator = iCat.identity);
    var result = false;
    if (obj == null) return result;
    if (nativeSome && obj.some === nativeSome) return obj.some(iterator, context);
    each(obj, function(value, index, list) {
      if (result || (result = iterator.call(context, value, index, list))) return breaker;
    });
    return !!result;
  };
  
  iCat.include = iCat.contains = function(obj, target) {
    var found = false;
    if (obj == null) return found;
    if (nativeIndexOf && obj.indexOf === nativeIndexOf) return obj.indexOf(target) != -1;
    found = any(obj, function(value) {
      return value === target;
    });
    return found;
  };
  
  iCat.invoke = function(obj, method) {
    var args = slice.call(arguments, 2);
    return iCat.map(obj, function(value) {
      return (iCat.isFunction(method) ? method || value : value[method]).apply(value, args);
    });
  };
  
  iCat.pluck = function(obj, key) {
    return iCat.map(obj, function(value){ return value[key]; });
  };
  
  iCat.max = function(obj, iterator, context) {
    if (!iterator && iCat.isArray(obj) && obj[0] === +obj[0]) return Math.max.apply(Math, obj);
    if (!iterator && iCat.isEmpty(obj)) return -Infinity;
    var result = {computed : -Infinity};
    each(obj, function(value, index, list) {
      var computed = iterator ? iterator.call(context, value, index, list) : value;
      computed >= result.computed && (result = {value : value, computed : computed});
    });
    return result.value;
  };
  
  iCat.min = function(obj, iterator, context) {
    if (!iterator && iCat.isArray(obj) && obj[0] === +obj[0]) return Math.min.apply(Math, obj);
    if (!iterator && iCat.isEmpty(obj)) return Infinity;
    var result = {computed : Infinity};
    each(obj, function(value, index, list) {
      var computed = iterator ? iterator.call(context, value, index, list) : value;
      computed < result.computed && (result = {value : value, computed : computed});
    });
    return result.value;
  };
  
  iCat.shuffle = function(obj) {
    var shuffled = [], rand;
    each(obj, function(value, index, list) {
      rand = Math.floor(Math.random() * (index + 1));
      shuffled[index] = shuffled[rand];
      shuffled[rand] = value;
    });
    return shuffled;
  };
  
  iCat.sortBy = function(obj, val, context) {
    var iterator = iCat.isFunction(val) ? val : function(obj) { return obj[val]; };
    return iCat.pluck(iCat.map(obj, function(value, index, list) {
      return {
        value : value,
        criteria : iterator.call(context, value, index, list)
      };
    }).sort(function(left, right) {
      var a = left.criteria, b = right.criteria;
      if (a === void 0) return 1;
      if (b === void 0) return -1;
      return a < b ? -1 : a > b ? 1 : 0;
    }), 'value');
  };
  
  iCat.groupBy = function(obj, val) {
    var result = {};
    var iterator = iCat.isFunction(val) ? val : function(obj) { return obj[val]; };
    each(obj, function(value, index) {
      var key = iterator(value, index);
      (result[key] || (result[key] = [])).push(value);
    });
    return result;
  };
  
  iCat.sortedIndex = function(array, obj, iterator) {
    iterator || (iterator = iCat.identity);
    var low = 0, high = array.length;
    while (low < high) {
      var mid = (low + high) >> 1;
      iterator(array[mid]) < iterator(obj) ? low = mid + 1 : high = mid;
    }
    return low;
  };
  
  iCat.toArray = function(obj) {
    if (!obj)                                     return [];
    if (iCat.isArray(obj))                           return slice.call(obj);
    if (iCat.isArguments(obj))                       return slice.call(obj);
    if (obj.toArray && iCat.isFunction(obj.toArray)) return obj.toArray();
    return iCat.values(obj);
  };
  
  iCat.size = function(obj) {
    return iCat.isArray(obj) ? obj.length : iCat.keys(obj).length;
  };

	// Array Functions
	// --------------------
  iCat.first = iCat.head = iCat.take = function(array, n, guard) {
    return (n != null) && !guard ? slice.call(array, 0, n) : array[0];
  };
  
  iCat.initial = function(array, n, guard) {
    return slice.call(array, 0, array.length - ((n == null) || guard ? 1 : n));
  };
  
  iCat.last = function(array, n, guard) {
    if ((n != null) && !guard) {
      return slice.call(array, Math.max(array.length - n, 0));
    } else {
      return array[array.length - 1];
    }
  };
  
  iCat.rest = iCat.tail = function(array, index, guard) {
    return slice.call(array, (index == null) || guard ? 1 : index);
  };
  
  iCat.compact = function(array) {
    return iCat.filter(array, function(value){ return !!value; });
  };
  
  iCat.flatten = function(array, shallow) {
    return iCat.reduce(array, function(memo, value) {
      if (iCat.isArray(value)) return memo.concat(shallow ? value : iCat.flatten(value));
      memo[memo.length] = value;
      return memo;
    }, []);
  };
  
  iCat.without = function(array) {
    return iCat.difference(array, slice.call(arguments, 1));
  };
  
  iCat.uniq = iCat.unique = function(array, isSorted, iterator) {
    var initial = iterator ? iCat.map(array, iterator) : array;
    var results = [];
    // The `isSorted` flag is irrelevant if the array only contains two elements.
    if (array.length < 3) isSorted = true;
    iCat.reduce(initial, function (memo, value, index) {
      if (isSorted ? iCat.last(memo) !== value || !memo.length : !iCat.include(memo, value)) {
        memo.push(value);
        results.push(array[index]);
      }
      return memo;
    }, []);
    return results;
  };
  
  iCat.union = function() {
    return iCat.uniq(iCat.flatten(arguments, true));
  };
  
  iCat.intersection = iCat.intersect = function(array) {
    var rest = slice.call(arguments, 1);
    return iCat.filter(iCat.uniq(array), function(item) {
      return iCat.every(rest, function(other) {
        return iCat.indexOf(other, item) >= 0;
      });
    });
  };
  
  iCat.difference = function(array) {
    var rest = iCat.flatten(slice.call(arguments, 1), true);
    return iCat.filter(array, function(value){ return !iCat.include(rest, value); });
  };
  
  iCat.zip = function() {
    var args = slice.call(arguments);
    var length = iCat.max(iCat.pluck(args, 'length'));
    var results = new Array(length);
    for (var i = 0; i < length; i++) results[i] = iCat.pluck(args, "" + i);
    return results;
  };
  
  iCat.indexOf = function(array, item, isSorted) {
    if (array == null) return -1;
    var i, l;
    if (isSorted) {
      i = iCat.sortedIndex(array, item);
      return array[i] === item ? i : -1;
    }
    if (nativeIndexOf && array.indexOf === nativeIndexOf) return array.indexOf(item);
    for (i = 0, l = array.length; i < l; i++) if (i in array && array[i] === item) return i;
    return -1;
  };
  
  iCat.lastIndexOf = function(array, item) {
    if (array == null) return -1;
    if (nativeLastIndexOf && array.lastIndexOf === nativeLastIndexOf) return array.lastIndexOf(item);
    var i = array.length;
    while (i--) if (i in array && array[i] === item) return i;
    return -1;
  };
  
  iCat.range = function(start, stop, step) {
    if (arguments.length <= 1) {
      stop = start || 0;
      start = 0;
    }
    step = arguments[2] || 1;

    var len = Math.max(Math.ceil((stop - start) / step), 0);
    var idx = 0;
    var range = new Array(len);

    while(idx < len) {
      range[idx++] = start;
      start += step;
    }

    return range;
  };
	
	// Function (ahem) Functions
	// --------------------
	
  // Reusable constructor function for prototype setting.
  var ctor = function(){};
  
  iCat.bind = function bind(func, context) {
    var bound, args;
    if (func.bind === nativeBind && nativeBind) return nativeBind.apply(func, slice.call(arguments, 1));
    if (!iCat.isFunction(func)) throw new TypeError;
    args = slice.call(arguments, 2);
    return bound = function() {
      if (!(this instanceof bound)) return func.apply(context, args.concat(slice.call(arguments)));
      ctor.prototype = func.prototype;
      var self = new ctor;
      var result = func.apply(self, args.concat(slice.call(arguments)));
      if (Object(result) === result) return result;
      return self;
    };
  };
  
  iCat.bindAll = function(obj) {
    var funcs = slice.call(arguments, 1);
    if (funcs.length == 0) funcs = iCat.functions(obj);
    each(funcs, function(f) { obj[f] = iCat.bind(obj[f], obj); });
    return obj;
  };
  
  iCat.memoize = function(func, hasher) {
    var memo = {};
    hasher || (hasher = iCat.identity);
    return function() {
      var key = hasher.apply(this, arguments);
      return iCat.has(memo, key) ? memo[key] : (memo[key] = func.apply(this, arguments));
    };
  };
  
  iCat.delay = function(func, wait) {
    var args = slice.call(arguments, 2);
    return setTimeout(function(){ return func.apply(null, args); }, wait);
  };
  
  iCat.defer = function(func) {
    return iCat.delay.apply(iCat, [func, 1].concat(slice.call(arguments, 1)));
  };
  
  iCat.throttle = function(func, wait) {
    var context, args, timeout, throttling, more, result;
    var whenDone = iCat.debounce(function(){ more = throttling = false; }, wait);
    return function() {
      context = this; args = arguments;
      var later = function() {
        timeout = null;
        if (more) func.apply(context, args);
        whenDone();
      };
      if (!timeout) timeout = setTimeout(later, wait);
      if (throttling) {
        more = true;
      } else {
        result = func.apply(context, args);
      }
      whenDone();
      throttling = true;
      return result;
    };
  };
  
  iCat.debounce = function(func, wait, immediate) {
    var timeout;
    return function() {
      var context = this, args = arguments;
      var later = function() {
        timeout = null;
        if (!immediate) func.apply(context, args);
      };
      if (immediate && !timeout) func.apply(context, args);
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  };
  
  iCat.once = function(func) {
    var ran = false, memo;
    return function() {
      if (ran) return memo;
      ran = true;
      return memo = func.apply(this, arguments);
    };
  };
  
  iCat.wrap = function(func, wrapper) {
    return function() {
      var args = [func].concat(slice.call(arguments, 0));
      return wrapper.apply(this, args);
    };
  };
  
  iCat.compose = function() {
    var funcs = arguments;
    return function() {
      var args = arguments;
      for (var i = funcs.length - 1; i >= 0; i--) {
        args = [funcs[i].apply(this, args)];
      }
      return args[0];
    };
  };
  
  iCat.after = function(times, func) {
    if (times <= 0) return func();
    return function() {
      if (--times < 1) { return func.apply(this, arguments); }
    };
  };
  
	// Object Functions
	// --------------------
  iCat.keys = nativeKeys || function(obj) {
    if (obj !== Object(obj)) throw new TypeError('Invalid object');
    var keys = [];
    for (var key in obj) if (iCat.has(obj, key)) keys[keys.length] = key;
    return keys;
  };
  
  iCat.values = function(obj) {
    return iCat.map(obj, iCat.identity);
  };
  
  iCat.functions = iCat.methods = function(obj) {
    var names = [];
    for (var key in obj) {
      if (iCat.isFunction(obj[key])) names.push(key);
    }
    return names.sort();
  };
  
  iCat.extend = function(obj) {
    each(slice.call(arguments, 1), function(source) {
      for (var prop in source) {
        obj[prop] = source[prop];
      }
    });
    return obj;
  };
  
  iCat.pick = function(obj) {
    var result = {};
    each(iCat.flatten(slice.call(arguments, 1)), function(key) {
      if (key in obj) result[key] = obj[key];
    });
    return result;
  };
  
  iCat.defaults = function(obj) {
    each(slice.call(arguments, 1), function(source) {
      for (var prop in source) {
        if (obj[prop] == null) obj[prop] = source[prop];
      }
    });
    return obj;
  };
  
  iCat.clone = function(obj) {
    if (!iCat.isObject(obj)) return obj;
    return iCat.isArray(obj) ? obj.slice() : iCat.extend({}, obj);
  };
  
  iCat.tap = function(obj, interceptor) {
    interceptor(obj);
    return obj;
  };
  
  // Internal recursive comparison function.
  function eq(a, b, stack) {
    // Identical objects are equal. `0 === -0`, but they aren't identical.
    // See the Harmony `egal` proposal: http://wiki.ecmascript.org/doku.php?id=harmony:egal.
    if (a === b) return a !== 0 || 1 / a == 1 / b;
    // A strict comparison is necessary because `null == undefined`.
    if (a == null || b == null) return a === b;
    // Unwrap any wrapped objects.
    if (a._chain) a = a._wrapped;
    if (b._chain) b = b._wrapped;
    // Invoke a custom `isEqual` method if one is provided.
    if (a.isEqual && iCat.isFunction(a.isEqual)) return a.isEqual(b);
    if (b.isEqual && iCat.isFunction(b.isEqual)) return b.isEqual(a);
    // Compare `[[Class]]` names.
    var className = toString.call(a);
    if (className != toString.call(b)) return false;
    switch (className) {
      // Strings, numbers, dates, and booleans are compared by value.
      case '[object String]':
        // Primitives and their corresponding object wrappers are equivalent; thus, `"5"` is
        // equivalent to `new String("5")`.
        return a == String(b);
      case '[object Number]':
        // `NaN`s are equivalent, but non-reflexive. An `egal` comparison is performed for
        // other numeric values.
        return a != +a ? b != +b : (a == 0 ? 1 / a == 1 / b : a == +b);
      case '[object Date]':
      case '[object Boolean]':
        // Coerce dates and booleans to numeric primitive values. Dates are compared by their
        // millisecond representations. Note that invalid dates with millisecond representations
        // of `NaN` are not equivalent.
        return +a == +b;
      // RegExps are compared by their source patterns and flags.
      case '[object RegExp]':
        return a.source == b.source &&
               a.global == b.global &&
               a.multiline == b.multiline &&
               a.ignoreCase == b.ignoreCase;
    }
    if (typeof a != 'object' || typeof b != 'object') return false;
    // Assume equality for cyclic structures. The algorithm for detecting cyclic
    // structures is adapted from ES 5.1 section 15.12.3, abstract operation `JO`.
    var length = stack.length;
    while (length--) {
      // Linear search. Performance is inversely proportional to the number of
      // unique nested structures.
      if (stack[length] == a) return true;
    }
    // Add the first object to the stack of traversed objects.
    stack.push(a);
    var size = 0, result = true;
    // Recursively compare objects and arrays.
    if (className == '[object Array]') {
      // Compare array lengths to determine if a deep comparison is necessary.
      size = a.length;
      result = size == b.length;
      if (result) {
        // Deep compare the contents, ignoring non-numeric properties.
        while (size--) {
          // Ensure commutative equality for sparse arrays.
          if (!(result = size in a == size in b && eq(a[size], b[size], stack))) break;
        }
      }
    } else {
      // Objects with different constructors are not equivalent.
      if ('constructor' in a != 'constructor' in b || a.constructor != b.constructor) return false;
      // Deep compare objects.
      for (var key in a) {
        if (iCat.has(a, key)) {
          // Count the expected number of properties.
          size++;
          // Deep compare each member.
          if (!(result = iCat.has(b, key) && eq(a[key], b[key], stack))) break;
        }
      }
      // Ensure that both objects contain the same number of properties.
      if (result) {
        for (key in b) {
          if (iCat.has(b, key) && !(size--)) break;
        }
        result = !size;
      }
    }
    // Remove the first object from the stack of traversed objects.
    stack.pop();
    return result;
  }
  
  iCat.isEqual = function(a, b) {
    return eq(a, b, []);
  };
  
  iCat.isEmpty = function(obj) {
    if (obj == null) return true;
    if (iCat.isArray(obj) || iCat.isString(obj)) return obj.length === 0;
    for (var key in obj) if (iCat.has(obj, key)) return false;
    return true;
  };
  
  iCat.isElement = function(obj) {
    return !!(obj && obj.nodeType == 1);
  };
  
  iCat.isObject = function(obj) {
    return obj === Object(obj);
  };
  
  iCat.isArguments = function(obj) {
    return toString.call(obj) == '[object Arguments]';
  };
  if (!iCat.isArguments(arguments)) {
    iCat.isArguments = function(obj) {
      return !!(obj && iCat.has(obj, 'callee'));
    };
  }
  
  iCat.isNumber = function(obj) {
    return toString.call(obj) == '[object Number]';
  };
  
  iCat.isFinite = function(obj) {
    return iCat.isNumber(obj) && isFinite(obj);
  };
  
  iCat.isNaN = function(obj) {
    // `NaN` is the only value for which `===` is not reflexive.
    return obj !== obj;
  };
  
  iCat.isBoolean = function(obj) {
    return obj === true || obj === false || toString.call(obj) == '[object Boolean]';
  };
  
  iCat.isDate = function(obj) {
    return toString.call(obj) == '[object Date]';
  };
  
  iCat.isRegExp = function(obj) {
    return toString.call(obj) == '[object RegExp]';
  };
  
  iCat.isNull = function(obj) {
    return obj === null;
  };
  
  iCat.isUndefined = function(obj) {
    return obj === void 0;
  };
  
  iCat.has = function(obj, key) {
    return hasOwnProperty.call(obj, key);
  };
	
	// Utility Functions
	// --------------------
  iCat.noConflict = function() {
    root._ = previcat;
    return this;
  };
  
  iCat.identity = function(value) {
    return value;
  };
  
  iCat.times = function (n, iterator, context) {
    for (var i = 0; i < n; i++) iterator.call(context, i);
  };
  
  iCat.escape = function(string) {
    return (''+string).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#x27;').replace(/\//g,'&#x2F;');
  };
  
  iCat.result = function(object, property) {
    if (object == null) return null;
    var value = object[property];
    return iCat.isFunction(value) ? value.call(object) : value;
  };
  
  iCat.mixin = function(obj) {
    each(iCat.functions(obj), function(name){
      addToWrapper(name, iCat[name] = obj[name]);
    });
  };
  
  var idCounter = 0;
  iCat.uniqueId = function(prefix) {
    var id = idCounter++;
    return prefix ? prefix + id : id;
  };
  
  iCat.templateSettings = {
    evaluate    : /<%([\s\S]+?)%>/g,
    interpolate : /<%=([\s\S]+?)%>/g,
    escape      : /<%-([\s\S]+?)%>/g
  };
  
  var noMatch = /.^/;
  
  var escapes = {
    '\\': '\\',
    "'": "'",
    'r': '\r',
    'n': '\n',
    't': '\t',
    'u2028': '\u2028',
    'u2029': '\u2029'
  };

  for (var p in escapes) escapes[escapes[p]] = p;
  var escaper = /\\|'|\r|\n|\t|\u2028|\u2029/g;
  var unescaper = /\\(\\|'|r|n|t|u2028|u2029)/g;
  
  var unescape = function(code) {
    return code.replace(unescaper, function(match, escape) {
      return escapes[escape];
    });
  };
  
  iCat.template = function(text, data, settings) {
    settings = iCat.defaults(settings || {}, iCat.templateSettings);

    // Compile the template source, taking care to escape characters that
    // cannot be included in a string literal and then unescape them in code
    // blocks.
    var source = "__p+='" + text
      .replace(escaper, function(match) {
        return '\\' + escapes[match];
      })
      .replace(settings.escape || noMatch, function(match, code) {
        return "'+\n_.escape(" + unescape(code) + ")+\n'";
      })
      .replace(settings.interpolate || noMatch, function(match, code) {
        return "'+\n(" + unescape(code) + ")+\n'";
      })
      .replace(settings.evaluate || noMatch, function(match, code) {
        return "';\n" + unescape(code) + "\n;__p+='";
      }) + "';\n";

    // If a variable is not specified, place data values in local scope.
    if (!settings.variable) source = 'with(obj||{}){\n' + source + '}\n';

    source = "var __p='';" +
      "var print=function(){__p+=Array.prototype.join.call(arguments, '')};\n" +
      source + "return __p;\n";

    var render = new Function(settings.variable || 'obj', 'iCat', source);
    if (data) return render(data, iCat);
    var template = function(data) {
      return render.call(this, data, iCat);
    };

    // Provide the compiled function source as a convenience for build time
    // precompilation.
    template.source = 'function(' + (settings.variable || 'obj') + '){\n' +
      source + '}';

    return template;
  };
  
  iCat.chain = function(obj) {
    return iCat(obj).chain();
  };
	
	// The OOP Wrapper
	// --------------------
	var wrapper = function(obj){this._wrapped = obj;};
	
	iCat.prototype = wrapper.prototype;
	
	var result = function(obj, chain){
		return chain ? iCat(obj).chain() : obj;
	};
	
	var addToWrapper = function(name, func) {
		wrapper.prototype[name] = function() {
			var args = slice.call(arguments);
			unshift.call(args, this._wrapped);
			return result(func.apply(iCat, args), this._chain);
		};
	};
	
	iCat.mixin(iCat);
	
	each(['pop', 'push', 'reverse', 'shift', 'sort', 'splice', 'unshift'], function(name) {
		var method = ArrayProto[name];
		wrapper.prototype[name] = function() {
			var wrapped = this._wrapped;
			method.apply(wrapped, arguments);
			var length = wrapped.length;
			if ((name == 'shift' || name == 'splice') && length === 0) delete wrapped[0];
			return result(wrapped, this._chain);
		};
	});
	
	each(['concat', 'join', 'slice'], function(name) {
		var method = ArrayProto[name];
		wrapper.prototype[name] = function() {
			return result(method.apply(this._wrapped, arguments), this._chain);
		};
	});
	
	wrapper.prototype.chain = function() {
		this._chain = true;
		return this;
	};
	
	wrapper.prototype.value = function() {
		return this._wrapped;
	};
	
	// 初始化
	iCat.__init();
}).call(this);

/** json2 */
if (!this.JSON){this.JSON = {};}
(function (){
    function f(n) {
        // Format integers to have at least two digits.
        return n < 10 ? '0' + n : n;
    }

    if (typeof Date.prototype.toJSON !== 'function') {

        Date.prototype.toJSON = function (key) {

            return isFinite(this.valueOf()) ?
                   this.getUTCFullYear()   + '-' +
                 f(this.getUTCMonth() + 1) + '-' +
                 f(this.getUTCDate())      + 'T' +
                 f(this.getUTCHours())     + ':' +
                 f(this.getUTCMinutes())   + ':' +
                 f(this.getUTCSeconds())   + 'Z' : null;
        };

        String.prototype.toJSON =
        Number.prototype.toJSON =
        Boolean.prototype.toJSON = function (key) {
            return this.valueOf();
        };
    }

    var cx = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
        escapable = /[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
        gap,
        indent,
        meta = {    // table of character substitutions
            '\b': '\\b',
            '\t': '\\t',
            '\n': '\\n',
            '\f': '\\f',
            '\r': '\\r',
            '"' : '\\"',
            '\\': '\\\\'
        },
        rep;


    function quote(string) {
        escapable.lastIndex = 0;
        return escapable.test(string) ?
            '"' + string.replace(escapable, function (a) {
                var c = meta[a];
                return typeof c === 'string' ? c :
                    '\\u' + ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
            }) + '"' :
            '"' + string + '"';
    }


    function str(key, holder) {
        var i,          // The loop counter.
            k,          // The member key.
            v,          // The member value.
            length,
            mind = gap,
            partial,
            value = holder[key];

        if (value && typeof value === 'object' &&
                typeof value.toJSON === 'function') {
            value = value.toJSON(key);
        }

        if (typeof rep === 'function') {
            value = rep.call(holder, key, value);
        }

        switch (typeof value) {
        case 'string':
            return quote(value);

        case 'number':
            return isFinite(value) ? String(value) : 'null';

        case 'boolean':
        case 'null':
            return String(value);

        case 'object':
            if (!value) {
                return 'null';
            }
            gap += indent;
            partial = [];

            if (Object.prototype.toString.apply(value) === '[object Array]') {
                length = value.length;
                for (i = 0; i < length; i += 1) {
                    partial[i] = str(i, value) || 'null';
                }

                v = partial.length === 0 ? '[]' :
                    gap ? '[\n' + gap +
                            partial.join(',\n' + gap) + '\n' +
                                mind + ']' :
                          '[' + partial.join(',') + ']';
                gap = mind;
                return v;
            }

            if (rep && typeof rep === 'object') {
                length = rep.length;
                for (i = 0; i < length; i += 1) {
                    k = rep[i];
                    if (typeof k === 'string') {
                        v = str(k, value);
                        if (v) {
                            partial.push(quote(k) + (gap ? ': ' : ':') + v);
                        }
                    }
                }
            } else {
                for (k in value) {
                    if (Object.hasOwnProperty.call(value, k)) {
                        v = str(k, value);
                        if (v) {
                            partial.push(quote(k) + (gap ? ': ' : ':') + v);
                        }
                    }
                }
            }

            v = partial.length === 0 ? '{}' :
                gap ? '{\n' + gap + partial.join(',\n' + gap) + '\n' +
                        mind + '}' : '{' + partial.join(',') + '}';
            gap = mind;
            return v;
        }
    }

    if (typeof JSON.stringify !== 'function') {
        JSON.stringify = function (value, replacer, space) {
            var i;
            gap = '';
            indent = '';
			
            if (typeof space === 'number') {
                for (i = 0; i < space; i += 1) {
                    indent += ' ';
                }
            } else if (typeof space === 'string') {
                indent = space;
            }

            rep = replacer;
            if (replacer && typeof replacer !== 'function' &&
                    (typeof replacer !== 'object' ||
                     typeof replacer.length !== 'number')) {
                throw new Error('JSON.stringify');
            }

            return str('', {'': value});
        };
    }

    if (typeof JSON.parse !== 'function') {
        JSON.parse = function (text, reviver) {
            var j;

            function walk(holder, key) {
                var k, v, value = holder[key];
                if (value && typeof value === 'object') {
                    for (k in value) {
                        if (Object.hasOwnProperty.call(value, k)) {
                            v = walk(value, k);
                            if (v !== undefined) {
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
            if (cx.test(text)) {
                text = text.replace(cx, function (a) {
                    return '\\u' +
                        ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
                });
            }

            if (/^[\],:{}\s]*$/.
test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, '@').
replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').
replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {
                j = eval('(' + text + ')');
                return typeof reviver === 'function' ?
                    walk({'': j}, '') : j;
            }
            throw new SyntaxError('JSON.parse');
        };
    }
}());

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
		incfile: function(f, cb){//加载一个或多个文件
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
		mainJS = iCat.refFile.getAttribute('main') || '', arrInit = [];
	
	if(/,/.test(corelib)){
		corelib = corelib.split(',');
		arrInit = iCat.union(arrInit, corelib);
		arrInit = iCat.union(arrInit, [iCat.corecss, mainJS]);
	} else {
		arrInit = iCat.union(arrInit, [iCat.corecss, corelib, mainJS]);
	}
	iCat.inc(arrInit);
})(ICAT);

/**
 *
 * NOTES:
 *
 * 2012-09-23 10:00:00
 * 为了更和谐地利用backbone，icat融合了underscore.js、json2.js
 * - 避免冲突，改include方法名为incfile
 * - corelib支持多个文件设置
 *
 */