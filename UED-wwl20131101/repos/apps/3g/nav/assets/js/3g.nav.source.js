(function(iCat){
	var Event = iCat.Event;
	//定义命名空间
	iCat.app("GNnav",function(){
		return {
			version:'1.0'
		};
	});

	iCat.mix(GNnav,{
		init: function(){
			iCat.include(['lib/zepto/zepto.js'],function(){
				Event.on(".J_nav_title",'click',function(){
					var that = $(this), navWrap = $(".J_nav_title"), contentWrap = $(".J_nav_content"),
						selLast  = that.data('last'),
						curNext  = that.next(),
						curIndex = navWrap.index(that);

					selLast == true ? that.addClass('ui-nav-last') : '';

					if(curNext.hasClass('ishide')){
						contentWrap.addClass('ishide');
						curNext.removeClass('ishide');
						navWrap.removeClass('ui-nav-arrow');
						that.addClass('ui-nav-arrow');
					} else {
						curNext.addClass('ishide');
						that.removeClass('ui-nav-arrow');
					}

					$(window).scrollTop(that.height() * curIndex + 5);
				});


				/**
				 *  @file 实现了通用highlight方法。
				 *  @name Highlight
				 *  @desc 点击高亮效果
				 *  @import core/zepto.js, core/zepto.extend.js
				 */
				
			    var actElem, inited = false, timer, cls, removeCls = function(){
			        clearTimeout(timer);
			        if(actElem && (cls = actElem.attr('highlight-cls'))){
			            actElem.removeClass(cls).attr('highlight-cls', '');
			            actElem = null;
			        }
			    };

		        $.fn.highlight = function(className) {
		            inited = inited || !!$(document).on('touchend.highlight touchmove.highlight touchcancel.highlight', removeCls);
		            removeCls();
		            className && $(this).delegate('a','touchstart.highlight',function(){
		            	var that = $(this);
		                timer = setTimeout(function() {
		                        actElem = that.attr('highlight-cls', className).addClass(className);

		                }, 100);

	            	});
		        }
			 	
			 	$('.J_nav_content').highlight('highlight');
		       
			});
		},
		getNavData: function(url,wrap,moreList){
			$.ajax({
				type: 'GET',
				url: url,
				dataType: 'json',
				success: function(res){
					wrap.addClass('done').html('');
					var data = res.data.list.data, arr = [], tmpl;
					$.each(data,function(index,item){
						tmpl = '<li>\
									<a href="'+item.link+'" highlight-cls="highlight" style="-webkit-tap-highlight-color: rgba(255, 255, 255, 0);">\
										<div>\
											<img src="'+item.icon+'" />\
											<span>'+item.name+'</span>\
										</div>\
									</a>\
								</li>';
						arr.push(tmpl);
					});
					wrap.append(arr.join(''));
				}
			});
		}
	});

	GNnav.init();

})(ICAT);