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
		webroot: location.protocol + "//" + location.host,
		firstcachejs: location.protocol + "//" + location.host + "/3g/app/v1/api/firstcachejs",
		version: "2013111501" // 发布版本
	};

	var releaseTest = {
		webroot: location.protocol + "//" + location.host,
		firstPageData: this.webroot + "app/book?tid=1",
		version: "2013111501" // 发布版本
	};

	var releaseOnline = {
		webroot: location.protocol + "//" + location.host,
		firstcachejs: "3g/app/v1/firstcachejs",
		version: "2013111501" // 发布版本
	};

	var releaseEnv; //自动选择环境
	if (location.host == 'dev.demo.gionee.com')
	{
		releaseEnv = releaseDev;
	}
	else if (location.host == '3g.3gtest.gionee.com')
	{
		releaseEnv = releaseTest;
	}
	else {
		releaseEnv = releaseOnline;
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
	GNapp.models.app = Backbone.Model.extend({
		urlRoot: "./root/api",
		url: releaseEnv.firstcachejs,
	});

	GNapp.models.firstPageData = Backbone.Model.extend({
		url: releaseEnv.firstcachejs,
	});

//collections
	GNapp.collections.app = Backbone.Collection.extend({
		model: GNapp.models.app,
		url: function(){
			return  'api' + '/' + this.models[0].get('data').curnav + '_more.php';
		},
	});


//controller
	GNapp.routers.app = Backbone.Router.extend({
		_index: null,
		_data: null,
		_apps: null,
		routes: {
			'': 'index',
			'~loop/index/tab/:name': 'showAppModule',
			'~loop/cdetail/cid/:id': 'showAppCateDetail'
		},

		initialize: function(options) {
			$('#app-menu').delegate('li', GNapp.TAP_EV, function(){
				var hashName = $(this).attr('data-link');
				$('#app-menu li').removeClass('actived');
				$(this).addClass('actived');
				//非chrome浏览器，物理返回页面不重新加载，JS初始化动作失效，因此手动达到应用程序的对应位置
				gnappRoute.navigate(hashName,true);
			});
		},

		index: function(){
			gnappRoute.navigate('#~loop/index/tab/recommend',true);
		},

		showAppModule: function(name){
			$('#app-menu li').removeClass('actived');
			$('#index').show();
			$('#cdetail').hide();
			$('#app-menu li[data-target='+name+']').addClass('actived');
			//this._header = new GNapp.views.header();
			//console.log(mapp);
			var mapp = new GNapp.models.app();
			mapp.fetch();
			console.log(mapp);
			this._data = JSON.parse($('.index_data').html());
			this._apps = new GNapp.collections.app(this._data[this._selectedMenu]);
			console.log(this._apps);
			//this._index = new GNapp.views.appList({model: this._apps, module: this._selectedMenu});
			this._index = new GNapp.views.appList({model: mapp, module: this._selectedMenu});
			var cacheId = name;
			this._selectedMenu = name;
			//this._header.render(this._selectedMenu);
			//this._header.showTopBanner();
			var capp = new GNapp.collections.app(this._data[this._selectedMenu]);

			if (this._selectedMenu == 'recommend') {
				$('#app-banner').show();
			} else {
				$('#app-banner').hide();
			}

			if(this._selectedMenu == "category") {
				$(".J_app_list","#index").html(_.template($("#J_appCateView").html()));
				return;
			}
			//初始化默认数据
			if ( this._selectedMenu == 'recommend') {
	        	var index_title = '<h3 class="index-recommmend-title">必备名站</h3>';
	        	$(".J_app_list","#index").html('');
	        	$(".J_app_list","#index").append(index_title)
	        	$(".J_app_list","#index").append(new GNapp.views.appList({model:capp,module:this._selectedMenu}).render().el);

			} else {
				$(".J_app_list","#index").html(new GNapp.views.appList({model:capp,module:this._selectedMenu}).render().el);
			}
		},

		showAppCateDetail: function(id){
			$('#index').hide();
			$('#cdetail').show();

			var that = this;
			$(".J_app_list","#cdetail").html('<div class="loading">数据加载...</div>');
			var capp = new GNapp.collections.app();

			capp.fetch({
				url: 'api/cate_more.php',
				success: function(){
					$(".J_app_list","#cdetail").html(new GNapp.views.appList({model:capp,module:'cate',typeId:id}).render().el);
				},
				data:{type_id:id, page:1}
			});	
		}
	});

	GNapp.views.appList = Backbone.View.extend({
		tagName:'ul',
		events:{},

		initialize: function(){
			console.log(this.model);
			
			this.events[GNapp.TAP_EV + ' .add-btn']   = 'addAppIcon';
			this.events[GNapp.TAP_EV + ' .list-more'] = 'showAppMore';
			this.events['click' + ' .item-cont'] = 'openAppLink';
			//this.model.bind("reset", this.render, this);
			this.listenTo(this.model,'reset',this.render);
		},

		render: function(){
			var clientData = [], i = 0;

			if(isLocal) clientData = this.getLocalData();

	        _.each(this.model.models, function (item,index) {
	        	if(this.options.module == 'cate'){
	        		$("#J_backNavTitle").text(item.get('data').cateName);
	        	}

	        	item.get('data').curnav = this.options.module;

	        	_.each(item.get("data").list, function(data){
					data.status = isLocal ? clientData.indexOf(data.id) > -1 ? 1 : 0 : 1;
					data.isNew = data.isNew ? 1 : 0;
	        		this.$el.append(new GNapp.views.appItem({model:item, module:this.options.module, index:i++}).render().el);
	        	}, this);

	        	$('.list-more').remove();

	        	if(item.get('data').hasnext == true) this.$el.append('<li class="item list-more"><span class="line"></span><span class="txt">点击加载更多</span></li>');

	        }, this);
	        if(this.options.module == 'cate'){
	        	this.$el.appendTo($(".J_app_list","#cdetail"));
	        } else {
	        	this.$el.appendTo($(".J_app_list","#index"));
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
			
			if (browser.android && isWap == false) {
				var val    = window.prompt("gn://GNNavSiteData/insert",appAddUrl);
				if (JSON.parse(val).result == '0') {
					window.prompt("gn://GNNavSiteData/tips","添加成功");
					target.text('打开').removeClass("add-btn").addClass('open-btn lock');
					setTimeout(function(){target.removeClass('lock');}, 50);
				} else {
					console.log('GNapp.views.appList:addAppIcon get interface data error.');
				}
			}
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
			var me = this;
			var target = evt.target;
			var item = this.model.at(0);
			var curpage = item.get('data').curpage + 1;
			if ($(target).hasClass('locked') || $(target).parent().hasClass('locked')) return;

			if(item.get('data').hasnext == false) this.$el.find('.list-more').remove();

			this.loadStatus();

			if (this.options.module == "cate") {
				this.model.fetch({
					data:{type_id:this.options.typeId,page:curpage},
					success: function(){
						GNapp.getDataStatus = true;
					}
				});
			} else {
				this.model.fetch({
					data:{page:curpage},
					success: function(){
						GNapp.getDataStatus = true;
					}
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
		className: "item",
		template:_.template($('#J_itemView').html()),

		initialize: function(){
			this.model.bind("change", this.render, this);
    		this.model.bind("destroy", this.close, this);
		},

		render: function(){
			this.$el.append(this.template(this.model.get("data").list[this.options.index]));
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

	GNapp.jsDataScript = function(callback){
		var script = document.createElement('script');
		script.setAttribute("type", "text/javascript");
		script.setAttribute("src", GNapp.config.firstcachejs);
		document.getElementsByTagName('head')[0].appendChild(script);
		script.onload = script.onreadystatechange = function() {
			if (!this.readyState || this.readyState == "loaded" || this.readyState == "complete") {
				callback();
			}
			script.onload = script.onreadystatechange = null;
		}
	};


})(ICAT);