define('browser', ['zepto'], function ($) {
	var ua = navigator.userAgent.toLowerCase();

	var browser = {
		isWeixinBrowser: function() {
			return /micromessenger/.test(ua) ? true : false;
		}
	}

	return browser;
});