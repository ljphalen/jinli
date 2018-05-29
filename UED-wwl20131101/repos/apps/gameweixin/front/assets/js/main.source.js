$(function() {
	FastClick.attach(document.body);
	login();
	getVerCode();
	gift();
	loadMore();
	weixinConfig();
});
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
	codeSendSuccess: '验证码发送成功',
	loginSuccess: '登录成功',
	registerSuccess: '注册成功'
};
var enumStatusCode = {
	1011: '账号或密码错误',
	1101: '验证码错误',
	1100: '账号已存在',
	unknow: '网络异常，请检查后重试',
	timeout: '请求超时'
}
var enumReg = {
	phone: /^1[34578]{1}[0-9]{9}$/,
	pwd: /^[\u0021-\u007E]{4,16}$/
};

function weixinConfig(){
	wx.config({
	    debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来
	    appId: '', // 必填，公众号的唯一标识
	    timestamp: '', // 必填，生成签名的时间戳
	    nonceStr: '', // 必填，生成签名的随机串
	    signature: '',// 必填，签名，见附录1
	    jsApiList: ['checkJsApi','closeWindow'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
	});
	
}

function countDown(){
	var t=2;
	setInterval(close,1000);
	function close(){
		$("#seconds").html(t);
		if(t==0){
			wx.closeWindow();
			return;
		}
		t--;
	}
}

//登录按钮逻辑
function login() {
	$(".J_login").click(function() {
		var phone = $("#phone").val(),
			pwd = $("#password").val(),
			hasCode = !$(".J_code").hasClass('hidden'),
			code = hasCode ? $("#code").val() : '';
		//判断手机或密码是否为空
		if (phone == '' || pwd == '') {
			showMsg(enumMsg.phoneAndPwdNull);
			return false;
		}

		//判断验证码是否为空
		if (hasCode && code == '') {
			showMsg(enumMsg.codeNull);
			return false;
		}
		//手机是否合法
		if (!enumReg.phone.test(phone)) {
			showMsg(enumMsg.phoneIllegal);
			return false;
		}
		//密码是否符合基本格式
		if (pwd.length < 4) {
			showMsg(enumMsg.pwdError);
			return false;
		}
		//验证码的基本格式
		if (hasCode && code.length < 4) {
			showMsg(enumMsg.codeError);
			return false;
		}
		$(".J_tips").addClass('invisible');
		$(".J_mask").removeClass('hidden');
		$.ajax({
			url: $(this).attr('data-ajaxurl'),
			dataType: 'json',
			timeout: 10000,
			data: {
				tn: phone,
				p: pwd,
				token:token,
				vid: $("#code").attr('data-vid'),
				vtx: code,
				vty: $("#code").attr('data-vty')
			},
			success: function(d) {
				$(".J_mask").addClass('hidden'); //去除遮罩
				var data = d.data;
				if ((data.vmt && data.vmt == 1)) { //显示验证码并自动刷新
					$(".J_code").removeClass('hidden');
					$("#code").attr('data-vty', data.vty[0]);
					$(".J_code img").trigger("click");
				}
				if (data.r) { //绑定失败,处理出错信息
					if (data.r == '1101' || hasCode) { //验证码错误或者其它错误都需重新刷新验证码
						$(".J_code").removeClass('hidden');
						$(".J_code img").trigger("click");
					}
					if (enumStatusCode[data.r]) {
						showMsg(enumStatusCode[data.r]);
					} else {
						bindFail();
					}

					return false;
				} else {
					//绑定成功，跳转至其它页
					bindSuccess(data.redirectUrl);
				}
			},
			error: function(xhr, textStatus) {
				$(".J_mask").addClass('hidden');
				if (textStatus == 'timeout') {
					showMsg(enumStatusCode.timeout);
				} else {
					showMsg(enumStatusCode.unknow);
				}
			}
		});
	});
	if($(".J_bindOK").length&&!$(".J_bindOK").hasClass('hidden')){
		countDown();
	}
}



function bindFail() {
	$(".J_loginContainer").addClass('hidden');
	$(".J_code").addClass('hidden');
	$("#code").val('');
	$(".J_bindOK").addClass('hidden');
	$(".J_bindError").removeClass('hidden');
	
}

$(".J_rebind").bind('click', function(event) {
	$(".J_bindError").addClass('hidden');
	$(".J_loginContainer").removeClass('hidden');
});

function bindSuccess(url) {
	$(".J_loginContainer").addClass('hidden');
	$(".J_bindError").addClass('hidden');
	$(".J_bindOK").removeClass('hidden');

	countDown();
}

//获取图形验证码或者短信验证码
function getVerCode() {
	//登录页图形验证码刷新
	$(".J_loading img").click(function(event) {
		$("#code").val('');
		$(this).addClass('hidden');
		$(this).siblings('i').removeClass('hidden');
		$.ajax({
			url: $(this).attr('data-ajaxurl'),
			dataType: 'json',
			timeout: 10000,
			success: function(d) {
				var d = d.data;
				var img = $(".J_loading img");
				var src = 'data:image/png;base64,' + d.vda;
				img.attr('src', src);
				$("#code").attr('data-vid', d.vid);
				img.siblings('i').addClass('hidden');
				img.removeClass('hidden');
			},
			error: function(xhr, textStatus) {
				var img = $(".J_loading img");
				img.siblings('i').addClass('hidden');
				img.removeClass('hidden');
				if (textStatus == 'timeout') {
					showMsg(enumStatusCode.timeout);
				} else {
					showMsg(enumStatusCode.unknow);
				}
				return false;
			}
		})
	});
}

function showMsg(msg) {
	if (msg != '') {
		$(".J_tips").removeClass('invisible').html(msg);
	} else {
		$(".J_tips").addClass('invisible').html(msg);
	}
}
	//显示弱提示
function showTip(msg) {
	var tip = $('.J_tipBox'),
		dw = $(document).width(),
		bw = tip.width();
	tip.find('p').html(msg);
	var len = msg.length * 6;
	tip.css('left', ((dw - bw) / 2 - len) + 'px').removeClass('hidden');

	setTimeout(function() {
		tip.addClass('hidden');
		tip.find('p').html('');
	}, 1000);
}

showLog=true;
isWin = $('body').attr('data-isWin') === 'true';
function gift() {
	if (!$("#scratcher").length) return;
	if (!supportsCanvas()) {
		return;
	}
	
	var canvasId="scratcher",
		picPath=getPicPath(),
		topBg=picPath+'/stratch-front.png',
		bottomBg=isWin?picPath+'/stratch-win.png':picPath+'/stratch-lose.png';
		scratcher=new Scratcher(canvasId,bottomBg,topBg);
		
		// called each time a scratcher loads
		function onScratcherLoaded(ev) {
			//图片加载完了才能开始刮奖
			scratcher.reset();
			scratcher.addEventListener('scratch', scratcherChanged);
		};

		scratcher.addEventListener('imagesloaded', onScratcherLoaded);
}

function scratcherChanged(ev) {
		var pct = (this.fullAmount(32) * 100)|0;

		//上报日志
		if(pct>=20){
			if(showLog==true){
				var ajaxUrl=$("#Scratch_Holder").attr('data-ajaxUrl');
				$.post(ajaxUrl,{
					token:token,
					giftId:$("body").attr('data-giftId'),
					code:$("#codeName").html()
					},function(data){
					}
				);
				showLog=false;
				if(isWin){
					$(".J_codeContainer").removeClass('hidden');// 提示用户是否有中奖
				}
			}
		}
	};

function supportsCanvas() {
	return !!document.createElement('canvas').getContext;
};

//获取图片文件夹路径
function getPicPath() {
	var mainJs = $('script[data-main]')[0],
		src = mainJs.hasAttribute ? mainJs.src : mainJs.getAttribute('src', 4);
	var picPath = src.replace(/assets\/js\/.*/g, 'pic');
	return picPath;
}

function getUrlParam(name) {
	var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
	var r = window.location.search.substr(1).match(reg); //匹配目标参数
	if (r != null) return unescape(r[2]);
	return null; //返回参数值
}

//我的礼包 翻页
function loadMore(){
	var btnMore = $('.J_loadMore[data-ajaxUrl]');
	if(!btnMore[0]) return;
	
	btnMore.on('click', function(){
		var hn = btnMore.attr('data-hasnext');
		if(hn==0 || hn=='false') return false;
		
		var curpage = parseInt(btnMore.attr('data-curpage'));
		btnMore.children('.J_load').addClass('hidden');
		btnMore.children('.J_loading').removeClass('hidden');

		var ajaxUrl=btnMore.attr('data-ajaxurl'),
			data={
				page:curpage+1,
				token:token,
				sign:getUrlParam('sign')
			}

		$.post(ajaxUrl, data, function(data){
			var pNode = $('.J_mygiftList ul'),html='';
			console.log(data);
			var list=data.data.list;
			for(var i=0,len=list.length;i<len;i++){
				html+='<li>\
							<a href="'+list[i]['url']+'">\
								<span class="name">'+list[i]['name']+'</span>\
								<span class="date">有效期：'+list[i]['startDate']+'至'+list[i]['endDate']+'</span>\
								<div class="code">兑换码：<span>'+list[i]['code']+'</span></div>\
							</a>\
						</li>';
			}
			pNode.append(html);
			if(data.data.hasnext==0 || data.data.hasnext=='false'){
				btnMore.hide();
			}
			
			btnMore.attr('data-hasnext', data.data.hasnext)
				.attr('data-curpage', data.data.curpage);
			btnMore.children('.J_load').removeClass('hidden');
			btnMore.children('.J_loading').addClass('hidden');
		}, 'json');
	});
}