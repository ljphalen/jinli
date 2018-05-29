var gioneeForget = {
		verifyForm : function() {
			$.formValidator.initConfig({
				theme : "126",
			errorFocus : false,
			formID : "regform"
		});
		$("#email")
				.formValidator({
					onShowFixText : "6~50个字符，包括字母、数字、下划线，以字母开头，字母或数字结尾",
					onCorrect : " "
				})
				.inputValidator({
					min : 6,
					max : 50,
					onError : "你输入的邮箱长度非法，请确认"
				})
				.regexValidator(
						{
							regExp : "^([\\w-.]+)@(([[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.)|(([\\w-]+.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(]?)$",
							onError : "你输入的邮箱格式不正确"
						});
		$("#authcode").formValidator({
			onShow : "输入验证码",
			onFocus : "输入验证码",
			onCorrect : " "
		}).inputValidator({
			min : 1,
			empty : {
				leftEmpty : false,
				rightEmpty : false,
				emptyError : "请输入正确的验证码"
			},
			onError : "验证码不能为空"
		}).ajaxValidator({
			dataType : "json",
			async : true,
			url : "/Auth/authcode/ajax/1",
			success : function(data) {
				if (data.status == "0"){
					eoe_forget.fleshVerify();
					return false;
				}else{
					return true;
				}
			},
			buttons : $("#submit"),
			error : function(jqXHR, textStatus, errorThrown) {
				alert("服务器没有返回数据，可能服务器忙，请重试" + errorThrown);
			},
			onError : "验证码不正确",
			onWait : " "
		});
	},
	fleshVerify : function(type) {
		//重载验证码
		var timenow = new Date().getTime();
		var url = "/Auth/verify/" + (type ? "adv/1/" : "") + timenow;
		$('#verifyImg').attr("src", url);
	}
}
jQuery(function($) {
	gioneeForget.verifyForm();
});