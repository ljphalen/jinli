(function(iCat,$){
	iCat.incfile(['//underscore.js','//backbone.js'],function(){GNapp.init();},true);

	iCat.app('GNapp',function(){
		return {
			version:'1.0'
		};
	});

	var Gionee        	  = {};
		Gionee.routers    = {};
		Gionee.views      = {};
		Gionee.models     = {};
		Gionee.collection = {};
		Gionee.domain	  = {};

	var isWap = (location.search.indexOf('source=wap') > -1) ? true : false, ua = navigator.userAgent,
		isLocal = false,
		browser = {
			android: /android/i.test(ua),
			gionee: /Gionee/i.test(ua)
		};

		isLocal = browser.android && isWap == false;

	switch(location.host){
		case 'dev.demo.gionee.com':
			Gionee.domain.url = 'http://dev.demo.gionee.com/3g/app/backbone/';
			break;
		case 'demo.3gtest.gionee.com':
			Gionee.domain.url = 'http://demo.3gtest.gionee.com/3g/app/backbone/';
			break;
		case '3g.3gtest.gionee.com':
			Gionee.domain.url = "http://3g.3gtest.gionee.com/app/";
			break;
		default: 
			Gionee.domain.url = 'http://3g.gionee.com/app/';
	}

	GNapp.init = function(){
		Gionee.models.app = Backbone.Model.extend({
			urlRoot: "/list_more",
		});

		Gionee.collection.app = Backbone.Collection.extend({
			model: Gionee.models.app,
			url: "/app/data?tid=",
			//localStorage: new Store("gnapp-h5v1.0")
		});

		//var Gnapps = new Gionee.collection.app;

		Gionee.routers.app = Backbone.Router.extend({
			routes: {
				'': 'list',
				':nav': 'list',
				'category/:id': 'showAppCateTmpl',
			},

			list: function(nav){
				var _this = this,
					selectedMenu = nav || 'recommend',
					initData = JSON.parse($('.index_data').html());

				var appView = new Gionee.views.header({nav:selectedMenu});

				appView.showTopBanner();

				if(nav == "category"){
					$("#J_appList").html(_.template($("#J_appCateView").html())); return;
				}

				//初始化默认数据
				this.gnapps = new Gionee.collection.app({data:initData[selectedMenu].data});

				$("#J_appList").html(new Gionee.views.appList({model:_this.gnapps,nav:selectedMenu}).render().el);
			},

			showAppCateTmpl: function(id){
				var that = this;
				new Gionee.views.backNav();

				$("#J_appList").html('<div class="loading">数据加载...</div>');
				
				//初始化应用分类默认数据
				this.gnapps = new Gionee.collection.app();

				this.gnapps.url = Gionee.domain.url+'list'+'_more'+'?type_id='+id+'&page=1';
				this.gnapps.fetch({
					success: function(){
						$("#J_appList").html(new Gionee.views.appList({model:that.gnapps,nav:'list',typeId:id}).render().el);
					}
				});
					
			}
		});

		Gionee.views.header = Backbone.View.extend({
			el:$("body"),

			template: _.template($('#J_tabMenuView').html()),
			
			events: {
				'tap #app-tabs li': 'changeTab',
			},

			initialize: function(){
				this.render();
			},

			render: function(){
				$("#J_tabMenu").html(this.template());
				this.selectMenuItem(this.options.nav);
			},

			showTopBanner: function(){
				$("#header").html(_.template($("#J_topBannerView").html()));
			},

			selectMenuItem: function(menuItem){
				$('#app-tabs li').removeClass('actived');
		        if(menuItem){
		            $('#app-tabs li[data-target='+menuItem+']').addClass('actived');
		        }
			},

			changeTab: function(evt){
				var el = $(evt.target);
				var hashName = el.attr('data-link');

				//window.location.href = hashName;
				//非chrome浏览器，物理返回页面不重新加载，JS初始化动作失效，因此手动达到应用程序的对应位置
				gnappRoute.navigate(hashName,true);
				return false;
			}

		});

		Gionee.views.backNav = Backbone.View.extend({
			el: $("#header"),

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
				$("#J_appList").remove();
			}
		});

		Gionee.views.appList = Backbone.View.extend({
			tagName:'ul',
			
			events:{
				'tap .add-btn': 'addAppIcon',
				'tap .open-btn': 'openAppLink',
				'tap .list-more': 'showAppMore',
			},

			initialize: function(){
				this.model.bind("reset", this.render, this);
			},

			render: function(){
				var localData = [], i = 0;

				if(isLocal){
					localData = this.getLocalData();
				}

		        _.each(this.model.models, function (item) {
		        	if(this.options.nav == 'list'){
		        		$("#J_backNavTitle").text(item.get('data').cateName);
		        	}
		        	_.each(item.toJSON().data.list, function(data){
						data.status = isLocal ? localData.indexOf(data.id) > -1 ? 1 : 0 : 1;
		        		this.$el.append(new Gionee.views.appItem({model:item, index:i++}).render().el);
		        	}, this);
		        	
		        	$('.list-more').remove();

			        if(item.get('data').hasnext == true) this.$el.append('<li class="item list-more">加载更多</li>');

		        }, this);

		        return this;
			},

			// 本地接口数据
			getLocalData: function(){
				//查询浏览器本地接口数据
				try{
					localData = JSON.parse(window.prompt("gn://GNNavSiteData/select",""));
				} catch(e){
					localData = 0; //当调用接口不成功时,显示wap版本
				}
				return localData.data;
			},

			addAppIcon: function(evt){
				var target = $(evt.target),appAddUrl = target.attr('data-addUrl');
				var val    = window.prompt("gn://GNNavSiteData/insert",appAddUrl);
				if (browser.android && isWap == false) {
					if (JSON.parse(val).result == '0') {
						window.prompt("gn://GNNavSiteData/tips","添加成功");
						target.html('打开').removeClass("add-btn").addClass('open-btn lock');
						setTimeout(function(){target.removeClass('lock');}, 50);
					} else {
						console.log('Gionee.views.appList:addAppIcon get interface data error.');
					}
				}
			},

			openAppLink: function(evt){
				var target = $(evt.target),appLink = target.attr('data-link'),addUrl = target.attr('data-addUrl');
				//通过addUrl == null 可能判断当前访问的是wap版本，否则访问的是内嵌浏览器版本
				if(!target.hasClass('lock') && addUrl != null){
					window.location.href = appLink;
				}
			},
			//显示更多应用
			showAppMore: function(evt){
				var target = evt.target, that = this;
				var items = this.model.models;
        		var len = items.length;

				if(items[0].get('data').hasnext == false){
					this.$el.find('.list-more').remove();
				}

				if(this.options.nav == 'list'){
					this.model.url = Gionee.domain.url+this.options.nav+'_more'+'?type_id='+this.options.typeId+'&page='+(+items[0].get('data').curpage+1);
				} else {
					this.model.url = Gionee.domain.url+this.options.nav+'_more'+'?page='+(+items[0].get('data').curpage+1);
				}

				this.model.fetch();

				this.loadStatus();

				if ($(target).hasClass('locked') || $(target).parent().hasClass('locked')) return;
					
			},

			loadStatus: function(){
				this.$el.find('.list-more').addClass('locked');
				this.$el.find('.list-more').html('正在加载数据...');
				return this;
			}

		});

		Gionee.views.appItem = Backbone.View.extend({
			tagName:"li",

			className: "item",

			template:_.template($('#J_itemView').html()),

			initialize: function(){
				this.model.bind("change", this.render, this);
        		this.model.bind("destroy", this.close, this);
			},

			render: function(){
				this.$el.append(this.template(this.model.toJSON().data.list[this.options.index]));

				return this;
			},

			close:function(){
		        this.$el.unbind();
		        this.$el.remove();
		    }
		});

		//入口视图、主视图
		Gionee.views.app = Backbone.View.extend({

		});

		var gnappRoute = new Gionee.routers.app();

		Backbone.history.start();
	};
})(ICAT,Zepto);