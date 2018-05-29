(function(iCat){
	iCat.PathConfig();
	var gnTheme = iCat.namespace('gntheme');
	iCat.mix(gnTheme,{
		init: function(){
			gnTheme.slidePic();
			gnTheme.gotoDetail();
		},

		openLocalActivity: function(data,selfPage){
			if(!data) return;
			data.indexOf(',') > -1 ? gnTheme.startpreactivity(data,selfPage) : gnTheme.startlistctivity(data);
		},

		startpreactivity: function(data,selfPage){
			var infTheme = data.split(',');
			navigator.gntheme[selfPage? 'reloadpretheme' : 'startaprectivity'](
				function(){}, //success callback
				function(){}, //error callback
				{id:infTheme[4], titleTheme:infTheme[1], preurl:infTheme[2], dlurl:infTheme[3]}
			);
		},

		startlistctivity: function(data){
			var d = data.indexOf('|')==-1? data : data.split('|'),
				argus = data.indexOf('|')==-1? {listurl: data} : {listurl: d[0], topicTheme: d[1]};
			navigator.gntheme.startlistctivity(
				function(){
					//alert('正在为您跳转页面');
				},
				function(){},
				argus
			);
		},

		slidePic: function(){
			var slideWrap = $('.J_slidePic, .J_scrollPic');
			//console.log(slideWrap);
			if(!slideWrap[0]) return;

			if(slideWrap.hasClass('J_scrollPic')){
				//图片延时渲染
				setTimeout(function(){
					$('.lazy').each(function(){
						var init_src = $(this).attr("data-original");
						$(this).attr('src',init_src);
						$(this).addClass('done');
					});
				},1000);
			}

			var pnBtn = $('.J_handelBtn span');
			pnBtn.on('click', function(){
				if($(this).hasClass('disabled')) return;
				gnTheme.openLocalActivity($(this).attr('data-inftheme')||'', true);
			});

			iCat.include('./iscroll-lite.js',function(){
				var introScroll;
					introScroll = new iScroll('intro-wrap',{
						bounce:false,
						hideScrollbar: false,
						scrollbarClass:'intro-scrollbar'
					});
			});

			iCat.include(['lib/zepto/touchSwipe.js', './slidePic.js'], function(){
				slideWrap.slidePic(
					slideWrap.hasClass('J_scrollPic')? {
						slidePanel: '.pic-wrap',
						slideItem: '.pic-wrap li',
						specialWidth: true,
						speed: 200,
						prev: '.J_scrollPic .prev',//使其失效
						next: '.J_scrollPic .next',
						fixCurrent: function(){return 1;},
						beforeFirst: function(){
							var prevTheme = slideWrap.attr('data-prevtheme');
							if(!prevTheme) return;
							gnTheme.openLocalActivity(prevTheme, true);
						},
						afterLast: function(){
							var nextTheme = slideWrap.attr('data-nexttheme');
							if(!nextTheme) return;
							gnTheme.openLocalActivity(nextTheme, true);
						},
						stepCallback: function(curIndex){
							//每操作一步执行回调
							if(curIndex < 1){
								$('.pic-wrap').addClass('pic-wrap-tmp');
							} else {
								$('.pic-wrap').removeClass('pic-wrap-tmp');
							}
						}
					} : {
						circle:true,
						auto:true
					}
				);
			}, true);
		},

		gotoDetail: function(){
			
			// $('.slide-pic').delegate('.pic a', 'click', function(evt){
			//     evt.preventDefault();
			//     gnTheme.openLocalActivity($(this).attr('data-inftheme')||'');
			// });

			$('.J_itemList').delegate('li a', 'click', function(evt){
				evt.preventDefault();
				gnTheme.openLocalActivity($(this).attr('data-inftheme')||'');
			});
		}
	});
	
	iCat.include(["./core/zepto.js","./core/zepto.extend.js","./core/zepto.ui.js","./widget/refresh.js","./widget/refresh.lite.js","./tempcore.js"],function(){
		iCat.util.lazyLoad('body',500,'img[src$="pic_nopreview.jpg"]');
		var url = $(".J_itemList").data("ajaxurl"), hasnext = $(".J_itemList").data("hasnext"), page = {curpage:1, hasnext: hasnext == true ? true : false};
		if(url){
			url = url.indexOf('?') > -1 ? url + '&page=': url + '?page=';
			if (hasnext == false)  return;
			$('.J_itemList').refresh({
	            ready: function (dir, type) {
	                var me = this;
	                $.getJSON(url+(1+page.curpage), function (data) {
	                	if(data.success) {
	                		page.curpage = data.data.curpage;
	                		page.hasnext = data.data.hasnext;
	                		var $list = $('.J_itemList ul');
		                    var tmpl = template('J_itemView', data);
		                    $list[dir == 'up' ? 'prepend' : 'append'](tmpl);
		                    me.afterDataLoading(); //数据加载完成后改变状态
		                    iCat.util.lazyLoad('body',500,'img[src$="pic_nopreview.jpg"]');
		                    if(data.data.hasnext == false){
		                    	me.disable();
		                    }
	                	}
	                });
	            },
	        });
		} else {
			iCat.util.lazyLoad('body',500,'img[src$="pic_nopreview.jpg"]');
		}
		$(function(){gnTheme.init();});
	},true,false);

})(ICAT);