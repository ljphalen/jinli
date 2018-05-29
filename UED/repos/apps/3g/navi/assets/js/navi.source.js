/*
 * Created with Sublime Text 2.
 * User: Hankewins
 * Date: 2014-03-12
 * Time: 20:36:21
 * Contact: hankewins@gmail.com
 */

$(function(){
	/**
	 * $.extend.hover
	 */
	$.extend($.fn, {
		hover: function(cname){
			var that = $(this), cname = cname || "hover";
			that.each(function(k,v){
				$(v).on("touchstart", function(){
					var tid = setTimeout(function(){
						$(v).addClass(cname);
					},50);

					$(document).one("touchmove touchend", function(){
						clearTimeout(tid);
						$(v).removeClass(cname);
					});
				});
			});
		}
	});

	var GNnavi = {};

	var _hmt = window._hmt ? window._hmt : [];

	window.hotWordIndex = 0, window.initIndex = 1;

	GNnavi.cookie = {
		get: function(name) {
			var cookieValue = document.cookie,
				cookieStart = cookieValue.indexOf(encodeURIComponent(name) + '='),
				cookieEnd;

			if (cookieStart != -1) {
				cookieStart = cookieStart + encodeURIComponent(name).length+1;
				cookieEnd = cookieValue.indexOf(";", cookieStart);

				if (cookieEnd == -1) {
					cookieEnd = cookieValue.length;
				}
				return decodeURIComponent(cookieValue.substring(cookieStart,cookieEnd));
			}

			return '';
		},

		set: function(name, value, expires, domain, path, secure) {
			var cookieName = encodeURIComponent(name),
				cookieValue = encodeURIComponent(value);

			var expires = new Date();
			expires.setTime(expires.getTime() + 30 * 30 * 24 * 60 * 60 * 1000);

			document.cookie = cookieName+'='+cookieValue+'; expires=' + expires; 
		}
	};

	window.hasTouch = 'touchstart' in window;
	var ENV_Touchstart= window.hasTouch ? 'tap' : 'click'; 
	
	//$('.btn-touch').hover('touchHover');

	showNavAds();
	showHotWords();
	autoCompalte();
	//setTimeout(window.scrollTo(0,0), 10);
	
	// 广告显示规则
	function showNavAds(){
		var now = new Date(), 
			curr_date =  [now.getMonth()+1, now.getDate()],
			prev_date = window.localStorage.getItem('nav_ads_flag') && window.localStorage.getItem('nav_ads_flag').split(',') || [0,0],
			now_date = $('#nowtime').val() != '' ? $('#nowtime').val() : [now.getFullYear(), now.getMonth()+1 < 10 ? '0' + (now.getMonth()+1) : now.getMonth()+1, now.getDate()].join('');
		// // 同一天内关闭的广告不显示
		// if (curr_date[0] > prev_date[0] || (curr_date[0] == prev_date[0] && curr_date[1] > prev_date[1])){
		// 	//getAdsImage(0);
		// } else {
		// 	$('#J_nav_top_ads').hide();
		// 	console.log('ads closed')
		// }
		$('#J_nav_top_ads .close').on('click', function(){
			_hmt.push(['_trackEvent','GNnavi数据','顶部Banner广告关闭','导航Banner广告-关闭']);
			//window.localStorage.setItem('nav_ads_flag', curr_date);
			GNnavi.cookie.set('navTopAds',now_date);
			var that = $(this);
			$('#J_nav_top_ads').animate({
				'margin-top':'-60px'
			}, 300, 'linear',function(){
				//console.log('nav_top_ads hide.');
			});
		});
	}

	// 换一换
	$('#J_convert').live('click',function(){
		if (window.localStorage && window.localStorage.getItem('navi_baidu_hotwords')) {
			var data = window.localStorage.getItem('navi_baidu_hotwords');
			roundHotWords(JSON.parse(data));
		}
	});


	function showHotWords(){
		// 一小时更新一次百度热词
		if (window.localStorage && window.localStorage.getItem('navi_baidu_hotwords')) {
			var date = new Date(), prev_times = window.localStorage.getItem('navi_start_time') || 0;
			if (date.getTime() - prev_times > 30 * 60 * 1000) {
				//alert('1 hour later.')
				getHotWords();
			} else {
				//alert('1 hour inner.')
				$('.baidu-hot-words').html('');
				
				var data = window.localStorage.getItem('navi_baidu_hotwords');
				//window.hotWordIndex = Math.ceil(Math.random() * (data.length - 1)+1);
				roundHotWords(JSON.parse(data));
			}
		} else {
			//alert('first get baidu hotwrod.');
			getHotWords();
		}
	}

	function getHotWords(){
		var url = $('#api-nav-hotwords').val();
		if (!url){return;}
		$.ajax({
			url: url,
			type: 'get',
			dataType: 'jsonp',
			success: function(data){
				var ret = data.data;
				if (ret) {
					//alert('ajx getHotWords');
					window.localStorage.setItem('navi_start_time', new Date().getTime());
					window.localStorage.setItem('navi_baidu_hotwords',JSON.stringify(ret));
					roundHotWords(ret);
					//window.hotWordIndex = Math.ceil(Math.random() * (ret.length - 1)+1);
				} else {
					$('.hot-words-box').hide();
				}
			}
		});
	}

	function roundHotWords(arr){
		var curr = arr[hotWordIndex], curl = '',nurl = '', surl= '',next = arr[hotWordIndex + 1], last = arr[arr.length -1], sClassName = '';
		if (hotWordIndex < arr.length - 1) {
			if (curr['text'].length + next['text'].length <= 18) {
				hotWordIndex = hotWordIndex + 2;
				sClassName = window.initIndex == 1 ? ' ' : ' class="anima-fade"';
				curl = curr['url'] ?  curr['url'] : "http://m.baidu.com/s?from=1670b&word=" + encodeURIComponent(curr['text']);
				nurl = next['url'] ?  next['url'] : "http://m.baidu.com/s?from=1670b&word=" + encodeURIComponent(next['text']);
				$('.baidu-hot-words').html('<span' + sClassName + '><a onclick="_hmt.push([\'_trackEvent\',\'GNnav数据\',\'导航搜索-百度热词\',\'导航百度热词搜索\'])" href="' + curl + '">' + curr['text'] + '</a></span><span' + sClassName + '><a onclick="_hmt.push([\'_trackEvent\',\'GNnav数据\',\'导航搜索-百度热词\',\'导航百度热词搜索\'])" href="' + nurl + '">' + next['text'] + '</a></span>');
			} else {
				// console.log(curr,next,4);
				var max = Math.max(curr['text'].length, next['text'].length), str;
				sClassName = window.initIndex == 1 ? ' class="center" ' : ' class="anima-fade center"';
				if (curr['text'].length == max) {
					hotWordIndex = hotWordIndex + 1;
					str = curr;
				} else {
					hotWordIndex = hotWordIndex + 2;
					str = next;
				}
				surl = str['url'] ? str['url'] : "http://m.baidu.com/s?from=1670b&word=" + encodeURIComponent(str['text']);
				$('.baidu-hot-words').html('<span' + sClassName + '><a onclick="_hmt.push([\'_trackEvent\',\'GNnav数据\',\'导航搜索-百度热词\',\'导航百度热词搜索\'])"  href="' + surl + '">' + str['text'] + '</a></span>');
			}
		} else {
			hotWordIndex = 0;
		}
		window.initIndex++;
	}

	var drapdownTitle = $('.nav-drapdown-title'), 
		fixedtop = 'nav-drapdown-fixed',
		arrowup = 'nav-drapdown-arrow-up', oheight = {};

	function getHtmlRender(wrap,arr){
		var tmp = []; otmpl = {
			'img1': 'tmpl_img1', // 一栏图片样式
			'img2': 'tmpl_img2', // 两栏图片样式
			'img3': 'tmpl_img3', // 三栏图片样式
			'words3': 'tmpl_words3', // 三栏文字链接
			'words5': 'tmpl_words5', // 五栏文字链接
			'link': 'tmpl_news_list', // 新闻列表
			'hotlink': 'tmpl_ads_link', // 推荐文字链广告
			'like': 'tmpl_like', // 猜你喜欢
			'bread': 'tmpl_bread', // 三栏小说样式
			'news': 'tmpl_cup_news',
			'video': 'tmpl_cup_video',
			'schedule': 'tmpl_cup_notice',
			'footer': 'tmpl_cup_links'
		};

		var oFragment = document.createDocumentFragment();

		for (var i = 0, lens = arr.data.length; i < lens; i++) {
			
			arr.data[i].t_bi = window.t_bi ? window.t_bi : '';

			if (arr.data[i]["list"]  != false){
				var tdata = template.render(otmpl[arr.data[i]["tpl"]],arr.data[i]);
				tmp.push(tdata);
			}
		}
		oFragment.innerHTML = tmp.join('');
		$("#"+wrap).html(oFragment.innerHTML);
		tmp = null;
	}

	$('.nav-drapdown-title').on(ENV_Touchstart,function(){
		var that = $(this);
		var wrap = that.attr('data-wrap');
		drapdownTitle.next().hide();
		drapdownTitle.css({'background':'#fff'});
		//drapdownTitle.next().hide();
		if (that.hasClass(arrowup)) {
			drapdownTitle.removeClass(arrowup);
			// drapdownTitle.removeClass(fixedtop);
			that.removeClass(arrowup);
			// that.removeClass(fixedtop);
		} else {
			if (that.attr('data-wrap') == 'cinner100'){
				that.css({'background':'#fae013'});
			}
			_hmt.push(['_trackEvent','GNnavi数据','一级栏目展开次数',that.find('h2').text()]);
			//that.addClass('loading');
			that.next().show();
			drapdownTitle.removeClass(arrowup);
			if (that.parent().attr('id') !== 'index-hist'){
				getHtmlRender(wrap,typeData[wrap]);
			}

			// oheight.top = that.offset().top;
			// document.body.scrollTop = oheight.top;
			that.addClass(arrowup);
			// that.addClass(fixedtop);
		}
	});


	var late = {  
		storage : {},  
		isinit : 0,  
		maxnum : 8,  
		key : 'app',  
		_init:function(){  
		  if (late.isinit === 1) {  
		      return true;  
		  } else if (late.isinit === 0 && window.localStorage) {  
		      late.isinit = 1;
		      late.storage = window.localStorage;
		      return true;  
		  } else {  
		      return false;  
		  }  
		},  

		get:function(){  
		  if(late._init()){  
		      var data = late.storage.getItem(late.key);
		      return JSON.parse(data);  
		  }else{  
		      return false;  
		  }  
		},  

		set:function(value){  
		  if(late._init()){  
		      var data = late.storage.getItem(late.key);
		      var appIds = late.storage.getItem(late.key + 'Ids');
		      data = JSON.parse(data);
		      appIds = JSON.parse(appIds);
		      console.log('appIds');
		      if(data === null){  
		          data = [];  
		      }

		      if(appIds === null){  
		          appIds = [];  
		      }

		      if (appIds.indexOf(value.appId) > -1) {
		         data.splice(appIds.indexOf(value.appId),1);
		         appIds.splice(appIds.indexOf(value.appId),1);
		      }
		      if (data.length === late.maxnum) {
		          data.pop();
		          appIds.pop();
		      }
		      appIds.unshift(value.appId);
		      data.unshift(value);
		      data = JSON.stringify(data); 
		      appIds = JSON.stringify(appIds);
		      late.storage.setItem(late.key, data);
		      late.storage.setItem(late.key + 'Ids', appIds);
		      return true;  
		  }else{  
		      return false;  
		  }  
		}  
	};

	if (!late.get()){
		$('#index-hist').hide();
	} else {
		$('#index-hist').show();
	}

	$('.aText').live('click',function(){
		var aText = $(this).attr('appTxt') ? $(this).attr('appTxt').substr(0,4) : $(this).text().substr(0,4);
		var appId = $(this).attr('appId');
		var aHref = $(this).attr('src');
		//console.log(aText,appId,aHref);
		var obj = {"aText":aText, "appId": appId, "aHref": aHref};
		late.set(obj);
		histRenderHtml();
		//console.log(obj);
		location.href = aHref;
	});

	function histRenderHtml(){
		var ohist = late.get() || [];
		var histTmpl = [];
		histTmpl.push('<p>');
		for (var i = 1, lens = ohist.length; i <= lens; i++) {
			histTmpl.push('<span><a class="aText" src="'+ohist[i-1].aHref+'" appId="' +ohist[i-1].appId+ '">' +ohist[i-1].aText+ '</a></span>');
			if(i%4 == 0){
				histTmpl.push('</p>');
				if( i != ohist.length){
					histTmpl.push('<p>');
				}
			}
		}

		$('#cinner60').html(histTmpl.join(''));
	}

	histRenderHtml();
	tabNews();
	function tabNews(){
		$('.tab-nav a').live('click',function(){
			$('.tab-nav a').removeClass('active');
			$('.tab-con').addClass('none');
			$(this).addClass('active');
			$('.tab-con').eq($(this).index()).removeClass('none');
		});
	}
	function autoCompalte(val) {
		var searchInput = $('.form-input');
		$('.form-button').on('click',function(evt){
			var defaultValue = val || searchInput.attr('defaultValue');
			if (searchInput.val() == '') {
				_hmt.push(['_trackEvent','GNnavi数据','导航搜索框点击','导航默认关键字搜索']);
			} else {
				_hmt.push(['_trackEvent','GNnavi数据','导航搜索框点击','导航主动关键字搜索']);
			}
		});	
	}

	// btn-love
	$('.btn-love').on('click',function(){
		var that = $(this);
		var num = $('.btn-love .num').text();
		var url = $('#loveUrl').val();

		if (that.hasClass('anim-done')){			
			return;
		}

		if (url) {
			$.getJSON(url,function(data){
				if (data.success) {
					//alert(num);
					$('.btn-love .num').text(+num+1);
				}
			});
		}

		$(".btn-love").animate({
			rotateY:'-180deg'
		}, 500, 'ease-out',function(){
			that.addClass('anim-done');
			$('.btn-love').addClass('btn-love-open')
			$('.btn-love').removeClass('active');
		});
	});

	// topic button
	$('.tools-panel span').on('click',function(){
		var box = $(this).attr('data-box');
		if (!$(this).hasClass('active')) {
			$('.topic-box').addClass('none');
			$('.tools-panel span').removeClass('active');
			$(this).addClass('active');
			$('.'+box).removeClass('none');
		} else {
			$(this).removeClass('active');
			$('.'+box).addClass('none');
		}
	});
	// ajax submit
	$('input[type="radio"]').on('click',function(){
		var comm = $('input[type="radio"]:checked').val();
		var last = $('input[type="radio"]').last().val();
		if (comm == last){
			$('.sel-other').show();
			$('.sel-default').hide();
		} else {
			$('.sel-other').hide();
			$('.sel-default').show();
		}
	});

	$('#btn-commit1,#btn-commit2').on('click',function(){
		var id = $(this).attr('id');
		var comm = $('input[type="radio"]:checked').val();
		var reson = $('textarea').val();
		var tel = $('input[name="contact"]').val();
		var topic_id = $('#topicId').val();
		var feedUrl = $('#feedUrl').val();
		var str = '';
		if (id === "btn-commit1"){
			str = {"token":token,"option_num":comm,"topic_id":topic_id};
		} else {
			str = {"token":token,"option_num":comm,"answer":reson,"contact":tel,"topic_id":topic_id};
		}

		if (feedUrl){
			$.ajax({
				url: feedUrl,
				type: 'POST',
				data: str,
				dataType: 'json',
				success: function(data){
					if (data.success){
						$('.topic-feed').html('感谢您的参与！');
						setTimeout(function(){
							$('.topic-feed').addClass('none done');
							$('.btn-feed').removeClass('active');
						},500);
					}
				}
			});
		}
	});

	$('.topic-vote .ui-radio input').on('click',function(){
		if($(this).attr('checked') === true && $(this).val() != 100){
			$('.btn-vote').addClass('btn-active');
		}

		if($(this).attr('checked') == true && $(this).val() == 100){
			if($('.topic-vote .ui-textarea textarea').val().length > 0){
				$('.btn-vote').addClass('btn-active');
			} else {
				$('.btn-vote').removeClass('btn-active');
			}
			$('#js-textarea-wrap').show();
		} else {
			$('#js-textarea-wrap').hide();
		}

	});

	$('.topic-vote .ui-textarea textarea').on('keyup',function(){
		if($(this).val().length > 0){
			if(!$('.btn-vote').hasClass('btn-active')){
				$('.btn-vote').addClass('btn-active');
			}
		} else {
			$('.btn-vote').removeClass('btn-active');
		}

	});

	$('.btn-vote').on('click',function(){
		var url = $(this).data('url');
		var answer = $('.ui-textarea textarea').val() || '';
		$.ajax({
			url: url,
			type: 'post',
			data:{"answer": $('.ui-textarea textarea').val(),"topic_id":$("#topicId").val(), "option_num": $('.ui-radio input:checked').val(),"token":token},
			dataType: 'json',
			beforeSend: function(){
				$('.topic-vsuccess').addClass('topic-vsuccess-ease');
			},
			success: function(data){
				$('.vote-result li').each(function(index,item){
					if(data.data['checked'] == $(item).find('input').val()){
						$(item).find('input').attr("checked","checked")
					}
					$(item).find('span').eq(0).css({"width":data.data['percent'][index]});
					$(item).find('span').eq(2).html(data.data['percent'][index]);
				});
				$('.topic-vote').addClass('none');
				$('.vote-result').removeClass('none');
				$('.topic-vsuccess').removeClass('topic-vsuccess-ease');
			}
		});
		
	});
});