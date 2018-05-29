/* mvc.js # */
(function(iCat, root, doc){
	var UTIL = iCat.util,
		WATCHERS = ['id', 'wrap', 'ajaxUrl', 'tmplId', 'modelId', 'hashArgus'];

	//各类默认工具
	UTIL({
		/**
		 * [lazyLoad description]
		 * @param  {Dom/String} pNode   [description]
		 * @param  {Number}     delay   停顿多少ms加载图片
		 * @param  {String}     imgSele 图片选择器
		 */
		lazyLoad: function(pNode, delay, imgSele){
			if(!pNode) return;

			delay = delay || 500;
			imgSele = imgSele || 'img[src$="blank.gif"]';

			setTimeout(function(){
				var imgs = UTIL.queryAll(imgSele, pNode),
					cacheImgs = iCat.__cache_images || (iCat.__cache_images = {});
				iCat.foreach(imgs, function(){ imgLoad(this, cacheImgs); });
			}, delay);
		},

		//需重写函数：获取dom元素
		queryAll: function(s, parent){
			if(!s || !doc.querySelectorAll) return;
			if(!iCat.isString(s)) return s;

			parent = parent || doc;
			return parent.querySelectorAll(s);
		},

		//需重写函数：获取dom元素
		queryOne: function(s, parent){
			if(!s || !doc.querySelector) return;
			if(!iCat.isString(s)) return s;

			parent = parent || doc;
			return parent.querySelector(s);
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
					return (cacheFuns[tmplId] = tmpl(sTmpl))(data);
				}
			} else {
				return tmpl(tmplId, data);
			}
		},

		//需重写函数：ajax获取数据
		ajax: function(options) {
			options = {
				type: options.type || 'POST',
				url: options.url || '',
				timeout: options.timeout || 5000,
				complete: options.complete || function(){},
				error: options.error || function(){},
				success: options.success || function(){},
				data: options.data || ''
			}
			
			var xml_http;
			try{
				if(window.ActiveXObject){
					xml_http = new ActiveXObject('Microsoft.XMLHTTP');
				}else if(window.XMLHttpRequest){
					xml_http = new XMLHttpRequest();
				}
			}catch(e){
				alert(e.name+' : '+e.message);
			}
			xml_http.open(options.type, options.url, true);
			xml_http.setRequestHeader('Content-Type', 'text/html;charset=UTF-8');
			var timeoutLength = options.timeout;
			var requestDone = false;
			
			setTimeout(function(){
				requestDone = true;
			}, timeoutLength);
			
			xml_http.onreadystatechange = function(){
				if(xml_http.readyState==4 && !requestDone){
					if(httpSuccess(xml_http)){
						options.success(httpData(xml_http, options.type));
					} else {
						options.error();
					}
					
					options.complete();
					xml_http = null;
				}
			}
			
			xml_http.send();
			
			function httpSuccess(r){
				try {
					return !r.status && location.protocol == 'file:' ||
						(r.status>=200 && r.status<300) || r.status == 304;
				} catch(e) {
					return false;
				}
			}
			
			function httpData(r, type){
				var ct = r.getResponseHeader('content-type');
				var data = !type && ct && ct.indexof('xml')>=0;
				data = type=='xml' || data ? r.responseXML : r.responseText;
				
				if(type == 'script')
					eval.call(window, data);
				
				return data;
			}
		},

		storage: _storage,
		getRepeatData: _repeatData
	});
	
	// for html
	UTIL(function(){
		var _str2Hooks = function(str){
			if(!str) return [];

			var s, sid, arrCla = [];
			s = str.match(/(\#[\w\-\d]+)|(\.[\w\-\d]+)/g);
			if(s!=null){
				iCat.foreach(s, function(me){
					/^\./.test(me)?
						arrCla.push(me.substring(1)) : (sid = sid || me.substring(1));
				});
			}
			return [sid, iCat.unique(arrCla)];
		},

		_tag = function(t){
			if(!t) return '';
			var s = _str2Hooks(t),
				sid, arrCla, rpStr;
			
			arrCla = s[1].length? (' class="'+s[1].join(' ')+'"') : '';
			sid = s[0]? (' id="'+s[0]+'"') : '';
			rpStr = /img|input|br|hr/i.test(t)? ('<$1'+sid+arrCla+' />') : ('<$1'+sid+arrCla+'>&nbsp;</$1>');
			return t = t.replace(/^(\w+).*/g, rpStr);
		},

		_repeat = function(s){
			if(!s) return '';
			if(s.indexOf('*')<0) return _tag(s);

			s = s.split('*');
			var str = '';
			for(var i=0; i<s[1]; i++){
				str += _tag(s[0]);
			}
			return str;
		},

		_sibling = function(s){
			if(!s) return '';

			if(s.indexOf('+')<0){
				if(s.indexOf('*')!=-1)
					return _repeat(s);
				else if(s.indexOf('>')!=-1)
					return _stack(s);
				else
					return _tag(s);
			}

			s = s.split('+');
			var str = '';
			iCat.foreach(s, function(v){
				if(v.indexOf('*')!=-1)
					str += _repeat(v);
				else if(v.indexOf('>')!=-1)
					str += _stack(v);
				else
					str += _tag(v);
			});
			return str;
		},

		_stack = function(s){
			if(!s) return '';

			if(s.indexOf('>')<0){
				if(s.indexOf('*')!=-1)
					return _repeat(s);
				else if(s.indexOf('+')!=-1)
					return _sibling(s);
				else
					return _tag(s);
			}

			s = s.split('>');
			var str = '&nbsp;';
			iCat.foreach(s, function(v){
				if(v.indexOf('*')!=-1)
					str = str.replace(/\&nbsp;/g, _repeat(v));
				else if(v.indexOf('+')!=-1)
					str = str.replace(/\&nbsp;/g, _sibling(v));
				else
					str = str.replace(/\&nbsp;/g, _tag(v));
			});

			return str;
		},

		__bracket = function(sExp){
			if(!/\(|\)/.test(sExp))
				return sExp.indexOf('+')? _sibling(sExp) : _stack(sExp);
			
			if(/\+\([^\)]+/.test(sExp)){
				var str = '';
				sExp = sExp.replace(/\+\(([^\)]+)/g, ',$1,');
				sExp = sExp.split(',');
				iCat.foreach(sExp, function(v){
					v = v.replace(/\(|\)\+?/g, '');
					str += _stack(v);
				});
				return str;
			}
		},

		_bracket = function(s){
			if(!s) return '';

			if(/\>\(/.test(s)){
				s = s.replace(/(\>)(\()/g, '$1,$2').split('>,');
				return _stack(s[0]).replace(/\&nbsp;/g, __bracket(s[1]));
			} else {
				return __bracket(s);
			}
		},

		zenCoding = function(s){
			if(!s) return '';
			if(/(\<[^\>]+\>)/.test(s)) return s;
			return _bracket(s.replace(/\s*/g, '')).replace(/\&nbsp;/g, '');
		},

		_makeHtml = function(s, pNode, clear){
			var p = pNode, o, shtml;
			if(!p) return;

			if(clear) p.innerHTML = '';
			
			var o = doc.createElement('wrap'), nodes;
				o.innerHTML = zenCoding(s) || '';
				nodes = o.childNodes;
			while(nodes.length){
				p.appendChild(nodes[0]);
			}
			o = null;
		};

		// 对外方法
		return {
			zenCoding: zenCoding,

			/*
			 * ss = 'header#iHeader.hd + div#iScroll'
			 * ss = [
			 * 		'header#iHeader.hd + div#iScroll + div.aaa.bbb + div.ccc',
					'span.aaa.bbb.ccc*9'
				]
			 */
			makeHtml: function(ss, pNode, clear){
				if(!ss || !pNode) return ss;

				if(iCat.isArray(ss)){
					iCat.foreach(ss, function(s){
						_makeHtml(s, UTIL.queryOne(pNode), clear);
					});
				}
				else if(iCat.isString(ss)){
					_makeHtml(ss, UTIL.queryOne(pNode), clear);
				}
			},

			unwrap: function(el){
				if(!el) return;

				var p = el.parentNode,
					nodes = el.childNodes;
				while(nodes.length){
					p.insertBefore(nodes[0], el);
				}
				p.removeChild(el);
			}
		};
	});

	// 对外接口
	iCat.View = View;
	iCat.View.extend = extend;

	iCat.Model = Model;
	iCat.Model.extend = extend;
	iCat.Model.GlobalData = {};

	iCat.Controller = Controller;
	iCat.Controller.extend = extend;

	/*
	 * view-module职责：ui中心
	 * - 设置与ui相关的参数
	 * - 设置与ui相关的函数(events挂钩)，调用其model的逻辑
	 * - 每次extend都会生成一个新的view-Class
	 */
	function View(vid, config, tmplData, requires){
		var that = this;
			that[vid+'_datachange'] = that._datachange;
		
		that.config = config;
		that.config.id = vid;
		that.oldconfig = JSON.stringify(cloneObj(that.config, WATCHERS));

		that.requires = requires;
		that.tmplData = tmplData;
		that.oldtmplData = JSON.stringify(that.tmplData);
	}
	View.prototype = {
		
		/**
		 * mdata.config = {
		 *     *id: 实例化的ID，也用于本地存储
		 *     *wrap: 所插入父层
		 *     *tmplId: 模板ID（规则同_fnTmpl）
		 *     globalKey: 大块数据一次获取，随处取用
		 *     repeatKey: 重复数据 存储有别
		 *     callback: 渲染完成后执行回调函数
		 *     delayTime: 惰性加载img，推迟时间点
		 *     imgSelector: 占位图片选择器
		 *     newCallback: (内部使用)当页面模块化加载时，此为控制函数
		 *
		 *     before: 是否在旧元素前渲染
		 *     clear: 是否先清空再渲染
		 * }
		 * 
		 */
		_render: function(mdata){
			if(!mdata || !mdata.config) return;

			var that = this, html = '',
				GData = iCat.Model.GlobalData,
				cfg = mdata.config, data = mdata.data, tmplData = mdata.tmplData,
				elBaseBed = UTIL.queryOne(GData.baseBed),

				//不设置clear时，默认为true
				clear = cfg.clear===undefined? true : cfg.clear,
				before = cfg.before;

			data = data || {success:true, msg:"", data:[]};
			data = iCat.isArray(data)?
				{success:true, msg:"", data:data} : data;
			iCat.mix(data, tmplData, undefined, true, false);
			
			//第二次渲染且被clear设置为false，应清除
			if(cfg._clear){
				clear = cfg._clear;
				delete cfg._clear;
			}

			//不明原因，ajax取不到数据时，保留旧数据？(待考虑)
			if(!mdata.data){
				clear = false;
			}

			var pWrap = cfg.wrap?
					UTIL.queryOne(cfg.wrap, elBaseBed) : elBaseBed,
				pseudoLabel = 'wrap' + cfg.id, wrap;
			
			if(pWrap){
				try {
					html = UTIL.render(cfg.tmplId, data);
				}
				catch(e){}
				
				if(wrap = UTIL.queryOne(pseudoLabel, pWrap)){
					if(clear)
						wrap.innerHTML = html;
					else {
						var o = doc.createElement('wrap'), cnodes;
						o.innerHTML = html;
						cnodes = o.childNodes;
						if(before){
							var el;
							for(var i=cnodes.length-1; i>=0; i--){
								el = cnodes[i];
								if(el && el.nodeType===1)
									wrap.insertBefore(cnodes[i], wrap.firstChild);
							}
						} else {
							iCat.foreach(cnodes, function(el){
								if(el && el.nodeType===1) wrap.appendChild(el);
							});
						}
						o = null;
					}
				} else {
					wrap = doc.createElement(pseudoLabel);
					wrap.innerHTML = html;
					pWrap.appendChild(wrap);
				}

				// 图片惰性加载
				UTIL.lazyLoad(wrap, cfg.delayTime, cfg.imgSelector);
				//UTIL.unwrap(wrap);

				// 包含表单
				if(cfg.hasForm && !that.getFormData){
					var tagName = pWrap.tagName.toLocaleLowerCase(),
						form = tagName==='form'? pWrap : UTIL.queryOne('form', pWrap);
					
					that.getFormData = function(format){
						format = format || 'string';
						var els = form? form.elements : UTIL.queryAll('input, select, textarea', pWrap),
							jsonFormat = format.toLocaleLowerCase()==='json',
							argus = jsonFormat? {} : [];

						iCat.foreach(els, function(el){
							var key = el.getAttribute('name'),
								value = el.value;
							if(key)//此处需优化，表单数据多样(待考虑)
								jsonFormat? argus[key]=value : argus.push(key+'='+value);
						});
						return jsonFormat? argus : argus.join('&');
					};
				}

				//绑定事件
				if(cfg.__bindEvents)
					cfg.__bindEvents();

				//渲染完成后执行回调
				if(cfg.callback)
					cfg.callback(wrap, cfg, data);
			}

			//ajax取数据
			if(cfg.ajaxUrl){
				var ajaxUrl = cfg.id + cfg.ajaxUrl.replace(/\?.*/, '');
				if(GData[ajaxUrl]===undefined){
					ajaxFetch(cfg, true);
				}
			}

			//next
			if(cfg.modCallback){
				cfg.modCallback(elBaseBed);
				delete cfg.modCallback;
			}

			//completed
			if(cfg.renderCompleted){
				cfg.renderCompleted(elBaseBed);
			}
		},

		_datachange: function(opt){
			this._render(opt.data);
		},

		setData: function(data, type){// add, delete, update
			if(!data) return;
			if(type===undefined) type = 'a';

			var that = this, cfg = that.config;
				type = type.replace(/\s+/g, '').toLocaleLowerCase();

			if(cfg.firePushing)//推送表单数据
				cfg.firePushing(data);

			switch(type){
				case 'd': break;
				case 'delete':
					type = 'd'; break;
				case 'u': break;
				case 'update':
					type = 'u'; break;
				default:
					type = 'a'; break;
			}
			
			that.trigger({
				type:cfg.id+'_cfgchange',
				data:{
					config:cfg, tmplData:that.tmplData,
					formData:{ item:data, type:type/* a, d, u */ }
				}
			});
		},

		setConfig: function(extcfg, must){
			var that = this,
				cfg = iCat.mix(that.config, extcfg, 'hashArgus'),
				newTData, oldTData = that.oldtmplData;

			cfg.hashArgus = extcfg.hashArgus || cfg.hashArgus;
			var	newcfg = cloneObj(cfg, WATCHERS),
				oldcfg = that.oldconfig;
			
			if(must //必须重新加载
				|| dataDiff(oldcfg, newcfg) //config-change
					|| dataDiff(oldTData, that.tmplData)) //tmplData-change
			{
				if(cfg.firePushing)//推送改变的config
					cfg.firePushing(cfg);
				
				that.trigger({
					type:cfg.id+'_cfgchange',
					data:{
						config:cfg, tmplData:that.tmplData
					}
				});
			}
			return that;
		},

		update: function(newcfg){
			this.setConfig(newcfg, true);
		},

		setModel: function(mid){
			this.setConfig({'modelId': mid});
		},

		setAjaxUrl: function(url){
			this.setConfig({'ajaxUrl': url});
		},

		setTmplId: function(tmplId){
			this.setConfig({'tmplId': tmplId});
		},

		setWrap: function(wrap){
			this.setConfig({'wrap': mid});
		},

		setHash: function(hashData){
			this.setConfig({'hashArgus': hashData});
		}
	};

	/*
	 * model-module职责：数据和逻辑处理中心
	 * - 设置与数据/逻辑处理相关的函数
	 * - 处理view发过来的指令，处理后返回相应结果
	 * - 每次extend都会生成一个新的model-Class
	 */
	function Model(mid){ this.id = mid; }
	Model.prototype = {

		//对所得数据进行处理
		//用户自定义覆盖
		output: function(data){
			return data;
		},

		//验证表单数据
		//用户自定义覆盖
		verify: function(fdata){
			return true;
		},

		_cfgchange: function(opt){
			var that = this,
				cfg = opt.data.config, fdata = opt.data.formData,
				GData = iCat.Model.GlobalData, ajaxUrl = cfg.ajaxUrl;
			if(fdata && !that.verify(fdata)) return;

			if(!cfg.__dataArrived){
				cfg.__dataArrived = function(jsonData){
					if(cfg.withHash!==false){
						var hash = cfg.hashArgus.concat();
						hash[0] = jsonData;
						jsonData = that.output.apply(that, hash);
					}

					if(cfg.firePulling)//推送获得的data
						cfg.firePulling(jsonData);

					that.trigger({
						type:cfg.id+'_datachange',
						data:iCat.mix(opt.data, {data:jsonData}, undefined, true, false)
					});
				};
			}

			if(ajaxUrl){
				ajaxUrl = cfg.id + ajaxUrl.replace(/\?.*/, '');
				GData[ajaxUrl]===undefined?
					localFetch(cfg, fdata) : ajaxFetch(cfg, undefined, fdata);
			} else {
				localFetch(cfg, fdata);
			}
		},

		saveData: _saveData,
		removeData: _removeData
	};

	/*
	 * controller-module职责：响应中心
	 * - 响应用户动作，调用对应的View处理函数
	 * - 每次extend都会生成一个新的controller-Class
	 */
	function Controller(cid, config){
		var that = this,
			cfg = that.config = config;
		
		cfg.id = cid;
		that.pageViews = {};
		that.routes || (that.routes = {});

		/**
		 * return {
		 * 		bind: ({el:xxx, type:xxx, callback:xxx, pd:false, sp:false}) 绑定事件,
		 * 		unbind: ({el:xxx, type:xxx}) 解绑事件,
		 * 		delegate: ({el:xxx, type:xxx, callback:xxx, pd:false, sp:false}) 代理绑定,
		 * 		undelegate: ({el:xxx, type:xxx}) 代理解绑
		 * }
		 */
		iCat.Event || (iCat.Event = {});
		if(that.setEvent){
			iCat.mix(iCat.Event, that.setEvent()); 
		}
		else if(iCat.isEmptyObject(iCat.Event)){
			iCat.Event = {
				bind: function(opt){
					if(!iCat.$) return;
					iCat.Event.available = !!iCat.$;
					if(iCat.isString(opt.selector) || !iCat.isjQueryObject(opt.selector)){
						var $Ele = iCat.$(opt.selector);
					} else {
						var $Ele = opt.selector;
					}
					$Ele.off(opt.type)// fixed bug:(重复绑定问题)先解绑，再绑定
						.on(opt.type, function(evt){
							if(opt.preventDefault) evt.preventDefault();
							if(opt.stopPropagation) evt.stopPropagation();
							opt.callback(/*that, */this, evt);
						});
				},

				unbind: function(opt){
					if(!iCat.$) return;
					iCat.Event.available = !!iCat.$;
					if(iCat.isString(opt.selector) || !iCat.isjQueryObject(opt.selector)){
						var $Ele = iCat.$(opt.selector);
					} else {
						var $Ele = opt.selector;
					}
					$Ele.off(opt.type);
				},

				delegate: function(opt){
					if(!iCat.$) return;
					iCat.Event.available = !!iCat.$;
					var doc = document,
						elBody = doc.querySelector('*[data-pagerole=body]') || doc.body;
					iCat.$(elBody).on(opt.type, opt.selector, function(evt){
						if(opt.preventDefault) evt.preventDefault();
						if(opt.stopPropagation) evt.stopPropagation();
						opt.callback(/*that, */this, evt);
					});
				},

				undelegate: function(opt){
					if(!iCat.$) return;
					iCat.Event.available = !!iCat.$;
					var doc = document,
						elBody = doc.querySelector('*[data-pagerole=body]') || doc.body;
					iCat.$(elBody).off(opt.selector, opt.type);
				}
			};
		}

		/**
		 * 处理routes
		 * routes = {
			'  ': 'homeInit', => '': fn
			'subalbum/c:num': 'albumInit', => 'subalbum/c(\\d+)': fn
			'subalbum/c:query/:num': 'detailInit' => 'subalbum/c(\\w+)/(\\d+)': fn
		 * }
		 */
		iCat.foreach(that.routes, function(v, k, _k){
			_k = k.replace(/\s+/g, '')
				 .replace(/\:num/gi, '(\\d+)')
				 .replace(/\:\w+/g, '(\\w+)');
			that.routes[_k] = iCat.isFunction(v)? v : that[v];
			if(k!==_k) delete that.routes[k];
		});

		var elBody = UTIL.queryOne('*[data-pagerole=body]') || doc.body,
			bodyId = elBody.getAttribute('id');
		
		//调整结构
		UTIL.makeHtml(cfg.adjustLayout, elBody);

		//监听hash变化
		var fnInit = function(id){
			var hash = getHashData(id, that.routes) || cfg.defHash || [""];
				that.hashData = hash;
			try{
				iCat.mix(UTIL, that.helper());
				that.routes[hash[0]].call(that);
			}
			catch(e){}
		};
		if(bodyId===null){//页面里没有id属性，则为锚点hash
			fnInit(location.hash);
			if(!root['onhashchange'])
				root['onhashchange'] = function(){ fnInit(location.hash); };
		} else{
			fnInit(bodyId);
		}
	}
	Controller.prototype = {
		/**
		 * o = {
		 *    *baseBed: 切换层
		 *    *modules: curPage所需模块
		 *    ajustLayout: curPage所需框架结构的调整
		 *    vSetting: view群所需setting的调整
		 *    mSetting: model-setting的调整
		 *    fnScroll: 滑动加载函数
		 *    switchLayer: 转场函数
		 * }
		 */
		init: function(o, fnSet){
			var that = this, cfg = that.config,
				mods = o.modules, fnScroll = o.fnScroll, modLoad,
				baseBed = that.setBaseBed(o.baseBed, o.switchLayer),
				newcfg = { "hashArgus": that.hashData };
			if(!mods || !baseBed) return;

			//初始化调整
			UTIL.makeHtml(o.adjustLayout, baseBed);
			iCat.foreach(that.pageViews, function(val, vid){
				that.vmRemove(vid);
			});

			//模块加载渲染
			mods = iCat.isString(mods)?
				mods.replace(/\s+/g, '').split(',') : mods;
			(modLoad = function(){
				if(!mods.length) return;
				var curVid = mods.shift(),
					curView = that.vmAdd(curVid, o.View, o.Model, o.vSetting, o.mSetting),
					cfg = curView.config, tdata = curView.tmplData || (curView.tmplData = {});
				if(!curView){
					modLoad(); return;
				}
				if(mods.length){
					cfg.modCallback = function(bed){
						if(fnScroll){
							fnScroll(curVid, bed, mods) && modLoad();
						} else {
							modLoad();
						}
					};
				}
				if(cfg.events){
					cfg.__bindEvents = function(){ that.bindEvents(cfg.id); }
				}

				if(fnSet){// 补充vsetting
					fnSet(cfg.id, cfg, tdata);
				}
				
				if(curView.init){//初始化
					curView.config.hashArgus = newcfg.hashArgus;
					curView.init.apply(curView, getRequires(curView));
				} else
					curView.setConfig(newcfg);
			})();
		},

		onmessage: function(opt){
			var that = this,
				vid = opt.data.config.id,
				cc_type = vid + '_cfgchange',
				dc_type = vid + '_datachange';
			
			switch(opt.type){
				case cc_type:
					that.trigger({type:'_cfgchange', data:opt.data});
					break;
				case dc_type:
					that.trigger({type:dc_type, data:opt.data});
					break;
			}
		},

		setBaseBed: function(bb, fnSwitch){
			var that = this,
				cfg = that.config,
				GData = iCat.Model.GlobalData,
				switchLayer = fnSwitch || cfg.switchLayer;
			try{
				switchLayer && switchLayer(bb);
			} catch(e){}
			return GData.baseBed = cfg.baseBed = bb || cfg.baseBed;
		},

		vmAdd: function(v, View, Model, vs, ms){
			if(!v) return;
			
			var that = this,
				cfg = that.config, vcfg,
				curView, curModel, vid, mid;
				View = View || cfg.View, events = [];
			
			if(iCat.isString(v)){
				if(!View) return;
				vid = v;
				vs = iCat.mix(View.setting || (View.setting = {}), vs);
				curView = new View(v, vs[v]);
			} else {
				vcfg = v.config;
				vid = v.config.id;
				vs = View? iCat.mix(View.setting || (View.setting = {}), vs) : (vs || {});
				curView = iCat.mix(v, vs[v]);
			}
			
			Model = Model || cfg.Model || iCat.Model.extend();
			ms = ms || Model.setting || {};
			mid = curView.config.modelId || (curView.config.modelId = 'pageModel');
			curModel = iCat.Model[mid]? iCat.Model[mid] : new Model(mid, ms[mid]);

			curView.bind(vid+'_cfgchange', that);
			that.bind(vid+'_datachange', curView);

			curModel.bind(vid+'_datachange', that);
			that.bind('_cfgchange', curModel);

			that.pageViews[vid] = {
				view: curView, active: false
			};

			return curView;
		},

		vmRemove: function(vid){
			if(!vid || !iCat.View[vid]) return;

			var that = this,
				curView = iCat.View[vid],
				curModel = iCat.Model[curView.config.modelId];

			that.unbindEvents(vid);
			that.unbind(vid+'_datachange', curView);
			curView.unbind(vid+'_cfgchange', that);
			curModel.unbind(vid+'_datachange', that);
		},

		bindEvents: function(){
			var that = this;
			if(!arguments.length)
				iCat.foreach(that.pageViews, function(o){
					oneBind(o.view, !o.active);
					if(iCat.Event.available) o.active = true;
				});
			else {
				var o = that.pageViews[arguments[0]];
				oneBind(o.view, !o.active);
				if(iCat.Event.available) o.active = true;
			}
		},

		unbindEvents: function(){
			var that = this;
			if(!arguments.length)
				iCat.foreach(that.pageViews, function(o){
					if(!o.active) return;
					o.active = false;
					oneUnbind(o.view);
				});
			else {
				var o = that.pageViews[arguments[0]];
				if(!o.active) return;
				o.active = false;
				oneUnbind(o.view);
			}
		},

		//设置各类工具和事件
		//用户自定义覆盖
		helper: function(){}
	};

	// 处理公有或特有的option
	function extend(oldopt){
		var Super = this, Class,
			claPro, oldcfg, oldTData;

		Class = function (id, config, tmplData, requires){// 新类
			var that = Super[id] = this;
			that.__Message_QueueStore = {};//对象的消息队列
			Super.call(that, id, config, tmplData, requires);
		}
		
		claPro = Class.prototype;
		oldopt || (oldopt = {});
		oldcfg = oldopt.config;
		oldTData = oldopt.tmplData;
		
		//必备方法
		iCat.mix(claPro, Super.prototype);
		iCat.mix(claPro, {
			//增加监听者
			bind: function(type){
				var that = this,
					argus = arguments,
					qs = that.__Message_QueueStore;
				for(var i=1, len=argus.length; i<len; i++){
					qs[type] || (qs[type] = []);
					if(!iCat.hasItem(qs[type], argus[i])){
						qs[type].push(argus[i]);
					}
				}
			},

			//删除监听者
			unbind: function(type){
				var that = this,
					argus = arguments, len=argus.length,
					qs = that.__Message_QueueStore;
				if(!qs[type]) return;

				if(len==1)
					delete qs[type];
				else
					for(var i=1; i<len; i++){
						if(iCat.hasItem(qs[type], argus[i])){
							iCat.delItem(qs[type], argus[i]);
						}
					}
			},

			//触发消息
			trigger: function(opt){
				var queue = this.__Message_QueueStore[opt.type];
				if(!queue) return;

				iCat.foreach(queue, function(o){
					var fn = o[opt.type] || o['onmessage'];
					if(!fn) return;
					fn.call(o, opt);
				});
			}
		});

		//公有属性/方法
		if(oldopt.config) delete oldopt.config;
		if(oldopt.tmplData) delete oldopt.tmplData;
		iCat.mix(claPro, oldopt);

		//return Class;
		return function(id, newopt){
			var that = Super[id];
			
			if(!that){
				var newcfg, newTData,
					cfg = {}, tdata = {}, requires;
				newopt || (newopt = {});
				newcfg = newopt.config;
				newTData = newopt.tmplData;
				requires = newopt.requires;

				//特有属性/方法
				if(newcfg) delete newopt.config;
				if(newTData) delete newopt.tmplData;
				if(requires) delete newopt.requires;

				//配置项
				iCat.mix(cfg, oldcfg); iCat.mix(cfg, newcfg);
				iCat.mix(tdata, oldTData); iCat.mix(tdata, newTData);
				that = new Class(id, cfg, tdata, requires);
			}
			
			return iCat.mix(that, newopt);
		};
	}

	/**
	 * single image load
	 * @param {Dom}    img       [description]
	 * @param {Object} imgsCache [description]
	 */
	function imgLoad(img, imgsCache){
		var src = img.getAttribute('data-src'), oImg;
		if(!src) return;

		if(!imgsCache[src]){
			var oImg = new Image();
			oImg.src = src;
			oImg.onload = function(){
				img.src = src;
				imgsCache[src] = true;
				oImg = null;
			};
		} else {
			img.src = src;
		}
		img.removeAttribute('data-src');
	}

	// 模板函数
	function tmpl(tmpl, data){
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

	// 克隆指定属性的对象
	function cloneObj(o, props){
		return iCat.mix({}, o, props);
	}

	// ajax或全局数据
	function ajaxFetch(cfg, now, fdata){
		var gkey = cfg.globalKey,
			fkey = cfg.hashArgus[0] + ':' + cfg.id + '@form',
			fnOpt = cfg.__dataArrived, GData = iCat.Model.GlobalData,
			ajaxUrl = cfg.id + cfg.ajaxUrl.replace(/\?.*/g, '');
		
		if(gkey && GData[gkey]) now = false;
		if(fdata && fdata.type!=='d' && cfg.hasForm) _saveData(fkey, fdata);// 回填的需要
		fdata = _str2json(fdata || _storage(fkey)) || {};

		if(now || !gkey){
			if(GData[ajaxUrl]===false || cfg.__hasnext===false){
				if(cfg.requestFail)//请求失败
					cfg.requestFail(cfg.__hasnext===false);
				return;//此次请求尚未结束(或没有下一页)，不重复发起请求
			} else {
				if(cfg.requestSuccess)//请求成功
					cfg.requestSuccess(cfg);
				cfg._clear = GData[ajaxUrl]===undefined;
				GData[ajaxUrl] = false;
			}

			UTIL.ajax(iCat.mix({
				type: 'POST', timeout: 10000,
				url: cfg.ajaxUrl, data: fdata.item, cache: false,
				success: function(data){
					var key = gkey//全局数据
							|| cfg.repeatKey //重复数据
								|| (cfg.hashArgus[0] + ':' + cfg.id + (cfg.subkey || '')),
						jsonData = _str2json(data),
						isDiff = dataDiff(_storage(key), jsonData);

					if(jsonData){
						var oData = jsonData.data || {};
						cfg.__hasnext = oData.hasnext!==undefined? oData.hasnext : oData.hasNext; //fixed bug:false时取到后面的undefined
					}

					cfg._clear==false && (cfg.dataSave = false);
					gkey && (GData[gkey] = true);
					GData[ajaxUrl] = true;//fix bugs: 数据取到即可认为此次请求结束

					if(cfg.repeatKey){
						if(!jsonData) return;
						if(cfg.dataSave!==false) _saveData(cfg, data);
						jsonData = _repeatData(_storage(key));

						if(cfg.hasForm) iCat.mix(jsonData, fdata.item);
						if(cfg.success) cfg.success(jsonData);
						fnOpt(jsonData);
					}
					else if(isDiff){
						if(cfg.dataSave!==false) _saveData(cfg, jsonData);//存数据
						if(cfg.hasForm) iCat.mix(jsonData, fdata.item);
						if(cfg.success) cfg.success(jsonData);
						fnOpt(jsonData);
					}
				},
				error: function(){
					GData[ajaxUrl] = true;
					if(cfg.error) cfg.error();
				}
			}, cfg, 'success, error'));
		} else {
			iCat.wait(function(k, isEnd, Cache){
				var data = _storage(gkey), jsonData;
				if(!data){
					Cache[k] = false;
					//if(isEnd) ajaxFetch(cfg, true);
					return;
				}
				delete Cache[k];
				var jsonData = _str2json(data) || {};
				if(cfg.hasForm) iCat.mix(jsonData, fdata.item);
				fnOpt(jsonData);
			}, 1000);
		}
	}

	// 初始化取数据或本地数据
	function localFetch(cfg, fdata){
		var key = cfg.globalKey //全局数据
					|| cfg.repeatKey //重复数据
						|| (cfg.hashArgus[0] + ':' + cfg.id + (cfg.subkey || '')),
			data = _storage(key), jsonData,
			fnOpt = cfg.__dataArrived;

		if(cfg.ajaxUrl){
			jsonData = cfg.repeatKey?
				_repeatData(data) : _str2json(data);
		} else {
			var fkey = cfg.hashArgus[0] + ':' + cfg.id + '@form',
				jfdata = _str2json(fdata || _storage(fkey)) || {}, jsonData, fn;

			if(fdata && fdata.type!=='d' && cfg.hasForm) _saveData(fkey, fdata);// 回填的需要
			if(cfg.repeatKey){
				jsonData = _repeatData(data) || [];
				if(fdata){
					(fn = function(d){
						if(iCat.isArray(d)){
							iCat.foreach(d, function(item){ fn(item); });
						} else {
							var index, updateItem;
							iCat.foreach(jsonData, function(v, i){
								if(v.rkey===d.rkey){
									index = i;
									fdata.type==='u'?
										updateItem = v : iCat.delItem(jsonData, v);
									return false;
								}
							});

							if(fdata.type==='u'){
								if(index!==undefined){
									iCat.mix(updateItem, d, undefined, true, false);
									jsonData[index] = d = updateItem;
								}
							}else if(fdata.type==='a'){
								jsonData.unshift(d);
							}

							if(fdata.type==='d'){
								_removeData(d.rkey, true);
							}else if(cfg.dataSave!==false){
								_saveData(cfg, d);
							}
						}
					})(fdata.item);
				}
			} else { 
				jsonData = _str2json(data) || jfdata.item;
			}

			if(cfg.hasForm)
				iCat.mix(
					iCat.isArray(jsonData)?
						{ success:true, msg:"", data:jsonData } : jsonData
					, jfdata.item
				);
		}
		fnOpt(jsonData);
	}

	// for ajaxFetch/localFetch: 根据keys获取重复数据
	function _repeatData(keys){
		if(!keys) return;

		var arr = [];
		iCat.foreach(keys.split(','), function(k){
			var item = JSON.parse(_storage(k));
			arr.push(item);
		});
		return arr;
	}

	// for ajaxFetch/localFetch: 字符串转化为json
	function _str2json(s){
		if(!s) return null;
		if(iCat.isString(s)){
			s = s.replace(/[\r\t\n]/g, '');
			s = /^[\{\[].*[\]\}]$/.test(s)? JSON.parse(s) : null;
		}
		return s;
	}

	// for ajaxFetch/localFetch: 保存数据
	function _saveData(cfg, data){
		if(!cfg || !data) return;
		if(iCat.isString(cfg))//直接有key
			return _storage(cfg, data);

		var key = cfg.globalKey
					|| cfg.repeatKey
						|| (cfg.hashArgus[0] + ':' + cfg.id + (cfg.subkey || ''));
		if(cfg.repeatKey){
			var keys = _storage(key),
				arr = keys? keys.split(',') : [];// fixed bug: keys=""的情况下，会出现一个空的item

			if(data.rkey && iCat.hasItem(arr, data.rkey)){
				return _storage(data.rkey, data);
			}
			
			if(iCat.isArray(data)){
				iCat.foreach(data, function(dataItem){
					var k = key + '_' + arr.length + Math.floor(Math.random()*1000+1);
					arr.unshift(k);
					dataItem.rkey = k;
					_storage(k, data);
				});
				_storage(key, arr.join(','));
			} else {
				var k = key + '_' + arr.length + Math.floor(Math.random()*1000+1);
				arr.unshift(k);
				_storage(key, arr.join(','));
				data.rkey = k;
				_storage(k, data);
			}
		} else {
			_storage(key, data);
		}
	}

	// for localFetch: 删除数据
	function _removeData(key, isRepeat){
		if(isRepeat){
			var mKey = key.replace(/\_\d+$/, ''),
				arrKeys = _storage(mKey).split(',');
			iCat.delItem(arrKeys, key);
			_storage(mKey, arrKeys.join(','));
		}
		root.localStorage.removeItem(key);
	}

	/**
	 * 存取数据
	 * @param {String} 键名
	 * @param {String} 键值
	 * @return {String} 键值
	 */
	function _storage(){
		var argus = arguments, len = argus.length,
			ls = root.localStorage,
			key, data;
		if(!len || !ls) return;

		if(len===1)
			return ls.getItem(argus[0]);
		else {
			key = argus[0];
			data = argus[1];
			data = iCat.isObject(data)? JSON.stringify(data) : data;
			ls.removeItem(key);
			ls.setItem(key, data);
		}
	}

	// 比较两个数据是否相同
	function dataDiff(json1, json2){
		return _toString(json1) !== _toString(json2);
	}

	// for dataDiff: json转化为字符串
	function _toString(json){
		if(json===undefined) json = null;
		json = iCat.isString(json)?
				json : JSON.stringify(json);
		json = json.replace(/[\r\t\n\s'"]/g, '');
		return json;
	};

	/**
	 * oHash = {
		'help'                 : 'help',
		'search/:query'        : 'search\/(\w+)',
		'search/:query/p:page' : 'search\/(\w+)\/p(\w+)''
	   }
	 *
	 * return [pid, argus]
	 */
	function getHashData(s, oHash){
		if(!s) return;

		s = s.replace(/\s+/g, '').match(/[^\#]+/g)[0];
		if(s.indexOf('/')<0)
			return [s];
		else {
			if(!oHash) return;
			var _s;
			iCat.foreach(oHash, function(fn, k){
				var _exp = new RegExp('^'+k+'$', 'i'),
					argus = k.match(/\([^\)]+\)/g),
					querys = '', len;
				if(argus && (len=argus.length)){
					iCat.foreach(argus, function(v, i){
						querys += '$' + (i+1) + (i==len-1? '':',');
					});
				}
				if(_exp.test(s)){
					s = s.replace(_exp, querys);
					s = s.split(',');
					s.unshift(k);
					_s = s;
					return false;
				}
			});

			return _s;
		}
	}

	// 一个view上的所有事件绑定
	function oneBind(curView, init){
		var Event = iCat.Event,
			events = _getEvents(curView) || {},
			dgEs = events.dgEvents, elEs = events.elEvents;
		if(!Event || (!dgEs && !elEs)) return;

		init && iCat.foreach(dgEs, function(item){ Event.delegate(item); });
		iCat.foreach(elEs, function(item){ Event.bind(item); });
	};

	// 一个view上的所有事件解绑
	function oneUnbind(curView){
		var Event = iCat.Event,
			events = _getEvents(curView) || {},
			dgEs = events.dgEvents, elEs = events.elEvents;
		if(!Event || (!dgEs && !elEs)) return;

		iCat.foreach(dgEs, function(item){ Event.undelegate(item); });
		iCat.foreach(elEs, function(item){ Event.unbind(item); });
	};

	/**
	 * for oneBind/oneUnbind: events转换为标准形式
	 *[
		{selector:'.J_topBanner', type:'@click', callback:'showTip', preventDefault:true},
		{'@click|0|0 .J_topBanner': 'hidebox'}
	  ]
	 *{
		'@click|1|1 .J_topBanner i' : 'show',
		'@click|1|1 .J_topBanner'   : 'hide'
	  }
	**/
	function _getEvents(view){
		var vid = view.config.id,
			cacheEvents = iCat.__cache_events || (iCat.__cache_events = {});
		if(cacheEvents[vid]) return cacheEvents[vid];
		if(!view || !view.config.events) return;

		var es = view.config.events,
			elEvents = [], dgEvents = [];
		iCat.foreach(es, function(e, k){
			var item, fn, eType;
			if(e.selector){
				item = e;
				eType = e.type;
				fn = e.callback;
				fn = iCat.isFunction(fn)? fn : view[fn];
			} else {
				var key, arr, item;
				if(iCat.isString(k)){
					key = k; fn = e;
				} else {
					for(var p in e){
						key = p; fn = e[p];
					}
				}
				arr = key.replace(/^([@\w\|\!]+)\s+/, '$1|').split('|');
				eType = arr[0];
				fn = iCat.isFunction(fn)? fn : view[fn];
				item = {
					selector:arr[3], preventDefault:arr[1]==1,
					stopPropagation:arr[2]==1
				};
			}

			item.type = eType.replace(/^@|!$/, '');
			item.callback = function(el, evt){/*c, el, evt*/
				var argus = [el, evt].concat(getRequires(view));
				fn.apply(view, argus);
			};
			if(/blur|focus|load|unload|change|scroll/i.test(eType) || eType.charAt(0)!=='@'){
				elEvents.push(item);
			} else {
				dgEvents.push(item);
			}
		});

		return cacheEvents[vid] = {
			elEvents:elEvents, dgEvents:dgEvents
		};
	}

	// 获取事件执行时所依赖的model和其他view
	function getRequires(view){
		var cfg = view.config,
			vid = cfg.id, mid = cfg.modelId,
			arr = [];
		iCat.foreach(view.requires || [], function(v, i){
			arr[i] = iCat.isString(v)? iCat.View[v] : v;
		});
		arr.unshift(iCat.Model[mid]);
		return arr;
	}
})(ICAT, this, document);