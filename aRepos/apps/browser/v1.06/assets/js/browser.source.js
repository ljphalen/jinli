
(function(iCat, $){
	iCat.app('MH', function(){
		return {
			version: '1.06'
		};
	});
	
	iCat.mix(MH, {
		scrollPic: function(){
			if(!$('.isTouch')[0]) return this;
			
			iCat.include([iCat.sysRef+'/lib/jtouchSwipe/jtouchSwipe.js','./~slideImg.js'], function(){
				$('#mainfocus').SlideImg({
					auto: true,
					loop: true
				});
				
				$('.channel-box').each(function(){
					$(this).SlideImg({css3: true, circleTime: 200});
				});
				
				$('.J_scrollPro').SlideImg({css3:true});
			});
			
			//iCat.include([iCat.sysRef+'/lib/jtouchSwipe/jtouchSwipe.js','./~uilib.js']);
			
			return this;
		},
		
		toggle: function(){
			var nav = $('.J_navList');
			if(!nav[0]) return this;
			
			nav.next('.handle').toggle(function(){
				nav.removeClass('hidden');
				$(this).html('<span><s></s></span>').removeClass('open');
			}, function(){
				nav.addClass('hidden');
				$(this).html('<span>分类<s></s></span>').addClass('open');
			});
			return this;
		},
		
		tab: function(){
			if(!$('.J_tabBox')[0]) return this;
			var wrap = $('.J_tabBox'),
				handles = wrap.find('.J_tabHandle li'), mains = wrap.find('.J_tabMain');
			handles.click(function(){
				var i = handles.index($(this));
				handles.removeClass('selected').eq(i).addClass('selected');
				mains.hide().eq(i).show();
			});
			return this;
		}
	});
	
	$(function(){
		MH.scrollPic().toggle().tab();
		
		//为了统计
		var cr = $.trim($('body').attr('dt-cr'));
		if(cr){
			$('a').click(function(evt){
				evt.preventDefault();
				//$.get(cr+'?url='+encodeURIComponent(this.href));
				var label = this.getAttribute('dt-labelCla')? '&tid='+this.getAttribute('dt-labelCla') : '';
				location.href = cr+'?url='+encodeURIComponent(this.href)+label;
			});
		}
	});
})(ICAT, jQuery);