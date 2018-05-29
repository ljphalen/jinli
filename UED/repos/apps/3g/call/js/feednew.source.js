(function(){
	var isSendData = false, stime = +new Date, sendNum = 0, curpage = 1, maxpage = 1;

	function parseAjaxUrl(url){
        var reg = /^dev.|demo.*/g;
        return reg.test(location.host) === true ? '/3g/' + url + '.php' : url;
    }
	// input focus and write content for lighthight button
	$("#js-feed-txt").on("keyup",function(){
		if($(this).val().length > 0){
			$("#js-feed-btn").addClass('inp-act');
		} else if($(this).val().length > 250) {
			showTips('太多啦，先把这些发给我吧');
			$("#js-feed-btn").removeClass('inp-act');
		} else {
			$("#js-feed-btn").removeClass('inp-act');
		}



	});

	// on click send button for apeend content to chat list.
	$("#js-feed-frm").on('submit',function(e){
		e.preventDefault();
		var opts = $(this).serialize();
		isSendData = +new Date -stime > 3000 ? false : isSendData;
		if($('#js-feed-btn').hasClass('inp-act')){
			if(!isSendData){
				$.ajax({
					url: "/front/feedback/msgsend",
					type: "get",
					data: opts,
					dataType: "json",
					success: function(data){
						$('.feed-tips-error').remove();
						if(data.success){
							++sendNum;
							var now = new Date(), htime = now.getHours() < 10 ? '0' + now.getHours() : now.getHours(), mtime = now.getMinutes() < 10 ? '0' + now.getMinutes() : now.getMinutes(); sendTime = htime + ':' + mtime;
							if(+new Date - stime > 60000 || sendNum == 1){
								feedback.renderHtml({
									wrap: $("#feed-new-list"),
									line: true,
									type: 1,
									time: sendTime,
									text: $("#js-feed-txt").val(),
									page: 0
								});
							} else {
								feedback.renderHtml({
									wrap: $("#feed-new-list"),
									line: false,
									type: 1,
									time: sendTime,
									text: $("#js-feed-txt").val(),
									page: 0
								});
							}

							$("#js-feed-txt").val('');
							$("#js-feed-btn").removeClass('inp-act');
							isSendData = true;
							stime = +new Date;
							myScroll.refresh();
							if(myScroll.y < -30){
								myScroll.scrollToElement("#feed-new-list div:last-child");
							}
						} else {
							showTips(data.msg);
						}
					},
					error: function(){
						if($('#js-feed-btn').hasClass('inp-act')){
							showTips('发送失败');
						}
					}
				});
			} else {
				showTips('发送太频繁啦，间隔时间为3秒哦');
			}
		}
	});

	function showTips(str){
		if(!$('.feed-tips-error')[0]){
			$('#feed-new-list').append('<div class="feed-tips-error"><i></i><span>'+str+'</span></div>');
			myScroll.refresh();
			myScroll.scrollToElement("#feed-new-list div:last-child");
		} else {
			$('#feed-tips-error span').html(str);
		}
	}

	var feedback = {
		init: function(status){
			var opts = $("#js-feed-frm").serialize();
			if(curpage > maxpage){
				setTimeout(function(){
					myScroll.refresh();
				},2000);
				return;
			}
			$.ajax({
				url: '/front/feedback/msglist',
				type: 'get',
				data: opts+'&page='+curpage,
				dataType: 'json',
				success: function(data){
					$('#pullDown').removeClass('feed-tips-error');
					if(data.success){
						var list = data.data.list, curTime, curType, i = 0;

						$.each(list,function(index,item){
							if(item.time == curTime && item.flag == curType){
								feedback.renderHtml({
									wrap: $("#feed-new-list"),
									line: false,
									type: item.flag,
									time: curTime,
									text: item.content,
									page: data.data.cur_page
								});
							} else {
								curTime = item.time;
								curType = item.type;
								feedback.renderHtml({
									wrap: $("#feed-new-list"),
									line: true,
									type: item.flag,
									time: curTime,
									text: item.content,
									page: data.data.cur_page
								});
							}
						});

						//curpage = data.data.max_page + 1;curpage
						curpage = data.data.cur_page;
						maxpage = data.data.max_page;
						curpage++;
						myScroll.refresh();
						if(status == 1){
							// initlaize app
							myScroll.refresh();
							myScroll.scrollToElement("#feed-new-list div:last-child",0);
						}
					}
				},
				error: function(){
					if(status != 1){
						$('#pullDown').addClass('feed-tips-error');
						$('#pullDown .pullDownLabel').html('加载失败，请检查网络');
					}
				}
			});
		},
		renderHtml: function(obj){
			var wrap = '', shtml;
			if(obj.type == 1){
				sname = 'sender';
			} else {
				sname = 'receiver';
			}
			if(obj.line == true && obj.time != ''){
				shtml = '\
				<div class="date-line">\
					<span class="time">'+obj.time+'</span>\
				</div>\
				<div class="'+sname+'-line">\
					<p class="'+sname+'">'+obj.text+'</p>\
				</div>';
			} else {
				shtml = '<div class="'+sname+'-line">\
					<p class="'+sname+'">'+obj.text+'</p>\
				</div>';
			}

			if(obj.page == curpage && curpage > 1){
				if(obj.page > 1){
					$("#feed-new-list div").first().before(shtml);
				} else {
					$("#feed-new-list div").last().after(shtml);
				}
			} else {
				$("#feed-new-list").append(shtml);
			}
		},
		contact: function(){
			var btnWrap = $('#js-contact-frm .inp-btn-wrap');
			$('#inp-mobile,#inp-qq,#inp-email').on('input',function(){
				if($(this).val().length > 0){
					btnWrap.addClass('inp-btn-act');
				}
			});

			$('#js-contact-frm').on('submit',function(e){
				e.preventDefault();
				var $this = $(this);

				if(btnWrap.hasClass('inp-btn-act')){
					$.ajax({
						url: '/front/feedback/contact',
						type: 'post',
						data: $this.serialize(),
						dataType: 'json',
						success: function(data){
							if(data.success){
								setTimeout(function(){
									window.history.go(-1);
								},1500);
							}
							$('.toast-wrap').show();
							$('.toast-wrap p').html(data.msg);

							setTimeout(function(){
								$('.toast-wrap').hide();
								$('.toast-wrap p').html('');
							},1000);
						}
					});
				}
			});
		}
	};

	var myScroll,pullDownEl, pullDownOffset;

	function pullDownAction () {
		feedback.init();
	}

	function loaded() {
		pullDownEl = document.getElementById('pullDown');
		pullDownOffset = pullDownEl.offsetHeight;
		
		myScroll = new iScroll('feed-new-wrapper', {
			useTransition: true,
			topOffset: pullDownOffset,
			onRefresh: function () {
				if (pullDownEl.className.match('loading')) {
					pullDownEl.className = '';
					pullDownEl.querySelector('.pullDownLabel').innerHTML = '加载完成';
				}
			},
			onScrollMove: function () {
				if (this.y > 5 && !pullDownEl.className.match('flip')) {
					pullDownEl.className = 'flip';
					pullDownEl.querySelector('.pullDownLabel').innerHTML = '下拉加载聊天记录';
					this.minScrollY = 0;
				} else if (this.y < 5 && pullDownEl.className.match('flip')) {
					pullDownEl.className = '';
					pullDownEl.querySelector('.pullDownLabel').innerHTML = '松开停止加载聊天记录';
					this.minScrollY = -pullDownOffset;
				}
			},
			onScrollEnd: function () {
				if (pullDownEl.className.match('flip')) {
					pullDownEl.className = 'loading';
					pullDownEl.querySelector('.pullDownLabel').innerHTML = '加载中，请稍候';				
					pullDownAction();
				}
			}
		});
		
		setTimeout(function () { document.getElementById('feed-new-wrapper').style.left = '0'; }, 100);
	}

	$(window).on('resize',function(){
		myScroll.refresh();
		myScroll.scrollToElement("#feed-new-list div:last-child",0);
	});

	$(function(){
		if($('#feed-new-wrapper')[0]){
			$(document).on('touchmove',function(e){e.preventDefault();});
			loaded();
			feedback.init(1);
		}

		feedback.contact();
	});
})($);