/*
 * Created with Sublime Text 2.
 * User: Hankewins
 * Date: 2014-09-15
 * Time: 14:58:28
 * Contact: hankewins@gmail.com
 */
$(function(){
	var _hmt = window._hmt ? window._hmt : [];

	function parseAjaxUrl(url){
		var reg = /^dev.|demo.*/g;
		return reg.test(location.host) === true ? '/3g/' + url + '.php' : url;
	}

	var newsData = {}, cnum = 1, oddTime = 0, evenTime = 0;

	var GNnav = {
		init: function(){
			var initData = ['cinner1','cinner2','cinner3','cinner4','cinner5','cinner6'];

			for (var i = 0; i < initData.length; i++){
				try{
					GNnav.cache.get('GNnav:clumn_'+initData[i]) ? getHtmlRender(initData[i],JSON.parse(GNnav.cache.get('GNnav:clumn_'+initData[i]))) : getHtmlRender(initData[i],typeData[initData[i]]);
				} catch(e){

				}
			}

			var tid = null;
			var drapdownTitle = $('.nav-drapdown-title'), fixedtop = 'nav-drapdown-fixed', arrowup = 'nav-drapdown-arrow-up';

			$('.nav-drapdown-title').on('click',function(){
				var that = $(this);
				var url = that.attr('data-ajax'), wrap = that.attr('data-wrap');
				var isopen = that.hasClass(arrowup) ? true : false;
				var offset = 0;

				if (!isopen) {
					report2baidu('一级栏目展开次数',that.find('.span2').text());
					var prevIndex = drapdownTitle.index($('.nav-drapdown-arrow-up'));
					var prevHeight = $('.nav-drapdown-arrow-up').next().height();
					that.addClass(arrowup);
					that.next().show();
					if (!that.hasClass('done')){
						getClumnData(that,wrap,url);
						that.addClass('done');
					}
					// var currIndex = drapdownTitle.index(that);
					// if (currIndex > prevIndex && prevIndex >= 0){
					// 	offset = prevHeight;
					// } else {
					// 	offset = 0;
					// }
					// setTimeout(function(){
					// 	scrollTo(that,0,function(){
					// 		if (!that.hasClass('done')){
					// 			getClumnData(that,wrap,url);
					// 			that.addClass('done');
					// 		}
					// 	});
					// },200);
				} else {
					that.removeClass(arrowup);
					that.next().hide();
				}

			});

		}
	};

	window.hotWordIndex = 0, window.initIndex = 1;

	GNnav.cache = {
		isLocalStorage: localStorage !== 'undefined' ? true : false,
		get: function(key){
			if(this.isLocalStorage){
				return localStorage.getItem(key);
			}
		},
		set: function(key,value){
			if(this.isLocalStorage){
				return localStorage.setItem(key,value);
			}
		},
		remove: function(){
			if(isLocalStorage){
				return localStorage.removeItem(key);
			}
		}
	};

	/**
	 * 获取每个栏目的动态数据
	 */
	function getClumnData(el,wrap,url){
		var localData = GNnav.cache.get('GNnav:clumn_'+wrap) ? JSON.parse(GNnav.cache.get('GNnav:clumn_'+wrap)) : 0;
		var ver = localData ? localData.data['ver'] : 1;
		if (url) {
			$.ajax({
				url: url,
				method: 'get',
				data:{"ver":ver},
				dataType: 'jsonp',
				success: function(data){
					if(data.success && data.data.data != '-1'){
						getHtmlRender(wrap,data);
						GNnav.cache.set('GNnav:clumn_'+wrap,JSON.stringify(data));
					}
				},
				error: function(){
					el.removeClass('done');
				}
			});
		}
	}

	function scrollTo(el,offset,fn){
		el.addClass('nav-drapdown-arrow-up');
		var otop = el.get(0).getBoundingClientRect().top + window.document.documentElement.scrollTop + window.document.body.scrollTop;
		$.scrollTo({
			endY: otop-offset, 
			duration: 200,
			callback: function() {
				fn();
			}
		});
	}

	function getHtmlRender(wrap,arr){
		var tmp = []; otmpl = {
			//'img1': 'tmpl_img1', // 一栏图片样式
			//'img2': 'tmpl_img2', // 两栏图片样式
			//'img3': 'tmpl_img3', // 三栏图片样式
			'words3': 'tmpl_words3', // 三栏文字链接
			//'words5': 'tmpl_words5', // 五栏文字链接
			//'news_list': 'tmpl_news_list', // 新闻列表
			//'hotlink': 'tmpl_ads_link', // 推荐文字链广告
			//'like': 'tmpl_like', // 猜你喜欢
			'bread': 'tmpl_bread', // 标题栏
			//'lottery': 'tmpl_lottery',
			'img4': 'tmpl_cols4',
			//'img5': 'tmpl_cols5',
			//'cltab': 'tmpl_cltab',
			//'ticket': 'tmpl_ticket'
		};

		var oFragment = document.createDocumentFragment();
		var content = arr.data.data || [];
		for (var i = 0, lens = arr.data.data.length; i < lens; i++) {
			if (arr.data.data[i]["list"]){
				var link = arr.data.data[i]["list"][0];
				if(link && link['link'].indexOf('http:') > -1){
					arr.data.data[i].prevUrl = '';
					arr.data.data[i].t_bi = window.t_bi ? window.t_bi : '';
				} else {
					arr.data.data[i].prevUrl = window.prevUrl ? window.prevUrl : '';
					arr.data.data[i].t_bi = window.t_bi ? "&t_bi=" + window.t_bi : '';
				}
				if(arr.data.data[i]["tpl"] in otmpl){
					var tdata = template.render(otmpl[arr.data.data[i]["tpl"]],arr.data.data[i]);
					tmp.push(tdata);
				}
			}
		}
		oFragment.innerHTML = tmp.join('');
		$("#"+wrap).html(oFragment.innerHTML);
		tmp = null;
	}

	GNnav.init();
});