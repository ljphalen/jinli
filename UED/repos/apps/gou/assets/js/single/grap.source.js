(function(global) {

	/***************************** 公用函数 ******************************************************/

	var prefix = 'apk';

	if (/(iPhone|iPad|iPod|iOS)/i.test(navigator.userAgent)) {
	    prefix = 'ios';
	} else if (/(Android)/i.test(navigator.userAgent)) {
	    prefix = 'apk';
	}

	var gouhost = 'http://' + prefix + '.gou.gionee.com/',
		assetsHost = 'http://assets.gionee.com/apps/gou/assets/';

	if (isDebug()) {
		gouhost = 'http://' + prefix + '.gou.3gtest.gionee.com/';
		assetsHost = 'http://assets.3gtest.gionee.com/apps/gou/assets/';
	}

	function fullUrl(url) {
		return url ? url.replace(/@/, prefix + '_') : '';
	}
	function isDebug() {
		var src;
		try {
			src =  document.currentScript.src;
		} catch (e) {
			var scripts = document.scripts;
			var node = scripts[scripts.length-1];
			src = node.src;
		}
		return src && (src.indexOf('3gtest') > -1) ? true : false;
	}
	function domId(id) { return document.getElementById(id) }
	function className(className, context) { return (context || document).getElementsByClassName(className) }
	function tag(tagName, context) { return (context || document).getElementsByTagName(tagName) }
	function elem(tag) { return document.createElement(tag) }
	function fragment() { return document.createDocumentFragment() }
	function clone(ori) {
		var k, ret = {};
		for (k in ori) {
			ret[k] = ori[k];
		}
		return ret;
	}
	function addClass(cls) {
		var className = this.className;
		className && ((' ' + cls + ' ').indexOf(' ' + className + ' ') != -1) ? '' : 
			(this.className = className + ' ' + cls);
		return this;
	}
	function removeClass(cls) {
		var className = this.className;
		this.className = (' ' + className + ' ')
			.replace(new RegExp(' ' + cls + ' '), ' ')
			.replace(/^\s*|\s*$/g, '');
		return this;
	}
	function jsonp(url, callback) {
		var script = elem('script');
		var key = 'jsonpcallback' + Date.now();
		global[key]= function(data) {
			callback(data);
			domId('jsonp-script').remove();
		}
		url = url + (url.indexOf('?') > -1 ? '&' : '?') + 'callback=' + key;
		script.src = url;
		script.id = 'jsonp-script';
		tag('head')[0].appendChild(script);
	}
	function setStyle(style) {
		this.setAttribute('style', style);
		return this;
	}
	function params(obj) {
		var k, ret = [];
		for (var k in obj) ret.push(k + '=' + obj[k]);
		return ret.join('&');
	}
	function url2Params(url) {
		var seg, k, len, ret = {}, atag;
		atag = elem('a');
		atag.href = url;
		seg = atag.search.replace(/^\?/, '').split('&');
		for (k=0, len=seg.length; k<len; k++) {
			s = seg[k].split('=');
			ret[s[0]] = s[1];
		}
    	return ret;
	}
	function pick(obj, keys) {
		var result = {}, key;
		if (obj == null) return result;
		if (keys instanceof Array) {
			for (var i = 0, length = keys.length; i < length; i++) {
				key = keys[i];
				if (key in obj) result[key] = obj[key];
			}
		}
		return result;
	}

	// 判断当前版本是否是可用版本
	function isVailableVersion(version) {
		var curVersion = window.share && window.share.getVersionName && window.share.getVersionName();
		if (curVersion) {
			var cur = curVersion.split('.');
			var arr = version.split('.');
			if (cur[0]*1 < arr[0]*1) return false;
			if (cur[0]*1 > arr[0]*1) return true;
			
			if (cur[1]*1 < arr[1]*1) return false;
			if (cur[1]*1 > arr[1]*1) return true;

			if (cur[2]*1 < arr[2]*1) return false;
			if (cur[2]*1 >= arr[2]*1) return true;
		}
	}

	/******************************** 麻吉宝入口提示 ************************************************/
	if (/wao.m.taobao.com\/main\/index/.test(location.href)){
		var mjbPlugin = document.createElement('a');
		mjbPlugin.innerHTML = '<div>赚钱</div><div>攻略</div>';
		mjbPlugin.href = gouhost + 'jfb';
		mjbPlugin.setAttribute('style', 'display: inline-block; width: 60px; height: 60px; position: fixed; top: 60%; left: 8px; color: #ff6633; background: rgba(255,255,255,0.9); z-index: 1000; -webkit-border-radius: 45px; border-radius: 45px; font-size: 16px; text-align: center;  padding-top: 10px; -webkit-box-sizing: border-box;box-sizing: border-box; -webkit-box-shadow: 0px 1px 0px 0px #aaa;box-shadow: 0px 1px 0px 0px #aaa;');
		document.body.appendChild(mjbPlugin);
	}

	if (prefix === 'apk') {

		/********************************** 设置微信分享给客户端调用 *****************************/
		try {
			var setBitmapTimer = setTimeout(function() {
				if (global.share) {
					if (global.share.setmDescription) {
						var tagH1 = document.getElementsByTagName('h1');
						if (tagH1.length) {
							global.share.setmDescription(tagH1[0].innerText)
						}
					}
				}
			}, 1500)
		} catch(e) {}

		/******************************* 浏览历史记录 *****************************************************/
		var historyObj = {};
		var domainsList = {
			'jd.com': '\u4eac\u4e1c\u5546\u57ce', // 京东商城
			'tmall.com': '\u5929\u732b', //天猫
			'tmall.hk': '\u5929\u732b', //天猫
			'taobao.com': '\u6dd8\u5b9d', //淘宝
			'mmb.cn': '\u4e70\u5356\u5b9d', //买卖宝
			'vip.com': '\u552f\u54c1\u4f1a', //唯品会
			'meilishuo.com': '\u7f8e\u4e3d\u8bf4', //美丽说
			'mogujie.com': '\u8611\u83c7\u8857', //蘑菇街
			'yhd.com': '\u0031\u53f7\u5e97', //1号店
			'zch168.com': '\u4e2d\u5f69\u6c47', //中彩汇
			'meituan.com': '\u7f8e\u56e2\u7f51', //美团网
			'aicai.com': '\u7231\u5f69\u7f51', //爱彩网
			'taohwu.com': '\u6843\u82b1\u575e\u5546\u57ce', //桃花坞商城
			'ytao.cn': '\u79fb\u6dd8\u5546\u57ce', //移淘商城
			'aizhigu.com.cn': '\u7231\u4e4b\u8c37\u5546\u57ce', //爱之谷商城
			'tieyou.com': '\u94c1\u53cb\u7f51', //铁友网
			'moonbasa.com': '\u68a6\u82ad\u838e', //梦芭莎
			'zg51.net': '\u638c\u8d2d\u65e0\u5fe7', //掌购无忧
			'lashou.com': '\u62c9\u624b\u7f51', //拉手网
			'nuomi.com': '\u767e\u5ea6\u7cef\u7c73\u7f51', //百度糯米网
			'ly.com': '\u540c\u7a0b\u7f51', //同程网
			'lefeng.com': '\u4e50\u8702\u7f51', //乐蜂网
			'mbaobao.com': '\u9ea6\u5305\u5305', //麦包包
			'paixie.net': '\u62cd\u978b\u7f51', //拍鞋网
			'amazon.cn': '\u4e9a\u9a6c\u900a', //亚马逊
			'suning.com': '\u82cf\u5b81\u6613\u8d2d', //苏宁易购
			'gome.com.cn': '\u56fd\u7f8e\u5728\u7ebf', //国美在线
			'qunar.com': '\u53bb\u54ea\u513f\u7f51', //去哪儿网
			'dianping.com': '\u5927\u4f17\u70b9\u8bc4', //大众点评
			'yixiangsc.com': '\u6613\u4eab\u5546\u57ce', //易享商城
			'tiantian.com': '\u5929\u5929\u7f51', //天天网
			'm18.com': 'M18', //M18
			'gionee.com': '\u91d1\u7acb', //金立
			'm.vancl.com': '\u51e1\u5ba2',	// 凡客
			'm.dangdang.com': '\u5f53\u5f53\u7f51'
		}

		var matchesHistoryFn = function() {
			jsonp( gouhost + fullUrl('api/@config/index'), function(result) {
				if (typeof result != 'object') result = JSON.parse(result);
				if (result.success) {
					var obj = result.data.history;
					var i = 0, len = obj.length;
					for (; i<len; i++) {
						historyObj[obj[i].preg] = obj[i];
					}

					var historyArray = Object.keys(historyObj);
					var matchesHistory;
					var type, platform, title = document.title;
					var domainsReg;
					var matches;
					var locationHost = location.host;
					historyArray.forEach(function(k) {
						var reg = new RegExp(k);
						if (reg.test(decodeURIComponent(location.href))) {
							matchesHistory = historyObj[k]
							return false;
						}
					})

					var insertHistory = function(href, title, type, platform) {
						if (window.share && window.share.insertUrlHistory) {
							window.share.insertUrlHistory(location.href, title, type, platform);
						}
					}

					if (matchesHistory) {
						type = matchesHistory.type;
						platform = matchesHistory.src;
						// 淘宝商品详情时的页面title
						if (type == 'goods' && new RegExp(matchesHistory.preg).test(decodeURIComponent(location.href))) {
							var taobaoTitle;
							var it = setInterval(function() {
								try {
									taobaoTitle = tag('h1')[0].innerText;
									if (taobaoTitle) {
										clearInterval(it);
										it = null;
										// alert(type + ':' + platform + ':' + taobaoTitle + ':' + location.href);
										insertHistory(location.href, taobaoTitle, type, platform);
									}
								} catch (e){}
							}, 100);
							return;
						}
					} else {
						type = 'others';
						domainsReg = new RegExp(Object.keys(domainsList).map(function(k) {
							return '(' + k + ')';
						}).join('|'));
						try {
							matches = locationHost.match(domainsReg)[0];
						} catch(e){}
						platform = matches ? domainsList[matches] : locationHost.substring(locationHost.indexOf('.')+1);
					}
					// alert(type + ':' + platform + ':' + title + ':' + location.href);
					insertHistory(location.href, title, type, platform);
				}
			})
		}

		try {
			matchesHistoryFn();
		} catch(e) {}

	}

	/**********屏蔽买卖宝、京东、唯品会、当当、苏宁易购、国美客户端下载提示 ***************************/
	try {
		var locationhref = location.href;
		var hrefRegexp = /.*\.(mmb)\.cn\/|m\.(jd)\.com|sale\.(jd)\.com|m\.(vip)\.com|m\.(dangdang)\.com|m\.(gome)\.com\.cn|m\.(suning)\.com|m\.(kuaidi100)\.com/;
		var fn = function(platform) {
			var node;
			switch (platform) {
				case 'mmb':
					node = domId('top_divs');
				break;
				case 'jd':
					// 屏蔽jd客户端下载
					localStorage.setItem('downCloseDate', Date.now() + '_259200000');
					node = domId('index_banner');
					var downHeader = domId('down_app_header');
					if (downHeader && downHeader.parentNode) downHeader.parentNode.removeChild(downHeader);
				break;
				case 'vip':
					node = className('u-download-bar')[0].parentNode;
				break;
				case 'dangdang':
					node = className('dt_box')[0];
					var d = domId('app_download');
					if (d) d.parentNode.removeChild(d);
				break;
				case 'gome':
					node = domId('spt');
				break;
				case 'suning':
					node = className('top-dload')[0];
				break;
				case 'kuaidi100':
					node = domId('container');
				break;
			}
			if (node && node.parentNode) node.parentNode.removeChild(node);
		}

		var matches = locationhref.match(hrefRegexp);
		if (matches) {
			matches.forEach(function(match, k) {
				if (0 !== k && match) {
					fn(match);
					return false;
				}
			})
		}
	} catch(e) {}

	/******************************** 同款比价 ********************************************************/

	// fixed 客户端重复载入jsbug
	if (global.SameStyle) return;
	// 只有在客户端版本大于等于2.4.0的时候才执行
	if (prefix === 'apk') {
		if (!isVailableVersion('2.4.0')) return;
	}
	
	var readyRE = /complete|loaded/,
		intval,
		unipid,
		urlParams = url2Params(location.href),
		detailInfo,
		unfoldTimer,
		current_store;		// 当前店铺
		

	global.SameStyle = {};	// 全局函数，暴露给客户端调用

	var getunipidUrl = gouhost + fullUrl('api/@same/getunipid'),
		getPriceUrl = gouhost + fullUrl('api/@same/getLower'),
		samePlugin = {css: assetsHost + 'css/grap.plugin.css'};
	
	function unfold() {	// 展开
		var sp = domId('J_samePlugin');
		removeClass.call(sp, 'fold');
		addClass.call(sp, 'unfold');
		unfoldTimer = setTimeout(function() {fold()}, 7000);
	} 
	function fold() {  // 收缩
		var sp = domId('J_samePlugin');
		removeClass.call(sp, 'unfold');
		addClass.call(sp, 'fold');
	}

	function fixedFloat(amount, fixed) {
		return parseFloat(amount).toFixed(fixed);
	}

	function getLowPrice(price) {
		var ind;
		price = String(price);
		ind = price.indexOf('-');
		if (ind > -1) {
			price = price.substring(0, ind);
		}
		return price;
	}

	function parsePrice(price) {
		var ind, isDot;

		if (price == null || price == '') return '';
		price = getLowPrice(price);
		price = price * 1;
		isDot = /\./.test(price);

		if (price < 100) {
			return isDot ? fixedFloat(price, 2) : price;
		}
		if (price >= 100 && price < 1000) {
			return isDot ? fixedFloat(price, 1) : price;
		}
		if (price >= 1000 && price < 10000) {
			return Math.round(price);
		}
		if (price >= 10000 && price < 1000000) {
			return fixedFloat(price / 10000, 1) + '\u4e07';
		}
	}

	// 显示浮动图标
	function showPlugin(isPrice, price) {
		var link, frag, wrap;
		// 加载css
		link = elem('link');
		link.type = 'text/css';
		link.rel = 'stylesheet';
		link.href = samePlugin.css;
		tag('head')[0].appendChild(link);

		frag = fragment();
		wrap = elem('div');
		wrap.id = 'J_samePlugin';
		wrap.className = 'same-plugin';
		wrap.innerHTML = '<div class="side"></div><div class="cont">\u540c\u6b3e<span id="J_priceWrap" class="price-wrap"></span></div>';
		wrap.addEventListener('touchend', function(e) {
			e.stopPropagation();
			showSameStyleList();
		}, false);
		// wrap.onclick = function(e) { showSameStyleList() } 
		frag.appendChild(wrap);
		document.body.appendChild(frag);
	}
	// 调用客户端接口，显示同款列表
	function showSameStyleList() {
		var ret = '', infos;
		infos = getDetailInfo(getStoreName());
		
		if (infos) {
			infos.unipid = unipid;
			infos.img = infos['image_urls'][0].img;
			if (infos.channel == 'taobao') {
				infos.score = infos.score ? (infos.score + '\u597d\u8bc4') : infos.score;
			} else if (infos.channel == 'tmall') {
				infos.score = infos.score ? (infos.score + '\u5206') : infos.score;
			}
			ret = pick(infos, ['id', 'url', 'unipid', 'img', 'title', 'price', 'express', 'pay_num', 'score', 'area', 'channel']);
		}
		
		if (prefix === 'ios') {
			if (ret) {
				ret.url = encodeURIComponent(ret.url);
				ret = JSON.stringify(ret);
			}
			location.href = 'objc://showSameStyleList/' + ret;
		} else {
			if (ret) ret = JSON.stringify(ret);
			global.share && global.share.showSameStyleList && global.share.showSameStyleList(ret)
		}
	}

	// 调用商品加入比价列表
	global.SameStyle.addShopToList = function() {
		var ret = '', infos;
		infos = getDetailInfo(getStoreName());
		if (infos) {
			if (infos.channel == 'taobao') {
				infos.channel = '\u6dd8\u5b9d';
				infos.score = infos.score ? (infos.score + '\u597d\u8bc4') : infos.score;
				infos.is_high_light = false;
			} else if (infos.channel == 'tmall') {
				// infos.score = infos.score ? (infos.score + '\u5206') : infos.score;
				infos.channel = '\u5929\u732b';
				if (infos.score) {
					infos.is_high_light = (infos.score >= 4.8 ? true : false);
					infos.score = infos.score + '\u5206';
				}
			}
			ret = JSON.stringify(pick(infos, ['id', 'is_high_light', 'url', 'image_urls', 'title', 'price', 'express', 'pay_num', 'score', 'area', 'channel']));
		}
		global.share && global.share.setContrastData && global.share.setContrastData(ret);
	}

	// 发送商品数据到服务器端 
	function postDataToServer(id, title, channel) {
		if (!id || !title || !channel) return;
		var url = getunipidUrl + '?id=' + id + '&title=' + title + '&channel=' + channel;
		jsonp(url, function(data) {
			if (data.data) {
				showPlugin();
				unipid = data.data;
				getLowerPrice(unipid);
			}
		});
	}
	
	// 显示同款最低价
	function getLowerPrice(unipid) {
		var obj, args, url,
			priceWrap, div,
			cout = 0, intval;
		detailInfo = getDetailInfo(current_store);
		if (detailInfo) {
			obj = clone(detailInfo);
			obj.img = encodeURIComponent(obj.image_urls[0].img);
			delete obj.image_urls;
			obj.pid = unipid;
			obj.url = encodeURIComponent(obj.url);
			url = getPriceUrl + '?' + params(obj);
			var count = 3;
			var priceInval;
			url = url + '&t=' + Date.now();
			(function getPrice() {
				count--;
				jsonp(url, function(data) {
					if (data.data) {
						var txt = '<em>\uffe5' + parsePrice(data.data) + '</em>\u8d77';
						priceWrap = domId('J_priceWrap');
						priceWrap.innerHTML = txt;
						// 获取长度值
						div = elem('div');
						div.setAttribute('style', 'display: inline-block; visibility:hidden;')
						div.innerHTML = txt;
						document.body.appendChild(div);
						wd = div.getBoundingClientRect().width;
						div.parentNode.removeChild(div);

						var sp = domId('J_samePlugin');
						priceWrap.style.width = wd + 'px';
						sp.style.marginRight = '-' + (wd+2) + 'px';

						if (unfoldTimer) clearTimeout(unfoldTimer)
						global.onscroll = function() {
							if (window.scrollY > screen.availHeight) {
								fold();
							}
						}
						var foldTimer = setTimeout(function() { 
							unfold();
							clearTimeout(foldTimer);
							foldTimer = null;
						}, 600);
					} else {
						if (count > 0) {
							priceInval = setTimeout(function() {
								getPrice();
								clearTimeout(priceInval)
							}, 500);
						}
					}
				})
			}())
		}
	}

	function getDetailInfo(storeName) {
		if (!storeName) return;
		var goodsInfo;
		switch (storeName) {
			case 'taobao':
				var shopEl = className('dt-shop')[0];
				var credit = tag('b', shopEl)[0].className;
				goodsInfo = {
					'id': urlParams.id,
					'channel': storeName,
					'shop_level': credit.match(/c-icon-lw(\d)/)[1],
					'level_icon': credit.match(/c-icon-b(\d)/)[1],
					'title': tag('h1')[0].innerText,
					'shop_title': tag('h2', shopEl)[0].innerText,
					'price': (function() {
						var price = tag('ins', domId('item-price-line'))[0].innerText.substring(1);
						return price.indexOf('-') > -1 ? price.substring(0, price.indexOf('-')) : price;
					}()),
					'image_urls': (function(){
						var imgs, ret = [], i = 0, len;
						imgs = tag('img', className('dt-slct-ul')[0]);
						len = imgs.length;
						for (; i<len; i++)
							if (imgs[i].className == '') ret.push({img: imgs[i].src});
						return ret;
					}()),
					'url': location.href,
					'create_time': '',
					'pay_num': className('dtifp-m')[0].innerText.match(/\d+/)[0],
					'score': className('orange', className('dtspu-r')[0])[0].innerText,
					'comment_num': '',
					'area': className('dtifp-r')[0].innerText,	// 地区
					// 'express': className('dtifp-l')[0].innerText || '',
					'express': (function() {
						var txt = className('dtifp-l')[0].innerText || '';
						return txt && txt == '\u5356\u5bb6\u5305\u90ae' ? '\u5305\u90ae' : txt
					}())
				}
				break;
			case 'tmall':
				goodsInfo = {
					'id': urlParams.id,
					'channel': storeName,
					'shop_level': '',
					'level_icon': '',
					'title': tag('h1')[0].innerText,
					'shop_title': className('shop', domId('s-shop'))[0].innerText,
					'price': (function() {
						var price = className('ui-yen', domId('s-price'))[0].innerText.substring(1);
						return price.indexOf('-') > -1 ? price.substring(0, price.indexOf('-')) : price;
					}()),
					'image_urls': [{img: tag('img', className('main', domId('s-showcase'))[0])[0].src}],
					'url': location.href,
					'create_time': '',
					'pay_num': className('v', className('sales')[0])[0].innerText,
					'score': (function(){
						var i = 0, len, els, scores = 0; 
						els = tag('b', className('score', domId('s-shop'))[0]);
						len = els.length;
						if (len) {
							for (; i<len; i++) scores += els[i].innerText*1;
							return parseFloat(scores / len).toPrecision(2);
						} else {
							return '';
						}
					}()),
					'comment_num': (function() {
						var review = className('review', domId('s-header'))[0];
						var el = tag('b', review);
						return el.length ? el[0].innerText : '';
					}()),
					'area': className('from', domId('s-adds'))[0].innerText,
					'express': ''
				}
				break;
			case 'jd':
				break;
		}
		return goodsInfo;
	}
	// 获取店铺名称
	function getStoreName() {
		var host = location.host;
		var path = location.pathname;
		if (host.indexOf('taobao.com') > -1 && (path.indexOf('/awp/core/detail.htm') > -1 || path.indexOf('/item.htm') > -1)) {
			return current_store = 'taobao';
		} else if (host.indexOf('tmall.com') > -1 && path.indexOf('/item.htm') > -1) {
			return current_store = 'tmall';	
		}
	}

	(function init() {
		var storeName, title, flag = true;
		if (storeName = getStoreName()) {
			intval = setInterval(function() {
				if (flag) {
					try {
						title = tag('h1')[0].innerText;
					} catch (e){}
					if (title) {
						flag = false;
						postDataToServer(urlParams.id, title, storeName);
						clearInterval(intval);
						intval = null;
					}
				}
			}, 50);
		}
	}())
})(window);