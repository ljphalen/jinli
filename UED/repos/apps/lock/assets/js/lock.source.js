(function(iCat){
	var gnLockScreen = iCat.namespace('gnlock');
	iCat.PathConfig();
	iCat.PathConfig._isConcat = false;

	iCat.mix(gnLockScreen,{
		init: function(){
			gnLockScreen.slidePic();
			gnLockScreen.gotoDetail();
		},
		//调用本地Activity
		openLocalActivity: function(data){
			if(!data) return;
			data.indexOf(',') > -1 ? gnLockScreen.startpreactivity(data) : gnLockScreen.startlistctivity(data);
		},
		//打开一个新的Activity
		startpreactivity: function(data){
			var infTheme = data.split(',');
			navigator.gnlockscreen.startpreactivity(
				function(){/*alert('正在为您跳转页面');*/},
				function(){},
				{titleLockScreen:infTheme[0], preurl:infTheme[1], dlurl:infTheme[2]}
			);
		},
		//打开分类Activity或者广告Activity
		startlistctivity: function(data){
			var d = data.indexOf('|') == -1 ? data : data.split('|'),
				argus = data.indexOf('|') == -1 ? {listurl: data} : {listurl: d[0], topicLockScreen: d[1]};
			navigator.gnlockscreen.startlistctivity(
				function(){/*alert('正在为您跳转页面');*/},
				function(){},
				argus
			);
		},
		lazyload: function(pnode,delay){
			if(!pnode) return;
			var pnode = document.querySelector(pnode),
				imgs = iCat.toArray(document.querySelectorAll('img[data-lazyload=true]',pnode));
			setTimeout(function(){
				imgs.forEach(function(o){
					var src = o.getAttribute('data-src');
					iCat.__cache_images = iCat.__cache_images || {};
					if(!src) return;

					if(!iCat.__cache_images[src]){
						var oImg = new Image(); oImg.src = src;
						oImg.onload = function(){
							o.src = src;
							iCat.__cache_images[src] = true;
							oImg = null;
						};
					} else {
						o.src = src;
					}
					o.removeAttribute('data-src');
					o.removeAttribute('data-lazyload')
				});
			}, delay || 1000);
		},

		slidePic: function(){
			var slideWrap = $('.J_slidePic, .J_scrollPic');

			if(!slideWrap[0]) return;

			if(slideWrap.hasClass('J_scrollPic')){
				//图片延时渲染
				gnLockScreen.lazyload(1000);
			}

			var pnBtn = $('.J_handelBtn span');
			pnBtn.on('click', function(){
				if($(this).hasClass('disabled')) return;
				gnLockScreen.openLocalActivity($(this).attr('data-inftheme')||'', true);
			});

			iCat.include(['/zepto/touchSwipe.js', './slidePic.js'], function(){
				slideWrap.slidePic(
					slideWrap.hasClass('J_scrollPic') ? {
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
							gnLockScreen.openLocalActivity(prevTheme, true);
						},
						afterLast: function(){
							var nextTheme = slideWrap.attr('data-nexttheme');
							if(!nextTheme) return;
							gnLockScreen.openLocalActivity(nextTheme, true);
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
			//     gnLockScreen.openLocalActivity($(this).attr('data-inftheme')||'');
			// });

			$('.J_itemList').delegate('li a', 'click', function(evt){
				evt.preventDefault();
				gnLockScreen.openLocalActivity($(this).attr('data-inftheme')||'');
			});
		}
	});

	iCat.include(["./core/zepto.js","./core/zepto.extend.js","./core/zepto.ui.js","./widget/refresh.js","./widget/refresh.lite.js","./tempcore.js"],function(){
			gnLockScreen.lazyload('.J_itemList',500);
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
			                    gnLockScreen.lazyload('.J_itemList',1000);
			                    if(data.data.hasnext == false){
			                    	me.disable();
			                    }
		                	}
		                });
		            },
		        });
			} else {
				gnLockScreen.lazyload('.J_itemList',1000);
			}
		$(function(){gnLockScreen.init();});
	},true);

})(ICAT);