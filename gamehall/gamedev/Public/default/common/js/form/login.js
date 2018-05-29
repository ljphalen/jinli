var firstPage = "/Index/index/";
var submiting = false;
var gioneeLogin = {
	verifyForm : function() {
		$.formValidator.initConfig({
			theme : "Default",
			formID : "loginForm",
			errorFocus : false,
			onSuccess : function(){gioneeLogin.formSubmit();}
		});
		$("#email").formValidator({
			tipID : "errorMsg",
			onShowFixText : "",
			onShow : "",
			onFocus : "",
			onCorrect : " "
		})
		.inputValidator({
			min : 6,
			max : 50,
			onError : "请输入邮箱"
		})
		.regexValidator({
					regExp : "^([\\w-.]+)@(([[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.)|(([\\w-]+.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(]?)$",
					onError : "您输入的邮箱格式不正确"
		});
		$("#password").formValidator({
			tipID : "errorMsg",
			onShowFixText : "",
			onShow : "",
			onFocus : "",
			onCorrect : " "
		}).inputValidator({
			min : 6,
			max : 16,
			empty : {
				leftEmpty : false,
				rightEmpty : false,
				emptyError : "密码两边不能有空符号"
			},
			onError : "请输入正确的密码"
		});
		
		$("#authcode").formValidator({
			tipID : "errorMsg",
			onShow : "",
			onFocus : "",
			onCorrect : " "
		}).inputValidator({
			min : 1,
			empty : {
				leftEmpty : false,
				rightEmpty : false,
				emptyError : "请输入正确的验证码"
			},
			onError : "请输入正确的验证码"
		});
	},
	formSubmit : function() {
	    if (! submiting) {
	    	submiting = true;
	        var loginUrl = '/Login/loginsub';
			
			var authcode = $("#authcode").val();
            var data = $("#loginForm").serialize();
            var key_hash = CryptoJS.MD5(authcode);
            var key = CryptoJS.enc.Utf8.parse(key_hash);
            var iv = CryptoJS.enc.Utf8.parse(key_hash.toString().substr(1 ^ 1, 16));
            var encrypted = CryptoJS.AES.encrypt(data.length + '|' + data, key, {
                iv: iv,
                mode: CryptoJS.mode.CBC,
                padding: CryptoJS.pad.ZeroPadding
            });
            var formdata = "encrypted=" + encrypted + "&authcode=" + authcode;
			
	        $.ajax({
	            type: "POST",
	            url: loginUrl + "/r/" + Math.random(),
	            contentType: "application/x-www-form-urlencoded; charset=utf-8",
	            data: formdata,
	            dataType: 'json',
	            success: function(result) {
	                if (result.status) {
	                    if(result.info!="登录成功！")
	                    	alert(result.info);
	                    location.href = result.jumpUrl;
	                }else{
	                	if(result.info=="noActived"){
	                		location.href = result.data.jumpUrl;
	                	}else if(result.info=="perfect"){
		                    location.href = result.data.jumpUrl;
	                	}else{
	                		$("#errorMsg").html('<div class="onFocus">'+result.info+'</div>');
	                		$(".active_email").html('');
	                	}
	                	gioneeLogin.fleshVerify();
	                	submiting = false;
	                }
	            }
	        });
	    }
	},
	fleshVerify : function(type) {
		//重载验证码
		var timenow = new Date().getTime();
		var url = "/Auth/verify/" + (type ? "adv/1/" : "") + timenow;
		$('#verifyImg').attr("src", url);
	}
};
jQuery(function($) {
	gioneeLogin.verifyForm();
	$("#email,#password,#authcode").bind('keyup', function(event) {
	    if (event.keyCode == 13) {
	        $("#Sub").click();
	    }
	});
});
