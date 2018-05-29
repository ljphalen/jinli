/** GOU-view.js # */
(function(iCat){
	// templates
	GOU.template = 
	{
		topBanner: //顶部banner
			'<%if(data.link){%>\
			<div class="ad-pic" style="background-image:url(<%=data.img%>);">\
				<i></i><a href="<%=data.link%>"></a>\
			</div>\
			<%}%>',

		gotop: //返回顶部
			'<div class="gotop" style="display:none;opacity:1;">\
				<span>返回顶部</span>\
			</div>',
		
		aHeader: //一级页头
			'<div class="top-wrap">\
				<div class="logo">\
					<h1>购物大厅</h1>\
					<a class="search" href="<%=url%>">\
						<em>请输入商品名</em><i></i>\
					</a>\
				</div>\
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
			</nav>',

		bHeader: //二级页头
			'<div class="top-wrap">\
				<%if(data.length){%>\
				<div class="title">\
					<a href="<%=data[0].homeurl%>" class="back"></a>\
					<h1><%=data[0].subtitle%></h1>\
				</div>\
				<%}else{%>\
				<div class="title">\
					<a href="<%=homeurl%>" class="back"></a>\
					<h1><%=subtitle%></h1>\
				</div>\
				<%}%>\
			</div>',

		brandSubHeader: //二级页头
			'<div class="top-wrap">\
				<div class="title">\
					<a href="<%=GOU.fullurl(return_url+\'?cate_id=\'+cate_id, true)%>" class="back"></a>\
					<h1><%=data.title%></h1>\
				</div>\
			</div>',

		activityTheme: // 春节专题头部http://apk.gou.3gtest.gionee.com/tejia?t_bi=_4214447139
			'<div class="activity-theme-wrap">\
				<a class="search" href="<%=url%>"></a>\
				<ul class="nav">\
					<%for(var i=0, len=nav.length; i<len; i++){%>\
						<%if(nav[i].selected){%>\
						<li class="selected"><span><%=nav[i].name%></span></li>\
						<%}else{%>\
						<li><a href="<%=nav[i].link%>"><%=nav[i].name%></a></li>\
						<%}%>\
					<%}%>\
				</ul>\
			</div>',

		subnav: //二级子导航
			'<nav>\
				<%if(data.length){%>\
				<ul class="J_subnav">\
					<%for(var i=0, len=data.length; i<len; i++){%>\
						<li<%if(data[i].selected){%> class="selected"<%}%>>\
							<a data-ajaxurl="<%=data[i].ajax_url%>"><%=data[i].title%></a>\
						</li>\
					<%}%>\
				</ul>\
				<%}else{%>\
				<ul>\
					<%for(var i=0, len=nav.length; i<len; i++){%>\
						<%if(nav[i].selected){%>\
						<li class="selected"><span><%=nav[i].name%></span></li>\
						<%}else{%>\
						<li><a href="<%=nav[i].link%>"><%=nav[i].name%></a></li>\
						<%}%>\
					<%}%>\
				</ul>\
				<%}%>\
			</nav>',

		activityNav: // 热门活动nav
			'<nav>\
				<ul>\
					<%for(var i=0, len=nav.length; i<len; i++){%>\
						<%if(nav[i].selected){%>\
							<%if(i==0) {%>\
								<li class="selected">\
									<span><%=nav[i].name%><i class="ico-put"></i></span>\
								</li>\
							<%} else {%>\
								<li class="selected"><span><%=nav[i].name%></span></li>\
							<% } %>\
						<%}else{%>\
						<li><a target="_self" href="<%=nav[i].link%>"><%=nav[i].name%></a></li>\
						<%}%>\
					<%}%>\
				</ul>\
			</nav>',

		activityFilter: // 热门活动-筛选
			'<div id="J_filterWrap" class="filter-wrap">\
				<nav class="filter">\
					<ul>\
						<li>\
							<span class="txt">精选淘宝热门频道</span>\
							<span class="filter-btn">筛选<i class="ico-put gray"></i></span>\
						</li>\
					</ul>\
				</nav>\
				<div id="J_merchantWrap"></div>\
			</div>',

		brandnav: //二级子导航
			'<nav>\
				<ul class="J_subnav">\
					<li cate-id="0">\
						<a href="<%=GOU.fullurl(\'/brand/index?cate_id=0\', true)%>">推荐</a>\
					</li>\
					<%for(var i=0, len=data.list.length; i<len; i++){%>\
						<li cate-id="<%=data.list[i].id%>" <%if(data.list[i].selected){%> class="selected"<%}%>>\
							<a href="<%=GOU.fullurl(\'/brand/index?cate_id=\'+data.list[i].id, true)%>"><%=data.list[i].title%></a>\
						</li>\
					<%}%>\
				</ul>\
			</nav>',


		banner: //轮播广告
			'<section class="scroll-banner J_scrollBanner" curIndex="0">\
				<div class="pic">\
					<ul>\
						<%for(var i=0, len=data.length; i<len; i++){%>\
						<li><a href="<%=data[i].link%>">\
							<img src="<%=blankPic%>" data-src="<%=data[i].img%>" alt="<%=data[i].title%>">\
							<span><%=data[i].title%></span>\
						</a></li>\
						<%}%>\
					</ul>\
				</div>\
				<div class="handle">\
					<%for(var j=0, len=data.length; j<len; j++){%>\
					<span<%if(j==0){%> class="on"<%}%>></span>\
					<%}%>\
				</div>\
			</section>',

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

		links: //首页积分链接 <%if(data.ad1.length){%> style="height:auto;"<%}%>
			'<section class="jf-links">\
				<ul>\
					<li>\
						<%for(var i=0, ilen=data.ad1.length; i<ilen; i++){%>\
						<a href="<%=GOU.fullurl(data.ad1[i].link, true)%>">\
							<span><img src="<%=blankPic%>" data-src="<%=data.ad1[i].img%>" alt="<%=data.ad1[i].title%>"></span>\
						</a>\
						<%}%>\
					</li>\
					<li>\
						<%for(var j=0, jlen=data.ad2.length; j<jlen; j++){%>\
						<a href="<%=GOU.fullurl(data.ad2[j].link, true)%>">\
							<span><img src="<%=blankPic%>" data-src="<%=data.ad2[j].img%>" alt="<%=data.ad2[j].title%>"></span>\
						</a>\
						<%}%>\
					</li>\
				</ul>\
			</section>',

		servpipe: //便捷服务 src="<%=blankPic%>" data-
			'<section class="serv-pipe">\
			<%if(data.ad.length){%>\
				<ul>\
					<%for(var i=0, len=data.ad.length; i<len; i++){%>\
					<li><a href="<%=data.ad[i].link%>"><img src="<%=data.ad[i].img%>"><%=data.ad[i].title%></a></li>\
					<%}%>\
				</ul>\
			<%}%>\
			</section>',

		amigoMall: //ami商场
			'<%if(data.link){%>\
			<section class="amigo-mall">\
				<div class="pic">\
					<a href="<%=data.link%>"><img src="<%=blankPic%>" data-src="<%=data.img%>" alt="<%=data.title%>"></a>\
				</div>\
			</section>\
			<%}%>',

		icoItem: //商城/主题/团购/助手
			'<section class="ico-list">\
				<h2 style="background-image:url(<%=(GOU.imgPath+icon)%>);"><%=title%></h2>\
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
					<a href="/weixin">点此关注微信公共账号，随时可知天下事。</a>\
				</div>\
				<div class="copyright">\
					<p>增值电信许可证: 粤B2-20120350</p>\
					<p>copyright &copy; 2012 <a target="_blank" href="http://www.miitbeian.gov.cn/">粤ICP备05087105号</a></p>\
					<p>网络文化经营许可证：粤网文[2013]029-029号</p>\
					<p>粤文市审[2012]196号</p>\
				</div>\
			</footer>',
		
		appFooter: //app的首页底部
			'<footer class="ft appft">\
					<div class="help '+(/ouxin/i.test(location.href)? 'hidden' : '')+'">' +
						(/xiaolajiao/i.test(location.href)?
							'<span>官方社区：bbs.xiaolajiao.com</span><span>微信公共账号：yusunshouji</span>' :
							'<span>官网QQ群：237057997</span><span>微信公共账号：金小号</span>') +
					'</div>\
					<div class="copyright hidden">\
						<p>copyright &copy; 2012 粤ICP备05087105号</p>\
					</div>\
				</footer>',

		app2Footer: //首页底部
			'<footer class="ft">\
				<div class="help">\
					<span>官网QQ群：237057997</span>\
				</div>\
				<div class="copyright">\
					<p>增值电信许可证: 粤B2-20120350</p>\
					<p>copyright &copy; 2012 <a target="_blank" href="http://www.miitbeian.gov.cn/">粤ICP备05087105号</a></p>\
					<p>网络文化经营许可证：粤网文[2013]029-029号</p>\
					<p>粤文市审[2012]196号</p>\
				</div>\
			</footer>',

		codSearch: //货到付款搜索框
			'<div class="cod-search">\
				<form action="<%=data.action%>" method="POST" accept-charset="utf-8">\
					<input type="text" name="<%=data.name%>" placeholder="<%=data.keyword%>" /><button></button>\
				</form>\
			</div>',

		codList: //货到付款列表
			'<%for(var i=0, ilen=data.list.length; i<ilen; i++){%>\
			<section class="cod-item<%=(data.list[i].type_dir===1? " pic-right":" pic-left")%>">\
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
			'<div class="pic-list">\
				<ul>\
					<%for(var i=0, len=data.list.length; i<len; i++){%>\
					<li>\
						<a href="<%=data.list[i].link%>">\
							<div class="pic">\
								<img src="<%=blankPic%>" data-src="<%=data.list[i].img%>">\
							</div>\
							<div class="desc"><%=data.list[i].descrip%></div>\
						</a>\
					</li>\
					<%}%>\
				</ul>\
			</div>',

		tejiaList: //特价列表
			'<div class="tejia-list">\
				<ul>\
					<%for(var i=0, len=data.list.length; i<len; i++){%>\
					<li>\
						<a href="<%=data.list[i].link%>">\
							<div class="pic">\
								<span><img src="<%=blankPic%>" data-src="<%=data.list[i].img%>"></span>\
								<%=(data.list[i].discount<=3? "<i class=\\"discount\\">"+data.list[i].discount+"折</i>" : "")%>\
							</div>\
							<div class="desc">\
								<h3><%=data.list[i].title%></h3>\
								<div class="price">\
									<em>￥<%=data.list[i].sale_price%></em>\
									<%if(data.list[i].market_price){%>\
									<u>￥<%=data.list[i].market_price%></u>\
									<%}%>\
								</div>\
								<div class="from"><%=data.list[i].from%></div>\
							</div>\
						</a>\
					</li>\
					<%}%>\
				</ul>\
			</div>',

		activityList: //活动列表
			'<div class="activities-list">\
				<ul>\
					<%for(var i=0, len=data.list.length; i<len; i++){%>\
					<li<%if(data.list[i].is_end){%> class="time-out"<%}%>>\
						<figure>\
							<div class="pic">\
								<%if(data.list[i].is_end){%>\
									<img src="<%=blankPic%>" data-src="<%=data.list[i].img%>">\
								<%} else {%>\
								<a href="<%=data.list[i].link%>"><img src="<%=blankPic%>" data-src="<%=data.list[i].img%>"></a>\
								<%}%>\
							</div>\
							<div class="desc">\
								<h3><%=data.list[i].title%></h3>\
								<time><%=data.list[i].start_time%>-<%=data.list[i].end_time%></time>\
							</div>\
						</figure>\
						<%if(data.list[i].is_end){%><i class="label">已结束</i><%}%>\
					</li>\
					<%}%>\
				</ul>\
			</div>',

		goodsList: //商品列表
			'<div class="goods-list">\
				<ul>\
					<%for(var i=0, len=data.list.length; i<len; i++){%>\
					<li<%if(data.list[i].isFirst){%> style="padding-bottom:2.4rem;"<%}%>>\
						<a href="<%=data.list[i].link%>"><figure class="item-pictext">\
							<div class="pic">\
								<span><img src="<%=blankPic%>" data-src="<%=data.list[i].img%>"></span>\
							</div>\
							<div class="desc">\
								<h3><%=data.list[i].title%></h3>\
								<p class="price">￥<%=data.list[i].price%></p>\
								<p class="text"><%=data.list[i].short_desc%></p>\
							</div>\
							<div class="options"></div>\
						</figure></a>\
						<%if(data.list[i].isFirst){%><div class="web-btn"><a href="">立即购买</a></div><%}%>\
					</li>\
					<%}%>\
				</ul>\
			</div>',

		brandList: // 品牌汇商标列表
			'<div class="brand-list">\
				<ul>\
					<%for(var i=0, len=data.list.length; i<len; i++){%>\
						<li>\
							<a class="pic" href="<%=GOU.fullurl(detail_url+\'?cate_id=\'+cate_id+\'&brand_id=\'+data.list[i].id, true)%>">\
								<span class="mask"></span>\
								<img src="<%=blankPic%>" data-src="<%=data.list[i].brand_img%>">\
							</a>\
						</li>\
					<%}%>\
				</ul>\
			</div>',

		brandGoodsList: //品牌商品列表
			'<div class="brand-goods-list">\
				<ul>\
					<%for(var i=0, len=data.list.length; i<len; i++){%>\
					<li>\
						<a href="<%=data.list[i].url%>">\
							<div class="pic">\
								<span class="img-wrap">\
									<img src="<%=blankPic%>" data-src="<%=data.list[i].pic_url%>">\
									<span class="price">￥<%=data.list[i].price%></span>\
								</span>\
							</div>\
							<div class="caption">\
								<%=data.list[i].title%>\
							</div>\
						</a>\
					</li>\
					<%}%>\
				</ul>\
			</div>',

		brandBanner: // 品牌汇banner
			'<%if(data.top!=null){%>\
			<div class="brand-banner">\
				<a class="pic" href="<%= GOU.fullurl(detail_url+\'?brand_id=\'+data.top.id+\'&cate_id=\'+cate_id, true)%>">\
					<img src="<%= blankPic %>" data-src="<%= data.top.banner_img %>">\
				</a>\
			</div>\
			<%}%>',
		branddesc: // 品牌详细页面
			'<div class="brand-desc">\
				<span class="pic">\
					<img src="<%=blankPic%>" data-src="<%=data.logo_img%>">\
				</span>\
				<p class="desc"><%= data.brand_desc %></p>\
			</div>',
		slideTab: // 淘宝好店横向滑动条
			'<div class="slide-wrap J_slideWrap">\
				<div class="slide-tab-box">\
					<ul id="J_slideTab" class="slide-tab">\
						<li class="active" data-tag-name="" data-tag-id=""><a href="javascript:;">全部</a></li>\
						<% for(var i=0, len=data.list.length; i<len; i++) {%>\
						<li data-tag-name="<%= data.list[i].name %>" data-tag-id="<%= data.list[i].id %>"><a href="javascript:;"><%= data.list[i].name %></a></li>\
						<% } %>\
					</ul>\
					<div class="slide-right" id="J_slideRight"><i class="ico-arrow-right"></i></div>\
				</div>\
			</div>',
		
		goodshopList: // 淘宝好店
			'<% for(var i=0, len=data.list.length; i<len; i++) { %>\
			<div class="goodshop">\
				<i class="serial ico-serial"><%= data.list[i].num %></i>\
				<div class="cont">\
					<div class="header">\
						<div class="logo">\
							<img src="<%= blankPic %>" data-src="<%= data.list[i].logo %>"/>\
						</div>\
						<a href="<%= data.list[i].shop_url %>" class="intro">\
							<h2><%= data.list[i].title %></h2>\
							<p><%= data.list[i].description %></p>\
						</a>\
						<div class="aside J_aside">\
							<% if(data.list[i].is_favorite != null && data.list[i].is_favorite==0){ %>\
							<i class="ico-collect" data-shopid="<%= data.list[i].shop_id %>"></i>\
							<% } else if(data.list[i].is_favorite != null) { %>\
							<i class="ico-collect on" fav-id="<%= data.list[i].fav_id %>" data-shopid="<%= data.list[i].shop_id %>"></i>\
							<% } %>\
						</div>\
					</div>\
					<% if(data.list[i].goods!=null&&data.list[i].goods.length){ %>\
					<a href="<%= data.list[i].shop_url %>">\
						<ul class="pics">\
							<% for(var j=0, max=data.list[i].goods.length; j<max; j++) { %>\
							<li><img src="<%= blankPic %>" data-src="<%= data.list[i].goods[j] %>"/></li>\
							<% } %>\
						</ul>\
					</a>\
					<% } %>\
				</div>\
			</div>\
			<% } %>',
		temptList: // 诱货
			'<% for(var i=0, len=data.list.length; i<len; i++){ %>\
			<div class="tempt-list">\
				<h3>\
					<i class="ico-divider"></i>\
					<%= data.list[i].tag %></h3>\
				<ul>\
					<% for(var j=0, list=data.list[i].list, max=list.length; j<max; j++){ %>\
					<li>\
						<a href="<%= list[j].link %>"><img src="<%= blankImg %>" data-src="<%= list[j].img %>"/></a>\
						<p><%= list[j].title %></p>\
					</li>\
					<% } %>\
				</ul>\
			</div>\
			<% } %>',
		merchantList: 
			'<div class="merchant-wrap">\
				<ul id="J_merchantList" style="display:none;" class="merchant-list">\
				<%for(var i=0, len=data.list.length; i<len; i++){%>\
					<li><%=data.list[i].tag%></li>\
				<%}%>\
				</ul>\
			</div>'
	};	

	// Class
	GOU.View = iCat.View.extend({
		tmplData: {
			blankPic: iCat.PathConfig.picPath + 'blank.gif',
			blankImg: iCat.PathConfig.picPath + 'blank-img.gif'
		},
		loadMore: function(el){
			var that = this, cfg = that.config,
				url = cfg.__base_ajaxurl.replace(/[\?\&]page=\d+/, ''),
				win = $(el), winH = win.height(),
				body = document.body,
				// wrap = $('.module');
				wrap = $(document);
			if(winH + body.scrollTop >= wrap.height()){
				that.setAjaxUrl(
					url + (url.indexOf('?')<0? '?' : '&') + 'page=' + (cfg.list_pageNum + 1)
				);
			}
		}
	});

	// setting
	GOU.View.setting =
	{
		'topBanner': {
			config: {
				tmplId: 'topBanner',
				wrap: '.J_topBanner',
				ajaxUrl: GOU.fullurl('/api/@gou/download'),
				events: {'@click|0|0 .J_topBanner i': 'hidebox'}
			},
			hidebox: function(el){
				var parent = $(el).parents('.J_topBanner');
				parent.slideUp();
				//parent.animate({'margin-top':-parent.height()+'px'}, 500);
			}
		},

		'header': {//页头
			config: {
				tmplId: 'aHeader',
				wrap: '#iHeader'
			},
			tmplData: {
				url:"/search?refer="+escape(document.location.href) + '&type=SEARCH',
				nav: [
					{name:'首 页', link:GOU.fullurl('/index', true)},
					{name:'货到付款', link:GOU.fullurl('/cod', true)},
					{name:'每日特价', link:GOU.fullurl('/tejia', true)}
				]
			}
		},

		'subheader': {//二级页头
			config: {
				tmplId: 'bHeader',
				wrap: '#iHeader'
			}
		},

		'brandsubheader': {
			config: {
				tmplId: 'brandSubHeader',
				wrap: '#iHeader'
			}
		},

		'activityTheme': {//春节节专题
			config: {
				tmplId: 'activityTheme',
				wrap: '#iHeader',
				ajaxUrl: GOU.fullurl('/api/@gou/download')
			},
			tmplData: {
				url:"/search?refer="+escape(document.location.href) + '&type=SEARCH',
				nav: [
					{name:'首 页', link:GOU.fullurl('/index', true)},
					{name:'货到付款', link:GOU.fullurl('/cod', true)},
					{name:'每日特价', link:GOU.fullurl('/tejia', true)}
				]
			}
		},

		'subnav': {
			config: {
				withhash: false,
				tmplId: 'activityFilter',
				wrap: '#iHeader',
				renderCompleted: function() {
					var box = $('#J_filterWrap');
					var boxTop = box[0].offsetTop;
				    window.addEventListener('scroll', function(){
					  	if (this.scrollY > boxTop) {
					  		box.css({
					  			'position': 'fixed',
					  			'z-index': 1000,
					  			'top': 0,
					  			'left': '50%',
					  			'margin-left': -box.width()/2
					  		})
					  	} else {
					  		box.attr('style', '');
					  	}
					}, false);

					var filterBtn = $('.filter-btn');
					filterBtn[0].addEventListener('touchend', function(e) {
						var putUpDown = $(this).find('.ico-put');
						var merchant = $('#J_merchantList');
						var maskWrap = $('#iScroll').find('wrapscrolllist');
						var mask = $('<div id="J_maskLayer" class="mask-layer"></div>');
						var body = $('body');

						if (putUpDown.length) {
							if (putUpDown.hasClass('active')) {
								putUpDown.removeClass('active');
								merchant.hide();
								$('#J_maskLayer').remove();
							} else {
								GOU.addBaiduStatic('FILTER'); // 百度统计
								putUpDown.addClass('active');
								merchant.show();										
								maskWrap.prepend(mask);
							}
						}
					}, false);
				}
			},
			tmplData: {}
		},
		/*'subnav': {
			config: {
				withhash: false,
				tmplId: 'activityFilter',
				wrap: '#iHeader'
			},
			tmplData: {
				nav: [
					{name:'诱货', link: GOU.fullurl('/amigo/activity/index', true)},
					{name:'活动荟萃', link: GOU.fullurl('/amigo/activity/long', true)}
				]
			}
		},*/

		'brandnav': {
			config: {
				tmplId: 'brandnav',
				wrap: '#iHeader',
				ajaxUrl: GOU.fullurl('/api/brand/category')
			}
		},

		'brandBanner': {
			config: {
				tmplId: 'brandBanner',
				wrap: '#iHeader'
			}
		},

		// 品牌聚详细页
		'branddesc': {
			config: {
				tmplId: 'branddesc',
				wrap: '#iHeader'
			}
		},

		'gotop': {//返回顶部
			config: {
				tmplId: 'gotop',
				wrap: '#iScroll',
				events: {
					'click|0|0 .gotop span': function(){window.scrollTo(0,1);}
				},
				callback: function(){
					window.onscroll = function(){
						if(document.body.scrollTop < 700){
							$('.gotop').hide();
						} else {
							$('.gotop').show();
						}
					};
				}
			}
		},
		
		'banner': {//轮播广告
			config: {
				tmplId: 'banner',
				wrap: '#iScroll',
				ajaxUrl: GOU.fullurl('/api/@gou/ad', true),
				callback: function(p, cfg){
					if(!iCat.$) return;
					var wrap = $('.J_scrollBanner'), sPannel = wrap.find('.pic ul'),
						item = sPannel.find('li'), len = item.length,
						handles = wrap.find('.handle span'),
					scroll = function(index){
						if(index==len){
							index = 0;
							sPannel.animate({left:0}, 0);
						} else {
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
						}, 3000);
					};

					autoplay(true);
				}
			}
		},

		'recommend': {//推荐
			config: {
				tmplId: 'recommend',
				wrap: '#iScroll',
				ajaxUrl: GOU.fullurl('/api/@gou/ad')
			}
		},

		'links': {
			config: {
				tmplId: 'links',
				wrap: '#iScroll',
				ajaxUrl: GOU.fullurl('/api/@gou/link')
			}
		},

		'servpipe': {
			config: {
				tmplId: 'servpipe',
				wrap: '#iScroll',
				ajaxUrl: GOU.fullurl('/api/APK_Gou/convenient')
			}
		},
		
		'amigoMall': {
			config: {
				tmplId: 'amigoMall',
				wrap: '#iScroll',
				ajaxUrl: GOU.fullurl('/api/@gou/amigo')
			}
		},

		'mall': {//商场
			config: {
				tmplId: 'icoItem',
				wrap: '#iScroll',
				ajaxUrl: GOU.fullurl('/api/@gou/mall')
			},
			tmplData: {title: '综合购物商城', icon: 'ico_mall.png'}
		},

		'theme': {//主题店
			config: {
				tmplId: 'icoItem',
				wrap: '#iScroll',
				ajaxUrl: GOU.fullurl('/api/@gou/theme')
			},
			tmplData: {title: '主题店', icon: 'ico_theme.png'}
		},

		'tuan': {//团购&折扣
			config: {
				tmplId: 'icoItem',
				wrap: '#iScroll',
				ajaxUrl: GOU.fullurl('/api/@gou/tuan')
			},
			tmplData: {title: '团购&折扣', icon: 'ico_tuan.png'}
		},

		'helper': {//助手
			config: {
				tmplId: 'icoItem',
				wrap: '#iScroll',
				ajaxUrl: GOU.fullurl('/api/@gou/helper')
			},
			tmplData: {title: '生活便民助手', icon: 'ico_helper.png'}
		},

		'footer': {//页尾
			config: {
				tmplId: 'footer',
				wrap: '#iScroll'
			}
		},

		'appFooter': {//页尾
			config: {
				tmplId: 'appFooter',
				wrap: '#iScroll'
			}
		},

		'app2Footer': {
			config: {
				tmplId: 'app2Footer',
				wrap: '#iScroll'
			}
		},

		'codSearch': {
			config: {
				tmplId: 'codSearch',
				wrap: '#iScroll',
				ajaxUrl: GOU.fullurl('/api/@cod/search')
			}
		},
		// // 淘宝好店滑动tab
		'slideTab': {
			config: {
				tmplId: 'slideTab',
				wrap: '#iScroll',
				ajaxUrl: GOU.fullurl('/api/@shop/tag'),
				renderCompleted: function() {
					var slideTab = $('#J_slideTab');
					var slide = $('#J_slideRight');
					slideTab[0].scrollWidth > slideTab.outerWidth() ? slide.show() : slide.hide();

					var box = $('.J_slideWrap');
					var boxTop = box[0].offsetTop;

					var scrollCallback = function() {
						if (this.scrollY > boxTop) {
							box.css({
								'position': 'fixed',
								'z-index': 1000,
								'top': 0,
								'left': '50%',
								'margin-left': -box.width()/2
							})
						} else {
							box.attr('style', '');
						}
					}
				    window.addEventListener('scroll', scrollCallback, false);

					$('body').on('click', '.gotop', function() {
						window.removeEventListener('scroll', scrollCallback);
						$('.J_slideWrap').attr('style', '');
						setTimeout(function() {
							window.addEventListener('scroll', scrollCallback, false);
						}, 100);
					})
				}
			}
		},
		// 淘宝好店滑动tab
		'merchantList': {
			config: {
				tmplId: 'merchantList',
				// wrap: 'wrapsubnav',
				wrap: '#J_merchantWrap',
				ajaxUrl: GOU.fullurl('/api/amigo_index/activity_long'),
				renderCompleted: function() {
					$('#J_merchantList').on('click', 'li', function(e) {
						e.stopPropagation();
						var list, $this = $(this), index;
						list = $this.parent().children();
						temptList = $('.tempt-list');
						$this.addClass('selected').siblings().removeClass('selected');
						index = list.index($this);
						GOU.addBaiduStatic('FILTER_PLATFORM', $this.text());	// 百度统计

						var filterWrap = $('#J_filterWrap');
						var top = temptList.eq(index).offset().top;
						var h = filterWrap.height();
						top = (filterWrap.css('position') === 'fixed') ? (top - h) : (top - 2 * h);
						window.scrollTo(0, top);

						$('.ico-put').removeClass('active');
						$this.parent().hide();
						$('#J_maskLayer').remove();
					})
				}
			}
		},
		'scrollList': {	// 图片滚动加载
			config: {
				wrap: '#iScroll',
				clear: false,
				events: [{selector:window, type:'scroll', callback: GOU.scrollLoad }],
				renderCompleted: function() { GOU.scrollLoad() } }
		},
		'list': {
			config: {
				wrap: '#iScroll',
				clear: false,
				events: [{selector:window, type:'scroll', callback:'loadMore'}],

				callback: function(w, cfg, data){
					cfg.list_pageNum = data.data.curpage;
					$('#J_loadStatus').css('display', 'none');
				},
				requestFail: function(noNext){
					GOU.showtip(noNext? '没有更多了' : '努力加载中...');
				},

				renderCompleted: function(){
					var body = $('#cod');
					if(body[0]){
						body.on('touchstart, touchmove, mousedown, mousemove', '.detail a', function(){
							var that = $(this);
							that.addClass('active');
							setTimeout(function(){ that.removeClass('active'); }, 500);
						}).on('touchend, mouseup', function(){
							$(this).removeClass('active');
						});
					}
				}
			}
		}
	};
})(ICAT);