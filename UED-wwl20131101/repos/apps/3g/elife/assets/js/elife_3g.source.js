(function(iCat){
	iCat.PathConfig();

	iCat.app('GiONEE',function(){ return {version: 'v1.0'};});

	iCat.mix(GiONEE,{
		init: function(){
			iCat.include(['lib/zepto/zepto.js','./elife.slider.js','lib/zepto/touchSwipe.js'],function(){
				var sliderEl = $("#J_full_slider .ui-slider-wrap");
				if(!sliderEl[0]) return;
				$("#J_full_slider").slidePic({
					slideWrap:   '.ui-slider-wrap',
					slidePanel:  '.ui-slider-pic',
					slideItem:   '.ui-slider-pic li',
					handlePanel: '.ui-slider-handle',
					handleItem:  '.ui-slider-handle > span',
					disableFirst: true,
					disableLast: true,
					circle: true,
					auto:true
				});

				$("#param").click(function(){
					$(".ui-tab-title a").removeClass('sel');
					$(this).addClass('sel');
					$(".ui-tab-content").show();
					$(".ui-show-pic").hide();
				});

				$("#show").click(function(){
					$(".ui-tab-title a").removeClass('sel');
					$(this).addClass('sel');
					$(".ui-tab-content").hide();
					$(".ui-show-pic").show();
				});

				/*window.onhashchange = function(){
					if(location.hash == "#show"){
						$(".ui-tab-title a").removeClass('sel');
						$(".ui-tab-title a").eq(1).addClass('sel');
						$(".ui-tab-content").hide();
						$(".ui-show-pic").show();
					} else {
						$(".ui-tab-title a").removeClass('sel');
						$(".ui-tab-title a").eq(0).addClass('sel');
						$(".ui-tab-content").show();
						$(".ui-show-pic").hide();
					}
				}*/

                $(".J_drapdown").click(function(){
                    $(this).parent().toggleClass('menu-panel-drapbox-active');
                });

			},true);

		}	
	});

    GiONEE.init();

})(ICAT);