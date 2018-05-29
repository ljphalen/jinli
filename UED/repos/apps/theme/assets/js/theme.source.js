/*
 * Created with Sublime Text 2.
 * User: Hankewins
 * Date: 2014-03-27
 * Time: 15:45:18
 * Contact: hankewins@gmail.com
 */
(function(iCat) {
	iCat.PathConfig();

	var gnTheme = iCat.namespace('gntheme');

	iCat.mix(gnTheme, {
		/**
		 * 选择性调用客户端activity接口
		 * @param  String data     根据字符串参数格式进行判断
		 * @param  Boolean selfPage 是否当前页面调用接口reloadpretheme
		 */
		openclientactivity: function(data, selfPage) {
			if (!data) return;
			data.indexOf(',') > -1 ? gnTheme.startpreactivity(data, selfPage) : gnTheme.startlistctivity(data);
		},
		/**
		 * 调用客户端接口startpreactivity
		 * @param  String data     根据字符串参数格式进行判断
		 * @param  Boolean selfPage 是否当前页面调用接口reloadpretheme
		 * @return {[type]}          [description]
		 */
		startpreactivity: function(data, selfPage) {
			var infTheme = data.split(',');

			if (window.theme) {
				// andorid 调用 js 模式
				window.theme.startNetThemePreActivity(infTheme[4], infTheme[1], infTheme[2], infTheme[3]);
			} else {
				// phonegap 模式
				navigator.gntheme[selfPage ? 'reloadpretheme' : 'startaprectivity'](
					function() {}, //success callback
					function() {}, //error callback
					{
						id: infTheme[4],
						titleTheme: infTheme[1],
						preurl: infTheme[2],
						dlurl: infTheme[3]
					}
				);
			}

		},

		/**
		 * 调用客户端接口startlistctivity
		 * @param  String data 字符串 OR 字符串数组
		 * @return {[type]}      [description]
		 */
		startlistctivity: function(str) {
			var flag = str.indexOf('|') == -1 ? 0 : 1;
			var arr = flag == true ? str.split('|') : str;
			var argus = flag == true ? {listurl: arr[0], topicTheme: arr[1]} : {listurl: arr};

			if (window.theme) {
				if (flag) {
					//alert('startNetThemeListActivity args 2');
					window.theme.startNetThemeListActivity(arr[0], arr[1]);
				} else {
					//alert('startNetThemeListActivity args 1');
					window.theme.startNetThemeListActivity(arr);
				}
			} else {
				//alert("phonegap startlistctivity");
				navigator.gntheme.startlistctivity(
					function() {},
					function() {},
					argus
				);
			}
		},
		// 主题详情页点击图片全屏显示（客户实现）
		// 采用swipe方法是为了修复5511平台对click事件无响应的BUG
		// swipe方法是touchswipe内置方法，提供多个对外事件接口
		fullScreenImg: function(){
			$(".J_scrollPic .pic-wrap li").not('.intro').swipe({
				click: function(){
					var me = $(this),
					imgUrl = me.attr('data-imgurl'),
					themeId = me.attr('data-themeid'),
					imgId = me.attr('data-imageid'),
					argus = {
						imgurl: imgUrl,
						themeid: themeId,
						imageid: imgId
					};
					//alert('img click');
					if (window.theme) {
						//alert('startNetThemeFullScreenActivity');
						window.theme.startNetThemeFullScreenActivity(imgUrl, themeId, imgId);
					} else {
						//alert('phonegap fullScreenPre');
						navigator.gntheme.fullScreenPre(
							function() {},
							function() {},
							argus
						);
					}
				}
			});
		},

		// 图片轮播函数
		slidePic: function() {
			var slideWrap = $('.J_slidePic, .J_scrollPic');
			//console.log(slideWrap);
			if (!slideWrap[0]) return;

			if (slideWrap.hasClass('J_scrollPic')) {
				//图片延时渲染
				setTimeout(function() {
					$('.lazy').each(function() {
						var init_src = $(this).attr("data-original");
						$(this).attr('src', init_src);
						$(this).addClass('done');
					});
				}, 1000);
			}

			var pnBtn = $('.J_handelBtn span');
			pnBtn.on('click', function() {
				if ($(this).hasClass('disabled')) return;
				gnTheme.openclientactivity($(this).attr('data-inftheme') || '', true);
			});

			iCat.include('./iscroll-lite.js', function() {
				var introScroll;
				introScroll = new iScroll('intro-wrap', {
					bounce: false,
					hideScrollbar: false,
					scrollbarClass: 'intro-scrollbar'
				});
			});

			iCat.include(['lib/zepto/touchSwipe.js', './slidePic.js'], function() {
				slideWrap.slidePic(
					slideWrap.hasClass('J_scrollPic') ? {
						slidePanel: '.pic-wrap',
						slideItem: '.pic-wrap li',
						specialWidth: true,
						speed: 200,
						prev: '.J_scrollPic .prev', //使其失效
						next: '.J_scrollPic .next',
						fixCurrent: function() {
							return 1;
						},
						/*beforeFirst: function(){
							var prevTheme = slideWrap.attr('data-prevtheme');
							if(!prevTheme) return;
							gnTheme.openclientactivity(prevTheme, true);
						},
						afterLast: function(){
							var nextTheme = slideWrap.attr('data-nexttheme');
							if(!nextTheme) return;
							gnTheme.openclientactivity(nextTheme, true);
						},*/
						stepCallback: function(curIndex) {
							//每操作一步执行回调
							if (curIndex < 1) {
								$('.pic-wrap').addClass('pic-wrap-tmp');
							} else {
								$('.pic-wrap').removeClass('pic-wrap-tmp');
							}
						}
					} : {
						circle: true,
						auto: true
					}
				);
				gnTheme.fullScreenImg();
			}, true);
		},

		gotoDetail: function() {
			$('.slide-pic').delegate('.pic a', 'click', function(evt) {
				evt.preventDefault();
				gnTheme.openclientactivity($(this).attr('data-inftheme') || '');
			});

			$('.ui-refresh').delegate('li a', 'click', function(evt) {
				evt.preventDefault();
				gnTheme.openclientactivity($(this).attr('data-inftheme') || '');
			});
		},

		init: function() {
			gnTheme.slidePic();
			gnTheme.gotoDetail();
		}
	});
	
	iCat.include(["./dist/zepto.js", "./tempcore.js", "./core/gmu.js", "./core/event.js", "./core/widget.js", "./widget/refresh/refresh.js", "./widget/refresh/$lite.js"], function() {
		// 加载gmu文件是为了解决局部上拉加载的功能（后期可优化）
		iCat.util.lazyLoad('body', 500, 'img[src$="pic_nopreview.png"]');

		var url = $("#J_dataList").data("ajaxurl"), hasnext = $("#J_dataList").data("hasnext"),
			page = {
				curpage: 1,
				hasnext: hasnext == true ? true : false
			};

		if (url) {
			//url = url.indexOf('?') > -1 ?  url+ '&page=': url + '?page=';
			if (hasnext == false) return;
			$('.ui-refresh').refresh({
				load: function(dir, type) {
					var me = this;
					$.ajax({
						url: url,
						type: 'post',
						dataType: 'json',
						data: {
							page: 1 + page.curpage,
							token: token
						},
						success: function(data) {
							if (data.success) {
								page.curpage = data.data.curpage;
								page.hasnext = data.data.hasnext;

								var $list = $('.data-box ul');
								var tmpl = template.render('J_itemView', data);
								$list[dir == 'up' ? 'prepend' : 'append'](tmpl);
								//console.log(tmpl);
								me.afterDataLoading(); //数据加载完成后改变状态
								iCat.util.lazyLoad('body', 500, 'img[src$="pic_nopreview.png"]');
								if (data.data.hasnext == false) {
									$('.ui-refresh-down').hide();
									me.disable();
									if (me.$el.hasClass('home') === true) {
										$('.J_home_topic').show();
									}
								}
							}
						}
					});
				}
			});
		} else {
			iCat.util.lazyLoad('body', 500, 'img[src$="pic_nopreview.png"]');
		}
		// init
		gnTheme.init();
	}, true, true);
})(ICAT);