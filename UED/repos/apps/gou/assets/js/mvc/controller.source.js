/** GOU-Controller.js # */
(function(iCat){
	var PF = GOU.platform;

	// Class
	GOU.Controller = iCat.Controller.extend({
		config: {
			View: GOU.View,
			Model: GOU.Model
		},

		routes: {
			'home': function(){
				this.fnInit('home', function(id, cfg, tdata){
					if(id==='activityTheme'){
						iCat.foreach(tdata.nav, function(v, i){
							v.selected = i===0;
						});
					}
				});
			},
			'cod': function(){
				this.fnInit('cod', function(id, cfg, tdata){
					if(id==='activityTheme'){
						iCat.foreach(tdata.nav, function(v, i){
							v.selected = i===1;
						});
					}
					if(id==='list'){
						iCat.mix(cfg, {
							tmplId: 'codList',
							ajaxUrl: GOU.fullurl('/api/@cod/index'),
							__base_ajaxurl: GOU.fullurl('/api/@cod/index')
						});
					}
				});
			},
			'bargain': function(){
				this.fnInit('bargain', function(id, cfg, tdata){
					if(id==='activityTheme'){
						iCat.foreach(tdata.nav, function(v, i){
							v.selected = i===2;
						});
					}
					if(id==='list'){
						iCat.mix(cfg, {
							tmplId: 'tejiaList',
							ajaxUrl: GOU.fullurl('/api/@tejia/tejia'),
							__base_ajaxurl: GOU.fullurl('/api/@tejia/tejia')
						});
					}
				});
			},
			'new': function(){
				this.fnInit('new', function(id, cfg, tdata){
					if(id==='subheader'){
						iCat.mix(tdata, {
							subtitle: '新品女装',
							homeurl: GOU.fullurl('/index')
						});
					}
					if(id==='list'){
						iCat.mix(cfg, {
							tmplId: 'picList',
							ajaxUrl: GOU.fullurl('/api/new/index'),
							__base_ajaxurl: GOU.fullurl('/api/new/index')
						});
					}
				});
			},
			'mall': function(){
				this.fnInit('mall', function(id, cfg, tdata){
					if(id==='subheader'){
						iCat.mix(tdata, {
							subtitle: '手机配件',
							homeurl: GOU.fullurl('/index')
						});
					}
					if(id==='subnav'){
						cfg.ajaxUrl = GOU.fullurl('/api/fitting/type');
						cfg.events = {
							'@click|1|0 .J_subnav li a': function(el, evt, m){
								var url = el.getAttribute('data-ajaxurl'),
									view = iCat.View['list'], cfg = view.config;
								$(el.parentNode).addClass('selected').siblings().removeClass('selected');
								$('wraplist').empty();

								m.storage('typeIndex', url);
								delete cfg.__hasnext; cfg.mustdo = true;
								cfg.firePulling = function(){
									delete cfg.mustdo;
									delete cfg.firePulling;
								};
								view.setAjaxUrl(url);
							}
						}
					}
					if(id==='list'){
						var m = iCat.Model[cfg.modelId],
							url = (m.storage('typeIndex') || '/api/fitting/goods').replace(/\s+/g, '');
						iCat.mix(cfg, {
							tmplId: 'picList',
							ajaxUrl: GOU.fullurl(url),
							__base_ajaxurl: GOU.fullurl(url)
						});
					}
				});
			},
			'activities': function(){
				this.fnInit('activities', function(id, cfg, tdata){
					if(id==='subheader'){
						iCat.mix(tdata, {
							subtitle: '热门活动',
							homeurl: GOU.fullurl('/index')
						});
					}
					if(id==='subnav'){
						iCat.foreach(tdata.nav, function(v, i){
							v.selected = i===1;
						});
					}
					if(id==='list'){
						iCat.mix(cfg, {
							mustdo: true,
							tmplId: 'activityList',
							ajaxUrl: GOU.fullurl('/api/amigo_index/activity_list'),
							__base_ajaxurl: GOU.fullurl('/api/amigo_index/activity_list')
						});
					}
				});
			},
			'goods': function(){
				this.fnInit('goods', function(id, cfg, tdata){
					if(id==='subheader'){
						iCat.mix(tdata, {
							subtitle: '热门活动',
							homeurl: GOU.fullurl('/index')
						});
					}
					if(id==='subnav'){
						iCat.foreach(tdata.nav, function(v, i){
							v.selected = i===0;
						});
					}
					if(id==='list'){
						iCat.mix(cfg, {
							tmplId: 'goodsList',
							ajaxUrl: GOU.fullurl('/api/amigo_index/goods_list'),
							__base_ajaxurl: GOU.fullurl('/api/amigo_index/goods_list')
						});
					}
				});
			},
			'brand': function(){
				this.fnInit('brand', function(id, cfg, tdata){

					var data_module = $('.module'),
						index_url = data_module.attr('data-ajaxurl'),
						cate_id = data_module.attr('data-cate_id') || 0;

					data_module = null;

					if(id==='subheader'){
						iCat.mix(tdata, {
							subtitle: '品牌汇',
							homeurl: GOU.fullurl('/index')
						});
					}

					if (id === 'brandBanner') {
						iCat.util.removeData('brand:brandBanner');
						iCat.mix(tdata, {
							cate_id: cate_id,
							detail_url: '/brand/detail'
						});
						iCat.mix(cfg, {
							tmplId: 'brandBanner',
							ajaxUrl: GOU.fullurl(index_url, true),
							__base_ajaxurl: GOU.fullurl(index_url, true)
						});
						// nav tab的下划线
						setTimeout(function(){
							var nav = $('#iHeader').find('.J_subnav');
							nav.find('li[cate-id=\"'+ cate_id +'\"]').addClass('selected').siblings().removeClass('selected');
						}, 500);
					}
					
					if(id==='list'){
						iCat.util.removeData('brand:list');
						iCat.mix(tdata, {
							cate_id: cate_id,
							detail_url: '/brand/detail'
						});

						iCat.mix(cfg, {
							tmplId: 'brandList',
							ajaxUrl: GOU.fullurl(index_url, true),
							__base_ajaxurl: GOU.fullurl(index_url, true),
							renderCompleted: function(){
								var body = $('#brand');
								if(body[0]){
									body.on('touchstart, touchmove, mousedown, mousemove', '.brand-list .mask', function(){
										var that = $(this);
										that.addClass('active');
										setTimeout(function(){ that.removeClass('active'); }, 500);
									}).on('touchend, mouseup', function(){
										$(this).removeClass('active');
									});
								}
							}
						});

					}
				});
			},
			'brandDetail': function(){
				this.fnInit('brandDetail', function(id, cfg, tdata){
					
					var data_module = $('.module'),
						detail_url = data_module.attr('data-desc-ajaxurl'),
						list_url = data_module.attr('data-goods-ajaxurl'),
						cate_id = data_module.attr('data-cate_id');

					data_module = null;

					if (id === 'brandsubheader') {
						iCat.util.removeData('brandDetail:brandsubheader');
						iCat.mix(cfg, {
							ajaxUrl: GOU.fullurl(detail_url),
							__base_ajaxurl: GOU.fullurl(detail_url)
						});
						iCat.mix(tdata, {
							cate_id: cate_id,
							return_url: '/brand/index'
						});
					}

					if(id==='branddesc'){
						iCat.util.removeData('brandDetail:branddesc');
						iCat.mix(cfg, {
							ajaxUrl: GOU.fullurl(detail_url),
							__base_ajaxurl: GOU.fullurl(detail_url),
							callback: function(w, cfg, data) {
								if (iCat.isObject(data.data)) {
									$('title').empty().html(data.data.title);
								} 
							}
						});
					}
					if(id==='list'){
						iCat.util.removeData('brandDetail:list');
						iCat.mix(cfg, {
							ajaxConfig: {
								timeout: 15000
							},
							tmplId: 'brandGoodsList',
							ajaxUrl: GOU.fullurl(list_url),
							__base_ajaxurl: GOU.fullurl(list_url)
						});
					}
				});
			},
			
			'goodshop': function(){
				this.fnInit('goodshop', function(id, cfg, tdata){
					var el, ajaxUrl, uid;

					el = $('#goodshop');
					uid = el.attr('data-uid');
					ajaxUrl = el.attr('data-ajaxurl');

					if(id==='subheader'){
						iCat.mix(tdata, {
							subtitle: '淘宝好店',
							homeurl: GOU.fullurl('/index')
						});
					}
					if (id === 'slideTab') {
						var slideTimer;
						cfg.events = {
							'@click|0|0 #J_slideTab li': function(el, evt, m) {
								var $el = $(el),
									tagId = $el.attr('data-tag-id'),
									tagName = $el.attr('data-tag-name'),
									view = iCat.View['list'], 
									cfg = view.config,
									url;

								$('.J_slideWrap').attr('style', '');
								GOU.addBaiduStatic('GOODSHOP_TAB', $el.text());

								$el.addClass('active').siblings().removeClass('active');
								$('wraplist').empty();
								delete cfg.__hasnext; cfg.mustdo = true;
								cfg.firePulling = function(){
									delete cfg.mustdo;
									delete cfg.firePulling;
								};
								var tagInfo = {
									tagId: tagId,
									tagName: tagName,
									scrollLeft: $el.parent().scrollLeft()
								}
								iCat.util.storage('goodshop:tagInfo', JSON.stringify(tagInfo));
								url = ajaxUrl + (ajaxUrl.indexOf('?')>-1 ? '&' : '?') + 'tag_id=' + tagId;
								cfg.__base_ajaxurl = url;
								view.setAjaxUrl(url);
							},
							'@scroll|0|0 #J_slideTab': function(el, evt, m) {
								if (!slideTimer) {
									var slideTab = $('#J_slideTab'),
										slide = $('#J_slideRight'),
										scrollWidth = slideTab[0].scrollWidth;
									slideTimer = setTimeout(function() {
										(slideTab.outerWidth() + slideTab.scrollLeft() >= scrollWidth) ?
											slide.hide() : slide.show();
										slideTimer = null;
									}, 300)
								}
							},
							'@click|1|0 #J_slideRight': function(el, evt, m) {
								var slideTab = $('#J_slideTab'),
									slide = $('#J_slideRight'),
									scrollWidth = slideTab[0].scrollWidth;
									size = slideTab.find('li').length,
									step = slideTab[0].scrollWidth/size;
								if (slideTab.outerWidth() + slideTab.scrollLeft() <= (scrollWidth-step)) {
									slideTab.scrollLeft(slideTab.scrollLeft() + step);
								}
							}
						}
					}
					if (id === 'list') {
						iCat.util.removeData('goodshop:list');
						var storeTagId = '';
						var storeTagName = '';
						var tagScroll = 0;
						var tagInfo = iCat.util.storage('goodshop:tagInfo');
						if (tagInfo != null) {
							tagInfo = JSON.parse(tagInfo);
							storeTagId = tagInfo.tagId || '';
							storeTagName = tagInfo.tagName || '';
							tagScroll = tagInfo.scrollLeft || 0;
						}

						var favoriteUrl = '/api/@favorite/addshop',
							unfavoriteUrl = '/api/@favorite/remove';

						var slideTab = $('#J_slideTab');
						var slide = $('#J_slideRight');
						var activeLi = slideTab.find('li[data-tag-id="' + storeTagId + '"]');
						
						if (!activeLi.length) {
							storeTagId = '';
							activeLi = slideTab.find('li[data-tag-id=""]')
						}
						activeLi.parent().scrollLeft(tagScroll);
						activeLi.addClass('active').siblings().removeClass('active');

						cfg.events.push({
							'click|0|0 #goodshop .J_aside': function(el, evt, m){
								var elem, shopId, url, data;
								elem = $(el).find('.ico-collect');
								if (elem.length === 0) return;
								shopId = elem.attr('data-shopid');
								if (elem.hasClass('dis')) return;

								if (!elem.hasClass('on')) {
									url = GOU.fullurl(favoriteUrl);
									data = { uid: uid, item_id: shopId }
								} else {
									var favId = elem.attr('fav-id');
									url = GOU.fullurl(unfavoriteUrl);
									data = {id: favId, item_id: shopId, type: 3, uid: uid};
								}

								$.ajax({
									url: url,
									type: 'POST',
									data: data,
									beforeSend: function(){
										elem.addClass('dis');
									},
									complete: function() {
										elem.removeClass('dis');
									},
									success: function(data) {
										if (data.success == 'true' || data.success == true) {
											if (elem.hasClass('on')) {
												elem.removeAttr('fav-id').removeClass('on');
												GOU.showtip('取消收藏');
											} else {
												elem.attr('fav-id', data.data.id).addClass('on');
												GOU.showtip('收藏成功');
											}
										} else {
											GOU.showtip('操作失败，请重试');
										}
									}
								})
							}
						});
						
						iCat.mix(cfg, {
							ajaxConfig: {
								timeout: 15000
							},
							tmplId: 'goodshopList',
							ajaxUrl: GOU.fullurl(ajaxUrl + '&tag_id=' + storeTagId),
							__base_ajaxurl: GOU.fullurl(ajaxUrl + '&tag_id=' + storeTagId)
						});
					}
				})
			},
			'tempt': function(){	// 热门活动-诱货
				this.fnInit('tempt', function(id, cfg, tdata){
					if(id==='subheader'){
						iCat.mix(tdata, {
							subtitle: '淘宝热门',
							homeurl: GOU.fullurl('/index')
						});
					}
					if (id==='scrollList') {
						iCat.mix(cfg, {
							tmplId: 'temptList',
							mustdo: true,
							ajaxUrl: GOU.fullurl('/api/amigo_index/activity_long'),
							__base_ajaxurl: GOU.fullurl('/api/amigo_index/activity_long')
						});
					}
				});
			}
		},

		fnInit: function(page, fn){
			var opt = PF[page];
				opt.baseBed = PF.baseBed;
			this.init(opt, fn);
		}
	});
})(ICAT);