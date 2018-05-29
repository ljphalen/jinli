(function(iCat){

	/*************************** 全局属性 *****************************************/
	// 是否支持微信分享
	var isSupportWeixinShare = window.share && window.share.shareToWeixin;
	var tap = 'ontouchstart' in window ? 'tap' : 'click';
	var locationHref = location.href;
	var keyword = location.host.split('.')[0]; // ios apk channel ...
	var shareType = {
		'weixin': '0',
		'weixinp': '1',
		'weibo': '2',
		'qq': '3',
		'qzone': '4'
	};

	var body = $('body');

	// 触摸图标时的遮罩效果
	body.on('touchstart touchmove mousedown mousemove', '.J_mask', function(){
		var that = $(this);
		that.addClass('active');
		setTimeout(function(){ that.removeClass('active'); }, 1000);
	}).on('touchend mouseup', function(){
		$(this).removeClass('active');
	});
	// string ==> object
	function parseJSON(o) {
		return $.isPlainObject(o) ? o : JSON.parse(o);
	}

	// 当没有uid = fun点赞功能是否启用缓存数据
	function voteCache(isCache) {
		var voteNode, key;
			
		voteNode = $('.J_vote');
		key = 'vote-' + location.href.split('?')[0].replace(/\W/g, '');
		if (voteNode.attr('data-cache') != 'true') return;
		try {
			if (isCache) {
					localStorage.setItem(key, '1');
			} else {
				if (localStorage.getItem(key) == '1') {
					voteNode.addClass('on');
				}
			}
		}catch(e) {}
	};

	// 点赞功能
	body.on('click', '.J_vote', function(evt) {
		var that = $(this),
			ajaxUrl;

		if (that.hasClass('on')) {
			GOU.showtip('你已点赞过~');
			return;
		}
		// 控制重复点击
		if (that.hasClass('dis')) { return; }
		ajaxUrl = that.attr('data-ajax');
		$.ajax({
			url: ajaxUrl,
			beforeSend: function(){
				that.addClass('dis');
			},
			complete: function() {
				that.removeClass('dis');
			},
			success: function(result) {
				result = JSON.parse(result);
				if (result.success) {
					that.addClass('on');
					voteCache(true);
					try {
						that.children('span')[0].innerHTML = '' + result.data.praise;
					} catch(e) {}
				}
			}
		})
		evt.stopPropagation();
	});

	// 弹出框操作
	body.on(tap, '.modal', function(e) {
		e.stopPropagation();
		if (e.target == this) this.style.display = 'none';
	})
	// 关闭弹出框
	body.on('tap', '.J_modalCancel', function(e) {
		e.preventDefault();
		var $this = $(this);
		setTimeout(function() {
			$this.closest('.modal').hide();
		}, 300);
	})

	body.on('click', '.J_modalSure', function(e) {
		$(this).closest('.modal').hide();
		// return false;
	})

	$('.J_quantity').on('click', 'span', function(){
		var me = $(this),
			ipt = me.siblings('input'), val = +ipt.val() || 0,
			limit = +me.siblings('em').html().replace(/\s+/g, '');

		if(me.hasClass('minus')){
			val>0? ipt.val(val-1) : ipt.val(val);
		}
		if(me.hasClass('add')){
			+ipt.val()<limit? ipt.val(val+1) : ipt.val(val);
		}
	});

	// 绑定打开共享窗口事件
	function openShareEvt() {
		var fun = function(e) {
			e.stopPropagation();
			e.preventDefault();
			$('#J_shareModal').show();
		};
		body.off('click', '.J_shareSns', fun);
		body.on('click', '.J_shareSns', fun);
	}

	// 省市区级联
	var areaWrap = $('.J_areaWrap'), oS = areaWrap.find('select');
	if(areaWrap[0]){
		iCat.include(['./web/dataArea','./web/linkage'], function(){
				iCat.widget.linkage({
					isArea: true,
					areaWrap: areaWrap,
					s1: oS.eq(0),
					s2: oS.eq(1),
					s3: oS.eq(2),
					aNode: areaWrap.attr('old-aNode') || ''
				});
		}, true);
	}

	// 点击调用拨号功能
	$('.voice').on('click', function(){
		location.href = 'tel:0755-82583525';
	});

	// 返回顶部
	var gotop = $('.gotop');
	if (gotop[0]) {
		gotop.on(tap, 'span', function(e) {
			window.scrollTo(0,1);
			e.stopPropagation();
		});
		
		window.onscroll = function(){
			if(document.body.scrollTop < 700){
				gotop.animate({
					opacity: 0
				}, 400, 'ease-out');
			} else {
				gotop.animate({
					opacity: 1
				}, 400, 'ease-in');
			}
		};
	}

	// 图片延迟加载
	var lazyloadImg = $('.J_lazyload_img');
	if(lazyloadImg[0]){
		iCat.foreach(lazyloadImg, function(k, o) {
			_lazyLoad(k);
		});
	}
	// 图片的延迟加载
	function _lazyLoad(pNode, t, imgSelector){
		if(!pNode) return;

		t = t || 500;
		pNode = _queryOne(pNode);
		imgSelector = imgSelector || 'img[src$="blank.gif"]';
		setTimeout(function(){
			iCat.foreach(_queryAll(imgSelector, pNode),
				function(k, o){
					var src = k.getAttribute('data-src');
					iCat.__cache_images = iCat.__cache_images || {};
					if(!src) return;

					if(!iCat.__cache_images[src]){
						var oImg = new Image(); oImg.src = src;
						oImg.onload = function(){
							k.src = src;
							iCat.__cache_images[src] = true;
							oImg = null;
						};
					} else {
						k.src = src;
					}
					k.removeAttribute('data-src');
				}
			);
		}, t);
	}

	// 分享事件
	function shareBindEvent(args) {

		var shareEvent = function() {
			var $this = $(this),
				shareNode = $this.parent(),
				type, pic, url, source, 
				site, desc, summary, obj;

			if (type = $this.attr('data-type')) {
				title = shareNode.attr('data-title') || '';
				pic = shareNode.attr('data-pic') || '';
				url = shareNode.attr('data-url') || '';
				source = shareNode.attr('data-source') || '';
				site = shareNode.attr('data-source') || '';
				url = shareNode.attr('data-url') || '';
				desc = shareNode.attr('data-desc') || '';
				summary = shareNode.attr('data-summary') || '';
				obj = {title: title, pic: pic, url: url, site: source, source: source, desc: desc, summary: desc };
				if (obj.url) {
					obj.url = obj.url + (obj.url.indexOf('?') > -1 ? '&' : '?') + 'from=' + type;
				}
				if (args) {
					if ($.isFunction(args)) args = args();
					obj = $.extend(obj, args);
				}

				body.off('click', '.J_shareToSns', shareEvent);
				$this.closest('.modal').hide();
				// alert('1111')
				shareToSns(type, obj);
			}
			
		}

		if (isSupportWeixinShare) $('.J_shareToSns').show();
		body.on('click', '.J_shareToSns', shareEvent);
	}

	function shareToSns(type, args) {
		if (!type || !args) return;
		if (!iCat.isObject(args)) return;
		var share = window.share,
			weiboUrl = 'http://v.t.sina.com.cn/share/share.php',
			qzoneUrl = 'http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey',
			qqUrl = 'http://connect.qq.com/widget/shareqq/index.html';
		
		if (share) {
			share.setmThumbBitmap && share.setmThumbBitmap(args.pic);
			share.setmDescription && share.setmDescription(args.desc);
			if (GOU.isVailableVersion('2.5.8')) {
				share.setShareUrl && share.setShareUrl(args.url);
			}
			share.setShareTitle && share.setShareTitle(args.title);
		}
		switch (type) {
			case 'weibo':
				window.open(weiboUrl + '?' + $.param(args), '_blank');
			break;
			case 'qzone':
				args.pics = args.pic;
				delete args.pic;
				window.open(qzoneUrl + '?' + $.param(args), '_blank');
			break;
			case 'qq':
				args.pics = args.pic;
				delete args.pic;
				// alert(qqUrl + '?' + $.param(args));
				window.open(qqUrl + '?' + $.param(args), '_blank');
			break;
			case 'weixin':
				share && share.shareToWeixin && share.shareToWeixin(false);
			break;
			case 'weixinp':
				share && share.shareToWeixin && share.shareToWeixin(true);
			break;
		}
	}
	// ios分享
	function iosShareToSns (args) {
		if (!args) return;
		var obj = {
			title: args.title || '',
			content: args.content || '',
			pic: args.pic || '',
			url: args.url ? encodeURIComponent(args.url) : '',
			type: args.type || ''
		};
		// alert(JSON.stringify(obj));
		location.href = 'objc://share/' + JSON.stringify(obj);
	}
	// 调用客户端分享
	function callClientShare(fn) {
		body.on('click', '.J_callClientShare', function(e) {
			if ($.isFunction(fn)) fn();
			window.share && window.share.shareApp && window.share.shareApp();
		});
	}

	// 表单验证
	function validator(el) {
		var val,
			verify, maxLen, minLen,
			wordLen, msg,
			verifyMsg = {};
		var hint = function(msg) {
			$(this).addClass('err-border')
			GOU.showtip(msg);
		}

		$el = $(el)
		val = $el.val();

		if (msg = $el.attr('data-msg')) {
			msg = msg.replace(/\'/g, '\"');
			try {
				verifyMsg = JSON.parse(msg);
			} catch(e) {}
		}

		if ($el.attr('type') === 'radio') {
			return;
		}
		// 是否必需
		if ($el.attr('require') != null) {
			if (val === '') {
				hint.call(el, verifyMsg.require || '不能为空');
				return false;
			}
		}
		// 长度验证
		if ((maxLen = $el.attr('maxlength')) != null) {
			if (val.length > maxLen) {
				hint.call(el, verifyMsg.maxlength || ('字符个数不能大于' + maxLen));
				return false;
			}
		}
		// 长度验证
		if ((minLen = $el.attr('minlength')) != null) {
			if (val.length < minLen) {
				hint.call(el, verifyMsg.minlength || ('至少输入' + minLen + '个字符'));
				return false;
			}
		}
		// 正则表达式
		if ($el.attr('data-verify') != null) {
			verify = new RegExp($el.attr('data-verify') || '.*');
			if (!verify.test(val)) {
				hint.call(el, verifyMsg.verify || '格式错误，请重新填写');
				return false;
			}
		}
		$el.removeClass('err-border');
		return true;
	}
	// 10000 ===> 1万+
	function numToText(num) {
		if (num == null) return '';
		num = num * 1;
		if (num < 10000) {
			return num;
		} else {
			return Math.floor(num/10000) + '万+';
		}
	}

	function showWebShareDialog(pic, title, url, desc){
		var share = window.share;
		// alert(pic + ' | ' + title + ' | ' + url + ' | ' + desc)
		if (!share) return;
		if (pic) share.setmThumbBitmap && share.setmThumbBitmap(pic);
		if (title) share.setShareTitle && share.setShareTitle(title);
		if (desc) share.setmDescription && share.setmDescription(desc);
		if (GOU.isVailableVersion('2.5.8')) {
			if (url) share.setShareUrl && share.setShareUrl(url);
		}
		share.showWebShareDialog && window.share.showWebShareDialog();
	}

	// 提交表单
	var form = $('form'), argus = {token: token};
	if(/\/amigo/.test(locationHref) && form[0]){
		var ckboxs = $('.J_selectOrder .options input'),
			hiddenIpt = $('input[name="parm"]');
		ckboxs.click(function(){
			var that = this;
			if(that.checked){
				hiddenIpt.val(this.value);
				ckboxs.each(function(){
					if(this!==that) this.checked = false;
				});
			} else {
				hiddenIpt.val('');
			}
		});

		var types = $('.J_selectType input');
		if(types.length){
			types.click(function(){
				var index = types.index(this),
					selects = $('.J_selectReason select');
				selects.eq(index).attr('name', 'reason').show()
					   .siblings().removeAttr('name').hide();
			});
		}

		$('body').on('click', '.J_formSubmit', function(evt){
			evt.preventDefault();
			var els = form[0].elements, mrEl = document.getElementById('J_mustRead'),
				len = els.length, i = 0,
				key, el, v, exp;
				
			for(; i<len; i++){
				el = els[i];
				v = el.value;
				key = el.getAttribute('name');
				exp = new RegExp(el.getAttribute('data-verify') || '.*');
				if(key){
					if(el.getAttribute('type')==='radio'){
						if(el.checked){
							argus[key] = v
						} else { continue; }
					}
					else if((v && exp.test(v)) || key==='gbook'){
						argus[key] = v;
					} else {
						GOU.showtip(el.getAttribute('data-error') || '格式错误，请重新填写');
						return;
					}
				}
				if(mrEl && !mrEl.checked){
					GOU.showtip(mrEl.getAttribute('data-error') || '请阅读规则后，再完成退换货');
					return;
				}
			}

			// $('body').off('click', '.J_formSubmit', formCallback);	// 防止在网络状况不好的时候多次提交表单
			$.post(form.attr('action'), argus, function(data){
				data = JSON.parse(data);
				if(data.success){
					location.href = data.data.url;
				} else {
					GOU.showtip(data.msg);
				}
				// $('body').on('click', '.J_formSubmit', formCallback);	// 提交失败可继续提交
			});

		});

		$('body').on('click', '.J_formVerify', function(evt){
			evt.preventDefault();
			var els = form[0].elements,
				len = els.length, i = 0,
				key, el, v, exp;
			for(; i<len; i++){
				el = els[i];
				v = el.value;
				key = el.getAttribute('name');
				exp = new RegExp(el.getAttribute('data-verify') || '.*');
				if(key){
					if((v && exp.test(v))){
						argus[key] = v;
					} else {
						GOU.showtip(el.getAttribute('data-error') || '格式错误，请重新填写');
						return;
					}
				}
			}
			form.submit();
		});
	}

	// 图片详情
	var detailWrap = $('.J_proDetail');
	if(detailWrap[0]){
		iCat.include('MVC', function(){
			var _ajaxurl = detailWrap.attr('data-ajaxurl').replace(/\s+/g, '');
			var moduleName = location.pathname.replace(/\W/g, '');

			iCat.util.removeData(':dv');
			Dview = iCat.View.extend({
				config: {
					tmplId: 'J_dtemplate',
					wrap: '.J_proDetail .wrap',
					ajaxUrl: _ajaxurl,
					__base_ajaxurl: _ajaxurl,
					clear: false,
					ajaxConfig: {
						data: {token: token}
					},
					events: [{selector:window, type:'scroll', callback:'loadMore'}],
					callback: function(w, cfg, data){
						cfg.list_pageNum = data.data.curpage;
						$('#J_loadStatus').css('display', 'none');
					},
					requestFail: function(noNext){
						GOU.showtip(noNext? '没有更多了' : '努力加载中...');
					}
				},
				tmplData: {
					blankPic: iCat.PathConfig.picPath + 'blank.gif',
				},
				loadMore: function(el){
					var that = this, cfg = that.config,
						url = cfg.__base_ajaxurl.replace(/[\?\&]page=\d+/, ''),
						win = $(el), winH = win.height(),
						body = document.body, 
						// wrap = $('.module');
						wrap = $(document);
					if(winH + body.scrollTop >= wrap.height()){
						that.setAjaxUrl(
							url + (url.indexOf('?')<0? '?' : '&') + 'page=' + (cfg.list_pageNum + 1)
						);
					}
				}
			});

			var Dctrl = iCat.Controller.extend({
				config: {View: Dview, baseBed: 'body'},
				routes: {'': 'detailInit'},
				detailInit: function(){
					this.init({
						modules: ['dv']
					})
				}
			});

			new Dctrl('dc');
		});
	} else {
		detailWrap = null;
	}

	// 个人中心
	if(/\/user\//.test(locationHref)){
		iCat.include('./web/gngou.new.js');
		var dw = $(document).width(), dh = $(document).height()+1000, wh = $(window).height(),
			box = $('.J_dialogBox'), w = box.width(), x_pos = (dw-w)/2;
			
		$('.J_showDialog').click(function(evt){
			evt.preventDefault();

			$('body').css({'overflow':'hidden'});
			dh = $('.J_showDialog').parent().hasClass('favor')? wh : dh;
			if(this.getAttribute('data-ajaxUrl')){
				var ajaxurl = this.getAttribute('data-ajaxUrl');
				$.post(ajaxurl, {token:token}, function(data){
					var d = $.parseJSON(data);
					box.find('p').html(d.msg);
					if(d.success){
						box.find('.btn a').removeAttr('href');
						if($('.J_showDialog').parent().hasClass('favor')){
							$('.J_showDialog').addClass('selected');
						}
					}
					box.css({'left': x_pos+'px', 'top': '35%'});
					box.on('click', '.btn span', function(){
						$('.JS-dbMask').hide();
						box.css('top', '-35%');
						$('body').css({'overflow':'auto'});
					});
					$('.JS-dbMask').height(dh).show();
					$('body').on('click', '.JS-dbMask', function(){
						this.style.display = 'none';
						box.css('top', '-35%');
						$('body').css({'overflow':'auto'});
					});
				});
			} else {
				box.css({'left': x_pos+'px', 'top': '35%'});
				box.on('click', '.btn span', function(){
					$('.JS-dbMask').hide();
					box.css('top', '-35%');
					$('body').css({'overflow':'auto'});
				});
				$('.JS-dbMask').height(dh).show();
				$('body').on('click', '.JS-dbMask', function(){
					this.style.display = 'none';
					box.css('top', '-35%');
					$('body').css({'overflow':'auto'});
				});
			}
		});

		$('body').on('click', '.J_unfoldWrap h2', function(){
			var _self = $(this), ajaxUrl = _self.attr('data-ajaxUrl'),
				_desc = _self.next('.desc'), _itemWrap = _desc.find('ul');

			if(ajaxUrl && !_self.hasClass('done')){
				_itemWrap.addClass('items-loading');
				$.post(
					GOU.fullurl(ajaxUrl, true), {token:token},
					function(data){
						data = JSON.parse(data);
						if(data.success){
							render({
								tempId: $('.J_unfoldWrap script'),
								wrap: _itemWrap, multiChild: true
							}, data);
							_itemWrap.removeClass('items-loading');
							if(data.data.hasnext==true && !_desc.find('.J_loadMore')[0])
								_itemWrap.after('<div class="btn items J_loadMore"><span class="rount-rect gray">加载更多...</span></div>');
							_self.attr('data-hasnext', data.data.hasnext)
								 .attr('data-curpage', data.data.curpage);
						}
					}
				);
				_self.addClass('done');
			}

			_self.hasClass('up')?
				_self.removeClass('up').addClass('down') :
				_self.removeClass('down').addClass('up');
			_desc.toggleClass('hidden');
		})
		.on('click', '.J_loadMore', function(){
			var _self = $(this), _itemWrap = _self.prev('ul'),
				infWrap, ajaxUrl, elTemp;

			infWrap = _self.hasClass('items')?
						_self.parent('.desc').prev('h2') : _self;
			ajaxUrl = infWrap.attr('data-ajaxurl');
			
			if(infWrap.attr('data-hasnext')=='true'){
				var curPage = infWrap.attr('data-curpage'),
					_page = curPage? (ajaxUrl.indexOf('?')<0? '?':'&')+'page=' + (parseInt(curPage)+1) : '';

				_self.removeClass('J_loadMore');
				_itemWrap.addClass('items-loading');
				$.post(
					GOU.fullurl(ajaxUrl+_page, true), {token:token},
					function(data){
						data = JSON.parse(data);
						if(data.success){
							render({
								tempId: 'templete',
								wrap: _itemWrap, multiChild: true
							}, data);
							_self.addClass('J_loadMore');
							_itemWrap.removeClass('items-loading');
							if(data.data.hasnext==false) _self.remove();
							infWrap.attr('data-hasnext', data.data.hasnext)
									  .attr('data-curpage', data.data.curpage);
						}
					}
				);
			}
		});

		/*
		 * cfg = {
		 *     wrap: 父层，没有设置则返回html
		 *     tempId: 模板ID（规则同_fnTmpl）
		 *     hooks: js-hooks，也可以设置伪属性
		 *     multiChild: 父层可非空渲染
		 *     callback: 渲染完成后执行回调函数
		 *     delayTime: 惰性加载img，推迟时间点
		 *     blankPic: 占位图片选择器
		 *     loadCallback: (内部使用)当页面模块化加载时，此为控制函数
		 * }
		 *
		 * before: 是否在旧元素前渲染
		 * clear: 是否先清空再渲染
		 */
		function render(cfg, data, before, clear){
			if(cfg && data){
				var pWrap = _queryOne(cfg.wrap, iCat.elCurWrap);
				iCat.isjQueryObject(pWrap) && (pWrap = pWrap[0]);

				var	o = document.createElement('wrap'),
					uncla = (cfg.viewId || (iCat.isString(cfg.tempId)? cfg.tempId:'wrap')) + '-loaded',
					oldNodes = _queryAll('*[data-unclass='+uncla+']', pWrap),
					isFirst = !oldNodes.length,
					curNode, html = '';

				try {// fixed bug:如果json不符合模版，报错(此问题已解)
					html = _fnTmpl(cfg.tempId)(data);
				} catch(e){}

				o.style.display = 'block';
				o.className = uncla;
				o.innerHTML = html;
			} else {
				// 如果没有数据，返回模板函数
				return _fnTmpl(cfg.tempId);
			}

			// 如果没有父层，返回html字符串
			if(!pWrap) return html;
			
			//辞旧
			if(clear || !cfg.multiChild){
				iCat.util.clearHtml(pWrap, uncla);
			}

			//迎新
			if(isFirst){
				before?
					pWrap.insertBefore(o, pWrap.firstChild) : pWrap.appendChild(o);
			}
			else {
				if(!pWrap.childNodes.length){
					pWrap.appendChild(o);
				} else {
					pWrap.insertBefore(o, oldNodes[0]);
					for(var i=oldNodes.length-1; i>=0; i--){
						if(!before){
							pWrap.removeChild(oldNodes[i]);
							o.insertBefore(oldNodes[i], o.firstChild);
						}
					}
				}
			}
			curNode = _queryOne('.'+uncla, pWrap);
			_unwrap(curNode);

			// 图片默认惰性加载
			_lazyLoad(pWrap, cfg.delayTime, cfg.blankPic);
			o = null;

			// 回调函数
			if(cfg.callback) cfg.callback(pWrap, cfg, data);
		}

		function _tmpl(tempId, data, strTmpl){
			if(!tempId) return;

			var cacheFuns = iCat.__cache_funs = iCat.__cache_funs || {},
				fnEmpty = function(){return '';},
				fBody;
			if(cacheFuns[tempId]){
				return data? cacheFuns[tempId](data) : cacheFuns[tempId];
			} else {
				if(!strTmpl) return fnEmpty;
				strTmpl = strTmpl.replace(/[\r\t\n]/g, '');
				fBody = "var __p_fun = [], _self = jsonData;with(jsonData){" +//typeof $1!='undefined' fixe bug:当json不包含某字段时，整个函数执行异常
							"__p_fun.push('" + strTmpl.replace(/<%=(.*?)%>/g, "',(typeof $1!='undefined'? $1:''),'").replace(/<%(.*?)%>/g, "');$1__p_fun.push('") + "');" +
						"};return __p_fun.join('');";
				
				cacheFuns[tempId] = new Function("jsonData", fBody);
				return data? cacheFuns[tempId](data) : cacheFuns[tempId];
			}
		}

		/*
		 * 根据tempId获得模板函数
		 * tempId可以是字符串ID，jquery对象，dom对象
		 */
		function _fnTmpl(tempId){
			tempId = iCat.isString(tempId)? tempId.trim() : tempId;

			var cacheTmpls = iCat.__cache_tmpls = iCat.__cache_tmpls || {};
			var cacheFuns = iCat.__cache_funs = iCat.__cache_funs || {};
			var _fn, sTmpl;

			// tempId的解析
			if(cacheFuns[tempId]){// 已有模板函数
				_fn = cacheFuns[tempId];
			} else if(cacheTmpls[tempId]) {// 已有模板字符串
				_fn = _tmpl( tempId, undefined, cacheTmpls[tempId] );
				cacheFuns[tempId] = _fn;
			} else if(tempId.hasClass || iCat.isjQueryObject(tempId)){// jquery对象
				_fn = _tmpl( tempId, undefined, tempId.html() );
				cacheFuns[tempId] = _fn;
			} else if(iCat.isString(tempId) || iCat.isObject(tempId)){// dom/选择器/id
				var el = iCat.isObject(tempId)?
						tempId : /\.|#|\[[\w\$\^\*\=]+\]/.test(tempId)?
							_queryOne(tempId) : document.getElementById(tempId);
				sTmpl = el? el.innerHTML.replace(/[\r\t\n]/g, '').replace(/\s+(<)|\s*$/g, '$1') : '';
				cacheFuns[tempId] = _fn = _tmpl(tempId, undefined, sTmpl);
				cacheTmpls[tempId] = sTmpl;
			}

			return _fn;
		}

		function _queryAll(selector, context){
			if(!selector) return [];
			return iCat.isString(selector)?
					(context || iCat.elBodyWrap || document).querySelectorAll(selector) : selector;
		}

		function _queryOne(selector, context){
			if(!selector) return;
			if(iCat.isString(selector)){
				selector = /\:[\d]+/.test(selector)?
					selector.replace(/(\:[\d]+).*/g, '$1').split(':') : [selector];
				return _queryAll(selector[0], context)[ selector[1] || 0 ];
			} else
				return selector.length? selector[0] : selector;
		}

		function _unwrap(el){
			if(!el) return;

			var p = el.parentNode,
				uncla = el.className,
				nodes = el.childNodes;
			while(nodes.length>0){
				if(uncla){
					nodes[0].setAttribute('data-unclass', uncla);
				}
				p.insertBefore(nodes[0], el);
			}
			p.removeChild(el);
		}
	}

	// 花费充值
	if(/\/recharge\//.test(locationHref)){
		// 使用淘宝充值时，提示用户并自动跳转至淘宝页面，隐藏自己的充值页面
		var remindTip = $('#J_remindTip');
		if (remindTip.length) {
			var tip = $(remindTip[0]).find('.tip');
			var time = 3;
			var timer = setInterval(function() {
				if (time === 0) {
					clearInterval(timer);
					tip.remove();
				}
				time--;
			}, 1000);
		}

		// 话费充值模块
		var rechargeForm = $('#recharge-form');
		if (rechargeForm[0]) {
			var recharge_phone = function() {

				var REMIND_MSG = {
					ERROR_MSG: {
						'NONE': '手机号码不能为空',
						'INVALID': '请输入正确的手机号码',
						'NOLONG': '手机号码位数不够'
					},
					LOADING_MSG: '加载中...' 
				};
				var PHONE_LENGTH = 11; // 手机号码长度

				var j_phone_clone = $('#j-phone-clone');
				var j_price = $('#j-price');

				var isVailable = false; // 号码是否可用
				var isSubmit = true;  // 控制不能重复提交

				return {
					init: function() {
						var that = this;
						that.trigger();
					},
					// 格式化phone
					format: function(el) {
						var that = this;
						var val = $.trim($(el).val().replace(/[^0-9]/gi, ''));
						$(el).val(val);
						$(el).val().length > PHONE_LENGTH ? $(el).val(val.substring(0, PHONE_LENGTH)) : '';
					},
					formatPrice: function(inprice) {
						var str = '';
						if (inprice.indexOf('-') == -1) {
							str = '￥' + inprice + '元'
						} else {
							str = '￥' + inprice.split('-')[0] + '-' + inprice.split('-')[1] + '元';
						}
						return str;
					},
					getData: function(callback) {
						var that = this;
						var j_phone = $('#j-phone');
						var j_cardnum = $('#j-cardnum');
						var len;
						j_price.html(REMIND_MSG.LOADING_MSG);
						$.get('/api/recharge/info?phone=' + j_phone.val() + '&cardnum=' + j_cardnum.val(), function(result) {
							var result = JSON.parse(result);				
							var val = j_phone.val() + '';
							if (result.success) {
								j_price.html(that.formatPrice(result.data.inprice));
								if (val !== '') {
									var str = val.substring(0, 3) + ' ' + val.substring(3, 7) + ' ' + val.substring(7);
									j_phone_clone.html(str + ' <span style="color:#888">(' + result.data.area + ')</span>').show();
									isVailable = true;
									$('#j-phone').hide();
								} else {
									isVailable = false;
								}
							} else {
								j_price.html(that.formatPrice(result.data.inprice));
								len = val.length;
								len>0 && len<PHONE_LENGTH ? GOU.showtip(REMIND_MSG.ERROR_MSG.NOLONG) : GOU.showtip(result.msg);
							}
						});
					},
					verify: function(p, c) {
						if (p === '') {
							GOU.showtip(REMIND_MSG.ERROR_MSG.NONE);
							return false;
						}
						if (p.length < PHONE_LENGTH) {
							GOU.showtip(REMIND_MSG.ERROR_MSG.NOLONG);
							return false;
						}
						if (/^\d{11}/.test(p) && c !== REMIND_MSG.LOADING_MSG) {
							return true;
						} else {
							GOU.showtip(REMIND_MSG.ERROR_MSG.INVALID);
						}
					},
					submitForm: function(phone, cardnum) {
						var that = this;
						var action = rechargeForm.attr('action');
						var channel_id = rechargeForm.attr('channel-id');
						if (that.verify(phone, cardnum)) {
							var args = {
								token: token,
								channel_id: channel_id,
								phone: phone, 
								cardnum: cardnum
							};
							isSubmit = false;
							$.post(action, args, function(data){
								data = JSON.parse(data);
								if(data.success){
									location.href = data.data.url;
								} else {
									GOU.showtip(data.msg);
								}
								isSubmit = true;
							});
						}
					},
					trigger: function() {
						var that = this;
						// 输入号码
						rechargeForm.on('input', '#j-phone', function(e) {
							that.format(this);
							if (/^\d{11}/.test($(this).val())) {
								that.getData();
							}
						});
						// 显示归属地切换
						rechargeForm.on('blur', '#j-phone', function(e) {
							if (/^\d{11}/.test($(this).val()) && isVailable) {
								$(this).hide();
								j_phone_clone.show();
							}
							
						});
						// 显示归属地切换
						rechargeForm.on('click', '#j-phone-clone', function(e) {
							$(this).hide();
							$('#j-phone').show().focus();
						});

						// 选择金额
						rechargeForm.on('click', '.num-list li', function(e) {
							var li = $(this);
							var num = $.trim(li.attr('data-num'));
							li.closest('.num-list-wrap').find('li').removeClass('active');
							li.addClass('active');
							$('#j-cardnum').val(num);
							that.getData();
						});

						// 提交验证表单
						rechargeForm.on('click', '#j-recharge-submit', function(e) {
							e.preventDefault();
							if (isSubmit) {
								var j_phone = $('#j-phone');
								var j_cardnum = $('#j-cardnum');
								that.submitForm(j_phone.val(), j_cardnum.val());
							}
						});

						// 提交按钮的按下效果
						rechargeForm.on('touchstart, touchmove, mousedown, mousemove', '#j-recharge-submit button', function(){
							var that = $(this);
							that.addClass('active');
							setTimeout(function(){ that.removeClass('active'); }, 500);
						}).on('touchend, mouseup', function(){
							$(this).removeClass('active');
						});
					}
				}
			}();
			recharge_phone.init();
		}

		return;
	}

	// 分类搜索
	if (/\/type\//.test(locationHref) && $('#J_categoryPage').length) {
		var categoryPage = $('#J_categoryPage');
		if (categoryPage.length) {

			// 搜索按钮
			$('#J_searchBtn').on('click', function(e) {
				$(this).parent('form').submit();
			});

			iCat.include('MVC', function(){

				var rTypeId = /\?.*id=(\d+)/,
					categoryNav = $('#J_categoryNav'),
					_ajaxurl,
					nav_id,
					li;
				
				if (location.href.match(rTypeId) != null) {
					nav_id = RegExp.$1;
				} else {
					nav_id = iCat.util.storage('category_nav_id') || categoryNav.find('li').first().attr('data-id');
				}

				iCat.util.removeData(':dv');
				li = categoryNav.find('li[data-id="' + nav_id + '"]');
				_ajaxurl = li.attr('data-link');
				li.addClass('active').siblings().removeClass('active');

				Dview = iCat.View.extend({
					config: {
						tmplId: 'J_dtemplate',
						wrap: '#J_goodsWrap',
						ajaxUrl: _ajaxurl,
						__base_ajaxurl: _ajaxurl,
						clear: false,
						ajaxConfig: {
							data: {token: token}
						},
						events: [
							{selector: '#J_categoryNav li', type:'click', callback:'loadList'}
						]
					},
					tmplData: {
						blankPic: iCat.PathConfig.picPath + 'blank.gif'
					},
					loadList: function(el) {
						var that = this,
							cfg = that.config,
							$el = $(el),
							url = $el.attr('data-link'),
							id = $el.attr('data-id');
						if($el.hasClass('active')) return;
						$el.addClass('active').siblings().removeClass('active');
						// 清空list
						$('#J_goodsWrap').empty();
						
						delete cfg.__hasnext; cfg.mustdo = true;
						cfg.firePulling = function(){
							delete cfg.mustdo;
							delete cfg.firePulling;
						};
						iCat.util.removeData(':dv');
						that.setConfig({
							ajaxUrl: url,
							__base_ajaxurl: url,
						})
						iCat.util.storage('category_nav_id', id);
					}
				});

				var Dctrl = iCat.Controller.extend({
					config: {View: Dview, baseBed: 'body'},
					routes: {
						'': 'detailInit'
					},
					detailInit: function(){
						this.init({
							modules: ['dv']
						})
					}
				});

				new Dctrl('dc');
			});
		}

		return;
	}
	// 专题-分享事件
	if (/\/topic/.test(locationHref)) {
		openShareEvt()	// 绑定分享按钮
		shareBindEvent();	// 绑定分享
		voteCache(); // 投票
	}

	// 物语-知物详情-知物评论
	if (/\/story\//i.test(locationHref)) {
		var storyDetail = $('#J_story_detail');
		var evtMethod = keyword === 'ios' ? 'tap' : 'click';
		window.COMMON_INTERFACE || (window.COMMON_INTERFACE = {});

		// 评论点赞功能
		body.on(evtMethod, '.J_praise', function(e) {
			e.stopImmediatePropagation();
			var $this = $(this);
			var praiseEl = $this.find('.icon-praise');
			var item_id = $this.attr('data-item-id');
			var storyId = '';
			if (praiseEl.hasClass('on')) return;
			if (/id=(\d+)/.test(locationHref)) {
				storyId = RegExp.$1;
			}
			if (keyword == 'ios') {
				GOU.baiduStatic('知物ios','知物ios-评论点赞', '知物ios-评论点赞-帖子('+ storyId +')');
			} else {
				GOU.baiduStatic('知物android','知物android-评论点赞', '知物android-评论点赞-帖子('+ storyId +')');
			}

			praiseEl.addClass('on');
			var praiseCountEl = $this.find('.J_praiseCount'),
				count = $this.attr('data-praise') || 0,
				TextAnimate = $('<span class="text-animate">+1</span>').appendTo($this);

			setTimeout(function() {
				TextAnimate.addClass('on');
				praiseCountEl.text(numToText(count*1 + 1));
			}, 50)

			$.get(GOU.fullurl('/api/@comment/like?type=1&item_id=' + item_id));
		});

		// 判断版本
		var praiseText = function(praise, id) {
			var txt = ['<div data-praise="' + praise + '" data-item-id="' + id + '" class="praise-wrap J_praise">',
					'<span class="praise-count J_praiseCount">' + (numToText(praise) || '') + '</span>',
					'<i class="icon-praise"></i>',
				'</div>'];

			return txt.join('');
		}

		if (keyword != 'ios') {
			if (storyDetail.length) {
				var img;
				var share = window.share;
				if (img = $('img')[0]) share && share.setmThumbBitmap && share.setmThumbBitmap(img.src);

				var storyComments = $('#J_story_comments');
				if (storyComments.length) {
					body.on('click', '.comments-item', function(e) {
						e.stopImmediatePropagation();
						// 调用客户端接口
						window.share && window.share.loadCommentsList && window.share.loadCommentsList();
					});
					window.COMMON_INTERFACE.loadComments = function() {
						var itemId = storyComments.attr('data-item');
						var commentList = storyComments.find('.comments-list');
						var loadingPic = [
							'<div class="dot-loading">',
								'<label>加载中</label>',
								'<span>.</span><span>.</span><span>.</span>',
							'</div>'
						].join('');
						
						$.ajax({
							url: GOU.fullurl('/api/@comment/list'),
							type: 'POST',
							data: {
								item_id: itemId,
								type: 'story',
								perpage: 3
							},
							beforeSend: function() {
								commentList.empty().html(loadingPic);
							},
							success: function(result) {
								result = JSON.parse(result);
								if (result.success) {
									var i = 0, html = [],
										len = result.data.list.length;

									if (len) {
										for (; i<len; i++) {
											var data = result.data.list[i];
											var temp = ['<div class="comments-item">',
												'<ul>',
													'<li>',
														'<h3>' + data.nickname + '</h3>',
														'<span class="date">' + data.create_time + '</span>',
													'</li>',
													'<li>' + (GOU.isVailableVersion('2.4.8') ? praiseText(data.praise, data.id) : '') + '</li>',
												'</ul>',
												'<p>' + data.content + '</p>',
											'</div>'];
											html = html.concat(temp);
										}
									} else {
										html = ['<div class="no-result">暂无评论</div>'];
									}
									commentList.empty().html(html.join(''));
								}
							}
						})
					}
					window.COMMON_INTERFACE.loadComments();
				}
				openShareEvt()	// 绑定打开分享事件
				shareBindEvent({
					pic: (function(){
						var imgs = $('img');
						return imgs.length ? imgs[0].src : '';
					}())
				}); // 绑定分享事件
				// 从缓存中获取已点赞
				voteCache();
			}
		}
		if (keyword == 'ios') {
			var commentEl = $('#J_story_comments');
			var getLocation = function(element) {
				if(element == null) return null;
				var offsetTop = element.offsetTop;
				var offsetLeft = element.offsetLeft;
				while(element = element.offsetParent) {
					offsetTop += element.offsetTop;
					offsetLeft += element.offsetLeft;
				}
				var o = {};
				o.left = offsetLeft;
				o.top = offsetTop;
				return o;
			}
			// 供ios客户端调用
			window.COMMON_INTERFACE.postComment = function(nickname, content, id) {
				var isBlank = $('#J_story_comments').find('.comment-blank');
				if (isBlank.length) isBlank.remove();
				var temp = ['<div class="comments-item">',
					'<ul>',
						'<li>',
							'<h3>' + nickname + '</h3>',
							'<span class="date">刚刚</span>',
						'</li>',
						'<li>' + (GOU.isVailableVersion('1.4.0') ? praiseText(0, id) : '') + '</li>',
					'</ul>',
					'<p>' + content + '</p>',
				'</div>']; 
				commentEl.find('.comments-list').prepend(temp.join(''));
				window.COMMON_INTERFACE.positionAnchor();
			}
			// 供ios客户端调用
			window.COMMON_INTERFACE.positionAnchor = function() {
				var offset = getLocation(commentEl[0]),
					elTop = offset.top - screen.availHeight/2,
					wTop = window.scrollY,
					diff = Math.abs(elTop - wTop),
					step = Math.floor(diff/(1000/10)),
					sum = 0,
					intval;

				intval = setInterval(function() {
					sum += step;
					if (0 < step && sum < diff) {
						elTop > wTop ? window.scrollTo(0, wTop + sum) : window.scrollTo(0, wTop - sum);
					} else {
						clearInterval(intval);
						intval = null;
					}
				}, 10)
			}
		}

		return;
	}

	if (/\/cut\/detail/.test(locationHref)) {
		// 砍价详情
		var cutBtn = $('#J_cutBtn'),
			goodsId = cutBtn.attr('data-id'),
			ajaxUrl = cutBtn.attr('data-ajaxurl');

		var buyUrl, cutUrl,
			isCut, isBuy, isClient, cutCode;

		var updateStatus = function(data) {
			var cutLegend = $('#J_legend');
			var cutCode = data['cut_code'];
			$('#J_info').html(data['cut_info']);
			$('#J_price').html(data['current_price']);
			cutBtn.html(data['cut_msg']);

			isBuy = data['is_buy'];
			buyUrl = data['buy_url'];
			isCut = data['is_cut'];
			cutUrl = data['cut_url'];
			if (data['is_buy']) {
				if ( isClient && cutCode == 4) {
					$('#J_share').addClass('dis').find('p').html('不能再低了')
				}
			} else if(data['is_cut']){
				
			} else {
				cutBtn.addClass('dis');
				$('#J_share').addClass('dis');
			}
		}
		var postHttp = function(type, url, data, callback) {
			data = data || {};
			$.ajax({ type: type, url: url, data: data,
				success: function(data) {
					data = JSON.parse(data);
					callback(data);
				}
			})
		}
		// 初始化状态
		postHttp('POST', ajaxUrl, {token: token}, function(data) {
			if (data.success) {
				isClient = data.data['is_client'];
				updateStatus(data.data);
			}
			GOU.showtip(data.msg);
		});
		// 砍价事件
		cutBtn.click(function() {
			var that = $(this);
			if (that.hasClass('dis') || that.hasClass('suspend')) return;
			if (isBuy) {
				if (isClient) {
					location.href = buyUrl;
				} else {
					$('#J_downModal').show();
				}
			}
			if (isCut) postHttp('POST', cutUrl, {token: token}, function(data) {
				if (data.success) {
					GOU.showtip(data.msg);
					updateStatus(data.data);
				} else {
					GOU.showtip(data.msg);
				}
			});
		})
		// 只有新版本有qq好友与qq空间
		if (window.share && !window.share.share) {
			var sns = $('.J_shareToSns');
			sns.filter('[data-type="qzone"]').remove();
			sns.filter('[data-type="qq"]').remove();
		}

		if (keyword == 'apk') {

			var version,
				isNewVersion = false,
				shareNode = $('.J_shareToSns').parent();
			// 判断新旧版本，只有2.4.6以上的版本才统计每日分享砍价
			if (window.share && window.share.getVersionName && (version = window.share.getVersionName())) {
				var arr = version.split('.');
				arr[0]*1<2 ? isNewVersion = false : 
					arr[1]*1<4 ? isNewVersion = false : 
						arr[2]*1>=6 ? isNewVersion = true : false;
			}
			// 新版本
			if (isNewVersion) {
				$('#J_share').click(function() {
					if($(this).hasClass('dis')) return;

					var price = $('#J_price').html(),
						origPrice = $('#J_origPrice').html(),
						shareCont = ' 大伙快来帮我砍价，原价'+ origPrice +'，现在只有￥' + price + '了，点点就可以砍价了',
						title = shareNode.attr('data-title') || '',
						pic = shareNode.attr('data-pic') || '';

					showWebShareDialog(pic, title, '', shareCont);
					return false;
				})
			} else {
				// 分享
				$('#J_share').click(function() {
					if($(this).hasClass('dis')) return;
					$('#J_shareModal').show();
				})

				shareBindEvent(function() {
					var price = $('#J_price').html(),
						origPrice = $('#J_origPrice').html(),
						shareCont = ' 大伙快来帮我砍价，原价'+ origPrice +'，现在只有￥' + price + '了，点点就可以砍价了',
						source = '<a href="gou.gionee.com">购物大厅<a>';

					return {
						site: source,
						source: source,
						desc: shareCont,
						summary: shareCont
					}
				})
			}

		}
		
		// $('.J_iosShareToSns').tap(function (e) {
		body.on('tap', '.J_iosShareToSns', function (e) {
			var $this = $(this);
			if ($this.hasClass('dis')) return;
			var	title = $this.attr('data-title'),
				pic = $this.attr('data-pic'),
				url = $this.attr('data-url'),
				price = $('#J_price').html(),
				origPrice = $('#J_origPrice').html(),
				content = ' 大伙快来帮我砍价，原价'+ origPrice +'，现在只有￥' + price + '了，点点就可以砍价了';

			iosShareToSns({
				'title': title, 
				'content': content, 
				'pic': pic, 
				'url': url
			});
			return false;
		})

		return;
	}

	// 砍价创建订单页面
	// 砍价-修改订单信息
	if (/\/cut\/buy/.test(locationHref) || /\/cutorder\/edit/.test(location.href)) {
		var form = $('form'),
			elems = form[0].elements,
			formSubmit = $('#J_formSubmit'),
			sureModal = $('#J_sureModal'),
			args;
			
		$.each(elems, function(elem) {
			if (elem.onchange) elem.onchange = validator(elem)
		});
		// 更新按钮的状态
		var updateState = function() {
			$.post(formSubmit.attr('data-ajaxurl'), function(data) {
				data = JSON.parse(data);
				if(data.success) {
					var code = data.data['cut_code'];
					if (code == 2 || code == 5 || code == 6) {
						formSubmit.addClass('dis').html(data.data['cut_msg']);
					} else if ((code == 3 || code == 4) && data.data['buy_url']) {
						formSubmit.html('立即下单');
					} else {
						formSubmit.html(data.data['cut_msg']);
					}
				}
			})
		}
		if (/\/cut\/buy/.test(location.href)) updateState();

		$('#J_reset').click(function(){
			var hiddenInput, gid, select;
			hiddenInput = form.find('input[name="goods_id"]');
			gid = hiddenInput.val();
			form.find('input').val('');
			hiddenInput.val(gid);
			select = form.find('select');
			select[0].selectedIndex = select[1].selectedIndex = 0;
			select[2].innerHTML = '';
		})

		var submitOrder = function(args) {
			args['token'] = token;
			$.ajax({
				url: form.attr('action'),
				data: args,
				type: 'POST',
				beforeSend: function() {formSubmit.addClass('suspend')},
				complete: function() {formSubmit.removeClass('suspend')},
				success: function(data) {
					data = JSON.parse(data);
					if(data.success){
						location.href = data.data.url;
					} else {
						if (/\/cut\/buy/.test(location.href)) updateState();
						GOU.showtip(data.msg);
					}
				}
			})
		}

		formSubmit.click(function() {
			var valid = true;
			var name;
			args = {};
			if (formSubmit.hasClass('dis') || formSubmit.hasClass('suspend')) return;
			$.each(elems, function() {
				if (!validator(this)) {
					valid = false;
					return false;
				}
				if (name = this.getAttribute('name')) args[name] = this.value;
			})
			if (valid) {
				if ($('#J_sureSubmit').length) {
					sureModal.show();
				} else {
					submitOrder(args);
				}
			}
		})
		
		$('#J_sureSubmit').tap(function(e) {
			e.stopImmediatePropagation();
			submitOrder(args);
			setTimeout(function() {
				sureModal.hide();
			}, 300);
		})

		return;
	}

	// 砍价-订单列表
	if (/\/cutorder\/list/i.test(locationHref)) {
		body.on('tap', '.J_cancel', function(e) {
			var $this = $(this);
			var url = $this.attr('data-url');
			$.post(url, {token: token}, function(data){
				data = JSON.parse(data);
				if (data.success) {
					$this.closest('li').find('.status').html('订单关闭');
					$this.parent('.handle').remove();
				} else {
					GOU.showtip(data.msg);
				}
			})
			return false;
		})
		// 晒单
		body.on('tap', '.J_hotOrder', function(e) {
			e.stopImmediatePropagation();
			var $this = $(this),
				oid = $this.attr('data-oid') || '',
				id = $this.attr('data-id') || '',
				nickname = $this.attr('data-nickname') || '';

			GOU.baiduStatic('砍价订单android','砍价订单android-晒单');
			window.share && window.share.gotoHotOrderInterface && window.share.gotoHotOrderInterface(oid, id, nickname);
		});

		body.on('tap', '.J_orderPay', function() {
			var $this = $(this);
			var url = $this.attr('data-url');
			$.get(url, function(data){
				data = JSON.parse(data);
				if (data.success) {
					location.href = data.data.url;
				} else {
					GOU.showtip(data.msg);
				}
			})
			return;
		})
		// v2.4.8以上版本并且第一次出现晒单时出现提醒
		if (GOU.isVailableVersion('2.4.8')) {
			var storagekey = 'gou-cut-order-guide';
			setTimeout(function() {
				if ($('.J_hotOrder').length) {
					if (iCat.util.storage('gou-cut-order-guide') == null) {
						var maskDiv = $('<div class="guide-mask"><div class="mask"></div></div>');
						maskDiv[0].addEventListener('touchstart', function() {
							maskDiv.remove();
						}, false);
						body.append(maskDiv);
						iCat.util.storage('gou-cut-order-guide', true);
					}
				}
			}, 1500);
		}

		return;
	}

	// 积分系统-我的成就
	if (/\/score\/summary/.test(locationHref)) {
		if (keyword == 'apk') {
			shareBindEvent();
		} else if (keyword == 'ios') {

			body.on('tap', '.J_shareToSns', function(e) {
				var $this = $(this);
				var shareNode = $this.parent();
				var type = shareType[$this.attr('data-type')];

				iosShareToSns({
					title: document.title,
					content: '购物大厅',
					pic: shareNode.attr('data-pic') || '',
					url: shareNode.attr('data-url') || '',
					type: type || ''
				});
			})
		}

		return;
	}
	// 积分系统
	if (/\/user_uid\/edit|\/user_uid\/modify/.test(locationHref) || (iCat.DemoMode && /\/score\/edit/.test(locationHref))) {
		var editForm = $('#J_editFrom');
		var submitBtn = $('#J_submitBtn')
		var elems = editForm[0].elements;

		$.each(elems, function(elem) {
			$(this).change(function() {
				validator(this)
			})
		});

		submitBtn.click(function() {
			var valid = true;
			var args = {};
			var name;
			$.each(elems, function() {
				if (!validator(this)) {
					valid = false;
					return false;
				}
				args[this.getAttribute('name')] = this.value;
			})
			args['token'] = token;
			if (valid) {
				$.ajax({
					type: 'POST',
					data: args,
					url: editForm.attr('action'),
					success: function(result) {
						if (!$.isPlainObject(result)) result = JSON.parse(result);
						if (result.success) {
							setTimeout(function() {
								var ind = location.href.indexOf('backurl');
								if (keyword == 'apk') {
									ind > -1 ? history.go(-1) : window.share && window.share.exitWebView && window.share.exitWebView();
								} else if (keyword == 'ios') {
									if (ind > -1) {
										location.href = 'objc://goBack';
									} else {
										location.href = 'objc://exitWebView';
									}
								}

							}, 1000)
							GOU.showtip(result.msg);
						} else {
							GOU.showtip(result.msg);
						}
					},
					error: function() {
						GOU.showtip('请求失败，请重试');
					}
				})
			}
		})

		return;
	}
	// 积分系统
	if (/\/score\/rank/.test(locationHref) || /\/score\/task/.test(locationHref)){
		// 关闭
		document.body.addEventListener('touchstart', function(e) {
			var $target = $(e.target);
			if ($target.attr('id') == 'J_closeToEdit') {
				$target.parent().remove();	
			}
		}, false);
		// body.on('click', '#J_closeToEdit', function() {
		// 	$(this).parent().remove();
		// })
		return;
	}

	// 积分系统每日任务
	if (/\/score\/task/.test(locationHref)){
		callClientShare();
		return;
	}
	// 积分系统个人信息
	if (/\/score\/profile/i.test(locationHref)){
		body.on('tap', '#J_todo', function() {
			try {
				$(this).find('.focus').remove()
			} catch(e) {}
		})
		return;
	}

	// 砍价游戏-获奖信息编辑
	if (/\/winprize\/award(?!list)/i.test(location.pathname)) {

		var form = $('form'),
			elems = form[0].elements,
			formSubmit = $('#J_formSubmit'),
			args,
			directUrl = formSubmit.attr('data-direct-url')
			;

		$.each(elems, function(elem) {
			if (elem.onchange) elem.onchange = validator(elem)
		});

		$('#J_reset').click(function(){
			var hiddenInput, gid, select;
			hiddenInput = form.find('input[name="goods_id"]');
			gid = hiddenInput.val();
			form.find('input').val('');
			hiddenInput.val(gid);
			select = form.find('select');
			select[0].selectedIndex = select[1].selectedIndex = 0;
			select[2].innerHTML = '';
		})

		var submitOrder = function(args) {
			args['token'] = token;
			$.ajax({
				url: form.attr('action'),
				data: args,
				type: 'POST',
				beforeSend: function() {formSubmit.addClass('suspend')},
				complete: function() {formSubmit.removeClass('suspend')},
				success: function(data) {
					data = JSON.parse(data);
					if(data.success){
						GOU.showtip(data.msg);
						setTimeout(function() {
							location.href = directUrl;
						}, 1500);
					} else {
						GOU.showtip(data.msg);
					}
					GOU.addBaiduStatic('CUTGAME_AWARD_RECORD');
					// GOU.baiduStatic('我的获奖记录','我的获奖记录-领取大奖','我的获奖记录-领取大奖-点击确定按钮次数');
				}
			})
		}

		formSubmit.click(function() {
			var valid = true;
			var name;
			args = {};
			if (formSubmit.hasClass('dis') || formSubmit.hasClass('suspend')) return;
			$.each(elems, function() {
				if (!validator(this)) {
					valid = false;
					return false;
				}
				if (name = this.getAttribute('name')) args[name] = this.value;
			})
			if (valid) {
				submitOrder(args);
			}
		});
		return;
	}

	// 砍价游戏-分享页面
	if (/\/winprize\/refuel/i.test(location.pathname)) {
		var refuelBtn = $('#J_refuel'),
			url = refuelBtn.attr('data-ajaxurl'),
			statusUrl = refuelBtn.attr('data-status-ajaxurl'),
			downloadUrl = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.gionee.client'
			;

		// 查询状态
		$.ajax({
			url: statusUrl,
			type: 'GET',
			success: function(result) {
				result = parseJSON(result);
				// GOU.baiduStatic('砍价游戏分享','砍价游戏分享-页面打开','砍价游戏分享-页面打开-页面打开次数');
				GOU.addBaiduStatic('CUTGAME_REFUEL_OPEN');
				if (result.success) {
					refuelBtn.attr('class', 'side ' + result.data[0]);	
				}
			}
		});

		refuelBtn.click(function() {
			var $this = $(this);

			if ($this.hasClass('want')) {
				// GOU.baiduStatic('砍价游戏分享','砍价游戏分享-我也要玩','砍价游戏分享-我也要玩-我也要玩点击次数');
				GOU.addBaiduStatic('CUTGAME_REFUEL_WANT');
				location.href = downloadUrl;
				return;
			}
			if ($this.hasClass('cheer')) {
				$.ajax({
					url: url,
					type: 'POST',
					success: function(result) {
						result = parseJSON(result);
						if (result.success) {
							GOU.showtip('成功为好友增加10点速度值');
							$this.removeClass('cheer').addClass('want');
							GOU.addBaiduStatic('CUTGAME_REFUEL_CHEER');
							// GOU.baiduStatic('砍价游戏分享','砍价游戏分享-为他助力','砍价游戏分享-为他助力-为他助力次数');
						} else {
							GOU.showtip(result.msg);
						}
					}
				})
			}
		})
		return;
	}

	// 砍价游戏-显示恭喜中奖提示
	if (/\/winprize\/activity/i.test(location.pathname)) {
		var winning = $('.game-winning');
		
		if (!winning.length) {
			return;
		}
		winning.show();
		winning.tap(function() {
			winning.hide();
		})
		setTimeout(function() {
			winning.hide();
		}, 5000);
		// 发请求标记只显示一次
		$.get(GOU.fullurl('/api/@winprize/closeTip'), function(){});
		return;
	}

	if (/\/prod/i.test(location.pathname)) {

		var addprodUrl = GOU.fullurl('/api/@favorite/addprod');
		var removeProdUrl = GOU.fullurl('/api/@favorite/remove');
		var postServer = function(url, type, data, callback) {
			$.ajax({
				url: url,
				type: type,
				data: data,
				success: function(result) {
					if (typeof result === 'string') {
						result = JSON.parse(result);
					}
					if (result.success && callback) {
						callback(result.data);
					}
					GOU.showtip(result.msg);
				}
			})
		};

		// 分享
		body.on('click', '.J_shareSns', function(evt) {
			var $this = $(this),
				parentLi = $this.closest('li'),
				parentUl = $this.closest('ul'),
				shareUrl = parentUl.attr('data-share-url'),
				uid = parentUl.attr('data-uid'),
				id = parentLi.attr('data-item-id'),
				title = parentLi.find('.title').text(),
				pic = parentLi.find('img').attr('src'),
				url = shareUrl + '?id=' + id;
			
			GOU.addBaiduStatic('GOODPRODUCT-SHARE-BUTTON');	// 分享按钮点击统计

			$('#J_shareModal').show();
			shareBindEvent({
				pic: pic,
				title: title,
				url: url
			});
			evt.stopPropagation();
			return false;
		});

		body.on('click', '.J_collect', function(evt) {

			var $this = $(this),
				id = $this.attr('data-fav-id'),
				item_id = $this.attr('data-item-id'),
				collectIcon = $this.find('.icon-collect');

			GOU.addBaiduStatic('GOODPRODUCT-SHARE-COLLECT');	// 收藏按钮点击统计

			if (collectIcon.hasClass('active')) {
				// 取消收藏
				postServer(GOU.fullurl('/api/@favorite/remove'), 'POST', {
					id: id,
					item_id: item_id,
					type: 2
				}, function(data) {
					$this.attr('data-fav-id', '');
					collectIcon.removeClass('active');
				});
			} else {
				// 收藏
				postServer(GOU.fullurl('/api/@favorite/addprod'), 'POST', {
					item_id: item_id
				}, function(data) {
					$this.attr('data-fav-id', data.fav_id);
					collectIcon.addClass('active');
				});
			}
			return false;
		});
	}

})(ICAT);