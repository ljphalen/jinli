(function() {
	var enumMsg = {
		phoneAndPwdNull: '请输入账号、密码',
		pwdNull: '请输入密码',
		pwdNotEqual: '两次输入的密码不一致',
		pwdIllegal: '密码为4-16位的字母或数字',
		pwdError: '密码错误',
		phoneNull: '请输入手机号码',
		phoneIllegal: '手机号码有误',
		verifyFail: '验证失败',
		codeNull: '请输入验证码',
		codeError: '验证码错误',
		codeSendSuccess:'验证码发送成功',
		loginSuccess:'登录成功',
		registerSuccess:'注册成功'
	};
	var enumStatusCode={
		1011:'账号或密码错误',
		1101:'验证码错误',
		1100:'账号已存在',
		unknow:'网络异常，请检查后重试',
		timeout:'请求超时'
	}
	var enumReg = {
		phone: /^1[34578]{1}[0-9]{9}$/,
		pwd: /^[\u0021-\u007E]{4,16}$/
	};
	var wait = 60; //等待时间
	var action = {
		//登录按钮逻辑
		login: function() {
			$(".J_login").click(function() {
				var phone = $("#phone").val(),
					pwd = $("#password").val(),
					hasCode = !$(".J_code").hasClass('hidden'),
					code = hasCode ? $("#code").val() : '';
				//判断手机或密码是否为空
				if (phone == '' || pwd == '') {
					action.showMsg(enumMsg.phoneAndPwdNull);
					return false;
				}
				
				//判断验证码是否为空
				if (hasCode && code == '') {
					action.showMsg(enumMsg.codeNull);
					return false;
				}
				//手机是否合法
				if (!enumReg.phone.test(phone)) {
					action.showMsg(enumMsg.phoneIllegal);
					return false;
				}
				//密码是否符合基本格式
				if (pwd.length < 4) {
					action.showMsg(enumMsg.pwdError);
					return false;
				}
				//验证码的基本格式
				if (hasCode && code.length < 4) {
					action.showMsg(enumMsg.codeError);
					return false;
				}
				$(".J_tips").addClass('invisible');
				$(".J_mask").removeClass('hidden');
				$.ajax({
					url:$(this).attr('data-ajaxurl'),
					dataType:'json',
					timeout:10000,
					data:{
						tn:phone,
						p:pwd,
						vid:$("#code").attr('data-vid'),
						vtx:code,
						vty:$("#code").attr('data-vty')
					},
					success:function(d){
						$(".J_mask").addClass('hidden');//去除遮罩
						var data=d.data;
						if((data.vmt&&data.vmt==1)){//显示验证码并自动刷新
							$(".J_code").removeClass('hidden');
							$("#code").attr('data-vty',data.vty[0]);
							$(".J_code img").trigger("click");
						}
						if(data.r){//登录失败,处理出错信息
							if(data.r=='1101'||hasCode){//验证码错误或者其它错误都需重新刷新验证码
								$(".J_code").removeClass('hidden');
								$(".J_code img").trigger("click");
							}
							if(enumStatusCode[data.r]){
								action.showMsg(enumStatusCode[data.r]);
							} else{
								action.showMsg(enumStatusCode.unknow);
							}
							
							return false;
						} else{//登录成功，跳转至活动页
							action.showTip(enumMsg.loginSuccess);
							// setTimeout(function(){
								location.href=redirect;
							// },2000);
							
						}
					},
					error:function(xhr,textStatus){
						$(".J_mask").addClass('hidden');
						if(textStatus=='timeout'){
							action.showMsg(enumStatusCode.timeout);
						} else{
							action.showMsg(enumStatusCode.unknow);
						}
					}
				});
				
			});
		},
	
		//获取图形验证码或者短信验证码
		getVerCode: function() {
			//登录页图形验证码刷新
			$(".J_loading img").click(function(event) {
				$("#code").val('');
				$(this).addClass('hidden');
				$(this).siblings('i').removeClass('hidden');
				$.ajax({
					url:$(this).attr('data-ajaxurl'),
					dataType:'json',
					timeout:10000,
					success:function(d){
						var d=d.data;
						var img=$(".J_loading img");
						var src='data:image/png;base64,'+d.vda;
						img.attr('src',src);
						$("#code").attr('data-vid',d.vid);
						img.siblings('i').addClass('hidden');
						img.removeClass('hidden');
					},
					error:function(xhr,textStatus){
						var img=$(".J_loading img");
						img.siblings('i').addClass('hidden');
						img.removeClass('hidden');
						if(textStatus=='timeout'){
							action.showMsg(enumStatusCode.timeout);
						} else{
							action.showMsg(enumStatusCode.unknow);
						}
						return false;
					}
				})
			});

			//注册页面发送短信验证码
			$(".J_getCode").click(function(event) {
				var phone = $("#phone").val();
				action.countDown();
				$.ajax({
					url:$(".J_getCode").attr('data-ajaxurl'),
					dataType:'json',
					timeout:10000,
					data:{
						tn:phone,
						s:$(".J_getCode").attr('data-s')
					},
					success:function(d){
						var d=d.data;
						if(d.r){//短信验证码发送失败
							if(enumStatusCode[d.r]){
								action.showMsg(enumStatusCode[d.r]);
							} else{
								action.showMsg(enumStatusCode.unknow);
							}
						} else{ //发送成功
							action.showTip(enumMsg.codeSendSuccess);
						}
						
					},
					error:function(xhr,textStatus){
						if(textStatus=='timeout'){
							action.showMsg(enumStatusCode.timeout);
						} else{
							action.showMsg(enumStatusCode.unknow);
						}
					}
				})
			});

			//自动加载图形验证码
			if(!$(".J_step1").hasClass('hidden')){
				$(".J_step1 .J_loading img").trigger('click');
			}
		},

		//注册
		register: function() {
			//step1 图形验证
			$(".J_step1 .J_next").click(function(){
				var phone = $("#phone").val(),
					code = $("#code").val();
				if (phone == '') {
					action.showMsg(enumMsg.phoneNull);
					return false;
				}
				if (code == '') {
					action.showMsg(enumMsg.codeNull);
					return false;
				}
				if (!enumReg.phone.test(phone)) {
					action.showMsg(enumMsg.phoneIllegal);
					return false;
				}
				if (code.length < 4) {
					action.showMsg(enumMsg.codeError);
					return false;
				}
				$(".J_tips").addClass('invisible');
				$(".J_mask").removeClass('hidden');
				$.ajax({
					url:$(this).attr('data-ajaxurl'),
					dataType:'json',
					timeout:10000,
					data:{
						tn:phone,
						vid:$("#code").attr('data-vid'),
						vtx:code,
						vty:$("#code").attr('data-vty')
					},
					success:function(d){
						var d=d.data;
						$(".J_mask").addClass('hidden');
						if(d.s){//图形验证成功
							var step2=$(".J_step2");
							$(".J_step1").addClass('hidden');
							step2.removeClass('hidden');
							step2.find('.J_getCode').attr('data-s',d.s);
							step2.find('#phone').val(phone);
							step2.find('.J_getCode').trigger('click');//手动触发短信验证码的下发
						}
						if(d.r){//图形验证失败
							if(d.r=='1101'){
								$(".J_step1 .J_code img").trigger('click');
							}
							if(enumStatusCode[d.r]){
								action.showMsg(enumStatusCode[d.r]);
							} else{
								action.showMsg(enumStatusCode.unknow);
							}
							return false;
						}
					},
					error:function(xhr,textStatus){
						$(".J_mask").addClass('hidden');
						if(textStatus=='timeout'){
							action.showMsg(enumStatusCode.timeout);
						} else{
							action.showMsg(enumStatusCode.unknow);
						}
					}
				})
			})
			
			//step2 短信验证
			$(".J_step2 .J_next").click(function(){
				var step2=$(".J_step2"),
					phone=step2.find('#phone').val(),
					code=step2.find("#code").val();
				if(code==''){
					action.showMsg(enumMsg.codeNull);
					return false;
				}
				if(code.length<6){
					action.showMsg(enumMsg.codeError);
					return false
				}
				$(".J_tips").addClass('invisible');
				$(".J_mask").removeClass('hidden');
				$.ajax({
					url:$(this).attr('data-ajaxurl'),
					dataType:'json',
					timeout:10000,
					data:{
						tn:phone,
						sc:code,
						s:step2.find(".J_getCode").attr('data-s')
					},
					success:function(d){
						var d=d.data;
						$(".J_mask").addClass('hidden');
						if(d.s){//短信验证成功
							var step3=$(".J_step3");
							$(".J_step2").addClass('hidden');
							step3.removeClass('hidden');
							step3.find('.J_complete').attr('data-s',d.s);
						}
						if(d.r){//图形验证失败
							if(enumStatusCode[d.r]){
								action.showMsg(enumStatusCode[d.r]);
							} else{
								action.showMsg(enumStatusCode.unknow);
							}
							return false;
						}
					},
					error:function(xhr,textStatus){
						$(".J_mask").addClass('hidden');
						if(textStatus=='timeout'){
							action.showMsg(enumStatusCode.timeout);
						} else{
							action.showMsg(enumStatusCode.unknow);
						}
					}
				})
			})
			
			//step3 设置密码，完成注册
			$(".J_step3 .J_complete").click(function(){
				var pwd = $("#password").val(),
					rePwd = $("#repassword").val();
				//先判空，再判是否一致，再判是否符合格式
				if (pwd == '' || rePwd == '') {
					action.showMsg(enumMsg.pwdNull);
					return false;
				}
				if(pwd!=rePwd){
					action.showMsg(enumMsg.pwdNotEqual);
					return false;
				}
				if(!enumReg.pwd.test(pwd)){
					action.showMsg(enumMsg.pwdIllegal);
					return false;
				}
				$(".J_tips").addClass('invisible');
				$(".J_mask").removeClass('hidden');
				$.ajax({
					url:$(this).attr('data-ajaxurl'),
					dataType:'json',
					timeout:10000,
					data:{
						s:$(this).attr('data-s'),
						p:pwd
					},
					success:function(d){
						$(".J_mask").addClass('hidden');
						var data=d.data;
						if(d.r){ //注册失败
							if(enumStatusCode[d.r]){
								action.showMsg(enumStatusCode[d.r]);
							} else{
								action.showMsg(enumStatusCode.unknow);
							}
							return false;
						}else{ //注册成功
							action.showTip(enumMsg.registerSuccess);
							// setTimeout(function(){
								location.href=redirect;
							// },2000);
						}
					},
					error:function(xhr,textStatus){
						$(".J_mask").addClass('hidden');
						if(textStatus=='timeout'){
							action.showMsg(enumStatusCode.timeout);
						} else{
							action.showMsg(enumStatusCode.unknow);
						}
					}
				})
			})
		},

		//验证码倒计时
		countDown: function() {
			var ele = $(".J_getCode");
			if (wait == 0) {
				ele.removeAttr('disabled').removeClass('disabled').html('获取验证码');
				wait = 60;
			} else {
				ele.attr('disabled', 'disabled').addClass('disabled').html('重新发送' + wait);
				wait--;
				setTimeout(function() {
					action.countDown();
				}, 1000);
			}
		},

		showMsg: function(msg) {
			if (msg != '') {
				$(".J_tips").removeClass('invisible').html(msg);
			} else {
				$(".J_tips").addClass('invisible').html(msg);
			}
		},
		//显示弱提示
		showTip :function(msg){
			var tip = $('.J_tipBox'), dw = $(document).width(), bw = tip.width();
			tip.find('p').html(msg);
			var len=msg.length*6;
			tip.css('left', ((dw-bw)/2-len)+'px').removeClass('hidden');
			
			setTimeout(function(){
				tip.addClass('hidden');
				tip.find('p').html('');
			},1000);
		},
		init: function() {
			this.login(); //登录逻辑
			this.register();//注册逻辑
			this.getVerCode(); //获取验证码
		}
	};
	$(function() {
		action.init(); //初始化，事件绑定等
	})
})();