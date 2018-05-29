(function(iCat, $){
	iCat.app('hotapp', function(){
		return {
			version: '1.0',
			blankPic:iCat.PathConfig.picPath + 'blank.gif',
		};
	});
	
	iCat.mix(hotapp, {
		init: function(){
			hotapp.firstLoad();
			hotapp.slidePic();
			hotapp.openclose();
			hotapp.lazyLoad(document.body,100);
			hotapp.hover();
		},
		
		slidePic: function(){
			var widthUl = $(".pic-show .wrap ul li").size() * $(".pic-show .wrap ul li").width();
			$(".pic-show .wrap ul").css({'width': widthUl});
			return;
		},

		lazyLoad: function(pNode, t) {
			var imgs = $(pNode).find('img[src$="blank.gif"]'),
				_fn = function(o) {
					var src = o.getAttribute('data-src');
					iCat.__IMAGE_CACHE = iCat.__IMAGE_CACHE || {};
					if (!src) return;

					if (!iCat.__IMAGE_CACHE[src]) {
						var oImg = new Image();
						oImg.src = src;
						oImg.onload = function() {
							o.src = src;
							iCat.__IMAGE_CACHE[src] = true;
							$(o).attr('data-src', src);
							oImg = null;
						};
					} else {
						o.src = src;
						$(o).attr('data-src', src);
					}
				};

			iCat.foreach(imgs, function(i, v) {
				t ? setTimeout(function() {
					_fn(i);
				}, v * t) : _fn(v);
			});
		},

		openclose: function(){
			var J_expand = $(".J_expand"),
				arrowDown=$(J_expand.children('p')[0]),
				arrowUp=$(J_expand.children('p')[1]),
				J_content=$("#J_content");
			J_expand.bind('click',function(){
				if(arrowDown.hasClass('hidden')){
					arrowDown.removeClass('hidden');
					arrowUp.addClass('hidden');
					J_content.removeClass('h-auto');
				} else{
					arrowUp.removeClass('hidden');
					arrowDown.addClass('hidden');
					J_content.addClass('h-auto');
				}
			});
		},

		firstLoad:function(){
			if(!$(".J_loadMore")[0]) return;
			var successCallback=function(){
				$(".J_app_item").show();
				$(".J_loadMore").show();
				hotapp.loadMore();
			};
			var errorCallback=function(){
			};
			hotapp.getData(successCallback,errorCallback);
		},

		loadMore:function(){
			var btnMore=$(".J_loadMore");
			if (!btnMore[0]) return;
			var locked = false,
				hn=btnMore.attr('data-hasnext'),
				text_tip=$("#J_text_tip"),
				img_tip=$("#J_img_tip");
			var successCallback=function(){
				locked = false;
				var hasnext=$(".J_loadMore").attr('data-hasnext');
				if (hasnext == 0 || hasnext == 'false') {
					$(window).unbind('scroll');
					btnMore.hide();
				} else {
					text_tip.hide();
					img_tip.css('display',"inline-block");
				}
			};
			var errorCallback=function(){
				locked = false;
				setTimeout(function(){
					img_tip.hide();
					text_tip.css('display',"inline-block");
				},4000);
			}
			$(window).scroll(function(event) {
				var boxHeight = document.body.clientHeight,
					visibleHeight = document.documentElement.clientHeight,
					boxScrollTop = document.body.scrollTop;
				
				if (hn == 0 || hn == 'false') {
					$(window).unbind('scroll');
				} else {
					if (locked == true) return;
					if (Math.abs(boxHeight - visibleHeight) <= boxScrollTop + 10) {
						text_tip.hide();
						img_tip.css('display','inline-block');
						locked = true;
						hotapp.getData(successCallback,errorCallback);
					}
				}
			});
		},
		getData:function(successCallback,errorCallback){
			var btnMore = $(".J_loadMore"),
				curpage = parseInt(btnMore.attr('data-curpage')),
				ajaxUrl= btnMore.attr('data-ajaxUrl');
			$.ajax({
				type: "POST",
				url: ajaxUrl,
				data: {
					page: curpage + 1,
					token: token
				},
				dataType: 'json',
				success: function(data) {
					btnMore.attr('data-hasnext', data.data.hasnext).attr('data-curpage', data.data.curpage);
					var pNode = $('.J_app_item'),
						s = '',
					strTemp ='<li>\
								<a class="info" href="{detailUrl}">\
									<div class="icon"><img alt=""  src="' + hotapp.blankPic + '" data-src="{icon}"></div>\
									<div class="desc">\
										<p class="title">{name}</p>\
										<p class="size">{size}</p>\
										<p class="rank-{score}">\
										<span></span><span></span><span></span><span></span><span></span></p>\
									</div>\
								</a>\
								<div class="download"><a class="btn btn-default" href="{link}">下载</a></div>\
							  </li>',
					/* template-data merge */
					tdMerge = function(t, d, r) {
						if (!iCat.isString(t) || !/\{|\}/g.test(t)) return false;
						var phs = t.match(/(\{[a-zA-Z]+-[a-zA-Z]+\})|(\{[a-zA-Z]+[a-zA-Z]+\})|(\{[a-zA-Z]+_[a-zA-Z]+\})/g); //fixed bug 判断{字符-字符}
						if (!phs.length) return false;
						iCat.foreach(phs, function() {
							var key = this.replace(/\{|\}/g, ''),
								regKey = new RegExp('\{' + key + '\}'),
								val = d[key];
							t = t.replace(regKey, val? val:(r? '{'+key+'}':''));
						});
						return t;
					};
					iCat.foreach(data.data.list, function(v, i) {
						s += tdMerge(strTemp, v);
					});
					pNode.append(s);
					hotapp.lazyLoad(document.body, 100);
					
					successCallback();
				},
				error: function() {
					errorCallback();
				}
			});
		},
		hover:function(){
			var btn=$(".download a.btn");
			if(!btn.length) return;
			btn.on('touchstart',function(){
				$(this).addClass('btnHover');
			});
			btn.on('touchmove',function(){
				$(this).addClass('btnHover');
			});
			btn.on('touchend',function(){
				$(this).removeClass('btnHover');
			});
		}
	});
	
	$(function(){hotapp.init();});
})(ICAT, Zepto);