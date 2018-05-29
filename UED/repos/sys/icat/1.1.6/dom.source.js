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
		html: function(el, shtml){
			if(!shtml) return;
			el.innerHTML = shtml;
		},

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