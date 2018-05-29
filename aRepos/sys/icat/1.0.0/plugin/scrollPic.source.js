(function($,iCat){
	
	iCat.widget.scrollPic = function(config){
		var defaultConfig = {
			scrollCtrl: '.J_scrollCtrl li',
			scrollPic: '.J_scrollPic a',
			onCla: 'on',
			context: '#J_scrollBox',
			timeout: 4000,
			isSlide: false
		},
		cfg = $.extend(defaultConfig, config);
		
		if(!$(cfg.scrollCtrl).length || !$(cfg.scrollPic).length) return;
		
		//动态函数
		var _showAuto = function()
			{
				n = n >=(count - 1) ? 0 : ++n;
				$(cfg.scrollCtrl).eq(n).trigger('click');
			},
		
			_ctrlAnimate = function(o){
				var i = o.attr('data-ctrl') - 1;
				n = i;
				if (i>=count) return;
				
				cfg.isSlide ? oUL.animate({left:-960*i+'px'}) : $(cfg.scrollPic).fadeOut(500).eq(i).fadeIn(1500);
				o.addClass(cfg.onCla).siblings().removeClass(cfg.onCla);
			};
		
		
		//初始化
		var t = n = 0, count;
		count = $(cfg.scrollPic).length;
		if(cfg.isSlide){
			var ulWidth = $(cfg.scrollPic).width()*count,
				oUL = $(cfg.scrollPic).parent('ul');
			oUL.width(ulWidth);
		}
		if(!cfg.isSlide)
			$(cfg.scrollPic).hide().eq(0).show();
		
		$(cfg.scrollCtrl).click(function() {
			_ctrlAnimate($(this));
		}).mouseover(function(){
			_ctrlAnimate($(this));
		});
		
		t = setInterval(function(){_showAuto();}, cfg.timeout);
		var objStop = cfg.context ? cfg.context : cfg.scrollCtrl;
		$(objStop).hover(function(){
			clearInterval(t);
		},function(){
			t = setInterval(function(){_showAuto();}, cfg.timeout);
		});
	}
})(jQuery,ICAT);