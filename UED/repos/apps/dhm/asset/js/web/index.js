// 大红帽首页
define('index', ['zepto', 'carousel', 'util', 'lazyload'], 
	function($, carousel, util, lazyload) {

	// 图片轮播
	carousel.render($('#J_carouselWrap'));
	// 置顶
	util.goTop();

});