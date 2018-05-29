(function(iCat, $){
	function notify(){
		this.sHtml = '<div class="popWrap msgMode hide" style="opacity:0;left:'+this.getPos().left+'px;top:'+this.getPos().top+'px;"></div>';
		this.wrap = $('#iScroll');
	}

	notify.prototype = {
		construtor: notify,
		templete: function(){
			$('#iScroll').append(this.sHtml);
		},
		message: function(str,retval){
			var _msgMode = $('.msgMode');
			if(!$('#iScroll').find('.msgMode')[0]){
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
			this.saveForm();
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
		}
	});

	$(function(){
		GOU.init();
		$('input,select').on('click', function(){
			var t = Math.floor($(this).offset().top);
			setTimeout(function(){window.scrollTo(0,t);}, 100);
		});
	});
})(ICAT, jQuery);