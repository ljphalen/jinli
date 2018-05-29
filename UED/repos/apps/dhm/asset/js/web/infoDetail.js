// 资讯详情
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
});