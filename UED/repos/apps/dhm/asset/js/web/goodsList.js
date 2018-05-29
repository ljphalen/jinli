define('goodsList', ['zepto', 'mvc', 'util', 'lazyload'], function($, mvc, util, lazyload) {
	
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

});