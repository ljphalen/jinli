define('lazyload', ['zepto'], function($) {

	var timer, init;

	init = function() {
		var offsetTop = window.pageYOffset ? window.pageYOffset : document.documentElement.scrollTop,
			offsetWin = offsetTop + (window.innerHeight ? window.innerHeight : document.documentElement.clientHeight),
			blankImgs = $('img[src$="blank.gif"]'),
			defaultImg = $('img[src$="pic_imgDefault.png"]'),
			imgs;

		imgs = $(blankImgs.concat(defaultImg));
		if (!imgs.length) return;
		imgs.each(function() {
			var img, src, rect, 
				imgTop, imgWin;

				img = $(this);
				src = img.attr('data-src');
				rect = img[0].getBoundingClientRect();
				imgTop = rect.top + document.body.scrollTop + document.documentElement.scrollTop;
				imgWin = imgTop + img.height();
			if ((imgTop > offsetTop && imgTop < offsetWin) || (imgWin > offsetTop && imgWin < offsetWin) ) {
				if (src != null) {
					img.attr('src', src)
					img.removeAttr('data-src');
				}
			}
		})
	}

	var scrollCallback = function() {
		if (timer) clearTimeout();
		var timer = setTimeout(function() {
			init();
		}, 700);
	}

	$(window).scroll(scrollCallback);
	$('.iScroll').scroll(scrollCallback);

	init();

	return {
		init: init
	}
});