define('browser', ['zepto'], function ($) {
	var ua = navigator.userAgent.toLowerCase();

	var browser = {
		isWeixinBrowser: function() {
			return /micromessenger/.test(ua) ? true : false;
		}
	}

	return browser;
});;// 图片轮播
define('carousel', ['zepto'], function($) {

	var render = function(selector) {
		// 图片轮播效果
		$(selector).each(function() {
			var carousel,
				indicators,
				imgs, len,
				intval, start = 0,
				lis, ind;

			carousel = $(this).find('.carousel');
			indicators = $(this).find('.carousel-indicators');
			imgs = carousel.find('img');
			len = imgs.length;
			start = 0;

			if (!len) return;

			ind = indicators.find('li');
			carousel.width(100 * len + '%');

			var setCarouselStyle = function(start) {
				carousel[0].style.marginLeft = '-' + 100 * start + '%';
				ind.eq(start).addClass('active').siblings().removeClass('active');
			};

			var carouselFn = function() {
				if (start >= len) start = 0;
				setCarouselStyle(start);
				start++;
			};

			var setTimer = function() {
				intval = setInterval(carouselFn, 5000);
			};

			var startCarousel = function(index) {
				setCarouselStyle(index);
				setTimer();
			}

			imgs.swipeLeft(function() {
				clearInterval(intval);
				start++;
				if (start >= len) start = len-1;
				startCarousel(start);
			});

			imgs.swipeRight(function() {
				clearInterval(intval);
				start--;
				if (start <= 0) start = 0;
				startCarousel(start);
			});

			startCarousel(0);
		})
	}
	return {
		render: render
	}
});;define('lazyload', ['zepto'], function($) {

	var timer, init;

	init = function() {
		var offsetTop = window.pageYOffset ? window.pageYOffset : document.documentElement.scrollTop,
			offsetWin = offsetTop + (window.innerHeight ? window.innerHeight : document.documentElement.clientHeight),
			blankImgs = $('img[src$="blank.gif"]'),
			defaultImg = $('img[src$="pic_imgDefault.png"]'),
			imgs;

		imgs = $(blankImgs.concat(defaultImg));
		if (!imgs.length) return;
		imgs.each(function() {
			var img, src, rect, 
				imgTop, imgWin;

				img = $(this);
				src = img.attr('data-src');
				rect = img[0].getBoundingClientRect();
				imgTop = rect.top + document.body.scrollTop + document.documentElement.scrollTop;
				imgWin = imgTop + img.height();
			if ((imgTop > offsetTop && imgTop < offsetWin) || (imgWin > offsetTop && imgWin < offsetWin) ) {
				if (src != null) {
					img.attr('src', src)
					img.removeAttr('data-src');
				}
			}
		})
	}

	var scrollCallback = function() {
		if (timer) clearTimeout();
		var timer = setTimeout(function() {
			init();
		}, 700);
	}

	$(window).scroll(scrollCallback);
	$('.iScroll').scroll(scrollCallback);

	init();

	return {
		init: init
	}
});;// mvc渲染页面
define('mvc', ['zepto', 'underscore', 'backbone', 'scroll', 'util', 'lazyload'], 
	function($, _, Backbone, scroll, util, lazyload) {
	
	/**
	 * el: '#J_listWrap',
	 * templateId: '#J_template',
	 * events: {},
	 * url: $('#J_listWrap').attr('data-ajaxUrl'),
	 * method: 'GET',
	 * scroll: false,
	 * templateData: {}
	 */

	var render = function(options) {
		
		var templateData;
		var viewOptions;
		var Model;
		var Collection;
		var collection;
		var View;
		var curPage;
		var hasNext;

		if (!options) return;
		if (!$.isPlainObject(options)) return;
		if (!options.el || !options.templateId) return;

		templateData = options.templateData || {};

		Model = Backbone.Model.extend({});

		Collection = Backbone.Collection.extend({
			model: Model,
			parse: function(response) {
				 if (!$.isPlainObject(response)) response = JSON.parse(response);
				 if (response.success) {
					 if (options.parse) {
					 	return options.parse(response);
					 }
					 return response.data;

				 }
			}
		});

		View = Backbone.View.extend({
			el: $(options.el),
			template: _.template($(options.templateId).html()),
			render: function() {
				$(this.el).append(this.template(this.model.toJSON()));
				if (options.lazyload) {
					lazyload.init();
				}
				return this;
			},
			initialize: function() {
				this.render();
				this.listenTo(this.model, 'change', this.render);
				return this;
			},
			events: options.events || ''
		});

		collection = new Collection();

		collection.on('add', function(model) {
			var view = new View({model: model});
		});

		if (!options.url) {
			collection.add(templateData);
			return;
		}
		// 滑动加载数据
		if (options.scroll) {
			scroll.init({
				collection: collection,
				view: View,
				url: options.url,

			})
			return;
		}

		// 请求数据
		collection.fetch({
			url: options.url
		})
	};

	return {
		render: render
	}
});;/*
 * 滑动加载
 */
