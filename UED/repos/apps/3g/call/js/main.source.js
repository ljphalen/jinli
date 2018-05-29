define(['./zepto','./ajax','./scrollfix','./dialog','./tab'],function(){
	var ctid = null;

	function report2baidu(n1,n2){
		_hmt = window._hmt || [];
		_hmt.push(['_trackEvent', 'gnnav-ucenter', n1, n2]);
	}

	if($('#js-collect-tips')){		
		ctid = setTimeout(function(){
			$('#js-collect-tips').hide();
		},10000);
	}

	$('#js-collect-tips i').on('click',function(){
		$.fn.cookie('stat-collect-tips',endTime,{expires:1});
		clearTimeout(ctid);
		$('#js-collect-tips').hide();
	});

	if($('.pop-full-mask')[0]){
		if(window.localStorage && window.localStorage.getItem('pop-full-ads') !== 'on'){
			$('.pop-full-mask').height($(document).height()).removeClass('none');
			$('.pop-full-wrap').removeClass('none');
		}
	}

	$('#js-btn-close,.pop-full-wrap a').on('click',function(){
		$('.pop-full-mask').hide();
		$('.pop-full-wrap').hide();
		report2baidu('个人中心-用户首次进入提示框点击数','个人中心-用户首次进入提示框点击数');
		window.localStorage.setItem('pop-full-ads','on');
	});

    // 立即兑换
    $('#award-page #js-actionsheet-ok').on('click',function(){
        $('#award-page .ui-actionsheet-cancel').trigger('tap');
    });

    // 
    $('.ui-award-item .btn span').on('click',function(){
    	report2baidu('个人中心-立即兑换点击次数','个人中心-立即兑换点击次数');
    	var that = $(this), url = that.data('url'), sid = that.data('type'), refurl = that.data('refurl');
    	if(!that.hasClass('done')){
			$.ajax({
	    		url: url,
	    		type: 'get',
	    		data:{'score_type': sid, 'refurl': refurl},
	    		dataType: 'json',
	    		success: function (data) {
	    			if(data.success){
	    				if(data.data.redirect){
	    					location.href = data.data.redirect;
	    				}
	    			} else {
	    				$('.js-dialog-content').find('li:first-child').html(data.msg);
						$('#triggerDialog').trigger('tap');
	    			}
	    			that.removeClass('done');
	    		}
	    	});
    	}
    });

    // 动态显示省市
    $('#province_id').on('change',function(){
    	var pid = $(this).val(), url = $(this).data('url'), city_arr = [];
    	$.ajax({
    		url: url,
    		type: 'get',
    		data: {"province_id": pid},
    		dataType: 'json',
    		success: function(data){
    			if(data.success){
    				$.each(data.data,function(index,item){
    					city_arr.push('<option value="'+item.id+'">'+item.name+'</option>');
    				});
    				$('#city_id').html(city_arr.join());
    			}
    		}
    	});
    });


    $('#setting-form').on('submit',function(e){
    	e.preventDefault();
    	var nickname = $('input[name="nickname"]').val(),
    		email = $('input[name="email"]').val(),
    		qq = $('input[name="qq"]').val(),
    		sex = $('input[name="sex"]:checked').val(),
    		pid = $('select[name="province_id"]').val(),
    		cid = $('select[name="city_id"]').val(),
    		addr = $('input[name="address"]').val();
    	var url = '/user/center/editPost';

    	$.ajax({
    		url: url,
    		type: "post",
    		data:{
    			"nickname": nickname,
    			"email": email,
    			"qq": qq,
    			"province_id": pid,
    			"city_id": cid,
    			"sex": sex,
    			"address": addr,
    			"token": token
    		},
    		dataType: "json",
    		success: function(data){
    			if(data.success){
    				if(data.data.redirect){
    					location.href = data.data.redirect;
    				}
    			} else {
    				showTips(data.msg);
    			}
    		},
    		error: function(){
    			showTips('aaa');
    		}
    	});	
    });

    function showMsg(el,str,time){
        el.removeClass('none').text(str);
        setTimeout(function(){
            el.addClass('none').text('');
        },time || 3000);
    }

    function showTips(str){
		$('.toast-wrap').show();
		$('.toast-wrap p').html(str);
		setTimeout(function(){
			$('.toast-wrap').hide();
			$('.toast-wrap p').html('');
		},1000);
	}


	$('#js-picture-list').on('click','span',function(e){
		report2baidu('个人中心-看图列表点击次数','个人中心-看图列表点击次数');
		var that = $(this), url = $('#js-picture-list').data('earn'), gid = that.data('id'), sid = that.data('type');
		if(!that.hasClass('done')){
			$.ajax({
				url: url,
				type: 'get',
				data: {'goods_id':gid,'score_type': sid},
				dataType: 'json',
				success: function(data){
					if(data.success){
						if(data.msg){
							report2baidu('个人中心-看图列表点击成功次数','个人中心-看图列表点击成功次数');
							that.addClass('done');
							that.append('<span class="ui-picture-mask">已领取<span>');
							if(data.data.redirect){
								location.href = data.data.redirect;
							}
						} else {
							if(data.data.redirect){
								location.href = data.data.redirect;
							}
						}
						
					}
				}
			});
		}
	});

	// $('#award-tel').on('keyup',function(){
	// 	var reg = /\d{11}/g;
	// 	var that = $(this), url = that.data('url');
	// 	if(that.val().length == 11){
	// 		if(!reg.test(that.val())){
	// 			$('#js-error-tips').html('请输入有效的电话号码');
	// 			return;
	// 		} else {
	// 			$('#js-error-tips').html('');
	// 		}
	// 		$(this).blur();
	// 		$.ajax({
	// 			url: url,
	// 			type: 'get',
	// 			data: {"tel": that.val()},
	// 			dataType: 'json',
	// 			success: function(data){
	// 				if(data.success){
	// 					$('#js-error-tips').html(data.msg);
	// 				} else {
	// 					$('#js-error-tips').html(data.msg);
	// 				}
	// 			}
	// 		});
	// 	}
	// });

	// 兑换中心－立即领取
	$('#js-award-btn').on('click',function(){
		var $this = $(this),
			tel = $('#award-tel'),
			mobileNo = tel.val(),
			goodsId = tel.data('id'),
			refurl = $this.data('refurl');
			reg = /\d{11}/g;
		var exchange_url = $('#js-award-btn').data('url');
		if(!$this.data('loaded')){
			if(mobileNo){
				if(!reg.test(tel.val())){
					$('#js-error-tips').html('请输入有效的电话号码');
					return;
				} else {
					$('#js-error-tips').html('');
				}
				$.ajax({
					url: tel.data('url'),
					type: 'get',
					dataType: 'json',
					data: {'mobile': mobileNo, 'goods_id': goodsId},
					success: function(data){
						if(data.success){
							$('#award-detail-page #ui-actionsheet-text').html('您确定为'+data.data.area+mobileNo+'冲值'+data.data.outprice+'元？');
							$('#triggerDialog').trigger('tap');
							var sending = false, dtime, stime = +new Date, count = 1;
							$('#award-detail-page #js-actionsheet-ok').on('click',function(){
								dtime = +new Date - stime;
								//console.log(sending,stime,dtime,count);
								if(count != 1){
									if(sending === true || dtime < 10000) {
										++count;
										stime = +new Date;
										return;
									}
								}
								
								++count;
						        var inprice = data.data.inprice, area = data.data.area;
								$.ajax({
									url: exchange_url,
									type: 'post',
									dataType: 'json',
									data: {'token':token, 'refurl': refurl, 'tel':mobileNo, 'goods_id':goodsId, 'inprice':inprice},
									success: function(res){
										if(res.success){
											sending = true;
											report2baidu('个人中心-兑换奖品次数','个人中心-兑换话费成功次数');
											if(res.data.redirect){
												location.href = res.data.redirect;
											}
											$this.attr('loaded',true);
										} else {
											$('#js-error-tips').html(res.msg);
										}
									}
								});
						    });	
						} else {
							$('#js-error-tips').html(data.msg);
						}
					}
				});


			} else {
				$.ajax({
					url: exchange_url,
					type: 'post',
					dataType: 'json',
					data: {'refurl':refurl, 'token':token},
					success: function(res){
						if(res.success){
							report2baidu('个人中心-兑换奖品次数','个人中心-兑换券成功次数');
							if(res.data.redirect){
								location.href = res.data.redirect;
							}
							$this.attr('loaded',true);
						} else {
							$('#js-error-tips').html(res.msg);
						}
					}
				});
			}
		}
	});

	$.scrollPage = $.extend({},{
	    config:{
			pageData:{},//页面数据
			dataLoding:false,//是否正在加载数据
			doneLoadData: false,
			canStartLoadData:false,//是否可以开始加载数据
			pageNo:1,//页码
			pageItem:10,//没有的数据条数（每页显示的条数）
			currentPageItem:30,//当前页面显示了多少数据
			time:null,//时间及时器
			speed:10,//判断是否可加载的间隔
	    },

	    init:function (_config){
			var _this = $.scrollPage;
			$.extend(_this.config,_config);
			return _this;
	    },

	    showLoading: function(){
	    	if($('#page-order')[0]){
	    		var curIndex = $('.ui-button.js-active').data('index');
	    		$('#page1-tab'+curIndex+' .ui-loading').html('正在努力加载中...');
	    	} else {
	    		$('.ui-loading').html('正在努力加载中...');
	    	}
	    },

	    closeLoading: function(){
	    	if($('#page-order')[0]){
	    		var curIndex = $('.ui-button.js-active').data('index');
	    		$('#page1-tab'+curIndex+' .ui-loading').html('上拉加载');
	    	} else {
	    		$('.ui-loading').html('上拉加载');
	    	}
	    },

	    doneLoading: function(){
	    	if($('#page-order')[0]){
				var curIndex = $('.ui-button.js-active').data('index');
	    		$('#page1-tab'+curIndex+' .ui-loading').remove();
	    	} else {
	    		$('.ui-loading').remove();
	    	}
	    },

	    loadData:function (obj){
			var _this = $.scrollPage;
			var cfg = _this.config;
			var _canStart = _this.isPageBottom();//是否滚动到页面底部
			if(_canStart){
				_this.showLoading();
				_this._startLoad(_this._endLoad);//开始加载
			}
			return _this;
	    },

	    _startLoad:function (callback){
			var _this = $.scrollPage;
			var cfg = _this.config;
			cfg.dataLoding = true;//正在加载数据
			var _loadData = _this.getNext();//要加载的数据
			// for(var i in _loadData){
			// _this._addRow(_loadData[i]);//添加行
			// }
			if(callback){
				callback();
			}
	        
	    },

	    getNext: function(){},

	    _endLoad:function (){
			var _this = $.scrollPage;
			var cfg = _this.config;
			cfg.dataLoding = false;
			cfg.canStartLoadData = false;
			setTimeout(function (){
				_this.closeLoading();
			},2000);
	    	return _this;
	    },

	    isPageBottom:function (rangeTop) {  
	        var scrollTop = 0;  
	        var clientHeight = 0;  
	        var scrollHeight = 0;
	        rangeTop = rangeTop || 0;

	        scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
	        //scrollTop = $(document).scrollTop();
	        clientHeight = document.documentElement.clientHeight||window.clientHeight;
	        scrollHeight = Math.max(document.body.scrollHeight, document.documentElement.scrollHeight);
	        
	        if (scrollTop + clientHeight + rangeTop >= scrollHeight) {  
	            return true;  
	        } else {  
	            return false;  
	        }  
	    }
	});

	$(window).on('scroll',function(){
		if($('#page-order')[0]){
			$.scrollPage.getNext = function(){
				var _this = $.scrollPage;
			    var curTab = $('.ui-button.js-active');
				var curIndex = curTab.data('index');
			    var pindex = curTab.data('pindex') || 2;
				var html = [];
				var url = curTab.data('url');
		    	if(!curTab.data('loaded')){
			    	$.ajax({
			    		url: url,
			    		type: 'get',
			    		dataType: 'json',
			    		data: {'token':token, 'page': pindex},
			    		success: function(res){
			    			if(res.success){
				    			if(res.data.hasNext === false){
				    				curTab.attr('data-loaded','true');
				    				_this.doneLoading();
				    			}

			    				pindex++;
			    				curTab.attr('data-pindex',pindex);

				    			$.each(res.data.list,function(index,item){
				    				html.push('<li><span>'+item.score_type+'</span><span>'+item.affected_score+'</span><span>'+item.add_time+'</span></li>');
				    			});

				    			$('#page1-tab'+curIndex+' ul').append(html.join(''));
			    			}
			    		}
			    	})
		    	}
			}
		}

		if($('#page-msg')[0]){
			$.scrollPage.getNext = function(){
				var _this = $.scrollPage;
				var wrap = $('#js-msg-list'), url = wrap.data('url'), pindex = wrap.data('pindex') || 2;
				var html = [];
		    	if(!wrap.data('loaded')){
			    	$.ajax({
			    		url: url,
			    		type: 'get',
			    		dataType: 'json',
			    		data: {'page': pindex},
			    		success: function(res){
			    			if(res.success){
			    				pindex++;
			    				wrap.attr('data-pindex',pindex);
				    			if(res.data.hasNext === false){
				    				wrap.attr('data-loaded','true');
				    				_this.doneLoading();
				    			}

				    			$.each(res.data.list,function(index,item){
				    				var class_status = item.status == 1 ? 'class="tips-success"' : 'class="tips-warning"';
				    				html.push('<li class="ui-item"><p '+class_status+'>'+item.content+'</p><span>'+item.add_time+'</span></li>');
				    			});

				    			wrap.append(html.join(''));
			    			}
			    		}
			    	})
		    	}
			}
		}

		if($('#page-picture')[0]){
			$.scrollPage.getNext = function(){
				var _this = $.scrollPage;
				var wrap = $('#js-picture-list'), url = wrap.data('url'), pindex = wrap.data('pindex') || 2;
				var html = [];
		    	if(!wrap.data('loaded')){
			    	$.ajax({
			    		url: url,
			    		type: 'get',
			    		dataType: 'json',
			    		data: {'token':token, 'page': pindex},
			    		success: function(res){
			    			if(res.success){
			    				pindex++;
			    				wrap.attr('data-pindex',pindex);
				    			if(res.data.hasNext === false){
				    				wrap.attr('data-loaded','true');
				    				_this.doneLoading();
				    			}

				    			$.each(res.data.list,function(index,item){
				    				if(item.get === 1){
				    					html.push('<li class="ui-item"><a class="link" href="'+item.link+'" data-id="'+item.id+'" data-type="'+res.data.score_type+'"><img src="'+res.data.attachPath+'/'+item.image+'" alt=""><span class="ui-picture-mask">已领取</span></a></li>');
				    				} else {
				    					html.push('<li class="ui-item"><span class="link" data-id="'+item.id+'" data-type="'+res.data.score_type+'"><img src="'+res.data.attachPath+'/'+item.image+'" alt=""></span></li>');
				    				}
				    			});

				    			wrap.append(html.join(''));
			    			}
			    		}
			    	})
		    	}
			}
		}

		$.scrollPage.loadData();
	})
});