/*
 * Applaction: gou
 * Author: vk
 * Date: 2013-12-22 19:02:07.
 */

(function(iCat){
	//定义应用
	iCat.app('GOU', function(){
		return {
			version: '0.0.1',
			imgPath: iCat.PathConfig.picPath.slice(0, -4) + 'assets/img/',
			keyword: location.host.split('.')[0],

			platform: function(){
				var cfg, keyword = location.host.split('.')[0];
				switch(keyword){
					case 'apk':
						cfg = {
							prefix: 'apk_', baseBed: '.module',
							'home': {
								adjustLayout: 'div#iScroll',
								modules: 'banner, links, servpipe, mall, theme, tuan, helper, appFooter'
							},
							'cod': {
								adjustLayout: 'div#iScroll',
								modules: 'gotop, codSearch, list'
							},
							'bargain': {
								adjustLayout: 'div#iScroll',
								modules: 'gotop, list'
							},
							'new': {
								adjustLayout: 'div#iScroll',
								modules: 'gotop, list'
							},
							'mall': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'subnav, gotop, list'
							},
							'activities': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'subnav, gotop, list'
							},
							'goods': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'subnav, list'
							},
							'brand': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'brandnav, brandBanner, gotop, list'
							},
							'brandDetail': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'branddesc, gotop, list'
							},
							'goodshop': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'slideTab, gotop, list'
							},
							'tempt': {	// 官方商城-诱货
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'scrollList'
							}
						};
						break;

					case 'channel':
						cfg = {
							prefix: 'channel_', baseBed: '.module',
							'home': {
								adjustLayout: 'div#iScroll',
								modules: 'banner, links, mall, theme, tuan, helper, appFooter'
							},
							'cod': {
								adjustLayout: 'div#iScroll',
								modules: 'gotop, codSearch, list'
							},
							'bargain': {
								adjustLayout: 'div#iScroll',
								modules: 'gotop, list'
							},
							'new': {
								adjustLayout: 'div#iScroll',
								modules: 'gotop, list'
							},
							'mall': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'subnav, gotop, list'
							},
							'activities': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'subnav, gotop, list'
							},
							'goods': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'subnav, list'
							},
							'goodshop': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'slideTab, gotop, list'
							},
							'tempt': {	// 官方商城-诱货
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'scrollList'
							}
						};
						break;
					
					case 'market':
						cfg = {
							prefix: 'market_', baseBed: '.module',
							'home': {
								adjustLayout: 'div#iScroll',
								modules: 'banner, links, mall, theme, tuan, helper, appFooter'
							},
							'cod': {
								adjustLayout: 'div#iScroll',
								modules: 'gotop, codSearch, list'
							},
							'bargain': {
								adjustLayout: 'div#iScroll',
								modules: 'gotop, list'
							},
							'new': {
								adjustLayout: 'div#iScroll',
								modules: 'gotop, list'
							},
							'mall': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'subnav, gotop, list'
							},
							'activities': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'subnav, gotop, list'
							},
							'goods': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'subnav, list'
							},
							'goodshop': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'slideTab, gotop, list'
							},
							'tempt': {	// 官方商城-诱货
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'scrollList'
							}
						};
						break;
					
					case 'ios':
						cfg = {
							prefix: 'ios_', baseBed: '.module',
							'home': {
								adjustLayout: 'div#iScroll',
								modules: 'banner, links, servpipe, mall, theme, tuan, helper, appFooter'
							},
							'cod': {
								adjustLayout: 'div#iScroll',
								modules: 'gotop, codSearch, list'
							},
							'bargain': {
								adjustLayout: 'div#iScroll',
								modules: 'gotop, list'
							},
							'new': {
								adjustLayout: 'div#iScroll',
								modules: 'gotop, list'
							},
							'mall': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'subnav, gotop, list'
							},
							'activities': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'gotop, list'
							},
							'goods': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'subnav, list'
							},
							'brand': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'brandnav, brandBanner, gotop, list'
							},
							'brandDetail': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'branddesc, gotop, list'
							},
							'goodshop': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'slideTab, gotop, list'
							},
							'tempt': {	// 官方商城-诱货
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'scrollList'
							}
						};
						break;

					case 'super':
						cfg = {
							prefix: '', baseBed: '.module',
							'home': {
								adjustLayout: 'div#iScroll',
								modules: 'banner, links, mall, theme, tuan, helper, appFooter'
							},
							'cod': {
								adjustLayout: 'div#iScroll',
								modules: 'gotop, codSearch, list'
							},
							'new': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'subheader, gotop, list'
							},
							'mall': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'subnav, gotop, list'
							},
							'activities': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'subnav, gotop, list'
							},
							'goods': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'subnav, list'
							},
							'tempt': {	// 官方商城-诱货
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'scrollList'
							}
						};
						break;

					case 'client':
						cfg = {
							prefix: 'apk_', baseBed: '.module',
							'home': {
								adjustLayout: 'div#iScroll',
								modules: 'banner, links, servpipe, mall, theme, tuan, helper, appFooter'
							},
							'cod': {
								adjustLayout: 'div#iScroll',
								modules: 'gotop, codSearch, list'
							},
							'new': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'subheader, gotop, list'
							},
							'mall': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'subheader, subnav, gotop, list'
							},
							'activities': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'subheader, subnav, gotop, list'
							},
							'goods': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'subnav, list'
							},
							'brand': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'subheader, brandnav, brandBanner, gotop, list'
							},
							'brandDetail': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'brandsubheader, branddesc, gotop, list'
							},
							'tempt': {	// 官方商城-诱货
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'subheader, scrollList'
							}
						};
						break;
					case 'app':
						cfg = {
							prefix: 'app_', baseBed: '.module',
							'home': {
								adjustLayout: 'div.J_topBanner + header#iHeader.hd + div#iScroll',
								modules: 'activityTheme, recommend, amigoMall, mall, theme, tuan, helper, app2Footer'
							},
							'cod': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'activityTheme, gotop, codSearch, list'
							},
							'bargain': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'activityTheme, gotop, list'
							},
							'new': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'subheader, gotop, list'
							},
							'mall': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'subheader, subnav, gotop, list'
							},
							'activities': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'subheader, subnav, gotop, list'
							},
							'goods': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'subheader, subnav, list'
							},
							'brand': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'subheader, brandnav, brandBanner, gotop, list'
							},
							'brandDetail': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'brandsubheader, branddesc, gotop, list'
							},
							'goodshop': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'subheader, slideTab, gotop, list'
							},
							'tempt': {	// 官方商城-诱货
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'subheader, scrollList'
							}
						};
						break;
					default:
						cfg = {
							prefix: '', baseBed: '.module',
							'home': {
								adjustLayout: 'div.J_topBanner + header#iHeader.hd + div#iScroll',
								modules: 'topBanner, activityTheme, recommend, amigoMall, mall, theme, tuan, helper, footer'
							},
							'cod': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'activityTheme, gotop, codSearch, list'
							},
							'bargain': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'activityTheme, gotop, list'
							},
							'new': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'subheader, gotop, list'
							},
							'mall': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'subheader, subnav, gotop, list'
							},
							'activities': {	// 热门活动
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'subheader, subnav, gotop, list'
							},
							'goods': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'subheader, subnav, list'
							},
							'brand': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'subheader, brandnav, brandBanner, gotop, list'
							},
							'brandDetail': {
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'brandsubheader, branddesc, gotop, list'
							},
							'goodshop': {	// 淘宝好店
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'subheader, slideTab, gotop, list'
							},
							'tempt': {	// 官方商城-诱货
								adjustLayout: 'header#iHeader.hd + div#iScroll',
								modules: 'subheader, scrollList'
							}
						};
				}
				return cfg;
			}(),

			fullurl: function(url, hasBI){
				if(url===undefined) return '';
				hasBI = !!hasBI;

				var bi = window['t_bi'] || '', arr,
					_url = url.replace(/@/, GOU.platform.prefix),
					hasArgus = url.indexOf('?')!==-1, hasHash = url.indexOf('#')!==-1;
					hasBI = hasBI && bi,
					demoPath = '';

				if (iCat.DemoMode) { // http://dev.demo.gionee.com/gou/new 域名
					demoPath = location.protocol + '//' + location.host + '/gou/new/';
				}

				if(iCat.DemoMode && !/^(\w+:\/{2,}|[^\/])/.test(_url)) _url = _url.slice(1);
				if(hasArgus){
					arr = _url.split('?');
					if(iCat.DemoMode) {
						arr[0] = arr[0].replace(/([^\.]\w+)$/, '$1.php');
						arr[0] = demoPath + arr[0];
					}
					arr[1] = arr[1] + (hasBI? '&t_bi='+bi : '');
					return arr.join('?');
				}
				else if(hasHash){
					arr = _url.split('#');
					if(iCat.DemoMode) {
						arr[0] = arr[0].replace(/([^\.]\w+)$/, '$1.php');
						arr[0] = demoPath + arr[0];
					}
					arr[1] = (hasBI? '?t_bi='+bi : '') + '#' + arr[1];
					return arr.join('');
				}
				else {
					if (iCat.DemoMode) _url = demoPath + _url.replace(/([^\.]\w+)$/, '$1.php');
					return _url + (hasBI? '?t_bi='+bi : '');
				}
			},

			showtip: function(msg){
				var body = $('body'),
					tip = body.find('.J_msgTip');
				if(tip[0]){
					//mask.show();
					tip.html(msg);
					tip.parent().show();
				} else {
					body.append('<div class="msg-tip"><span class="J_msgTip">'+msg+'</span></div>');
					tip = body.find('.J_msgTip');
				}

				setTimeout(function(){
					//mask.hide();
					tip.parent().hide();//fixed bug: 行内元素居然不起作用。。。
				}, 3000);
			},

			scrollLoad: function(el) {
				if (scrollTimer) clearTimeout();
				var scrollTimer = setTimeout(function() {
					var offsetTop = window.pageYOffset ? window.pageYOffset : document.documentElement.scrollTop,
						offsetWin = offsetTop + (window.innerHeight ? window.innerHeight : document.documentElement.clientHeight),
						imgs = $('img[src$="blank-img.gif"]');
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
							if (src != null) img.attr('src', src)
						}
					})
				}, 300)
			},

			// 适应全屏
			fitScreen: function() {
				var doc = window.document, 
					root = doc.documentElement,
					currentW = 320, maxWidth = 540, minWidth = 320, timer,
					flexible = function() {
						currentW = root.getBoundingClientRect().width;
						currentW = currentW > maxWidth ? maxWidth : (currentW < minWidth ? minWidth : currentW);
						root.style.fontSize = currentW / 16 + 'px';
					};
				window.addEventListener('resize', function(){
					if (timer) clearTimeout(timer);
					timer = setTimeout(function() { flexible() }, 0);
				}, false)
				flexible();
			},
			// 百度统计代码
			baiduStatic: function(id, action, label) {
				// alert(id + ', ' + action + ', ' + label);
				try {
					if (!_hmt || !id || !action) return;
					label = label || '';
					_hmt.push(['_trackEvent', id, action, label]);
				} catch(e) {};
			},

			baiduStaticConfig: {
				// 淘宝热门
				'FILTER': {id: '淘宝热门', action: '筛选', label: '筛选的点击次数'},
				'FILTER_PLATFORM': {id: '淘宝热门', action: '筛选', label: '@@的点击次数'},
				// 淘宝好店
				'GOODSHOP_TAB': {id: '淘宝好店', action: 'tab', label: '@@的点击次数'},
				// 砍价游戏
				'CUTGAME_REFUEL_OPEN': {id: '砍价游戏分享', action: '页面打开', label: '页面打开次数'},
				'CUTGAME_REFUEL_WANT': {id: '砍价游戏分享', action: '我也要玩', label: '我也要玩点击次数'},
				'CUTGAME_REFUEL_CHEER': {id: '砍价游戏分享', action: '为它助力', label: '为他助力次数'},
				'CUTGAME_REFUEL_DOWNLOAD': {id: '砍价游戏分享', action: '页面头部下载', label: '下载按钮点击次数'},
				'CUTGAME_AWARD_RECORD': {id: '我的获奖记录', action: '领取大奖', label: '点击确定按钮次数'},
				'CUTGAME_AWARD_RESET': {id: '我的获奖记录', action: '领取大奖', label: '重置次数'},
				'ACTIVITY_RECORD_RECEIVE': {id: '我的活动记录', action: '立即领取', label: '立即领取点击次数'},
				'ACTIVITY_RECORD_HIT': {id: '我的活动记录', action: '我的获奖记录', label: '点击次数'},
				'ACTIVITY_RANK': {id: '我的活动记录', action: '查看排行榜', label: '点击第@@期次数'},
				'GOODPRODUCT-GOODS': {id: '良品', action: '商品', label: '点击商品id=@@的次数'},
				'GOODPRODUCT-SHARE-BUTTON': {id: '良品', action: '分享', label: '点击分享按钮的次数'},
				'GOODPRODUCT-SHARE-COLLECT': {id: '良品', action: '收藏', label: '点击收藏按钮的次数'},
				'GOODPRODUCT-SHARE-QQ': {id: '良品', action: '分享', label: 'QQ'},
				'GOODPRODUCT-SHARE-QZONE': {id: '良品', action: '分享', label: 'QZONE'},
				'GOODPRODUCT-SHARE-WEIBO': {id: '良品', action: '分享', label: 'WEIBO'}
			},

			addBaiduStatic: function(type, unique) {
				var _id, _action, _label;
				var action = GOU.baiduStaticConfig[type];
				if (!action) return;
				if (unique == null) unique = '';
				_label = action.label.replace('@@', unique);
				if (action.id) {
					_id = (GOU.keyword == 'gou' ? '' : GOU.keyword) + action.id;
					_action = [_id, action.action].join('-');
					_label = [_action, _label].join('-');
					GOU.baiduStatic(_id, _action, _label);
				}
			},

			debug: function(msg) {
				if (msg.nodeType) return;
				if (typeof msg == 'object') {
					msg = JSON.stringify(msg);
				}
				var debugWrap = document.getElementById('debug-wrap');
				if (!debugWrap) {
					debugWrap = document.createElement('div');
					debugWrap.id = 'debug-wrap';
					document.body.appendChild(debugWrap);
				}
				var div = document.createElement('div');
				div.className = 'info';
				div.innerText = msg;
				debugWrap.appendChild(div);
			},

			// 判断当前版本是否是可用版本
			isVailableVersion: function(version) {
				var curVersion;
				if (GOU.keyword === 'ios') {
					curVersion = window.share && window.share.getVersionName;
				} else if (GOU.keyword === 'apk') {
					curVersion = window.share && window.share.getVersionName && window.share.getVersionName();
				}

				if (curVersion) {
					var cur = curVersion.split('.');
					var arr = version.split('.');
					if (cur[0]*1 < arr[0]*1) return false;
					if (cur[0]*1 > arr[0]*1) return true;
					
					if (cur[1]*1 < arr[1]*1) return false;
					if (cur[1]*1 > arr[1]*1) return true;

					if (cur[2]*1 < arr[2]*1) return false;
					if (cur[2]*1 >= arr[2]*1) return true;
				}
			},

			init: function(){
				if(window['singlePage'] && singlePage){
					iCat.include(['jQuery', './single/index'], undefined, true, !(iCat.DemoMode || iCat.DebugMode));
				}
				else if(window['webPage'] && webPage || /\/user\//.test(location.href)){
					iCat.include(['/sys/lib/zepto/zepto-1.1.6', './web/index'], undefined, true, !(iCat.DemoMode || iCat.DebugMode));
				}
				else {
					iCat.require({
						modName: 'appmvc', isCombo: !(iCat.DemoMode || iCat.DebugMode),
						callback: function(){
							iCat.include(['../css/mvc.css', 'jQuery'], function(){
								var c = new GOU.Controller('mc');
							});
							// for tongji
							/*var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
							iCat.include([
								_bdhmProtocol + 'hm.baidu.com/h.js?54f9efbc293ab61ca3f751a53d4594d5!',
								_bdhmProtocol + 'stats.gionee.com/stats/g.js?0F383847972EECB5!'
							]);*/
						}
					});
				}
				if (GOU.keyword === 'ios') GOU.fitScreen();
			}

		};
	});

	//初始化
	GOU.init();

})(ICAT);