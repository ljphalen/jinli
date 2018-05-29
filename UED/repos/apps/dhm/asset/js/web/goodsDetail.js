define('goodsDetail', ['zepto', 'carousel', 'lazyload', 'util'], function($, carousel, lazyload, util) {
						
	carousel.render($('#J_carouselWrap'));

	// 置顶
	util.goTop();
})