var gioneeReg = {
	verifyForm : function() {
		$.formValidator.initConfig({
			theme : "126",
			submitOnce : true,
			errorFocus : false,
			submitAfterAjaxPrompt : '有数据正在异步验证，请稍等...',
			formID : "regform"
		});
		$("#email").formValidator({
			onShowFixText : "6~50个字符，包括字母、数字、下划线，以字母开头，字母或数字结尾",
			onShow : "请输入邮箱",
			onFocus : "邮箱6-50个字符，输入正确了才能离开焦点",
			onCorrect : "此邮箱可以使用",
			triggerEvent : "blur"
		})
		.inputValidator({
			min : 6,
			max : 50,
			onError : "您输入的邮箱长度不正确，请确认"
		})
		.regexValidator({
					regExp : "^([\\w-.]+)@(([[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.)|(([\\w-]+.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(]?)$",
					onError : "您输入的邮箱格式不正确，请确认"
		})
		.ajaxValidator({	
			dataType : "json",
			async : true,
			url : "/Login/checkEmail/ajax/1",
			success : function(data) {
				if (data.status == "1") {
					return true;
				}else{
					//$('#emailTip').html('email不可用');
					return false;
				}
			},
			buttons : $("#submit,#subVerify"),
			error : function(jqXHR, textStatus, errorThrown) {
				
				alert("服务器没有返回数据，可能服务器忙，请重试" + errorThrown);
			},
			onError : "该email不可用，请更换email",
			onWait : "正在进行合法性校验，请稍候..."
		});
		$("#pwd").formValidator({
			onShowFixText : "6~16个字符，包括字母、数字、特殊符号，区分大小写",
			onShow : "请输入密码",
			onFocus : "至少1个长度",
			onCorrect : " "
		}).inputValidator({
			min : 6,
			max : 16,
			empty : {
				leftEmpty : false,
				rightEmpty : false,
				emptyError : "密码两边不能有空符号，请确认"
			},
			onError : "密码长度错误，请确认"
		}).passwordValidator({
			compareID : "login"
		});
		$("#repwd").formValidator({
			onShowFixText : "请再次输入密码",
			onShow : "输再次输入密码",
			onFocus : "至少1个长度",
			onCorrect : " "
		}).inputValidator({
			min : 1,
			empty : {
				leftEmpty : false,
				rightEmpty : false,
				emptyError : "重复密码两边不能有空符号，请确认"
			},
			onError : "重复密码不能为空，请确认"
		}).compareValidator({
			desID : "pwd",
			operateor : "=",
			onError : "2次密码不一致，请确认"
		});
	},
	fleshVerify : function(type) {
		//重载验证码
		var timenow = new Date().getTime();
		var url = "/Auth/verify/" + (type ? "adv/1/" : "") + timenow;
		$('#verifyImg').attr("src", url);
	}
};
jQuery(function($) {
	gioneeReg.verifyForm();
});