define('scroll', ['zepto', 'util'], function($, util) {

	var scrollTop;
	var scrollHeight;
	var windowHeight;

	var timer;

	var init = function(options) {

		var collection = options.collection;
		var url = options.url;
		var curPage;
		var hasNext;

		var fetch = function(options) {
			collection.fetch({
				url: options.url,
				success: function(collect, response) {
					options.callback(response);
				}
			})
		}

		var scrollEvent = function(curPage, hasNext) {
			$(window).scroll(function() {
				var $this = $(this);
				scrollTop = $this.scrollTop();
				scrollHeight = $(document).height();
				windowHeight = $this.height();
				if (scrollTop + windowHeight >= scrollHeight) {
					if (timer) clearTimeout(timer);
					timer = setTimeout(function() {
						loadMore();
					}, 200);
				}
			})
		}

		var loadMore = function() {
			if (!hasNext) {
				util.showTip('没有更多了');
				return;
			}
			url = url.replace(/[\?\&]page=(\d+)/, '');
			url = url + (url.indexOf('?') < 0? '?' : '&') + 'page=' + (curPage * 1 + 1);
			fetch({
				url: url,
				callback: function(response) {
					if (response.success) {
						curPage = response.data.curpage;
						hasNext = response.data.hasnext;
						if (hasNext) {
							util.showTip('努力加载中...');
						} else {
							util.showTip('没有更多了');
						}
					}
				}
			})
		}

		fetch({
			url: options.url,
			callback: function(response) {
				if (response.success) {
					curPage = response.data.curpage;
					hasNext = response.data.hasnext;
					scrollEvent();
				}
			}
		})

	}

	return {
		init: init
	}

});;define('shareToSns', ['zepto'], function($) {
	var $body = $('body'),
		urlMap = {
			weibo: 'http://v.t.sina.com.cn/share/share.php',
			qq: 'http://connect.qq.com/widget/shareqq/index.html',
			qzone: 'http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey'
			// qzone: 'http://openmobile.qq.com/oauth2.0/m_jump'
		},
		options,
		setOptions
		;
	
	$body.on('click', '.J_shareToWeibo, .J_shareToQQ, .J_shareToQzone', function(e) {
		e.stopPropagation();
		var $this = $(this);
		var parent = $(this).parent();
		var type = $this.attr('data-type');
		var href;
		var params = {
			url: encodeURIComponent(parent.attr('share-url') || ''),
			pic: parent.attr('share-pic') || '',
			title: parent.attr('share-title') || ''
		};

		if (options) {
			var args = $.isFunction(options) ? options.call($this) : options;
			$.extend(params, args);
		}

		if (type === 'qzone') {
			params.pics = params.pic;
			// params.webid = 'qzone';
			delete params.pic;
		}

		href = urlMap[type] + '?' + $.param(params);
		window.open(href);
	});

	setOptions = function(args) {
		options = args;
	};

	return {
		setOptions: setOptions
	}

});;define('util', ['zepto'], function($) {

	// 底部toast提示
	var showTip = function(msg) {
		var body = $('body'),
			tip = body.find('.J_msgTip');

		if(tip[0]){
			tip.html(msg);
			tip.parent().show();
		} else {
			body.append('<div class="msg-tip"><span class="J_msgTip">'+msg+'</span></div>');
			tip = body.find('.J_msgTip');
		}

		setTimeout(function(){
			tip.parent().hide();//fixed bug: 行内元素居然不起作用。。。
		}, 3000);
	};
	// 置顶按钮
	var goTop = function() {
		var gotop = $('.gotop');
		if (gotop[0]) {
			gotop.on('tap', 'span', function(e) {
				window.scrollTo(0,1);
				e.stopPropagation();
			});
			
			$(window).scroll(function() {
				if(document.body.scrollTop < 200){
					gotop.animate({
						opacity: 0
					}, 400, 'ease-out', function() {
						gotop[0].style.display = 'none';
					});
				} else {
					gotop[0].style.display = 'block';
					gotop.animate({
						opacity: 1
					}, 400, 'ease-in');
				}
			})
		}
	}
	// 加载微信sdk
	var loadWeixinSdk = function(callback) {
		require(['http://res.wx.qq.com/open/js/jweixin-1.0.0.js'], function(wx) {

			var url = location.href.replace(location.hash, '');
			var setConfig = function(config) {
				// 通过config接口注入权限验证配置
				wx.config({
					// debug: true,
				    appId: config.appId,
				    timestamp: config.timestamp,
				    nonceStr: config.nonceStr,
				    signature: config.signature,
				    jsApiList: config.jsApiList
				})
				if (callback && $.isFunction(callback)) {
					callback(wx);
				}
			};

			$.ajax({
				type: 'GET',
				dataType: 'jsonp',
				url: 'http://findjoy.cn/api/weixin/jsparams?callback=jsoncallback&pageUrl=' + url,
				success: function(result) {
					if (result.success) {
						setConfig(result.data);
					}
				}
			});

			
		})
	};

	// 微信图像预览功能
	var previewImage = function(callback) {
		loadWeixinSdk(callback);
	};

	return {
		showTip: showTip,
		goTop: goTop,
		loadWeixinSdk: loadWeixinSdk,
		previewImage: previewImage
	}
});;define('brandIntro', ['zepto', 'mvc', 'util', 'lazyload'], function($, mvc, util, lazyload) {
	
	mvc.render({
		el: '#J_listWrap',
		templateId: '#J_template',
		scroll: true,
		lazyload: true,
		url: $('#J_listWrap').attr('data-ajaxUrl')
	});

	// 置顶
	util.goTop();

});;define('goodsDetail', ['zepto', 'carousel', 'lazyload', 'util'], function($, carousel, lazyload, util) {
						
	carousel.render($('#J_carouselWrap'));

	// 置顶
	util.goTop();
});define('goodsList', ['zepto', 'mvc', 'util', 'lazyload'], function($, mvc, util, lazyload) {
	
	mvc.render({
		el: '#J_listWrap',
		templateId: '#J_template',
		scroll: true,
		lazyload: true,
		url: $('#J_listWrap').attr('data-ajaxUrl'),
		parse: function(response) {
			if (response.success) {
				if (response.data.list.length) {
					return response.data;
				} else {
					$('.module').append($('#J_noResultTemplate').html());
				}
			}
		}
	});

	// 置顶
	util.goTop();

});;// 大红帽首页
define('index', ['zepto', 'carousel', 'util', 'lazyload'], 
	function($, carousel, util, lazyload) {

	// 图片轮播
	carousel.render($('#J_carouselWrap'));
	// 置顶
	util.goTop();

});;// 资讯详情
define('infoDetail', ['lazyload', 'shareToSns', 'zepto', 'util', 'backbone', 'underscore', 'browser'], 
	function(lazyload, sns, $, util, Backbone, _, browser) {

	var AttentionModel = Backbone.Model.extend({
		defaults: {
			type: ''
		}
	});

	var model = new AttentionModel();

	var AttentionView = Backbone.View.extend({
		model: model,
		el: '#J_templateWrap',
		template: _.template($('#J_attentionTemplate').html()),
		render: function() {
			this.$el.html(this.template(this.model.attributes));
			return this;
		}
	});

	var callback = function() {
		var $this = $(this);
		var result = {};
		var article = $('article');
		var href = location.href;
		var type = $this.attr('data-type');

		result.pic = article.find('img').first().attr('src') || '';
		result.url = href + (href.indexOf('?') == -1 ? '?' : '&') + 'from=' + type;
		if (type == 'qzone') {
			result.title = article.find('h1').text();
		}

		return result;
	}

	var renderView = function(type) {
		model.set({type: type});
		new AttentionView().render();
	}

	var match = location.search.match(/from=(weibo|qzone|weixin)/);
	if (match == null) {
		if (browser.isWeixinBrowser()) {
			renderView('weixin');
		}
	} else {
		var type = match[1];
		renderView(type);
	}

	// 设置微博分享内容
	sns.setOptions(callback);

	// 图片预览
	var articleWrap =  $('.mixed-map-and-text');
	var imgs = articleWrap.find('img');
	if (imgs.length) {
		// 微信页面图像预览
		if (browser.isWeixinBrowser()) {
			util.previewImage(function(wx){
				var current = imgs[0].src;
				var urls = imgs.slice(1).map(function(idx, img){
					return img.src;
				});
				articleWrap.on('tap', 'img', function() {
					wx.previewImage({ current: current, urls: urls });	
				})
			});
		} else {
			imgs.each(function(idx, img) {
				if (img.parentNode && img.parentNode.tagName != 'A') {
					$(img).wrap('<a target="_blank" href="'+ img.src +'"></a>')
				}
			});
		}
	}
});;// 知物列表
define('storyList', ['zepto', 'underscore', 'backbone', 'carousel', 'scroll', 'mvc', 'util', 'lazyload'], 
	function($, _, Backbone, carousel, scroll, mvc, util, lazyload) {

	mvc.render({
		el: '#J_listWrap',
		templateId: '#J_template',
		lazyload: true,
		url: $('#J_listWrap').attr('data-ajaxUrl'),
		scroll: true
	});

	// 置顶
	util.goTop();

});;(function(global) {

	// 窗口适配
	var adapterScreen = function() {
		var doc = global.document, 
			root = doc.documentElement,
			currentW = 320, maxWidth = 540, minWidth = 320, timer,
			flexible = function() {
				currentW = root.getBoundingClientRect().width;
				currentW = currentW > maxWidth ? maxWidth : (currentW < minWidth ? minWidth : currentW);
				root.style.fontSize = currentW / 16 + 'px';
			};
		global.addEventListener('resize', function(){
			if (timer) clearTimeout(timer);
			timer = setTimeout(function() { flexible() }, 0);
		}, false)

		flexible();
	}

	// 禁止左右滑屏
	var disableUcGesture = function() {
		var userAgent;
		var control;
		userAgent = navigator.userAgent;
		if (/ucbrowser/i.test(userAgent)) {
			control = navigator.control || {};
			if (control.gesture) control.gesture(false);
		}
	}

	// 百度统计代码
	var baiduStatic = function(id, action, label) {
		try {
			if (!_hmt || !id || !action) return;
			label = label || '';
			_hmt.push(['_trackEvent', id, action, label]);
		} catch(e) {};
	};

	// weinre远程调试代码
	var weinreDebug = function() {
		var node = document.createElement('script');
		node.src = 'http://192.168.115.23:8080/target/target-script-min.js#anonymous'
		document.head.appendChild(node);
	}

	// 开始加载模块
	var startInit = function() {
		var bodyId = document.body.id;
		if (bodyId) {
			require(bodyId.split('|'));
		}
	}
	// 滑动时隐藏/显示header
	var header = $('#J_header');
	var height = header.height();
	var marginTop = '-' + height + 'px';
	var scrollWrap = $('#J_scrollWrap');
	var timer;

	var showHeader = function() {
		header.animate({
			'margin-top': 0
		}, 300, 'ease-out');
	};

	var hideHeader = function() {
		header.animate({
			'margin-top': marginTop
		}, 300, 'ease-out')
	}

	scrollWrap[0].addEventListener('touchstart', function() {
		showHeader();
		if (timer) timer = clearTimeout(timer);
		timer = setTimeout(function() {
			if (scrollWrap.scrollTop() > 0) {
				hideHeader();
			}
		}, 3000);
	}, false);

	adapterScreen();
	disableUcGesture();
	global.baiduStatic = baiduStatic;
	startInit();
	
	// weinreDebug();

})(this);