(function(iCat, $){
	function notify(){
		this.sHtml = '<div class="msgMode popWrap hide" style="opacity:0;left:50%; margin-left:-6rem; bottom:10%;"></div>';
		this.wrap = $('#page');
		this.count = 1;
	}

	notify.prototype = {
		//construtor: notify,
		templete: function(){
			$('#page').append(this.sHtml);
		},
		message: function(str,retval){
			var _msgMode = $('.msgMode');
			if(this.count == 1){
				this.templete();
			}
			this.count++;
			$('.msgMode').html(str).removeClass('hide').addClass('show').animate({opacity:1},300,'linear');
			if($('.msgMode').hasClass('show')){
				setTimeout(function(){
					$('.msgMode').animate({opacity:0},300,'linear',function(){$('.msgMode').removeClass('show').addClass('hide')});
				},1500);
			}
			return retval;
		},
		/*getPos: function(){
			var w = $(window).width(),
				h = $(window).height();
				return {
					left: w/2-80,
					top: h/2+100
				}
		}*/
	};

	var info = new notify();

	var lang = {
		//porfile center
		pc_error1: "请填写正确的邮政编码！",
		pc_error2: "请填写正确的11位手机号！",

		nicknameNull: "昵称不能为空！",
		nicknameFormatError: "昵称长度不能大于20个字符！",
		bornDateNull: "请选择出生年月日！",
		emailNull: "Email不能为空！",
		emailFormatError: "Email格式错误！",
		phoneTypeNull: "请选择机型！",
		qqNull: "QQ号不能为空！",
		qqFormatError: "QQ号不正确！"
	};

    var Gnwap = iCat.namespace('Gnwap');
    Gnwap.version = '0.8';

	iCat.mix(Gnwap,{
		init: function(){
			Gnwap.checkForm();
            Gnwap.saveForm();
		},
		//验证表单
		checkInput: function(){
			var options = {
				nickname: $('input[name=nickname]'),
				qq: $('input[name=qq]'),
				email: $('input[name=email]'),
				phoneType: $('select[name=model]'),
				byear: $('select[name=year]'),
				bmonth: $('select[name=month]'),
				bday: $('select[name=day]')
			};

			var rg = null;

			if(options.nickname && options.nickname.val() == ''){
				return info.message(lang.nicknameNull,false);
			}

			if(options.qq && options.qq.val() == ''){
				return info.message(lang.qqNull,false);
			} else {
				rg = /^\d+$/;

				if(options.qq.get().length && !rg.test(options.qq.val()))
					return info.message(lang.qqFormatError,false);
			}

			if(options.email && options.email.val() == ''){
				return info.message(lang.emailNull,false);
			} else {
				rg = /^[a-zA-Z0-9_+.-]+\@([a-zA-Z0-9-]+\.)+[a-zA-Z0-9]{2,4}$/;

				if(options.email.get().length && !rg.test(options.email.val()))
					return info.message(lang.emailFormatError,false);
			}

			if(options.byear && options.byear.val() == "请选择年"){

				return info.message(lang.bornDateNull,false);
			}

			if(options.bmonth && options.bmonth.val() == "请选择月"){
				return info.message(lang.bornDateNull,false);
			}

			if(options.bday && options.bday.val() == "请选择日"){
				return info.message(lang.bornDateNull,false);
			}

			if(options.phoneType && options.phoneType.val() == 0){
				return info.message(lang.phoneTypeNull,false);
			}

			return true;

		},

		saveForm:function(){
			$("#J_saveForm").on('submit',function(evt){
				evt.preventDefault();

				if(!Gnwap.checkInput()) return false;

				var _this = $(this), ajaxUrl = _this.attr("action");
				var param = _this.serialize()+"&token="+token;

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

        checkForm: function(){
            if(!$('#J_checkForm')) return;

            var sUrl = $('#J_checkForm').attr('action'), oText = $('textarea[name=react]'), oEmail = $('input[name=contact]');
			$('#J_checkForm').on('submit',function(e){
                e.preventDefault();
				var that = $(this),
                    isEmail = /^[a-zA-Z0-9_+.-]+\@([a-zA-Z0-9-]+\.)+[a-zA-Z0-9]{2,4}$/.test(oEmail.val()),
                    isTelephone = /^1[3458]\d{9}$/.test(oEmail.val());

				if (oText.val() == "")
                    return info.message('请填写反馈内容！',false);

				if (oText.val().length > 500)
                    return info.message('反馈内容过长！',false);

                if ( oEmail.val().length > 0 && !(isEmail || isTelephone) )
                    return info.message('联系方式格式错误！',false);
				
				$.post(sUrl,{"react":oText.val(),"contact":oEmail.val(),token:token},function(res){
                    if (res.success === false) {
                        //服务端返回失败
                        return info.message(res.msg, false);
                    } else {
                        //服务端返回成功
                        if (info.message(res.msg, true)) {
                            if (res.data.type == 'redirect' && res.data.url) {
                                setTimeout(function(){
                                    location.href=res.data.url;
                                },2000);
                            }
                        }
                    }
				},'json');
			});
		}
	});

	$(function(){
		Gnwap.init();
		//为了统计
		var cr = $('body').attr('dt-cr');
		if(cr){
			$('a').click(function(evt){
				evt.preventDefault();
				//$.get(cr+'?url='+encodeURIComponent(this.href));
				var label = this.getAttribute('dt-labelCla')? '&tid='+this.getAttribute('dt-labelCla') : '';
				location.href = cr+'?url='+encodeURIComponent(this.href)+label;
			});
		}
	});
})(ICAT, Zepto);