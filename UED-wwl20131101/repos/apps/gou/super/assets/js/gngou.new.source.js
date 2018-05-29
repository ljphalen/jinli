(function(iCat, $){

	function notify(){
		this.sHtml = '<div class="popWrap msgMode hide" style="opacity:0;left:'+this.getPos().left+"px;top:"+this.getPos().top+"px;"+'"></div>';
		this.wrap = $('#page');
	}

	notify.prototype = {
		construtor: notify,
		templete: function(){
			$('#page').append(this.sHtml);
		},
		message: function(str,retval){
			var _msgMode = $('.msgMode');
			if(!$('#page').find('.msgMode')[0]){
				this.templete();
			}
			$('.msgMode').html(str);

			$('.msgMode').removeClass('hide').addClass('show').animate({opacity:1},300,'linear');
			if($('.msgMode').hasClass('show')){
				setTimeout(function(){
					$('.msgMode').animate({opacity:0},300,'linear',function(){$('.msgMode').removeClass('show').addClass('hide')});
				},1500);
			}
			return retval;
		},
		getPos: function(){
			var w = $(window).width(),
				h = $(window).height();
				return {
					left: w/2-80,
					top: h-100
				}
		}
	};

	var info = new notify();


	var lang = {
		//order
		order:{
			createdOrder:"订单已生成！",
			cneeInfoNull:"请完善收货人信息！",
			stopBuyTips:"您已经购买过了，此商品每人限购1个，再看看其他的吧。",
			cneeNameNull:"请填写收货人！",
			cneePhoneNull:"请填写联系方式！",
			cneeAddressNull:"请填写收货地址！",
			cneePostCodeNull:"请填写邮政编码！",
			cneeProvinceNull:"请选择省份！",
			cneeCityNull:"请选择城市！",
			accountUmidNull:"请输入帐号！",
			accountUnameNull:"请输入姓名！",
			phoneNull:"请填写您的充值号码",
			phoneNotConfirm:"两次输入不一致"
		},
		//order tips
		GOU_tips1: "商品加入心愿清单了！",
		GOU_tips2: "心愿清单中已有些商品。",
		GOU_tips3: "此商品每人限购一件哦！",
		GOU_tips4: "活动结束",
		GOU_tips5: "您已领取过，下次再来！",
		GOU_tips6: "请勿重复操作！",
		GOU_tips7: "服务器繁忙,请稍后重试！",
		GOU_tips8: "已到最后一页",
		GOU_tips9: "请勿频繁操作！",
		GOU_tips10: "反馈内容超过500字！",

		//ERROR TIPS
		GOU_error1: "请填写正确的邮政编码！",
		GOU_error2: "请填写正确的11位手机号！",
		//check login tips
		noLoginTip: "请登录!",
		feedbackNull: "内容不能为空！",
		resendInfo: "请勿重复提交！",

		realnameNull: "姓名不能为空！",
		bornDateNull: "请选择出生年月日！",
	};

	iCat.app('GOU',function(){
		return {version: '0.9.3',isLogin:false,currTime:0}
	});

	iCat.mix(GOU,{
		init: function(){
			this.slidePic();
			this.saveForm();
			this.orderList();
			this.morder();
			this.countdown();

			sessionStorage.setItem('curpage', location.href);
			var domain = location.protocol+'//'+location.host;
			if($('html').attr('manifest')){
				$('body').delegate('a', 'click', function(evt){
					evt.preventDefault();
					if(!this.getAttribute('data-ajaxUrl'))
						location.href = !navigator.onLine? domain+'/offline' : this.href;
				});
			}

			(function(){
				function shake(){
					var i = 0,
					t = setInterval(function(){
						i++;
						if(i<=5) $('.goto-rule a').toggleClass('active');
						else clearInterval(t);
					},600);
				}
				
				shake();
				var ix = 0,
				timer =setInterval(function(){
					ix++;
					ix==3? clearInterval(timer) : shake();
				},5000);
			})();

			(function(){
				var input = $('input');
				input.focus(function(){
					$(document.body).css('padding-bottom','100px');
				})
				.blur(function(){
					$(document.body).css('padding-bottom','0');
				});
			})();
		},

		slidePic: function(){
			var wrap = $('.J_slide, .J_quickLinks');
			if(!wrap[0]) return;

			iCat.incfile(['/zepto/touchSwipe.js','../slidePic.js'], function(){
				wrap.slidePic(
					wrap.hasClass('J_slide')?
					{
						circle:true,
						auto:true,
						disableFirst:true,
						disableLast:true
					} : {
						slidePanel: '.slideWrap',
						slideItem: '.slideWrap ul',
						prev: '.J_quickLinks .prev',
						next: '.J_quickLinks .next',
						speed:400
					}
				);
			}, true);
		},
		//倒计时
		countdown: function(){
			if($('.J_countDown')[0]){
				iCat.incfile([iCat.appRef+'/super/assets/js/countDown.js'], function(){
					$('.J_countDown').countDown({
						startHandle: function(index){
							//活动开始时触发
							$('.J_countDown').eq(index).parent('li').hide();
						},
						endHandle: function(index){
							//活动结束时触发
							$('.J_countDown').eq(index).parent().find('.button .btn').remove();
							$('.J_countDown').eq(index).parent().find('.button').html('<span class="btn gray-arrow">'+lang.gng_tips4+'</span>');
							
						}
					});
				});
			}
		},

		//验证表单
		checkForm: function(){
			var options = {
				realname: $('input[name=realname]'),
				byear: $('select[name=year]'),
				bmonth: $('select[name=month]'),
				bday: $('select[name=day]'),
			};

			var rg = null;

			if(options.realname && options.realname.val() == ''){
				return info.message(lang.realnameNull,false);
			}

			if(options.byear && options.byear.val() == 0){

				return info.message(lang.bornDateNull,false);
			}

			if(options.bmonth && options.bmonth.val() == 0){
				return info.message(lang.bornDateNull,false);
			}

			if(options.bday && options.bday.val() == 0){
				return info.message(lang.bornDateNull,false);
			}

			return true;

		},

		saveForm:function(){
			$("#J_saveForm").on('submit',function(evt){
				evt.preventDefault();

				if(!GOU.checkForm()) return false;

				var _this = $(this), ajaxUrl = _this.attr("action");
				var param = _this.serialize();

				 iCat.mix(param,{token:token});
				_this.addClass('required');

				if(_this.hasClass( "required" )){
					$.post(ajaxUrl,param,function(data){
						if(data.success == false){
							_this.removeClass('required');
							return info.message(data.msg,false);
							
						} else {
							//登录成功
							if(info.message(data.msg,true)){
								if(data.data.type == 'redirect' && data.data.url){
									setTimeout(function(){
										window.location.href=data.data.url;
									},2000);
									
								}
							}
						}
					},"json");
				}
			});
		},
		orderList: function(){
			if(!$('#J_createOrder')[0]) return;

			var __quantity     = $('.quantity'), quantity,
				__subtotal     = $('.subtotal'), subtotal,
				__totalPrice   = $('.total-price'), totalPrice,
				__maxLimitNum  = $('.maxLimitNum'), maxLimitNum,
				__coinNum      = $('.coinNum'), coinNum,
				__subcointotal = $('.subcointotal'), subcointotal,
				__maxCoinLimit = $('.maxCoinLimit'), maxCoinLimit,
				__result       = [], __out = [], __unit = "元";

			//修改购买数量
			maxLimitNum = + __maxLimitNum.text();
			quantity = + __quantity.val();
			coinNum     = __coinNum.val();
			subcointotal = __subcointotal.html().match(/\d+.\d+/ig)[0];
			maxCoinLimit = __maxCoinLimit.html() == null ? 0.00 : + __maxCoinLimit.html().match(/\d+.\d+/ig)[0];
			subtotal = + __subtotal.html().match(/\d+.\d+/ig)[0];
			totalPrice = + __totalPrice.html().match(/\d+.\d+/ig)[0];

			__out.push(quantity); //[0] 默认数量 1
			__out.push(subtotal); //[1] 默认单价
			__out.push(coinNum);  //[2] 默认使用银币数量
			__out.push(subcointotal); //[3] 默认使用银币
			__out.push(totalPrice); //[4] 默认实际支付

			$('.quantity').bind('keypress',function(evt){
				//只能输入数字和小数点
				return evt.keyCode >= 48 && evt.keyCode <=57;
			});

			__quantity.bind('change',function(){
				if(__quantity.val() > maxLimitNum){
					__quantity.val(maxLimitNum);
				}

				if(__quantity.val() == '' || __quantity.val() < 1){
					__quantity.val(__out[0]);
					__coinNum.val(__out[2]);
				} else {

				}

				console.log(__quantity.val());

				subtotal = parseInt(__quantity.val()) * __out[1];

				__subtotal.html(subtotal.toFixed(2)+__unit);

				coinNum  = parseInt(__quantity.val()) * __out[2];

				//subcointotal = parseInt(__coinNum.val()) * parseInt(__quantity.val());

				if(coinNum > maxCoinLimit){
					coinNum = maxCoinLimit;
					//subcointotal = maxCoinLimit;
				}
				__coinNum.val(coinNum.toFixed(2));

				__subcointotal.html("-"+coinNum.toFixed(2)+__unit);

				__totalPrice.html( (subtotal - coinNum).toFixed(2)+__unit);

			});

			__coinNum.bind('change',function(){
				if(__coinNum.val() > maxCoinLimit){
					__coinNum.val(maxCoinLimit);
				}

				if(__coinNum.val() == ''){
					__coinNum.val(0);
				}

				subcointotal = parseInt(__coinNum.val());
				var tmp = parseInt(__quantity.val()) * __out[2];

				if(subcointotal > tmp && tmp < maxCoinLimit){
					subcointotal = tmp;
					__coinNum.val(tmp);
				}

				__subcointotal.html("-"+subcointotal.toFixed(2)+__unit);

				__totalPrice.html( (subtotal - subcointotal).toFixed(2)+__unit);
			});
		},
		
		//订单处理
		morder: function(){
			var silver_coin = $('.coinNum');
			var goods_id = $('#goods_id');
			var address_id = $('#address_id');
			var number = $('.quantity');
			var info = new notify();

			var gbook = $('.J_leaveMsg');
			var phone = $('.J_phonePay');
			var phone_conf = $('.J_phonePay_again');

			//确认订单
			$("#J_createOrder").bind('click',function(evt){

				var _this = $(this), url = _this.attr('data-ajaxUrl'),
					params = {
						token: token,
						number: number.val(),
						silver_coin : silver_coin.val(),
						goods_id : goods_id.val(),
						address_id : address_id.val()
					};

				if(address_id.val() == ''){return info.message(lang.order.cneeInfoNull,false);}
				if(phone[0]){
					if(phone.val() == ''){return info.message(lang.order.phoneNull,false);}
					if(phone.val() !== phone_conf.val()){return info.message(lang.order.phoneNotConfirm,false);}
				}
				
				iCat.mix(params,
					!gbook[0]? {phone:phone.val(), phone_conf:phone_conf.val()} : {gbook:gbook.val()}
				);

				_this.addClass('hadsend');

				$.post(url,params,function(data){
					var odata = data;
					if(odata.success){
						info.message(odata.msg,true);
						
						if(odata.data.url){
							setTimeout(function(){
								$('.msgMode').hide();
								window.location.href = odata.data.url;
							},1000);
							

						}

					} else {
						_this.removeClass('hadsend');
						return info.message(odata.msg,false);
					}
				},'json');

			});

			//收货人表单

			$('#J_checkForm').bind('submit',function(evt){
				evt.preventDefault();
			});

			//点击马上领取
			$('#J_getCoinLink').bind('click',function(evt){
				evt.preventDefault();

				var _this = $(this), url = _this.attr('data-ajaxUrl');

				$.getJSON(url,function(data){
					if(data.success){
						_this.addClass('requested');
						if(data.data.type == 'redirect' && data.data.url){
							window.location.href = data.data.url;
						} else {
							return info.message(data.msg,true);
						}

					} else {
						return info.message(data.msg,false);
					}
				});
			});


		}
	});

	$(function(){
		GOU.init();
		/*var win = $(window), h = win.height();

		win.on('resize', function(){
			window.scrollTo(0, h-win.height()-50);
		});*/

		$('input,select').on('click', function(){
			var t = Math.floor($(this).offset().top);
			setTimeout(function(){window.scrollTo(0,t);}, 100);
		});

		//为了统计
		/*var cr = $('body').attr('dt-cr');
		if(cr){
			$('a').click(function(evt){
				evt.preventDefault();
				//$.get(cr+'?url='+encodeURIComponent(this.href));
				var label = this.getAttribute('dt-labelCla')? '&tid='+this.getAttribute('dt-labelCla') : '';
				location.href = cr+'?url='+encodeURIComponent(this.href)+label;
			});
		}*/
	});
})(ICAT, Zepto);