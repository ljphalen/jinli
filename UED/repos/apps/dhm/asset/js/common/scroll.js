/*
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

});