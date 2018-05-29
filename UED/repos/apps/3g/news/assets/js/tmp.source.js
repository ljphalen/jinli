(function(iCat){
	iCat.PathConfig();
	iCat.PathConfig._isConcat = false;
	//定义命名空间
	iCat.app("GNnews",function(){
		return {version:'1.1'};
	});
	//setTimeout(function(){window.scrollTo(0,1);},100);
	iCat.mix(GNnews,{
		init: function(){
			//官网预置页面获取数据从缓存中读取，存在中断网络的情况，因此做短暂的延时处理
			setTimeout(function(){
				GNnews.fetchData();
				GNnews.slider();
			},1500);

			$('.ui-search form').on('submit',function(e){
				e.preventDefault();
				var saction = $(this).attr('action'),
					sname = $('.inp-search').attr('name'),
					svalue = $('.inp-search').val(),
					surl = saction + '&' + sname + '=' + encodeURIComponent(svalue);
				window.location.href = surl;
			});

			GNnews.gotop();
			GNnews.showCateMore();
			GNnews.baiduHots();
		},

		baiduHots: function(){
				var url = $('#api-baidu-hots').val();

				$.getJSON(url,function(res){
					$('.inp-search').val(res.data.top1);
					$('.in-hot-world').html(res.data.keywords);
				})
		},

		fetchData: function(){
			if (!$(".news-tab-item")) return;


			var hotUrl = $(".news-hot-title").attr('remote'), wrap = $(".news-tab-item"), url, curport, newsNav = $(".news-tab");
			//初始化头条新闻
			//GNnews.getNews(hotUrl,$("#J_news_hot"),false);
			$.getJSON(hotUrl,function(res){
				var str = "<a href=\""+ res.data.list.data[0].url +"\">"+ res.data.list.data[0].title +"</a>";
				$(".news-hot-title").html(str);
			});


			if(navigator.onLine){
				//初始化默认新闻栏目
				$.each(newsNav,function(index,item){
                    curport = $(item).children().first().data('part');
					url = $("#J_news_tab"+curport+" li").first().data('remote');
					GNnews.getNews(url,$("#J_news_tab_item"+index+" ul").first());
				});
			}

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
				if(navigator.onLine){
					GNnews.getNews(remoteUrl,subWrap);
				}
			});
		},
		getNews: function(url,wrap,moreList){
			$.ajax({
				type: 'GET',
				url: url,
				dataType: 'json',
				success: function(res){
                    //if(res.success) {
                        wrap.addClass('done').html('');
                        var data = res.data.list.data, arr = [], tmpl;
                        $.each(data,function(index,item){
                            if(item.color == "")
                                tmpl = '<li class="item"><a href="'+item.url+'">'+item.title+'</a></li>';
                            else
                                tmpl = '<li class="item"><a href="'+item.url+'" style="color:'+item.color+'">'+item.title+'</a></li>';
                            arr.push(tmpl);
                        });

                        if(res.data.moreUrl && moreList == undefined){
                            arr.push('<li class="item list-more"><a href="'+res.data.moreUrl+'">更多资讯</a></li>');
                        }
                        wrap.append(arr.join(''));
                    //}
				}
			});
		},
		slider: function(){
			if (!$("#J_full_slider")) return;
			var sliderEl = $("#J_full_slider");
			var	pannel = sliderEl.find('.ui-slider-content'),
				item = pannel.find('li'),
				len = item.length,
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
		},
		gotop: function(){
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
		}
	});

	GNnews.init();

})(ICAT);