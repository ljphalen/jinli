(function(iCat){
	iCat.PathConfig();
	iCat.PathConfig._isConcat = false;
	//定义命名空间
	iCat.app("GNnews",function(){
		return {version:'1.1'};
	});

	//setTimeout(function(){window.scrollTo(0,1);},100);
	iCat.include(['lib/zepto/zepto.js'],function(){
		//$('.wrapper , #footer').hide();
		//http://m.sohu.com/c/8315/?v=3&_trans_=000011_jl_qsy&qq-pf-to=pcqq.discussion
		//var url = "http://m.sohu.com/c/8315/?v=3&_trans_=000011_jl_qsy&qq-pf-to=pcqq.discussion";

		//var _ifr = $("body").append('<iframe border="0" frameborder="no" style="position:absolute; z-index:10000; top:0; left:0;" width="100%" height="100%" src="' + url + '"></iframe>');
		GNnews.init();
	});

	iCat.mix(GNnews,{
		init: function(){
			// $('.ui-search form').on('submit',function(e){
			// 	e.preventDefault();
			// 	var saction = $(this).attr('action'),
			// 		sname = $('.inp-search').attr('name'),
			// 		svalue = $('.inp-search').val(),
			// 		surl = saction + '&' + sname + '=' + encodeURIComponent(svalue);
			// 		console.log('aa');
			// 	window.location.href = surl;
			// });

			setTimeout(function(){
				GNnews.fetchData();
				GNnews.slider();
				GNnews.baiduHots();
			},1500);
			/*GNnews.gotop();
			GNnews.showCateMore();*/

			$(".news-tab").delegate('li','click',function(evt){
				var that = $(this), supWrap, subWrap, 
					part = that.data('part'),num = that.data('num'),
					remoteUrl = that.data('remote');

				supWrap = $("#J_news_tab_item"+part+" ul");
				subWrap = supWrap.eq(num);
				that.parent().children().removeClass('sel');
				that.addClass('sel');

				supWrap.addClass('ishide');
				subWrap.removeClass('ishide');
				if(subWrap.hasClass('done')) return;
				
				GNnews.getNews(remoteUrl,subWrap);
			});
		},

		cache: function(){
			if (arguments[1] == undefined) {
				return window.localStorage.getItem(arguments[0]);
			}

			return window.localStorage.setItem(arguments[0], arguments[1]);
		},

		baiduHots: function(){
			var url = $('#api-baidu-hots').val();

			$.getJSON(url,function(res){
				$('.inp-search').val(res.data.top1);
				$('.in-hot-world').html(res.data.keywords);
				$('.btn-search').on('click', function(){
					var that = $(this), defaultValue = res.data.top1;
					if ($('.inp-search').val() == defaultValue) {
						_hmt.push(['_trackEvent','GNnews数据','GNnews-搜索框点击','GNnews-默认关键字搜索']);
					} else {
						_hmt.push(['_trackEvent','GNnews数据','GNnews-搜索框点击','GNnews-主动关键字搜索']);
					}
				});
			})
		},

		fetchData: function(){
			if (!$(".news-tab-item")) return;

			var wrap = $(".news-tab-item"), url, curport, newsNav = $(".news-tab");
			
			//初始化默认新闻栏目
			var initDataUrl = $('#initNewsData').val();
			var ids = initDataUrl.slice(initDataUrl.indexOf('ids=')+4).split(',');

			if (ids) {
				$.getJSON(initDataUrl, function(res){
				    if(res.success) {
				    	//初始化头条新闻
					    var str = "<a href=\""+ res.data[0][1].list.data[0].url +"\">"+ res.data[0][1].list.data[0].title +"</a>";
						$(".news-hot-title").html(str);

						$.each(newsNav,function(key,val){
			                curport = $(val).children().first().data('part');
							var wrap = $("#J_news_tab_item"+key+" ul").first();
		                    var data = res.data[key+1][1].list.data, arr = [], tmpl;

							wrap.addClass('done').html('');

		                    $.each(data,function(index,item) {
		                        if(item.color == "") {
		                            tmpl = '<li class="item"><a href="'+item.url+'">'+item.title+'</a></li>';
		                        } else {
		                            tmpl = '<li class="item"><a href="'+item.url+'" style="color:'+item.color+'">'+item.title+'</a></li>';
		                        }

		                        arr.push(tmpl);
		                    });

		                    if (res.data[key+1][1].moreUrl) {
		                        arr.push('<li class="item list-more"><a href="'+res.data[key+1][1].moreUrl+'">更多资讯</a></li>');
		                    }

		                    wrap.append(arr.join(''));
						});
	                }
				});
			}
		},
		getNews: function(url,wrap,moreList){
			$.ajax({
				type: 'GET',
				url: url,
				dataType: 'json',
				success: function(res){
                    if(res.success) {
                        wrap.addClass('done').html('');
                        var data = res.data[0][1].list.data, arr = [], tmpl;
                        $.each(data,function(index,item){
                            if(item.color == "")
                                tmpl = '<li class="item"><a href="'+item.url+'">'+item.title+'</a></li>';
                            else
                                tmpl = '<li class="item"><a href="'+item.url+'" style="color:'+item.color+'">'+item.title+'</a></li>';
                            arr.push(tmpl);
                        });

                        if(res.data[0][1].moreUrl && moreList == undefined){
                            arr.push('<li class="item list-more"><a href="'+res.data[0][1].moreUrl+'">更多资讯</a></li>');
                        }
                        wrap.append(arr.join(''));
                    }
				}
			});
		},

		slider: function(){
			if (!$("#J_full_slider")) return;

			var sliderEl = $("#J_full_slider"), touchMode = false, url = sliderEl.data('ajaxurl');

			//$.getJSON(url, function(data){renderSlider(data.data);});

			$.ajax({
				type: 'GET',
				url: url,
				dataType: 'json',
				timeout: 3000,
				success: function(data){
					renderSlider(data.data);
					GNnews.cache('news_top_ads',JSON.stringify(data.data));
				},
				error: function(xhr, type, error) {
					var data = JSON.parse(GNnews.cache('news_top_ads'));
					renderSlider(data);
				}
			});

			function renderSlider(data){
				var sHtml = '', sHandle = '', sTmpl = '', i = 0, lens = data.length;
				for (; i < lens; i++) {
					sHtml = sHtml + '<li><a href="' + data[i].link + '"><img src="' + data[i].img + '" ></a><a href="' + data[i].link + '"><span>' + data[i].title + '</span></a></li>';
					sHandle = sHandle + (i == 0 ? '<span class="on"></span> ': '<span></span> ');
				}

				sTmpl = '<div class="ui-slider-wrap">\
							<div class="ui-slider-content">\
						 		<ul class="ui-slider-pic ui-slider-text">\
						 			' + sHtml + '\
						 		</ul>\
						 	</div>\
						 	<div class="ui-slider-handle ui-slider-handle-circle">\
								' + sHandle + '\
							</div>\
						</div>';

				$('#J_full_slider').append(sTmpl);

				var	pannel = sliderEl.find('.ui-slider-content'), item = pannel.find('li'), len = item.length,
					handles = sliderEl.find('.ui-slider-handle span'),

				scroll = function(index){
					if(index==len){
						index = 0;
						pannel.animate({left:0}, 0);
					} else {
						pannel.animate({left:(-item.width()*index)+'px'},500);
					}
					handles.removeClass('on').eq(index).addClass('on');
					sliderEl.attr('curIndex', index);
				},

				autoplay = function(isFirst){
					var timer = setTimeout(function(){
						if(isFirst) pannel.width(item.width()*len);
						var curIndex = (parseInt(sliderEl.attr('curIndex')) || 0) + 1;
						scroll(curIndex);
						autoplay();
					}, 8000);
				};

				autoplay(true);

			}

			/*iCat.include(['./gn.slider.js'],function(){
				$("#J_full_slider").slidePic({
					slideWrap:   '.ui-slider-wrap',
					slidePanel:  '.ui-slider-pic',
					slideItem:   '.ui-slider-pic li',
					handlePanel: '.ui-slider-handle',
					handleItem:  '.ui-slider-handle > span',
					circle: true,
					auto:true,
					touchMode: false
				});
			});*/
		},

/*		gotop: function(){
			if(!$('.ui-goptop')) return;

			window.onscroll = function(){
				if (document.body.scrollTop > 50) {
					$('.ui-gotop').show();
				} else {
					$('.ui-gotop').hide();
				}
			}

			$('.ui-gotop').on('click',function(){
				setTimeout(function(){window.scrollTo(0,1);},100);
			});
		},
		showCateMore: function(){
			if (!$('.J_news_more')) return;

			$('.J_news_more').on('click',function(){
				$('.ui-toolbar-right .icon-arrow').toggleClass('icon-arrow-state');
				$('.news-menu-wrap').toggleClass('ishide');
			});
		}*/
	});
})(ICAT);