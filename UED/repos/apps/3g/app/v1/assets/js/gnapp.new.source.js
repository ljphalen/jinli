/*
 * Created with Sublime Text 2.
 * User: Hankewins
 * Date: 2013-11-14
 * Time: 18:38:13
 * Contact: hankewins@gmail.com
 */

(function(iCat){
	//通过iCat.PathConfig初始化参考路径
	iCat.PathConfig();
	//设置命名空间
	var GNapp = iCat.namespace('GNapp');
	//设置二级命名空间（后期修改为CMD OR　AMD模块后，不需要命名空间的依赖）
	GNapp.models = {}; GNapp.collections = {}; GNapp.views = {}; GNapp.routers = {};
	//设置事件兼容处理
	GNapp.hasTouch  = "ontouchstart" in window;
	GNapp.TAP_EV  = GNapp.hasTouch ? "tap" : "click";
	GNapp.START_EV  = GNapp.hasTouch ? "touchstart" : "mousedown";
	GNapp.MOVE_EV   = GNapp.hasTouch ? "touchmove" : 'mousemove';
	GNapp.END_EV    = GNapp.hasTouch ? "touchend" : 'mouseup';
	GNapp.CANCEL_EV = GNapp.hasTouch ? "touchcancel" : "mouseup";

	//网络自动选择
	GNapp.netType = 2; // wifi:1 2G:2 3G:3
	navigator.connection = navigator.connection ? navigator.connection : {type:"2g", WIFI: "wifi", CELL_2G: "2g", CELL_3G: "3g"};
	switch (navigator.connection.type) {
        case navigator.connection.WIFI:
            GNapp.netType = 1; break;
        case navigator.connection.CELL_2G:
            GNapp.netType = 2; break;
        case navigator.connection.CELL_3G:
            GNapp.netType = 3; break;
        default:
            GNapp.netType = 1; break;
    }

	//UA判断当前环境
	var ua = navigator.userAgent;
	var isIos = /iphone|ipad/ig.test(ua);

	var releaseDev = {
		pageDataApi: "http://" +location.host + "/3g/app/v1/api/firstcachejs.php",
		loadMoreApi: "http://" +location.host + "/3g/app/v1/api/recommend_more.php",
		cateListApi: "http://" +location.host + "/3g/app/v1/api/recommend_more.php",
		version: "2013111501" // 发布版本
	};

	var releaseTest = {
		pageDataApi: "http://3g.3gtest.gionee.com/webapp/book",
		loadMoreApi: "http://3g.3gtest.gionee.com/webapp/more",
		cateListApi: "http://3g.3gtest.gionee.com/webapp/typelist",
		version: "2013111501" // 发布版本
	};

	var releaseOnline = {
		pageDataApi: "http://3g.gionee.com/webapp/book",
		loadMoreApi: "http://3g.gionee.com/webapp/more",
		cateListApi: "http://3g.gionee.com/webapp/typelist",
		version: "2013111501" // 发布版本
	};

	var releaseEnv; //自动选择环境
	if (location.host == '3g.gionee.com')
	{
		releaseEnv = releaseOnline;
	}
	else if (location.host == '3g.3gtest.gionee.com')
	{
		releaseEnv = releaseTest;
	}
	else {
		releaseEnv = releaseDev;

	}


	// 本地缓存方案
	GNapp.store = {
		localPrefix: 'v1',
	};

	GNapp.getDataStatus = false;

	var isWap = (location.search.indexOf('source=wap') > -1) ? true : false, ua = navigator.userAgent, isLocal = false,
		browser = {
			android: /android/i.test(ua),
			gionee: /Gionee/i.test(ua)
		};

	isLocal = browser.android && isWap == false;
	isLocal = false;
	iCat.include(['lib/zepto/zepto.js','lib/underscore/underscore.js','lib/backbone/backbone.js'/*,'./cacheprovider.js'*/],function (){//加载依赖文件

	/**
	 * $.extend.hover
	 */
	$.extend($.fn, {
		hover: function(cname){

			var that = $(this), cname = cname || "hover";
			var timeId;

			function callback(){
				GNapp.hasActive = true;
				$(GNapp.touchTarget).parents('.item').addClass(cname);
			}

			that.on(GNapp.START_EV, function(ev){
				GNapp.touchTarget = ev.target;
				timeId = setTimeout(callback,50);
			});

			that.on(GNapp.MOVE_EV + ' ' + GNapp.END_EV + ' ' + GNapp.CANCEL_EV, function(){
				if (timeId) {
					clearTimeout(timeId);
				}

				if (GNapp.hasActive) {
					$(GNapp.touchTarget).removeClass(cname);
					$(GNapp.touchTarget).parents('.item').removeClass(cname);
					GNapp.hasActive = false;
				}
			});
		}
	});

	//var cache = new CacheProvider;
	var appWrap = $(".J_app_list");

	//model
	GNapp.models.app = Backbone.Model.extend({});

	var mapp = new GNapp.models.app();

	//collections
	GNapp.collections.app = Backbone.Collection.extend({
		model: GNapp.models.app,
		url: function(){
			return releaseEnv.pageDataApi;
		},
	});

	//routes
	GNapp.routers.app = Backbone.Router.extend({
		_index: null,
		_data: null,
		_apps: null,
		routes: {
			'': 'index',
			'~loop/index/tab/:name': 'show',
			'~loop/cdetail/cid/:id': 'detail'
		},

		initialize: function(){
			var that = this;
			var capp = new GNapp.collections.app();
		},

		index: function(){
			gnappRoute.navigate('#~loop/index/tab/recommend',true);
		},

		show: function(name){
			var onav = {'recommend': 1, 'rank': 2, 'category': 3, 'news': 4};
			var index = new IndexView();
			var nav = new GNapp.views.header();
			var that = this;
			nav.render(name);

			var capp = new GNapp.collections.app();
			capp.fetch({
				data: "tid="+onav[name],
				success:function(data){
					that._data = data;
					if (name == 'recommend') {
						$('#index').show();
						$('#app-banner').show();
			        	var applistWrap = $(".J_app_list","#index");
			        	applistWrap.html('');
			        	applistWrap.append('<h3 class="index-must-title">必备名站</h3>');
						applistWrap.append(new GNapp.views.appList({model:capp, name:'must'}).render().el);
				        applistWrap.append('<h3 class="index-recommmend-title">精品推荐</h3>');
				        applistWrap.append(new GNapp.views.appList({model:capp, name:'recommend'}).render().el);		        
					} else {
						$('#app-banner').hide();
						$(".J_app_list","#index").html(new GNapp.views.appList({model:capp,name:name}).render().el);
					}
				}
			});
		},

		detail: function(id){
			var cdetail = new CdetailView();
			var that = this;
			$(".J_app_list","#cdetail").html('<div class="loading">数据加载...</div>');
			var capp = new GNapp.collections.app();
			capp.fetch({
				url: releaseEnv.cateListApi,
				data:{type_id:id, page:1},
				success: function(){
					$(".J_app_list","#cdetail").html(new GNapp.views.appList({model:capp, name:'cate', typeId:id}).render().el);
				}
			});
		}
	});

	// 默认显示Index页面
	window.IndexView = Backbone.View.extend({
		initialize: function(){
			$('#index').show();
			$('#cdetail').hide();
		}
	});

	// 分页详情页面
	window.CdetailView = Backbone.View.extend({
		initialize: function(){
			$('#index').hide();
			$('#cdetail').show();
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
		events:{},

		initialize: function(){
			this.events[GNapp.TAP_EV + ' .add-btn']   = 'addAppIcon';
			this.events[GNapp.TAP_EV + ' .list-more'] = 'showAppMore';
			this.events['click' + ' .item-cont'] = 'openAppLink';
			this.listenTo(this.model,'reset',this.render);
		},

		render: function(){
			var clientData = [], i = 0;
			var onav = {'recommend': 0, 'category': 1, 'rank': 2, 'news': 3};
			var name = this.options.name;
			var models = this.model.models;
		if (this.options.name == 'category') {
        	this.$el.addClass('app-cate');
			_.each(models, function(item, index){
				_.each(item.get('data')[name], function(data, key){
					this.$el.append(new GNapp.views.appItem({model:item, name: this.options.name, index:key}).render().el);
				}, this);
			}, this);
	    } else {
	    	//读取本地的数据
			clientData = this.getLocalData() || [];

			_.each(models, function(item, index){
	        	if(this.options.name == 'cate'){
	        		$("#J_backNavTitle").text(item.get('data').cateName);
	        	}

				_.each(item.get('data')[name], function(data, key){
					if (item.get('data')['ad']) {
						$('.app-banner-box').html(_.template($('#J_topBannerView').html(), item.get('data')['ad'][0]));
					}

					data.status = clientData.indexOf(data.id) > -1 ? 1 : 0;

					this.$el.append(new GNapp.views.appItem({model:item, name: this.options.name, index:key}).render().el);

				}, this);

				if (this.options.name != 'must') {
					$('.list-more').remove();
        			if(item.get('data').hasnext == true) this.$el.append('<li class="item list-more"><span class="line"></span><span class="txt">点击加载更多</span></li>');
				}
			}, this);	
        }

		    //add touch effect
		    $('.J_app_list').hover('app-item-hover');

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
			var target = $(evt.target),appAddUrl = target.attr('data-addUrl');
			//if (browser.android && isWap == false) {
				var val    = window.prompt("gn://GNNavSiteData/insert",appAddUrl);
				if (JSON.parse(val).result == '0') {
					window.prompt("gn://GNNavSiteData/tips","添加成功");
					target.removeClass("add-btn").addClass('open-btn lock');
					setTimeout(function(){target.removeClass('lock');}, 50);
				} else {
					console.log('GNapp.views.appList:addAppIcon get interface data error.');
				}
			//}
		},

		openAppLink: function(evt){
			evt.preventDefault();
			var target = $(evt.target).parents('li').find('.button .btn');
			//通过addUrl == null 可以判断当前访问的是wap版本，否则访问的是内嵌浏览器版本
			var appLink = target.attr('data-addUrl') == null ? target.attr('href') : target.attr('data-link');
			//window.open(appLink); //仅在当前窗口中打开
			window.location.href = appLink;
		},
		//显示更多应用
		showAppMore: function(evt){
			var that = this;
			var target = evt.target;
			var item = this.model.models[0];
			var curpage = (item.get('data').page || 1) + 1;
			if ($(target).hasClass('locked') || $(target).parent().hasClass('locked')) return;
			if(item.get('data').hasnext == false) this.$el.find('.list-more').remove();

			this.loadStatus();

			if (this.options.name == "cate") {
				this.model.fetch({
					url: releaseEnv.loadMoreApi,
					data:{type:this.options.name,type_id:this.options.typeId,page:curpage}
				});
			} else {
				this.model.fetch({
					url: releaseEnv.loadMoreApi,
					data:{type: that.options.name, page:curpage}
				});
			}
		},

		loadStatus: function(){
			this.$el.find('.list-more').addClass('locked');
			this.$el.find('.list-more').html('正在加载数据...');
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

	gnappRoute = new GNapp.routers.app();
	Backbone.history.start();
	},true);
})(ICAT);