// JavaScript Document
/** 
 *  @Author zhiming 
 *  @Date 2012-07-04
 */
$(function(){
	
	$($("input")[0]).focus();
	
	var $username = $("#username");
	var $pwd = $("#pwd");
	var $serial = $("#serial-num");
	/*var $error = $("form .error-tips");*/
	
	$(".input-item input").keypress(function(){
		$(this).nextAll(".error-tips").text("");										 
	});
	
	$("#submit-btn").click(function() {
		var bool = true;
		
		if($username.val() == "") {
			$username.nextAll(".error-tips").text("用户名不能为空!");
			$username.focus();
			bool = false;
		}
		
		if($pwd.val() == "") {
			$pwd.nextAll(".error-tips").text("密码不能为空!");
			$pwd.focus();
			bool = false;
		}
		
		if($serial.val() == ""){
			$serial.nextAll(".error-tips").text("验证码不能为空!");
			$serial.focus();
			bool = false;
		}
		
		/*if(bool){
			//发送ajax请求
			$.get(url,function(_data){
				if(_data) {
					window.location.href = "validate_mobile.html";
				} else {
					window.location.href = "index.html";
				}
			});
		}*/
		if(bool)
		$("form").submit();
									
	});		   
		   
		   
});