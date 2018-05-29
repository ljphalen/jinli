(function(iCat) {
	//通过iCat.PathConfig初始化参考路径
	iCat.PathConfig();
	//设置命名空间
	var GNapp = iCat.namespace('GNapp');
	//设置二级命名空间（后期修改为CMD OR　AMD模块后，不需要命名空间的依赖）
	GNapp.models = {};
	GNapp.collections = {};
	GNapp.views = {};
	GNapp.routers = {};

	var isWap = (location.search.indexOf('source=wap') > -1) ? true : false,
		ua = navigator.userAgent,
		isLocal = false,
		browser = {
			android: /android/i.test(ua),
			gionee: /Gionee/i.test(ua)
		};

	isLocal = browser.android && isWap == false;

	iCat.include(['lib/zepto/zepto.js', 'lib/underscore/underscore.js', 'lib/backbone/backbone.js' /*,'./cacheprovider.js'*/ ], function() { //加载依赖文件

		/**
		 * $.extend.hover
		 */
		var G = {};
		G.hasTouch = "ontouchstart" in window;
		G.TAP_EV = G.hasTouch ? 'tap' : 'mousedown';
		G.START_EV = G.hasTouch ? "touchstart" : "mousedown";
		G.MOVE_EV = G.hasTouch ? "touchmove" : 'mousemove';
		G.END_EV = G.hasTouch ? "touchend" : 'mouseup';
		G.CANCEL_EV = G.hasTouch ? "touchcancel" : "mouseup";

		$.extend($.fn, {
			hover: function(cname) {


				var that = $(this),
					cname = cname || "hover";
				var timeId;

				function callback() {
					G.hasActive = true;
					$(G.touchTarget).parents('.item').addClass(cname);
				}

				that.on(G.START_EV, function(ev) {
					G.touchTarget = ev.target;
					timeId = setTimeout(callback, 50);
				});

				that.on(G.MOVE_EV + ' ' + G.END_EV + ' ' + G.CANCEL_EV, function() {
					if (timeId) {
						clearTimeout(timeId);
					}

					if (G.hasActive) {
						$(G.touchTarget).removeClass(cname);
						$(G.touchTarget).parents('.item').removeClass(cname);
						G.hasActive = false;
					}
				});
			}
		});

		var moduleWrap = $(".module").eq(2);
		//var cache = new CacheProvider;
		moduleWrap.append('<div class="app-list J_app_list"></div>');
		var appWrap = $(".J_app_list");

		//model
		GNapp.models.app = Backbone.Model.extend({
			urlRoot: "./root/api",
		});

		//collections
		GNapp.collections.app = Backbone.Collection.extend({
			model: GNapp.models.app,
			url: function() {
				return './app' + '/' + this.models[0].get('data').curnav + '_more';
			},
		});


		//controller
		GNapp.routers.app = Backbone.Router.extend({
			_index: null,
			_data: null,
			_apps: null,
			routes: {
				'': 'index',
				'module/:name': 'showAppModule',
				'category/:id': 'showAppCateDetail',
			},

			initialize: function(options) {
				this._header = new GNapp.views.header();
				this._selectedMenu = 'recommend';
				this._data = JSON.parse($('.index_data').html());
				this._apps = new GNapp.collections.app(this._data[this._selectedMenu]);
				this._index = new GNapp.views.appList({
					model: this._apps,
					module: this._selectedMenu
				});
			},

			index: function() {
				this._header.render(this._selectedMenu);
				this._header.showTopBanner();
				this._index.render();
			},

			showAppModule: function(name) {
				var cacheId = name;
				this._selectedMenu = name;
				this._header.render(this._selectedMenu);
				this._header.showTopBanner();
				var capp = new GNapp.collections.app(this._data[this._selectedMenu]);

				if (this._selectedMenu == "category") {
					appWrap.html(_.template($("#J_appCateView").html()));
					return;
				}
				//初始化默认数据
				appWrap.html(new GNapp.views.appList({
					model: capp,
					module: this._selectedMenu
				}).render().el);
			},

			showAppCateDetail: function(id) {
				var that = this;
				new GNapp.views.backNav();

				appWrap.html('<div class="loading">数据加载...</div>');
				var capp = new GNapp.collections.app();

				capp.fetch({
					url: './app/list_more',
					success: function() {
						appWrap.html(new GNapp.views.appList({
							model: capp,
							module: 'cate',
							typeId: id
						}).render().el);
					},
					data: {
						type_id: id,
						page: 1
					}
				});
			}
		});

		GNapp.views.header = Backbone.View.extend({
			el: 'body',
			template: _.template($('#J_tabMenuView').html()),

			initialize: function() {
				this.$el.delegate('.in-tabs li', G.TAP_EV, this.swichTab);

				this.render();

			},

			render: function(id) {
				$(".module").eq(1).html(this.template());
				this.selectMenuItem(id);
			},

			showTopBanner: function() {
				$(".module").eq(0).html(_.template($("#J_topBannerView").html()));
			},

			selectMenuItem: function(menuItem) {
				$('#app-tabs li').removeClass('actived');
				if (menuItem) {
					$('#app-tabs li[data-target=' + menuItem + ']').addClass('actived');
				}
			},

			swichTab: function() {
				var hashName = $(this).attr('data-link');
				//window.location.href = hashName;
				//非chrome浏览器，物理返回页面不重新加载，JS初始化动作失效，因此手动达到应用程序的对应位置
				gnappRoute.navigate(hashName, true);
			}
		});

		GNapp.views.backNav = Backbone.View.extend({
			el: $(".module").eq(0),
			template: _.template($('#J_backNavView').html()),

			initialize: function() {
				this.render();
			},

			render: function() {
				$(this.el).html(this.template({
					cateName: ''
				}));
				this.hideTabMenu();
				return this;
			},

			hideTabMenu: function() {
				$("#app-tabs").remove();
			},

			hideAppList: function() {
				$(".app-list").remove();
			}
		});

		GNapp.views.appList = Backbone.View.extend({
			tagName: 'ul',
			events: {
				'click .add-btn': 'addAppIcon',
				'click .list-more': 'showAppMore',
				'click .item-cont': 'openAppLink',
				'click .open-btn': 'openUrlLink'
			},

			initialize: function() {
				//this.model.bind("reset", this.render, this);
				this.listenTo(this.model, 'reset', this.render);
			},

			render: function() {
				var clientData = [],
					i = 0;

				if (isLocal) clientData = this.getLocalData();

				_.each(this.model.models, function(item, index) {
					if (this.options.module == 'cate') {
						$("#J_backNavTitle").text(item.get('data').cateName);
					}

					item.get('data').curnav = this.options.module;

					_.each(item.get("data").list, function(data) {
						data.status = isLocal ? clientData.indexOf(data.id) > -1 ? 1 : 0 : 1;
						this.$el.append(new GNapp.views.appItem({
							model: item,
							module: this.options.module,
							index: i++
						}).render().el);
					}, this);

					$('.list-more').remove();

					if (item.get('data').hasnext == true) this.$el.append('<li class="item list-more">加载更多</li>');

				}, this);

				this.$el.appendTo(appWrap);
				//add touch effect
				//$('.J_app_list').hover('app-item-hover');

				return this;
			},

			// 本地接口数据
			getLocalData: function() {
				//查询浏览器本地接口数据
				try {
					localData = JSON.parse(window.prompt("gn://GNNavSiteData/select", ""));
				} catch (e) {
					localData = 0; //当调用接口不成功时,显示wap版本
					console.log('GNapp.views.appList:getLocalData get interface data error.');
				}
				return localData.data;
			},

			addAppIcon: function(evt) {
				var target = $(evt.target),
					appAddUrl = target.attr('data-addUrl');

				if (browser.android && isWap == false) {
					var val = window.prompt("gn://GNNavSiteData/insert", appAddUrl);
					if (JSON.parse(val).result == '0') {
						window.prompt("gn://GNNavSiteData/tips", "添加成功");
						target.text('打开').removeClass("add-btn").addClass('open-btn lock');
						setTimeout(function() {
							target.removeClass('lock');
						}, 50);
					} else {
						console.log('GNapp.views.appList:addAppIcon get interface data error.');
					}
				}
			},

			openAppLink: function(evt) {
				var target = $(evt.target).parents('li').find('.button .btn');
				//通过addUrl == null 可以判断当前访问的是wap版本，否则访问的是内嵌浏览器版本
				var appLink = target.attr('data-addUrl') == null ? target.attr('href') : target.attr('data-link');
				//window.open(appLink); //仅在当前窗口中打开
				window.location.href = appLink;
			},

			openUrlLink: function(evt) {
				var target = $(evt.target);
				if (target.attr('data-addUrl') && target.attr('data-link') && !target.hasClass('lock')) {
					window.location.href = target.attr('data-link');
				}
			},

			//显示更多应用
			showAppMore: function(evt) {
				var me = this;
				var target = evt.target;
				var item = this.model.at(0);
				var curpage = item.get('data').curpage + 1;
				if ($(target).hasClass('locked') || $(target).parent().hasClass('locked')) return;

				if (item.get('data').hasnext == false) this.$el.find('.list-more').remove();

				this.loadStatus();

				if (this.options.module == "cate")
					this.model.fetch({
						data: {
							type_id: this.options.typeId,
							page: curpage
						}
					});
				else
					this.model.fetch({
						data: {
							page: curpage
						}
					});
			},

			loadStatus: function() {
				this.$el.find('.list-more').addClass('locked');
				this.$el.find('.list-more').html('正在加载数据...');
				return this;
			}

		});

		GNapp.views.appItem = Backbone.View.extend({
			tagName: "li",
			className: "item",
			template: _.template($('#J_itemView').html()),

			initialize: function() {
				this.model.bind("change", this.render, this);
				this.model.bind("destroy", this.close, this);
			},

			render: function() {
				this.$el.append(this.template(this.model.get("data").list[this.options.index]));
				return this;
			},

			close: function() {
				this.$el.unbind();
				this.$el.remove();
			},
		});

		gnappRoute = new GNapp.routers.app();
		Backbone.history.start();
		
	}, true);
})(ICAT);