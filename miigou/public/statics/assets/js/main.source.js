(function(iCat, undefined){
	iCat.app('Gsex');
	
	iCat.mix(Gsex, {
		init: function(){
			iCat.include({
				files: ['./lib/zepto', './lib/iscroll'],
				callback: function(){
					if($('.J_tjList')[0]){Gsex.loaded();}
					if($('.comments')[0]){Gsex.commentMore();}
				},
				isCombo: true
			});
		},

		loadMore: function(oScroll){
			var wrap = $('.J_tjList'),
				ajaxUrl = wrap.attr('data-ajaxurl'),
				curPage = wrap.attr('data-curpage') || 1,
				hasNext = wrap.attr('data-hasnext')==='true',
				num = parseInt(curPage) + 1, page = '';

			if(hasNext && oScroll['page'+num]===undefined){
				page = (iCat.contains(ajaxUrl, '?')? '&':'?') + 'page=' + num;
				oScroll['page'+num] = false;
				$.post(ajaxUrl+page, function(data){
					var html = '', json = JSON.parse(data);
					if(json.success){
						iCat.foreach(json.data.list, function(v){
							html += '<li>\
										<a href="'+v.link+'">\
											<div class="pic">\
												<img src="'+v.img+'" alt="'+v.title+'">\
												<span class="price">￥'+v.price+'</span>\
											</div>\
											<div class="desc">'+v.title+'</div>\
										</a>\
									</li>';
						});
						wrap.find('ul').append(html);
						wrap.attr('data-hasnext', json.data.hasnext)
							.attr('data-curpage', num);
						
						if(!json.data.hasnext) $('#pullUp').hide();
						oScroll['page'+num] = true;
						oScroll.refresh();
					}
				});
			} else {
				oScroll.refresh();
			}
		},

		pullUpAction: function(oScroll){
			var _self = this;
			//setTimeout(function(){ _self.loadMore(oScroll); }, 1000);
			setTimeout(function(){ _self.loadMore(oScroll); }, 500);
		},

		loaded: function(){
			var _self = this,
				myScroll, pullUpEl = $('#pullUp'),
				puEl = pullUpEl[0], puLabel = pullUpEl.find('span.pullUpLabel'),
				wrap = $('.J_tjList');

			myScroll = new iScroll('iScroll', {
				scrollbarClass: 'myScrollbar',
				pullLock: true,
				useTransition: true,

				onRefresh: function(){
					if(pullUpEl.hasClass('loading')){
						puEl.className = '';
						puLabel.html('滑动加载更多...');
					}
				},

				onScrollMove: function(){
					if(wrap.attr('data-hasnext')!=='true') return;

					if(this.y<(this.maxScrollY-5) && !pullUpEl.hasClass('flip')){
						puEl.className = 'flip';
						puLabel.html('松开后加载...');
						this.maxScrollY = this.maxScrollY;
					}
					else if(this.y>(this.maxScrollY+5) && pullUpEl.hasClass('flip')){
						puEl.className = '';
						puLabel.html('滑动加载更多...');
						this.maxScrollY = puEl.offsetHeight;
					}
				},

				onTouchEnd: function(){
					if(wrap.attr('data-hasnext')!=='true') return;

					if(puEl.className.match('flip')){
						puEl.className = 'loading';
						puLabel.html('加载中...');
						_self.pullUpAction(myScroll);
					}
				}
			});

			setTimeout(function(){ $('#iScroll').css('left', 0); }, 800);
		},

		commentMore: function(){
			var box = $('#content'), win = $(window),
				list = $('.comments'), ajaxUrl = box.attr('data-ajaxurl') || '',
				temp = '<%for(var i=0, lists=data.list, len=lists.length; i<len; i++){%>\
						<div class="item">\
							<div class="user-inf">\
								<em><%=lists[i].username%>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<%=lists[i].create_time%></em>\
								<span><%=lists[i].num%>楼</span>\
							</div>\
							<p><%=lists[i].content%></p>\
						</div>\
						<%}%>';
			win.on('scroll', function(){
				if(Gsex.ajaxEnd===false || Gsex.__comment_hasnext===false) return;

				var st = document.body.scrollTop,
					boxH = win.height(),
					panelH = box.height();
				console.log(st+ ' ' +boxH+ ' ' +panelH);
				if(st+boxH===panelH){
					Gsex.ajaxEnd = false;
					$.get(Gsex.__comment_ajaxurl || ajaxUrl, function(data){
						var jsonData = data && iCat.isString(data)?
								(/(.+:.+,?){1,}/.test(data)? JSON.parse(data) : null) : data,
							shtml = '';
							Gsex.ajaxEnd = true;
						if(jsonData){
							var d = jsonData.data || {};
								d.curpage || (d.curpage = 1);
							Gsex.__comment_hasnext = d.hasnext;
							Gsex.__comment_ajaxurl = ajaxUrl + '&page=' + (d.curpage+1);

							if(jsonData.success){
								shtml = iCat.render(temp, jsonData);
								list.append(shtml);
							}
						}
					});
				}
			});
		}
	});

	Gsex.init();
})(ICAT);