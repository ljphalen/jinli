// 图片轮播
define('carousel', ['zepto'], function($) {

	var render = function(selector) {
		// 图片轮播效果
		$(selector).each(function() {
			var carousel,
				indicators,
				imgs, len,
				intval, start = 0,
				lis, ind;

			carousel = $(this).find('.carousel');
			indicators = $(this).find('.carousel-indicators');
			imgs = carousel.find('img');
			len = imgs.length;
			start = 0;

			if (!len) return;

			ind = indicators.find('li');
			carousel.width(100 * len + '%');

			var setCarouselStyle = function(start) {
				carousel[0].style.marginLeft = '-' + 100 * start + '%';
				ind.eq(start).addClass('active').siblings().removeClass('active');
			};

			var carouselFn = function() {
				if (start >= len) start = 0;
				setCarouselStyle(start);
				start++;
			};

			var setTimer = function() {
				intval = setInterval(carouselFn, 5000);
			};

			var startCarousel = function(index) {
				setCarouselStyle(index);
				setTimer();
			}

			imgs.swipeLeft(function() {
				clearInterval(intval);
				start++;
				if (start >= len) start = len-1;
				startCarousel(start);
			});

			imgs.swipeRight(function() {
				clearInterval(intval);
				start--;
				if (start <= 0) start = 0;
				startCarousel(start);
			});

			startCarousel(0);
		})
	}
	return {
		render: render
	}
});