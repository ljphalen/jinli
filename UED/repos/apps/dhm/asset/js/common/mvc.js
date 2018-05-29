// mvc渲染页面
define('mvc', ['zepto', 'underscore', 'backbone', 'scroll', 'util', 'lazyload'], 
	function($, _, Backbone, scroll, util, lazyload) {
	
	/**
	 * el: '#J_listWrap',
	 * templateId: '#J_template',
	 * events: {},
	 * url: $('#J_listWrap').attr('data-ajaxUrl'),
	 * method: 'GET',
	 * scroll: false,
	 * templateData: {}
	 */

	var render = function(options) {
		
		var templateData;
		var viewOptions;
		var Model;
		var Collection;
		var collection;
		var View;
		var curPage;
		var hasNext;

		if (!options) return;
		if (!$.isPlainObject(options)) return;
		if (!options.el || !options.templateId) return;

		templateData = options.templateData || {};

		Model = Backbone.Model.extend({});

		Collection = Backbone.Collection.extend({
			model: Model,
			parse: function(response) {
				 if (!$.isPlainObject(response)) response = JSON.parse(response);
				 if (response.success) {
					 if (options.parse) {
					 	return options.parse(response);
					 }
					 return response.data;

				 }
			}
		});

		View = Backbone.View.extend({
			el: $(options.el),
			template: _.template($(options.templateId).html()),
			render: function() {
				$(this.el).append(this.template(this.model.toJSON()));
				if (options.lazyload) {
					lazyload.init();
				}
				return this;
			},
			initialize: function() {
				this.render();
				this.listenTo(this.model, 'change', this.render);
				return this;
			},
			events: options.events || ''
		});

		collection = new Collection();

		collection.on('add', function(model) {
			var view = new View({model: model});
		});

		if (!options.url) {
			collection.add(templateData);
			return;
		}
		// 滑动加载数据
		if (options.scroll) {
			scroll.init({
				collection: collection,
				view: View,
				url: options.url,

			})
			return;
		}

		// 请求数据
		collection.fetch({
			url: options.url
		})
	};

	return {
		render: render
	}
});