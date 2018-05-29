var pwdUpdate = {
	editForm : function() {
		$.formValidator.initConfig({
			theme : "126",
			errorFocus : false,
			formID : "editForm"
		});
		$("#oldpassword").formValidator({
			onCorrect : " "
		}).inputValidator({
			min : 6,
			max : 16,
			empty : {
				leftEmpty : false,
				rightEmpty : false,
				emptyError : "密码两边不能有空符号"
			},
			onError : "密码长度错误,请确认"
		}).ajaxValidator({
			dataType : "json",
			async : true,
			url : "/user/checkOldpwd/ajax/1",
			success : function(data) {
				if (data.info == "200")
					return true;
				else
					return false;
			},
			buttons : $("#save"),
//			error : function(jqXHR, textStatus, errorThrown) {
//				alert("服务器没有返回数据，可能服务器忙，请重试" + errorThrown);
//			},
			onError : "旧密码不正确",
//			onWait : "正在进行合法性校验，请稍候..."
		});
		$("#password").formValidator({
			onCorrect : " "
		}).inputValidator({
			min : 6,
			max : 16,
			empty : {
				leftEmpty : false,
				rightEmpty : false,
				emptyError : "密码两边不能有空符号"
			},
			onError : "密码长度错误,请确认"
		}).passwordValidator({
			//compareID : "oldpassword"
		});
		$("#repassword").formValidator({
			onCorrect : " "
		}).inputValidator({
			min : 1,
			empty : {
				leftEmpty : false,
				rightEmpty : false,
				emptyError : "重复密码两边不能有空符号"
			},
			onError : "重复密码不能为空,请确认"
		}).compareValidator({
			desID : "password",
			operateor : "=",
			onError : "2次密码不一致,请确认"
		});
	}
};
jQuery(function($) {
	pwdUpdate.editForm();
});