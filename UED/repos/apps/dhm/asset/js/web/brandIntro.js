define('brandIntro', ['zepto', 'mvc', 'util', 'lazyload'], function($, mvc, util, lazyload) {
	
	mvc.render({
		el: '#J_listWrap',
		templateId: '#J_template',
		scroll: true,
		lazyload: true,
		url: $('#J_listWrap').attr('data-ajaxUrl')
	});

	// 置顶
	util.goTop();

});