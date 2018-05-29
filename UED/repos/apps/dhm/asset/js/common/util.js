define('util', ['zepto'], function($) {

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
});