/*
 * Created with Sublime Text 2.
 * User: Hankewins
 * Date: 2013-11-14
 * Time: 18:38:13
 * Contact: hankewins@gmail.com
 */

(function(iCat){
	var _hmt = window._hmt ? window._hmt : [];
	//通过iCat.PathConfig初始化参考路径
	iCat.PathConfig();
	//设置命名空间
	var GNapp = iCat.namespace('GNapp');
	//设置二级命名空间（后期修改为CMD OR　AMD模块后，不需要命名空间的依赖）
	GNapp.models = {}; GNapp.collections = {}; GNapp.views = {}; GNapp.routers = {};
	//设置事件兼容处理
	GNapp.hasTouch  = "ontouchstart" in window;
	GNapp.TAP_EV  = GNapp.hasTouch ? "click" : "click";
	GNapp.START_EV  = GNapp.hasTouch ? "touchstart" : "mousedown";
	GNapp.MOVE_EV   = GNapp.hasTouch ? "touchmove" : 'mousemove';
	GNapp.END_EV    = GNapp.hasTouch ? "touchend" : 'mouseup';
	GNapp.CANCEL_EV = GNapp.hasTouch ? "touchcancel" : "mouseup";
	//UA判断当前环境
	var ua = navigator.userAgent;
	GNapp.browser = {
		isAndroid: /android/ig.test(ua),
		isGioneemb: /gionee/ig.test(ua),
		isIos: /iphone|ipad/ig.test(ua)
	};

	var releaseDev = {
		pageDataApi: "/3g/api/v1_firstcachejs.php",
		loadMoreApi: "/3g/api/v1_recommend_more.php",
		cateListApi: "/3g/api/v1_recommend_more.php",
		reportStatsApi: "/3g/api/v1_report_stats.php",
		searchApi: "/3g/api/v1_search.php",
		getHotWordsApi: "/3g/api/v1_hotwords.php",
		topicListApi: "/3g/api/v1_themelist.php",
		topicDetailListApi: "/3g/api/v1_themeapps.php"
		//version: "2013111501" // 发布版本
	};

	var releaseTest = {
		pageDataApi: "/webapp/book",
		loadMoreApi: "/webapp/more",
		cateListApi: "/webapp/typelist",
		reportStatsApi: "/webapp/stats",
		searchApi: "/webapp/search",
		getHotWordsApi: "/webapp/keywords",
		topicListApi: "/webapp/themelist",
		topicDetailListApi: "/webapp/themeapps",
		//version: "2013111501" // 发布版本
	};

	var releaseOnline = {
		pageDataApi: "/webapp/book",
		loadMoreApi: "/webapp/more",
		cateListApi: "/webapp/typelist",
		reportStatsApi: "/webapp/stats",
		searchApi: "/webapp/search",
		getHotWordsApi: "/webapp/keywords",
		topicListApi: "/webapp/themelist",
		topicDetailListApi: "/webapp/themeapps",
		//version: "2013111501" // 发布版本
	};

	var releaseEnv, staticPath, webRoot = location.protocol + '//' + location.host;; //自动选择环境
	if (location.host.indexOf('3g.gionee.com') > -1 || location.host.indexOf('sige.gionee.com') > -1) {
		staticPath = "http://assets.gionee.com";
		releaseEnv = releaseOnline;
	} else if (location.host.indexOf('3gtest.gionee.com') > -1) {
		staticPath = "http://assets.3gtest.gionee.com";
		releaseEnv = releaseTest;
	} else {
		staticPath = "http://18.8.10.194:8899";
		releaseEnv = releaseDev;
	}

	iCat.include(['lib/zepto/zepto.js','lib/underscore/underscore.js','lib/backbone/backbone.js', './slide.js','lib/zepto/touchSwipe.js'],function (){//加载依赖文件
	GNapp.slider = function(){
		$("#J_full_slider3").slidePic({
			slideWrap:   '.ui-slider-wrap',
			slidePanel:  '.ui-slider-pic',
			slideItem:   '.ui-slider-pic li',
			handlePanel: '.ui-slider-handle',
			handleItem:  '#J_full_slider3 .ui-slider-handle > span',
			circle: true,
			auto:true
		});
	};

	$('.index-top-bookmark').on('click',function(){
		// 从书签页添加接口
		_hmt.push(['_trackEvent','轻应用Webapp','调用浏览器接口次数','接口-书签页添加次数']);
		if (GNapp.browser.isAndroid) {
			var bookmark = window.prompt("gn://GNNavSiteData/frombookmark","");
		}
	});

	$('.app-banner-box a').live('click', function(evt){
		var that = $(this), adLink = that.attr('href'), appId = that.attr('data-appId');
		evt.preventDefault();
		// BI数据统计
		$.ajax({
			url: webRoot + releaseEnv.reportStatsApi,
			type: 'get',
			data: {"columnName":'ad', "to_href": adLink, "appId": appId, "action": "open-ad"},
			timeout: 1000,
			success: function(){
				window.location.href = adLink;
			},
			error: function(){
				window.location.href = adLink;
			}
		});
	});

	/**
	 * $.extend.hover
	 */
	$.extend($.fn, {
		hover: function(cname){

			var that = $(this), cname = cname || "hover";
			var timeId;

			function callback(){
				GNapp.hasActive = true;
				if ($(GNapp.touchTarget).parents('li.item').hasClass('list-loading')) {
					$(GNapp.touchTarget).parents('li.list-loading').addClass('app-more-hover');
				} else {
					$(GNapp.touchTarget).parents('li.item').addClass(cname);
				}
			}

			that.on(GNapp.START_EV, function(ev){
				GNapp.touchTarget = ev.target;
				timeId = setTimeout(callback,50);
			});

			that.on(GNapp.END_EV + ' ' + GNapp.CANCEL_EV, function(){
				if (timeId) {
					clearTimeout(timeId);
				}

				if (GNapp.hasActive) {
					setTimeout(function(){
						$(GNapp.touchTarget).removeClass(cname);
						if ($(GNapp.touchTarget).parents('li.item').hasClass('list-loading')) {
							$(GNapp.touchTarget).parents('li.list-loading').removeClass('app-more-hover');
						} else {
							$(GNapp.touchTarget).parents('li.item').removeClass(cname);
						}
						GNapp.hasActive = false;
					},100);
				}
			});
		},
		
		addTouchEffect: function(cname){
			var that = this, cname = cname || "active";
			var timeId;

			function callback(){
				GNapp.hasActive = true;
				that.addClass(cname);
			}

			this.on(GNapp.START_EV, function(ev){
				timeId = setTimeout(callback,50);
			});

			this.on(GNapp.END_EV + ' ' + GNapp.CANCEL_EV, function(){
				if (timeId) {
					clearTimeout(timeId);
				}

				if (GNapp.hasActive) {
					setTimeout(function(){
						that.removeClass(cname);
						GNapp.hasActive = false;
					},100);
				}
			});
		}
	});

	$('#index-search-words, #index-search-btn').addTouchEffect('active');
	$('.index-top-bookmark').addTouchEffect('active');
	$('.back').addTouchEffect('active');
	$('.app-banner-box a').addTouchEffect('active');
	$('.btn-search').addTouchEffect('active');
	$('#app-search-page .input-search-btn').addTouchEffect('active');

	//var cache = new CacheProvider;
	var appWrap = $(".J_app_list");

	var onav = {'recommend': 't1', 'rank': 't2', 'category': 't3', 'news': 't4'};
	var ostat = {
		'recommend': '推荐页－精品推荐',
		'must': '推荐－必备名站',
		'rank': '排行',
		'category': '分类',
		'news': '新品',
		'cate': '分类页－分类详情',
		'search': '搜索页－搜索结果',
		'usercomm': '搜索页－相关推荐',
		'topics': '专题页－专题列表',
		'theme': '专题页－专题详情',
	};

	Backbone.imgLoad = function(){
		if ($('.lazyload').size() > 0) {
			_.each($('.lazyload'),function(item,index){
	    		var img = new Image();
	    		var src = $(item).attr('data-src');
	    		img.src = src;
	    		img.onload = function(){
		    		setTimeout(function(){
		    			$(item).attr('src',src);
		    			$(item).removeAttr('data-src');
		    		},100);
	    		}
	    	});
		}
	}

	// 初始化数据
	$.ajax({
		url: releaseEnv.pageDataApi,
		type: 'get',
		dataType: 'json',
		timeout: 2000,
		beforeSend: function () {
			$('#loading').removeClass('none');
		},
		success: function(data){
			$('#loading').addClass('none');
			window.localStorage.setItem('gnapp_v1_firstPageData', JSON.stringify(data));
			window.pageInitData = data;			
			gnappRoute = new GNapp.routers.app();
			Backbone.history.start();
		},
		error: function(xhr, type, error){
			$('#loading').addClass('none');
			if (GNapp.browser.isAndroid) {
				window.prompt("gn://GNNavSiteData/tips","网络出错！");
			}
			window.pageInitData = JSON.parse(window.localStorage.getItem('gnapp_v1_firstPageData'));
			gnappRoute = new GNapp.routers.app();
			Backbone.history.start();
		}
	});

	$.ajax({
		url: releaseEnv.getHotWordsApi,
		type: 'get',
		dataType: 'json',
		timeout: 2000,
		success: function(data){
			window.localStorage.setItem('gnapp_v1_getHotWords', JSON.stringify(data));
		}
	});

	// START BACKBONE

	//model 数据模型
	GNapp.models.app = Backbone.Model.extend({});

	var mapp = new GNapp.models.app();

	//collections 控制器
	GNapp.collections.app = Backbone.Collection.extend({
		model: GNapp.models.app,
		url: releaseEnv.pageDataApi
	});
	
	// Route 路由控制
	GNapp.routers.app = Backbone.Router.extend({
		routes: {
			'': 'index',
			'~loop:svt=index/tab/:name': 'showIndex',
			'~loop:svt=cdetail/id/:id': 'showDetail',
			'~loop:svt=topicList': 'showTopicList',
			'~loop:svt=topic/id/:id': 'showTopic',
			'~loop:svt=search': 'showSearch',
			'~loop:svt=result': 'showResult'
		},

		initialize: function(options) {
			var that = this;
			if (GNapp.browser.isAndroid) {
				this.on('route', function(router, route, params){
					document.title = '轻应用市场';
				});
			} else {
				document.title = '网页应用';
			}
		},

		index: function(){
			this.showIndex();
			//gnappRoute.navigate('#~loop/index/tab/recommend',true);
		},

		showTopic: function(svt, id) {
			// 专题页面
			var topic = new topicView({id:id});
			topic.render();
		},

		showTopicList: function(svt) {
			// 专题页面
			var topicList = new topicListView();
			topicList.render();
		},

		showSearch: function(){
			window.searchView.render();
		},

		showResult: function(){
			var result = new resultView();
			result.render();
		},

		showIndex: function(svt, name){
			var name = name || 'recommend';
			var onav = {'recommend': 't1', 'rank': 't2', 'category': 't3', 'news': 't4'};
			var index = new IndexView();
			var nav = new GNapp.views.header({menu: name});
			var that = this;
			nav.render(name);

			var capp = new GNapp.collections.app({"data":pageInitData.data[onav[name]]});

			$(".J_app_list","#index").html('');
			if (name == 'recommend') {
				$('#index').show();
				$('#app-banner').show();
				$('#app-banner .app-banner-box').html(new GNapp.views.appList({model:capp, name:'ads'}).render()._slide);
	        	GNapp.slider();
	        	var applistWrap = $(".J_app_list","#index");
	        	applistWrap.append('<h3 class="index-must-title">必备名站</h3>');
				applistWrap.append(new GNapp.views.appList({model:capp, name:'must'}).render().el);
		        applistWrap.append('<h3 class="index-recommmend-title">精品推荐</h3>');
		        applistWrap.append(new GNapp.views.appList({model:capp, name:'recommend'}).render().el);
				//setTimeout(function(){window.scrollTo(0, 1);},100);
				//add touch effect
		    	$(".J_app_list","#index").hover('app-item-hover');
			} else {
				$('#app-banner').hide();
				$(".J_app_list","#index").html(new GNapp.views.appList({model:capp,name:name, groupId: onav[name]}).render().el);
				//add touch effect
		    	$(".J_app_list","#index").hover('app-item-hover');
			}
	    	Backbone.imgLoad();
		},

		showDetail: function(svt, id){
			var cdetail = new CdetailView({id:id});
			cdetail.render();
		}
	});

	// 默认显示Index页面
	window.IndexView = Backbone.View.extend({
		initialize: function(){
			$('.page-tile').addClass('none');
			$('#index').removeClass('none');	
			$('#index-search-words, #index-search-btn').on('click',function(){
				gnappRoute.navigate('#~loop:svt=search',true);
			});		
		}
	});

	// 搜索页面
	window.SearchView = Backbone.View.extend({
		locked: false,

		el: $('#app-search-page'),
		events: {
			'click .input-search-btn': 'showSearch',
		},

		render: function(){
			var that = this;
			$('.page-tile').addClass('none');
			$('#app-search-page').removeClass('none');
			var hotWords = JSON.parse(window.localStorage.getItem('gnapp_v1_getHotWords'));
			if (hotWords) {
				var defaultWords = hotWords.data['default'];
				var hotWordsData = hotWords.data.hotwords;
				var defaultHotWords = defaultWords[Math.round(Math.random()* (defaultWords.length-1))];
				$('#app-search-page .input-search').val(defaultHotWords);
				$('#app-search-page .input-search').on('focus',function(){
					if ($(this).val() == defaultHotWords){
						$(this).val('');
					}
				});
				
				var li = '', arr = [], colors = [1,2,3,4];
				if (hotWordsData.length < 6) {
					for (var i = 0, length = hotWordsData.length; i < length; i++) {
						var m = Math.round(Math.random()* 3);
						li = li + '<li><a class="hots-bg' + colors[m] + '">' + hotWordsData[i] + '</a></li>';
					}
					$('.app-search-hots').html('<ul>' + li + '</ul>')
				} else {
					for (var j = 0; j < 6; j++) {
						var m = Math.round(Math.random()* 3);
						var n = Math.round(Math.random()* (hotWordsData.length-1));
						arr.push('<li><a class="hots-bg' + colors[m] + '">' + hotWordsData[n] + '</a></li>');
						hotWordsData.splice(n,1);
					}
					$('.app-search-hots').html('<ul>' + arr.join('') + '</ul>')
				}
			}


			$('.app-search-hots a').on('click',function(){
				$('#app-search-page .input-search').val($(this).text());
				that.showSearch('search-hots-link');
			});

			$('#form-search').on('submit', function(evt){
				evt.preventDefault();
				that.showSearch();
			});
		},

		showSearch: function(){
			var that = this;
			var capp = new GNapp.collections.app();
			var hotWords = JSON.parse(window.localStorage.getItem('gnapp_v1_getHotWords'));

			if (this.locked == false) {
				var keywords = $('#app-search-page .input-search').val();
				if (keywords == '') {
					$('#pSearchTip').removeClass('none');
					$('#pSearchNoData').addClass('none');
					$('#pSearch1Words').addClass('none');
					return;
				}

				if (keywords.length < 2) {
					$('#pSearch1Words').removeClass('none');
					$('#pSearchTip').addClass('none');
					$('#pSearchNoData').addClass('none');
					return;
				}

				if (arguments[0] == 'search-hots-link') {
					_hmt.push(['_trackEvent','轻应用Webapp','搜索页－搜索总量','搜索页-关键词链接搜索']);
				} else if (keywords == hotWords.data['default']) {
					_hmt.push(['_trackEvent','轻应用Webapp','搜索页－搜索总量','搜索页-搜索框默认搜索']);
				} else {
					_hmt.push(['_trackEvent','轻应用Webapp','搜索页－搜索总量','搜索页-搜索框主动搜索']);
				}

				this.locked = true;
				window.localStorage.setItem('gnapp_v1_searchKey', '"' + keywords + '"');
				$('#app-search-page .input-search').addClass('active');
				capp.fetch({
					url: releaseEnv.searchApi,
					data:{like:keywords},
					dataType: 'json',
					beforeSend: function(){
						$('#app-search-page .input-search').val(keywords + ' 搜索中...');
					},
					success: function(data){
						that.locked = false;
						var res = data.models[0].get('data');
						if (res.total > 0) {
							$('.app-search-tips').addClass('none');
							$('#app-search-page .input-search').val('');
							window.localStorage.setItem('gnapp_v1_searchResult', JSON.stringify(data.models[0].attributes));
							gnappRoute.navigate('#~loop:svt=result',true);
						} else {
							$('#app-search-page .input-search').val(keywords);
							$('#pSearchNoData').removeClass('none');
							$('#pSearchTip').addClass('none');
							$('#pSearch1Words').addClass('none');
							that.locked = false;
						}
						$('#pSearchTip').addClass('none');
						$('#pSearch1Words').addClass('none');
						$('#app-search-page .input-search').removeClass('active');
					},
					error: function(){
						$('#app-search-page .input-search').val('');
						$('#app-search-page .input-search').removeClass('active');
						that.locked = false;
					}
				});
			}
			return this;
		}
	});

	window.searchView = new window.SearchView();
	
	// 搜索结果
	window.resultView = Backbone.View.extend({
		initialize: function () {
			$('.page-tile').addClass('none');
			$('#app-result-page').removeClass('none');
			$('#app-search-page .input-search').val('');
		},

		render: function () {
			var that = this;
			var data = JSON.parse(window.localStorage.getItem('gnapp_v1_searchResult'));
			var searchKey = window.localStorage.getItem('gnapp_v1_searchKey').slice(1,-1);
			var searchItemWrap = $(".J_app_list","#app-result-page");
			var capp = new GNapp.collections.app(data);
			searchItemWrap.html('');
			searchItemWrap.append('<h3 class="index-recommmend-title">' + searchKey + ' <span>搜索到' + data.data.total + '个结果</span></h3>');
			searchItemWrap.append(new GNapp.views.appList({model:capp, name:'search', like: searchKey}).render().el);
			if (data.data.usercomm) {
				searchItemWrap.append('<h3 class="index-recommmend-title">相关推荐</h3>');
		    	searchItemWrap.append(new GNapp.views.appList({model:capp, name:'usercomm'}).render().el);
			}
			//add touch effect
    		searchItemWrap.hover('app-item-hover');
	    	Backbone.imgLoad();	
		}
	});

	// 分页详情页面
	window.CdetailView = Backbone.View.extend({
		initialize: function(){

		},
		render: function(){
			$('.page-tile').addClass('none');
			$('#app-cdetail-page').removeClass('none');
			var that = this;
			var cateName = $('#cate-id-'+this.options.id).text();
			var capp = new GNapp.collections.app();
			capp.fetch({
				url: releaseEnv.cateListApi,
				data:{type_id:this.options.id, page:1},
				beforeSend: function(){
					//$('#loading').removeClass('none');
				},
				success: function(){
					//$('#loading').addClass('none');
					$(".J_app_list","#app-cdetail-page").html(new GNapp.views.appList({model:capp, name:'cate', typeId:that.options.id, cateName: cateName}).render().el);
					//add touch effect
		    		$(".J_app_list","#app-cdetail-page").hover('app-item-hover');
			    	Backbone.imgLoad();
				},
				error: function(){
					//$('#loading').addClass('none');
				}
			});
		}
	});

	// view专题详情页面
	window.topicView = Backbone.View.extend({
		render: function (){
			$('.page-tile').addClass('none');
			$('#app-topic-page').removeClass('none');
			var that = this;
			var capp = new GNapp.collections.app();
			capp.fetch({
				url: releaseEnv.topicDetailListApi,
				data:{type: 'theme', type_id:this.options.id, page:1},
				beforeSend: function(){
					//$('#loading').removeClass('none');
				},
				success: function(){
					//$('#loading').addClass('none');
					
					$(".J_app_list","#app-topic-page").html(new GNapp.views.appList({model:capp, name:'theme', typeId:that.options.id}).render().el);
					//add touch effect
		    		$(".J_app_list","#app-topic-page").hover('app-item-hover');
			    	Backbone.imgLoad();
				},
				error: function(){
					//$('#loading').addClass('none');
				}
			});
		}
	});

	// view专题列表页面
	window.topicListView = Backbone.View.extend({
		render: function (){
			$('.page-tile').addClass('none');
			$('#app-topic-list').removeClass('none');
			var that = this;
			var capp = new GNapp.collections.app();
			capp.fetch({
				url: releaseEnv.topicListApi,
				success: function(){
					$(".J_app_list","#app-topic-list").html(new GNapp.views.appList({model:capp, name:'topics'}).render().el);
					//add touch effect
		    		$(".J_app_list","#app-topic-list").hover('app-item-hover');
			    	Backbone.imgLoad();
				}
			});
		}
	});

	GNapp.views.header = Backbone.View.extend({
		el: $('.app-menu-box'),
		template: _.template($('#J_tabMenuView').html()),
		events: {
			//"click .app-nav-item": "swichTab"
		},

		initialize: function(){
			//this.model.on('change',this.showTopBanner,this);
			this.$el.delegate('.in-tabs li',GNapp.TAP_EV,this.swichTab);
		},

		render: function(id){
			$(".app-menu-box").html(this.template());
			this.selectMenuItem(id);
		},

		selectMenuItem: function(menuItem){
			$('#app-tabs li').removeClass('actived');
			var columnName = this.options.menu != undefined ? this.options.menu : 'recommend';
			console.log("start send", 1);
			$.ajax({
				url: releaseEnv.reportStatsApi,
				type: 'get',
				data: {"columnName":columnName},
				success: function(data){
					console.log("SEND SUCC");
				}
			});
	        if(menuItem){
	            $('#app-tabs li[data-target='+menuItem+']').addClass('actived');
	        }
		},

		swichTab: function(evt){
			var hashName = $(this).attr('data-link');
			//window.location.href = hashName;
			//非chrome浏览器，物理返回页面不重新加载，JS初始化动作失效，因此手动达到应用程序的对应位置
			gnappRoute.navigate(hashName,true);
		}
	});

	GNapp.views.backNav = Backbone.View.extend({
		el: $(".module").eq(0),
		template: _.template($('#J_backNavView').html()),

		initialize: function(){
			this.render();
		},

		render: function(){
			$(this.el).html(this.template({cateName:''}));
			this.hideTabMenu();
			return this;
		},

		hideTabMenu: function(){
			$("#app-tabs").remove();
		},

		hideAppList: function(){
			$(".app-list").remove();
		}
	});

	GNapp.views.appList = Backbone.View.extend({
		tagName:'ul',
		events:{
			'click .list-more': 'showAppMore'
		},

		initialize: function(){
			this.events[GNapp.TAP_EV + ' .add-btn']   = 'addAppIcon';
			//this.events[GNapp.TAP_EV + ' .list-more'] = 'showAppMore';
			this.events['click' + ' .item-cont'] = 'openAppLink';
			this.listenTo(this.model,'reset',this.changeList);
		},

		_cateName: null,

		changeList: function () {
			this.render();
			Backbone.imgLoad();
		},

		render: function(){
			var clientData = [], dataList = [], i = 0;
			var name = this.options.name;
			var models = this.model.models;

			if (this.options.name == 'category') {
        		this.$el.addClass('app-cate');
        	} else {
        		if (GNapp.browser.isAndroid) {
        			//if is GiONEE Browser
        			clientData = this.getLocalData() || [];
        		}
        	}
        	_.each(models, function(item, index){
	        	if (this.options.name == 'cate' || this.options.name == 'theme') {
	        		this._cateName = this.options.cateName || item.get('data')['cateName'];
	        		if (this.options.name == 'cate') {
	        			$("#app-cdetail-page .J_backNavTitle").text(this._cateName);
	        		}
	        		if (this.options.name == 'theme') {
	        			$("#app-topic-page .J_backNavTitle").text(this._cateName);
						$("#app-topic-box").html('<div class="app-topic-banner"><img class="lazyload" src="' + staticPath + '/apps/3g/app/v1/assets/img/blank.gif" data-src="' + item.get('data')['cateImg'] + '"/></div>');
	        		}
					// BI数据统计(应用分类)
					console.log("start send", 1);
					$.ajax({
						url: releaseEnv.reportStatsApi,
						type: 'get',
						data: {"cloumnName":"cateList", "cateId": this.options.typeId, "cateName": this._cateName},
						success: function(){
							console.log("SEND SUCC");
						}
					});
	        		dataList = item.get('data')[name];
	        	} else if (this.options.name =='search') {
	        		dataList = item.get('data')[this.options.name];
	        	} else if (this.options.name == 'topics') {
	        		this._cateName = this.options.cateName || item.get('data')['cateName'];
	        		$("#app-topic-list .J_backNavTitle").text(this._cateName);
	        		dataList = item.get('data')['theme'];
	        	} else if (this.options.name == 'ads') {
					dataList = item.get('data')['ads'];
					// ads
					if (item.get('data')['ads'] && item.get('data')['ads'].length > 0) {
						var ads = item.get('data')['ads'];
						_.each(ads, function(val, idx){
							val.no_img = staticPath + '/apps/3g/app/v1/assets/img/no-img.png';
						});
						this._slide = _.template($('#J_slideView').html(), item.get('data'));
					}

					return this;

				} else {
	        		dataList = item.get('data')[name];
	        	}

				_.each(dataList, function(data, key){
					// Default image
					data.no_img = staticPath + '/apps/3g/app/v1/assets/img/no-img.png';
					if (this.options.name != 'category') {
						if (GNapp.browser.isAndroid) {
							data.status = clientData.indexOf(data.id) > -1 ? 1 : 0;
						} else {
							data.status = 200;
						}
					}

					if (this.options.name != 'news') {
						data.is_new = 0;
					}

					this.$el.append(new GNapp.views.appItem({model:item, name: this.options.name, index:key}).render().el);

				}, this);

				if (this.options.name != 'must' && this.options.name != 'ads' && this.options.name != 'category' && this.options.name != 'topics' && this.options.name != 'usercomm') {
					$('.list-more').remove();
        			if(item.get('data').hasnext === true) {
        				this.$el.append('<li class="item list-loading list-more"><span class="line"></span><span class="txt">点击加载更多</span></li>');
        			} else if (item.get('data').hasnext === false) {
        				this.$el.append('<li class="list-loading list-had"><span class="line"></span><span class="txt">已显示全部</span></li>')
        			}
				}
			}, this);

			if (this.options.name == 'category' && this.model.models[0].get('data')[this.options.name].length%2 != 0) {
				this.$el.append('<li class="white-space"></li>');
			}

	        return this;
		},

		// 本地接口数据
		getLocalData: function(){
			//查询浏览器本地接口数据
			try{
				localData = JSON.parse(window.prompt("gn://GNNavSiteData/select",""));
			} catch(e){
				localData = 0; //当调用接口不成功时,显示wap版本
				console.log('GNapp.views.appList:getLocalData get interface data error.');
			}
			return localData.data;
		},

		addAppIcon: function(evt){
			var target = $(evt.target),
				appAddUrl = target.attr('data-addUrl'),
				appId = target.attr('data-appId'),
				appName = target.attr('data-appName');
			if (this.options.name == 'cate') {
				$.ajax({
					url: releaseEnv.reportStatsApi,
					type: 'get',
					data: {"cloumnName":"cateList", "appId": appId, "cateId": this.options.typeId, "cateName": this._cateName, "action": "add"},
					success: function(){}
				});
			} else {
				$.ajax({
					url: releaseEnv.reportStatsApi,
					type: 'get',
					data: {"columnName":this.options.name, "appId": appId, "action": "add"},
					success: function(){}
				});
			}
			_hmt.push(['_trackEvent','轻应用Webapp','ADDICON' + ostat[this.options.name],'APPICON-' + appName]);

			//if (browser.android && isWap == false) {
				var val    = window.prompt("gn://GNNavSiteData/insert",appAddUrl);
				//alert('GNNavSiteData-insert:' + val);
				if (JSON.parse(val).result == '0') {
					_hmt.push(['_trackEvent','轻应用Webapp','调用浏览器接口次数','接口-ICON成功添加次数']);
					window.prompt("gn://GNNavSiteData/tips","添加成功");
					target.removeClass("add-btn").addClass('had-btn lock');
					setTimeout(function(){target.removeClass('lock');}, 50);
				} else {
					_hmt.push(['_trackEvent','轻应用Webapp','调用浏览器接口次数','接口-ICON失败添加次数']);
					console.log('GNapp.views.appList:addAppIcon get interface data error.');
				}
			//}
		},

		openAppLink: function(evt){
			evt.preventDefault();
			var that=this; 
			if (GNapp.browser.isAndroid && this.options.name != 'topics') {
				target = $(evt.target).parents('li').find('.button .btn');
			} else {
				target = $(evt.target).parents('li').find('.button .open-arrow');
			}
			var appLink = target.attr('data-link');
			var appId = target.attr('data-appId') ;
			var appName = target.attr('data-appName') ;
			var data;
			_hmt.push(['_trackEvent','轻应用Webapp','OPENICON' + ostat[this.options.name],'APPICON-' + appName]);
			// BI数据统计
			if (this.options.name == 'cate') {
				data = {"cloumnName":"cateList", "to_href": appLink,"cateId": this.options.typeId, "appId": appId, "cateName": this._cateName, "action": "open-url"};
			} else {
				data = {"cloumnName":this.options.name, "appId": appId, "to_href": appLink, "action": "open-url"};
			}
			$.ajax({
				url: releaseEnv.reportStatsApi,
				type: 'get',
				data: data,
				timeout: 1000,
				success: function(){
					window.location.href = appLink;
				},
				error: function(){
					window.location.href = appLink;
				}
			});
		},

		_locked: false,

		//显示更多应用
		showAppMore: function(evt){
			if (this._locked === false) {
				var that = this;
				var target = $(evt.target);

				var item = this.model.models[0];
				var curpage = (item.get('data').page || 1) + 1;

				if (target.hasClass('locked') || target.parent().hasClass('locked')) return;

				if(item.get('data').hasnext == false) this.$el.find('.list-more').remove();

				this.loadStatus();
				_hmt.push(['_trackEvent','轻应用Webapp','LOADICON' + ostat[this.options.name],'点击加载更多']);
				if (that.options.name == "cate" || that.options.name == "theme") {
					that.model.fetch({
						url: webRoot + releaseEnv.loadMoreApi,
						data:{type:that.options.name,type_id:that.options.typeId,page:curpage}
					});
				} else if (that.options.name == "search") {
					that.model.fetch({
						url: webRoot + releaseEnv.loadMoreApi,
						data:{type: that.options.name, like: this.options.like , page:curpage}
					});
				} else {
					that.model.fetch({
						url: webRoot + releaseEnv.loadMoreApi,
						data:{type: that.options.name, page:curpage}
					});
				}
			}
		},

		loadStatus: function(){
			this.$el.find('.list-more').addClass('locked');
			this.$el.find('.list-more').html('<span class="line"></span><span class="txt">正在加载数据...</span>');
			return this;
		}

	});

	GNapp.views.appItem = Backbone.View.extend({
		tagName:"li",
		template:_.template($('#J_itemView').html()),

		initialize: function(){
			this.model.bind("change", this.render, this);
    		this.model.bind("destroy", this.close, this);
		},

		render: function(){
			if (this.options.name == 'category') {
				this.$el.append(_.template($('#J_appCateView').html(), this.model.get('data')[this.options.name][this.options.index]));
			} else if (this.options.name == 'cate') {
				this.$el.addClass('item');
				this.$el.append(this.template(this.model.get('data')[this.options.name][this.options.index]));
			} else if (this.options.name == 'search') {
				this.$el.addClass('item');
				this.$el.append(this.template(this.model.get('data')[this.options.name][this.options.index]));
			} else if (this.options.name == 'topics') {
				this.$el.addClass('item');
				this.$el.append(_.template($('#J_topicView').html(), this.model.get('data')['theme'][this.options.index]));
			} else {
				this.$el.addClass('item');
				this.$el.append(this.template(this.model.get('data')[this.options.name][this.options.index]));
			}

			return this;
		},

		close:function(){
	        this.$el.unbind();
	        this.$el.remove();
	    },
	});

	},true,true);
})(ICAT);