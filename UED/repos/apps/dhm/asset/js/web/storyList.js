// 知物列表
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

});