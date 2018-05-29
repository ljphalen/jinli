(function(iCat, $){
	var GNnav = iCat.namespace('GNnav');
	//简单的选项卡切换插件
	$.fn.tabs = function(options){
		var defaults = {
			navWrap: '.ui-tabs-nav',
			itemWrap: 'li',
			contentWrap: '.ui-tabs-content',
			parentAjax: false
		};
		var opt = $.extend(defaults, options), me = $(this);
		var element = me.find(opt.navWrap),
			control = me.find(opt.contentWrap);

		if (opt.parentAjax) {
			opt.callback({
				url: element.data('ajaxurl')
			});
		}

		element.delegate(opt.itemWrap, "click", function(){
			var tabName = $(this).data("tab"), ajaxUrl = $(this).data('ajaxurl');
			if (ajaxUrl === 'undefined') 
				element.trigger("change:tabs", tabName);
			else {
				element.trigger("change:tabs", [tabName, ajaxUrl]);
			}
		});

		element.on("change:tabs", function(e, tabName){
			element.find(opt.itemWrap).removeClass('ui-state-active');
			element.find("[data-tab='" + tabName + "']").addClass('ui-state-active');
		});

		element.on("change:tabs", function(e, tabName, ajaxUrl){
			if (ajaxUrl) {
				if (!element.find("[data-tab='" + tabName + "']").hasClass('done')) {
					opt.callback({
						url: ajaxUrl,
						tabName: tabName,
					});
				}
			}
			control.find("[data-tab]").removeClass('ui-state-active');
			control.find("[data-tab='" + tabName + "']").addClass('ui-state-active');
		});

		var firstName = element.find("li:first-child").attr("data-tab");
		element.trigger("change:tabs", firstName);

		return this;
	};


	iCat.mix(GNnav,{
		init: function(){
			iCat.incfile(["../tempcore.js"],function(){
				//在线应用、软件下载数据请求
				if($("#J_tab_siteWrap")[0]){
					$("#J_tab_siteWrap").tabs({
						callback: function(data){
							GNnav.getApps(data);
						}
					});
				}
				//新闻动态数据请求
				if($("#J_tab_newsWrap")[0]){
					$("#J_tab_newsWrap").tabs({
						parentAjax: true,
						callback: function(data){
							GNnav.getNews(data);
						}
					});
				}

				GNnav.autocomplete();

				sessionStorage.setItem('curpage', location.href);
				var domain = location.protocol+'//'+location.host;
				if ($('html').attr('manifest')) {
					$('body').delegate('a', 'click', function(evt){
						evt.preventDefault();
						if(!this.getAttribute('data-ajaxUrl'))
							location.href = !navigator.onLine? domain+'/offline' : this.href;
					});
				}
			});
		},

		getApps: function(data){
			var element = $('#J_tab_siteWrap'), 
				navWrap = element.find('.ui-tabs-nav [data-tab='+data.tabName+']'),
				contentWrap = element.find('.ui-tabs-content [data-tab='+data.tabName+']');
			$.ajax({
				type: 'get',
				url: data.url,
				dataType: 'json',
				success: function(data) {
					if(data.success) {
						navWrap.addClass('done');
						contentWrap.append('<ul class="hot-site-icon">'+template("J_siteAppView",data)+'</ul>');
					}
				}
			});
		},

		getNews: function getNews(data) {
			var tabLink1 = $('#tabLink1 .nav-news-list'),
				tabLink2 = $('#tabLink2 .nav-news-list'),
				tabLink3 = $('#tabLink3 .nav-news-list'),
				tabLink4 = $('#tabLink4 .nav-news-list');
			$.ajax({
				type: 'get',
				url: data.url,
				dataType: 'json',
				success: function(data){
					tabLink1.html('').append(template("J_siteNewsView",data.data.top_list));
					tabLink1.append('<li class="btn-more news-more"><a href="http://i.ifeng.com/news/newsi?ch=zd_jl_dh&vt=5&dh=touch&mid=9yCLji">更多资讯</a></li>');
					tabLink2.html('').append(template("J_siteNewsView",data.data.news_list));
					tabLink2.append('<li class="btn-more news-more"><a href="http://i.ifeng.com/?from=itouch&mid=9yCLji&ch=zd_jl_dh&vt=5&vt=5">更多资讯</a></li>');
					tabLink3.html('').append(template("J_siteNewsView",data.data.play_list));
					tabLink3.append('<li class="btn-more news-more"><a href="http://i.ifeng.com/ent/enti?ch=zd_jl_dh&vt=5&dh=touch&mid=9yCLji">更多资讯</a></li>');
					tabLink4.html('').append(template("J_siteNewsView",data.data.war_list));
					tabLink4.append('<li class="btn-more news-more"><a href="http://i.ifeng.com/mil/mili?ch=zd_jl_dh&vt=5&dh=touch&mid=9yCLji">更多资讯</a></li>');
				},
				error: function(xhr, type, error){
					console.log('network connection is error.');
				}
			});
		},

		autocomplete: function() {
			var searchInput = $('.inp-search');

			if(!searchInput[0]) return;

			searchInput.addClass("inp-default");
			searchInput.on('focus',function(evt){
				var that = $(this), defaultValue = that.attr('defaultValue');
				if(that.val() == defaultValue) that.val('').removeClass("inp-default");
			});

			searchInput.on('blur',function(evt){
				var that = $(this), defaultValue = that.attr('defaultValue');
				if(that.val() == "") that.val(defaultValue).addClass("inp-default");
			});

			searchInput.parent().find("input[type=submit]").on('click',function(evt){
				if(searchInput.val() == '') evt.preventDefault();
			});
		}
	});

	$(function() {
		GNnav.init();
		//为了统计
		var cr = $('body').attr('dt-cr');
		if (cr) {
			$('a').click(function(evt){
				evt.preventDefault();
				var label = this.getAttribute('dt-labelCla')? '&tid='+this.getAttribute('dt-labelCla') : '';
				location.href = cr+'?url='+encodeURIComponent(this.href)+label;
			});
		}
	});
})(ICAT, Zepto);