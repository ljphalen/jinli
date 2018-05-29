var is_authcode = false;		//是否验证验证码
var mobile_send = false;		//验证码是否获取成功
var infoEdit = {
	infoForm : function() {
		$.formValidator.initConfig({
			theme : "126",
			errorFocus : false,
			formID : "editform"
		});
		$("#nickname").formValidator({
			onShowFixText : "请填写昵称",
			onShow : " ",
			onFocus : " ",
			onCorrect : " "
		}).inputValidator({
			min : 1,
			max : 60,
			onError : "昵称不正确，请确认"
		});
		$("#campanyName").formValidator({
			onShowFixText : "请输入公司名称",
			onShow : "请输入公司名称",
			onFocus : "公司名称不能为空",
			onCorrect : " "
		}).inputValidator({
			min : 1,
			onError : "公司名称不正确，请确认"
		});
		$("#passportNum").formValidator({
			onShowFixText : "执照注册号",
			onShow : "请输入执照注册号",
			onFocus : "执照注册号不能为空",
			onCorrect : " "
		}).inputValidator({
			min : 1,
			onError : "执照注册号不正确，请确认"
		}).regexValidator({
			regExp : "^([\\w]+)$",
			onError : "只允许字母和数字"
		});
	/*	$("#companyPassport").formValidator({
			onShowFixText : "请上传营业执照注册号",
			onShow : " ",
			onFocus : " ",
			onCorrect : " "
		}).functionValidator({
			fun : function(val, elem) {
				if (val == "") {
					return "请上传营业执照注册号";
				} else {
					return true;
				}
			}
		});*/
		$("#contact").formValidator({
			onShowFixText : "请填写真实姓名，请勿填写昵称",
			onShow : "请填写真实姓名",
			onFocus : "请填写真实姓名",
			onCorrect : " "
		}).inputValidator({
			min : 1,
			max : 20,
			onError : "您输入的真实姓名不正确，请确认"
		});
		$("#phone").formValidator({
			onShowFixText : "请输入常用联系手机，方便及时联系",
			onShow : "请输入手机号码",
			onFocus : "手机的长度必须是11位",
			onCorrect : " "
		}).inputValidator({
			min : 11,
			max : 11,
			onError : "手机号码必须为11位，请确认"
		}).regexValidator({
			regExp : "mobile",
			dataType : "enum",
			onError : "手机的格式不正确，请确认"
		}).functionValidator({
			fun : function(val, elem) {
				if(val != phone_old)
				{
					$("#authcode_div").css('display','block');
					is_authcode = true;
				}else
				{
					$("#authcode_div").css('display','none');
					is_authcode = false;				
				}	
			}
		});
		$("#authcode").formValidator({
			empty: is_authcode,
			onShowFixText : "请填写验证码",
			onShow : " ",
			onFocus : " ",
			onCorrect : " "
		}).inputValidator({
			min : 0,
			max : 6,
			onError : "验证码不正确"
		}).functionValidator({
			fun : function(val, elem) {
				if (is_authcode && mobile_send == false) {
					return "请获取手机验证码";
				} else {
					return true;
				}
			}
		});
		$("#contactEmail").formValidator({
			onShowFixText : "6~50个字符，包括字母、数字、下划线，以字母开头，字母或数字结尾",
			onShow : "请输入邮箱",
			onFocus : "邮箱6-50个字符，输入正确了才能离开焦点",
			onCorrect : " "
		}).inputValidator({
			min : 6,
			max : 50,
			onError : "您输入的邮箱长度不正确，请确认"
		}).regexValidator({
					regExp : "^([\\w-.]+)@(([[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.)|(([\\w-]+.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(]?)$",
					onError : "您输入的邮箱格式不正确，请确认"
		});
	}
};
jQuery(function($) {
	//短信验证码发送
	$("#fetchAuthcode").click(function(e){
//		if(mobile_send) 
//		{
//			alert('短信已发送，请耐心等待');
//			return false;
//		}
		var mobile = $("#phone").val();
		if(mobile == '' || mobile == undefined )
		{
			alert('手机号有误');
			return false;
		}
		$.ajax({
		//	dataType : "json",
			type:'post',
		//	async : true,
			url : "/Mobilecode/index",
			data:'mobile='+mobile+'&module=2',
			success : function(res) {
				if(res.status == 1)
				{
					//alert(res.info);
					mobile_send = true;
					fun_timedown(60);
					//$(this).unbind(e);
				}else{
					alert(res.info)
				}	
			}
				
			});
	});
	infoEdit.infoForm();
	
});
function fun_timedown(time)
{
    if(time=='undefined')
        time = 30;
    $("#fetchAuthcode").html(time+"秒");
    time = time-1;
    if(time>=0)
    {
    	$("#fetchAuthcode").attr("disabled",true);
        setTimeout("fun_timedown("+time+")",1000);
    }else
    {
    	$("#fetchAuthcode").html('获取验证码');
    	$("#fetchAuthcode").attr("disabled",false);
    	
    }

}
