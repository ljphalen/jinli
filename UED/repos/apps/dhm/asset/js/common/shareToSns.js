define('shareToSns', ['zepto'], function($) {
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

});