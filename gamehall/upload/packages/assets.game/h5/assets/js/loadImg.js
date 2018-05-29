 //因使用pgwslider 因该插件是jquery的 使用zetop时 滑动模块与获取当前位置函数失效 
var isLandscape = false
	,currentSlide//获取当前滚动图片位置
;
$(function(){
	var bodyHeight = $(document.body).height();
	$("img[data-original]").lazyload();
	if($('.bg-layer').length){
		$('.bg-layer').css('height',bodyHeight);
	}
	if($('.pop-layer').length){
		$('.pop-layer').css('height',bodyHeight);
	}
	if(typeof rollData == 'undefined')
		return;
	if(!rollData.data.length)
		return;
	
	var pgwSlider = $('.pgwSlider').pgwSlider({
		displayList:false
		,layerId:'#roll'
	});
	$('#prompt [name="del"]').swipe({
		swipe:function(){
			$('#prompt').hide();
		},
		threshold:0
	});
	
	$("#roll img").swipe({
		swipeLeft:function() {
			pgwSlider.nextSlide();
		},
		swipeRight:function(){
			 pgwSlider.previousSlide();
			 
		},
		swipe:function(event, direction, distance, duration, fingerCount, fingerData) {
			if(!distance){
				window.location.href = rollData.data[currentSlide].href;
			}
		},
		threshold:0
	});

	$('.index-search').show();

	function orientationChange(firstEntry){
		switch(window.orientation) {
			case 0: 
			case 180:
				isLandscape = false;
				$('#roll').removeClass('landscape');
			break; 
			case -90: 
			case 90:
				isLandscape = true;
				$('#roll').addClass('landscape');
			break; 
		}
		if(!firstEntry)
			pgwSlider.nextSlide();
	}
	orientationChange(true);
	window.addEventListener("onorientationchange" in window ? "orientationchange" : "resize", orientationChange, false);
	
})