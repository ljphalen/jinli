var mobile_send = false;		//验证码是否获取成功
var gioneePerfect = {
	verifyForm : function() {
		$.formValidator.initConfig({
			theme : "126",
			submitOnce : true,
			errorFocus : false,
			submitAfterAjaxPrompt : '有数据正在异步验证，请稍等...',
			formID : "perfectForm"
		});
		$("#nickname").formValidator({
			onShowFixText : "请填写昵称",
			onShow : " ",
			onFocus : "" ,
			onCorrect : " "
		}).inputValidator({
			min : 1,
			max : 100,
			onError : "昵称不正确，请确认"
		});
		$("#companyName").formValidator({
			onShowFixText : "请输入公司名称",
			onShow : "请输入公司名称",
			onFocus : "公司名称不能为空",
			onCorrect : " "
		}).inputValidator({
			min : 1,
			max : 100,
			onError : "公司名称不正确，请确认"
		});
		$("#passportNum").formValidator({
			onShowFixText : "执照注册号",
			onShow : "请输入执照注册号",
			onFocus : "执照注册号不能为空",
			onCorrect : " "
		}).inputValidator({
			min : 13,
			max : 15,
			onError : "执照注册号不正确，请确认"
		}).regexValidator({
			regExp : "^([\\w]+)$",
			onError : "只允许字母和数字"
		});
		$("#companyPassport").formValidator({
			onShowFixText : "请上传营业执照扫描件",
			triggerEvent:"change",
			onShow : " ",
			onFocus : " ",
			onCorrect : " "
		}).functionValidator({
			fun : function(val, elem) {
				if (val == "") {
					return "请上传营业执照扫描件";
				} else {
					return true;
				}
			}
		});
		$("#contact").formValidator({
			onShowFixText : "请填写真实姓名，请勿填写昵称",
			onShow : "请填写真实姓名",
			onFocus : "请填写真实姓名",
			onCorrect : " "
		}).inputValidator({
			min : 1,
			max : 20,
			onError : "真实姓名不正确，请确认"
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
			onError : "手机号码格式不正确"
		});
		$("#authcode").formValidator({
			onShowFixText : "请填写验证码",
			onShow : " ",
			onFocus : " ",
			onCorrect : ""
		}).inputValidator({
			min : 1,
			max : 6,
			onError : "验证码不正确"
		}).functionValidator({
			fun : function(val, elem) {
				if (mobile_send == false) {
					return "请获取手机验证码";
				} else {
					return true;
				}
			}
		});
		$("#address").formValidator({
			onShowFixText : "请输入联系地址",
			onShow : "请输入地址",
			onFocus : "地址不能为空",
			onCorrect : " "
		}).inputValidator({
			min : 1,
			onError : "地址不正确，请确认"
		});
		$("#contactEmail").formValidator({
			onShowFixText : "6~50个字符，包括字母、数字、下划线，以字母开头，字母或数字结尾",
			onShow : "请输入邮箱",
			onFocus : "邮箱6-50个字符，输入正确了才能离开焦点",
			onCorrect : " "
		}).inputValidator({
			min : 6,
			max : 50,
			onError : "邮箱长度不正确，请确认"
		}).regexValidator({
					regExp : "^([\\w-.]+)@(([[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.)|(([\\w-]+.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(]?)$",
					onError : "邮箱格式不正确，请确认"
		});
		$("#taxNumber").formValidator({}).inputValidator({
			min : 0,
			max : 18,
			onError : "税务号长度不正确(限18个字符)"
		}).regexValidator({
			regExp : "^([\\w]+)$",
			onError : "只允许字母和数字"
		});
		$("#taxPassport").formValidator({
			onShowFixText : "请上传税务登记证扫描件",
			triggerEvent:"change",
			onShow : " ",
			onFocus : " ",
			onCorrect : " "
		}).functionValidator({
			fun : function(val, elem) {
				if (val == "") {
					return "请上传税务登记证扫描件";
				} else {
					return true;
				}
			}
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
		if(mobile == '' || mobile == undefined || mobile.length !=11 || mobile.split("")[0] != 1)
		{
			$("#phone")[0].focus();
			$("#fetchAuthcode")[0].focus();
			return false;
		}
		$.ajax({
		//	dataType : "json",
			type:'post',
		//	async : true,
			url : "/Mobilecode/index",
			data:'mobile='+mobile+'&module=1',
			success : function(res) {
				if(res.status == 1)
				{
					mobile_send = true;
					fun_timedown(60);
				}else{
					alert(res.info)
				}	
			}
				
			});
	});
	
	gioneePerfect.verifyForm();
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