(function(iCat, root){
	var _cfg_ = {
		'partslist':{
			curIndex:2,
			ajaxUrl: ''
		}
	};
	//template
	Gou.template =
	{
		/*aHeader: //一级页头
			'<div class="logo-search">\
				<h1>购物大厅</h1>\
				<a class="search" href="<%=url%>">搜索</a>\
			</div>\
			<nav>\
				<ul>\
					<%for(var i=0, len=nav.length; i<len; i++){%>\
						<%if(nav[i].selected){%>\
						<li class="selected"><span><%=nav[i].name%></span></li>\
						<%}else{%>\
						<li><a href="<%=nav[i].link%>"><%=nav[i].name%></a></li>\
						<%}%>\
					<%}%>\
				</ul>\
			</nav>',*/

		bHeader:  //二级页头
				'<div class="title-back">\
					<h1><%=subtitle%></h1>\
					<div class="back"><a href="<%=homeurl%>"></a></div>\
				</div>\
				<%if(_self.data){%>\
				<nav>\
					<ul>\
						<%for(var i=0, len=data.length; i<len; i++){%>\
							<li<%if(data[i].selected){%> class="selected"<%}%>>\
								<a data-ajaxurl="<%=data[i].ajax_url%>"><%=data[i].title%></a>\
							</li>\
						<%}%>\
					</ul>\
				</nav>\
				<%}%>',

		topBanner: //顶部banner
				'<%if(data){%>\
				<div class="ad-pic">\
					<i></i><a href="<%=data%>"></a>\
				</div>\
				<%}%>',

		recommend: //首页推荐位
			'<section class="tj-webapp">\
			<%if(data.length){%>\
				<ul>\
					<%for(var i=0, len=data.length; i<len; i++){%>\
					<li><a href="<%=data[i].link%>"><img src="<%=blankPic%>" data-src="<%=data[i].img%>"></a></li>\
					<%}%>\
				</ul>\
			<%}%>\
			</section>',

		banner: //轮播广告
			'<section class="scroll-banner">\
				<div class="pic">\
					<ul>\
						<%for(var i=0, len=data.length; i<len; i++){%>\
						<li><a href="<%=data[i].link%>"><img src="<%=data[i].img%>" alt="<%=data[i].title%>"><span><%=data[i].title%></span></a></li>\
						<%}%>\
					</ul>\
				</div>\
				<div class="handle">\
					<%for(var j=0, len=data.length; j<len; j++){%>\
					<span<%if(j==0){%> class="on"<%}%>></span>\
					<%}%>\
				</div>\
			</section>',

		points: //首页积分链接
			'<section class="jf-links">\
				<ul>\
					<%for(var i=0, len=data.length; i<len; i++){%>\
					<li><a href="<%=data[i]%>"></a></li>\
					<%}%>\
				</ul>\
			</section>',

		notice: '', //首页消息通知

		icoItem: //首页 购物商城/便民助手
			'<section class="ico-show">\
				<h2><%=title%></h2>\
				<%if(data.length){%>\
				<ul>\
					<%for(var i=0, len=data.length; i<len; i++){%>\
					<li>\
						<a href="<%=data[i].link%>">\
							<span><img src="<%=blankPic%>" data-src="<%=data[i].img%>"></span>\
							<em><%=data[i].name%></em>\
						</a>\
					</li>\
					<%}%>\
				</ul>\
				<%}%>\
			</section>',

		footer: //首页底部
			'<footer class="ft">\
				<div class="help">\
					<span>官网QQ群：237057997</span>\
					<span>微信公共账号：金小号</span>\
				</div>\
				<div class="copyright">\
					<p>copyright &copy; 2012 粤ICP备05087105号</p>\
				</div>\
			</footer>',

		gotop: //返回顶部
			'<div class="gotop"><a href="javascript:window.scrollTo(0,1);">返回顶部</a></div>',

		codSearch: //
			'<div class="cod-search">\
				<form action="<%=data.action%>"><input type="text" name="<%=data.name%>" placeholder="<%=data.keyword%>" /><button></button></form>\
			</div>',

		codList: //货到付款列表
			'<%for(var i=0, ilen=data.list.length; i<ilen; i++){%>\
			<section class="item<%=(data.list[i].type_dir===1? " pic-right":" pic-left")%>">\
				<figure class="clearfix">\
					<div class="c-main">\
						<div class="pic">\
							<a href="<%=data.list[i].img_data.link%>"><img src="<%=blankPic%>" data-src="<%=data.list[i].img_data.img%>" /></a>\
							<div class="mask"><%=data.list[i].img_data.title%></div>\
						</div>\
					</div>\
					<div class="desc c-sub" style="background-color:<%=(data.list[i].color? data.list[i].color:"#f57766")%>"><a href="<%=data.list[i].link%>"><h3><%=data.list[i].type_name%></h3></a></div>\
				</figure>\
				<div class="detail">\
					<ul><%var codlinks=data.list[i].text_data; while(codlinks.length){%>\
						<li><a href="<%=codlinks[0].link%>" style="color:<%=codlinks[0].color%>"><%=codlinks[0].title%></a></li>\
					<%codlinks.shift();}%></ul>\
				</div>\
			</section>\
			<%}%>',

		picList: //新品列表
			'<div class="list_item">\
				<ul>\
					<%for(var i=0, len=data.list.length; i<len; i++){%>\
					<li>\
						<a href="<%=data.list[i].link%>">\
							<div class="pic">\
								<img src="<%=blankPic%>" data-src="<%=data.list[i].img%>" alt="">\
							</div>\
							<div class="desc1"><%=data.list[i].descrip%></div>\
						</a>\
					</li>\
					<%}%>\
				</ul>\
			</div>',

		shopList: //淘宝好店
			'<div class="list">\
				<ul>\
					<%for(var i=0, len=data.list.length; i<len; i++){%>\
					<li>\
						<a href="<%=data.list[i].shop_url%>">\
						<dl>\
							<dt><%=data.list[i].num%></dt>\
							<dd>\
								<p><%=data.list[i].title%></p>\
								<p class="level">\
									<img src="<%=data.list[i].credit_img%>" alt="<%=data.list[i].title%>" />\
								</p>\
							</dd>\
						</dl>\
							<div class="pic">\
								<%if(data.list[i].goods!==null){%>\
								<%for(var j=0, l=data.list[i].goods.length; j<l; j++){%><span><img src="<%=data.list[i].goods[j]%>" /></span><%}%>\
								<%}%>\
							</div>\
						</a>\
					</li>\
					<%}%>\
				</ul>\
			</div>'
	};

	//view
	var bi = typeof t_bi != 'undefined'? ('t_bi=' + t_bi) : '',
		i = 0;
	Gou.view = iCat.View.extend({
		config: {
			multiChild: true,
			dataSave: true
		},
		blankPic: iCat.PathConfig.appRef.replace(/assets\/js\//g, 'pic/blank.gif')
	});

	Gou.view.setting = {
		/*'topBanner': {
			config: {
				tempId: 'topBanner',
				wrap: '.top-banner',
				ajaxUrl: '/api/apk_gou/download',
				hooks: {'&': '.J_topBanner'},
				events: [
					{selector:'.J_topBanner i', type:'singleTap', callback: 'hidebox'},
				]
			},

			hidebox: function(){
				var me = $(this), parent = me.parents('.J_topBanner');
				parent.hide();
			},

			loadurl: typeof apk_download_url != 'undefined'? apk_download_url : ''
		},*/

		/*'header': {//页头
			config: {
				tempId: 'aHeader',
				wrap: '#iHeader',
				multiChild: false,
				dataSave: false
			},
			nav: [
				{name:'推 荐', link:iCat.util.fullUrl('', bi)},
				{name:'货到付款', link:iCat.util.fullUrl('/cod', bi)},
				{name:'个人中心', link:iCat.util.fullUrl('/user/account/index', bi)}
			],
			url:"/search?refer="+escape(document.location.href)
		},*/

		'subheader': {
			config: {
				tempId: 'bHeader',
				wrap: '#iHeader',
				multiChild: false,
				dataSave: false
			},
			homeurl: iCat.util.fullUrl('', bi)
		},

		// ------------
		/*'recommend': {//推荐
			config: {
				tempId: 'recommend',
				wrap: '#iScroll',
				ajaxUrl: '/api/apk_gou/ad'
			}
		},*/
		'banner': {//轮播广告
			config: {
				tempId: 'banner',
				wrap: '#iScroll',
				ajaxUrl: '/api/apk_gou/ad?'+bi,
				delay: 3000,
				hooks: {
					'&>0': ['.J_scrollBanner', 'curIndex~0'],
				},
				callback: function(p, cfg){
					var wrap = $('.J_scrollBanner'),
						sPannel = wrap.find('.pic ul'),
						item = sPannel.find('li'),
						len = item.length,
						handles = wrap.find('.handle span'),

					scroll = function(index){
						if(index==len){
							index = 0;
							//sPannel.css({'-webkit-transform':'translate3d(-'+w*index+'px, 0, 0)', '-webkit-transition':'0ms'});
							sPannel.animate({left:0}, 0);
						} else {
							//sPannel.css({'-webkit-transform':'translate3d(-'+w*index+'px, 0, 0)', '-webkit-transition':'500ms'});
							sPannel.animate({left:(-item.width()*index)+'px'}, 500);
						}
						handles.removeClass('on').eq(index).addClass('on');
						wrap.attr('curIndex', index);
					},

					autoplay = function(isFirst){
						var timer = setTimeout(function(){
							if(isFirst) sPannel.width(item.width()*len);
							var curIndex = parseInt(wrap.attr('curIndex')) + 1;
							scroll(curIndex);
							autoplay();
						}, cfg.delay);
					};

					autoplay(true);
				}
			}
		},
		'points': {//小banner
			config: {
				tempId: 'points',
				wrap: '#iScroll',
				ajaxUrl: '/api/apk_gou/link',
				getBIData: function(d){
					var arr = [];
					d.data.forEach(function(i){
						arr.push(iCat.util.fullUrl(i, bi));
					});
					d.data = arr;
					return d;
				}
			}
		},
		'notice': {//通告
			config: {
				tempId: 'notice',
				wrap: '#iScroll'
			}
		},
		'mall': {//商场
			config: {
				tempId: 'icoItem',
				wrap: '#iScroll',
				ajaxUrl: '/api/apk_gou/mall'
			},
			title: '综合购物商城'
		},
		'theme': {//主题店
			config: {
				tempId: 'icoItem',
				wrap: '#iScroll',
				ajaxUrl: '/api/apk_gou/theme'
			},
			title: '主题店'
		},
		'tuan': {//团购&折扣
			config: {
				tempId: 'icoItem',
				wrap: '#iScroll',
				ajaxUrl: '/api/apk_gou/tuan'
			},
			title: '团购&折扣'
		},
		'helper': {//助手
			config: {
				tempId: 'icoItem',
				wrap: '#iScroll',
				ajaxUrl: '/api/apk_gou/helper'
			},
			title: '生活便民助手'
		},
		'footer': {//页尾
			config: {
				tempId: 'footer',
				wrap: '#iScroll'
			}
		},
		'gotop': {//返回顶部
			config: {
				tempId: 'gotop',
				wrap: '#iScroll'
			}
		},

		// ------------

		'codSearch': {
			config: {
				tempId: 'codSearch',
				wrap: '#iScroll',
				multiChild: true,
				hooks: {'&': '.cod-list'},
				ajaxUrl: '/api/apk_cod/search',
				callback: function(){
					$('#iScroll').css('min-height', $(window).height() + 'px');
				}
			}
		},

		'list': {
			config: {
				tempId: 'codList',
				wrap: '#iScroll',
				multiChild: true,
				ajaxUrl: '/api/apk_cod/index',
				callback: function(parent, config, initDataInfo){
					Gou.view.setting.loadingData(parent, config, initDataInfo, 1);
				}
			}
		},

		// ---------
		
		'newlist': {
			config: {
				tempId: 'picList',
				wrap: '#iScroll',
				hooks: {'&<0': '.new-wrap'},
				ajaxUrl: '/api/apk_new/index',
				multiChild: true,
				callback: function(parent, config, initDataInfo) {
					Gou.view.setting.loadingData(parent, config, initDataInfo, 1);
				}
			}
		},

		'partslist': {
			config: {
				tempId: 'picList',
				wrap: '#iScroll',
				hooks: {'&<0': '.mall-wrap'},
				ajaxUrl: iCat.util.storage('typeIndex') || '/api/apk_fitting/goods',
				events:[
					{selector:'.J_partsNav li a', type:'singleTap', callback: 'typeShow'},
				],
				multiChild: true,
				callback: function(parent, config, initDataInfo){
					Gou.view.setting.loadingData(parent, config, initDataInfo, 2);
				}
			},

			typeShow: function(v, m){
				var me = $(this), li = $(this.parentNode),
					ajaxUrl = me.attr('data-ajaxurl');
				li.siblings().removeClass('selected');
				li.addClass('selected');
				iCat.util.storage('typeIndex', ajaxUrl);
				$('#iScroll').scrollTop(0).attr('data-ajaxurl', ajaxUrl);
				v.setAjaxUrl(ajaxUrl, false, true);
			}
		},

		loadingData: function(parent, config, initDataInfo, flag){
			//如果两个URL一致，说明是在同一个分类下操作
			var ajaxUrl;
			switch (flag) {
				case 1: ajaxUrl = config.ajaxUrl; break;
				case 2: ajaxUrl = iCat.util.storage('typeIndex') || '/api/apk_fitting/goods';
			}
			if (_cfg_.partslist.ajaxUrl != ajaxUrl) {
				_cfg_.partslist = {
					curIndex: 2,
					ajaxUrl: ajaxUrl,
					hasNext: undefined
				}
			} else {
				_cfg_.partslist.ajaxUrl = ajaxUrl;
			}

			_cfg_.partslist.splite = _cfg_.partslist.ajaxUrl.indexOf('?') > -1 ? '&page=' : '?page=';

			if(_cfg_.partslist.hasNext === undefined) {
				_cfg_.partslist.hasNext = initDataInfo.data.hasnext;
				iCat.util.scroll(document, function(sheight, stop, pheight){
					if (stop + sheight >= pheight) {
						//滑动到底部加载更多
						if(_cfg_.partslist.hasNext === true) {
							_cfg_.partslist.hasNext = false;
							if(!_cfg_.partslist.loading){
								_cfg_.partslist.loading = true;
								$(parent).append('<div class="loading"><i class="icon-img"></i><span class="icon-label">正在加载中...</span></div>');
							} else {
								$(parent).find('.loading').show();
							}
							iCat.util.ajax({
								url: _cfg_.partslist.ajaxUrl + _cfg_.partslist.splite + _cfg_.partslist.curIndex,
								success: function(data) {
									if (data.success) {
										data.blankPic = iCat.PathConfig.appRef.replace(/assets\/js\//g, 'pic/blank.gif');
										_cfg_.partslist.curIndex = data.data.curpage + 1;
										_cfg_.partslist.hasNext = data.data.hasnext;
										setTimeout(function(){
											iCat.View[config.viewId].setData(data, false, false);
											$(parent).find('.loading').hide();
										},1000);
									}
								}
							});
						}
					}
				});
			}
		},

		'shopList': {
			config: {
				tempId: 'shopList',
				wrap: '#iScroll .panel',
				ajaxUrl: '/api/apk_shop/index',
				hooks: {'&<0': ['data-hasnext~true', 'data-curpage~1', 'data-ajaxurl~/api/apk_shop/index']},
				callback: function(parent, config, initDataInfo){
					Gou.view.setting.loadingData(parent, config, initDataInfo, 1);
				}
			}
		}
	};
})(ICAT, window);