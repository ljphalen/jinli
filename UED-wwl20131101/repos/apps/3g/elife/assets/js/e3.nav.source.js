(function(iCat){
	//定义命名空间
	iCat.app("E3nav",function(){
		return {
			version:'1.0'
		};
	});

	iCat.mix(E3nav,{
		init: function(){
			iCat.include(['lib/zepto/zepto.js'],function(){
				//初始化默认容器START
				var ulWrap = [], initUrl, initWrap, a, l, i, curzIndex, mr;
				for(i = 0, l = $('.nav-tab li').length; i < l; i++){
					if(i == 0) {
						ulWrap.push('<ul></ul>');
					} else {
						ulWrap.push('<ul class="ishide"></ul>');
					}
				}
				$("#J_nav_box").append(ulWrap.join(''));

				initWrap = $('#J_nav_box ul').eq(0);
				initUrl = $('.nav-tab li').eq(0).data('remote');
				
				setTimeout(function(){E3nav.getNavData(initUrl,initWrap);},1500);
				
				//初始化默认容器END

				$("#J_nav_tab").delegate('li','click',function(evt){
					var that = $(this), id = that.data('id'), url = that.data('remote'), wrap = $("#J_nav_box ul").eq(id-1),
						prevId = parseInt(that.prev().data('id')), nextId = parseInt(that.next().data('id'));

					$('.nav-tab li').removeClass('sel');
					$('.nav-tab li').removeClass('prevbg');
					$('.nav-tab li').removeClass('nextbg');
					that.addClass('sel').css({'margin':0});

					if(isNaN(prevId) === true){
						that.next().addClass('nextbg');
						a = that.siblings(), curzIndex = 15, mr = 10;
						for(i = 0, l = a.length; i < l; i++){
							mr = mr + 2;
							$(a[i]).css({'z-index':--curzIndex,'margin':'0 -'+ mr +'px 0 0','background-postion':mr+'px top'});
							if(i == l -1){
								$(a[i]).css({'margin':'0 0 0 -10px'});
							}
						}

					} else if(isNaN(nextId)){
						that.prev().addClass('prevbg');
						a = that.siblings(), curzIndex = 15, mr = 10;
						for(i = 0, l = a.length; i < l; i++){
							mr = mr + 2;
							$(a[(l-1)-i]).css({'z-index':--curzIndex,'margin':'0 0 0 -'+mr+'px','background-postion':mr+'px top'});
						}
					} else {
						that.prev().addClass('prevbg');
						that.next().addClass('nextbg');
					}


					$("#J_nav_box ul").addClass('ishide');
					wrap.removeClass('ishide');
					if(!wrap.hasClass('done')){
						E3nav.getNavData(url,wrap);
					}
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

		            /*return this.find('li').each(function() {
		                var $el = $(this).find('a');
		                $el.css('-webkit-tap-highlight-color', 'rgba(255,255,255,0)').off('touchstart.highlight');
		                className && $el.on('mousedown.highlight', function() {
		                	console.log('aa');
		                    timer = setTimeout(function() {
		                        actElem = $el.attr('highlight-cls', className).addClass(className);
		                    }, 100);
		                });
		            });*/
		        }
			 	
			 	$('#J_nav_box').highlight('highlight');
		       
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
						var linkColor = item.color ? "color:" + item.color +";" : "";
						
						tmpl = '<li>\
									<a href="'+item.link+'" highlight-cls="highlight" style="'+linkColor+'-webkit-tap-highlight-color: rgba(255, 255, 255, 0);">\
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

	E3nav.init();

})(ICAT);