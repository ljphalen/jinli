// JavaScript Document

/** 
 *  @Author zhiming 
 *  @Date 2012-07-04
 */
 
$(function(){
	
	$($("input")[0]).focus();
	
	var $send_btn = $("#send-btn");
	var timer = -1;
	
	function countDown() {
		var allTime = 120;
		
		$("#send-btn").attr("disabled",true);
		$send_btn.val(allTime+"秒后重新发送验证码");
		
		timer = setInterval(function(){
			
			allTime --;
			$send_btn.val(allTime+"秒后重新发送验证码");
			
			if(!allTime){
				clearInterval(timer);
				$("#send-btn").attr("disabled",false);
				$send_btn.val("重新发送手机验证码");
			}
			
		},1000);
		
	}
	
	$("#send-btn").click(function(){
		countDown();
	});
	
	$("#submit-btn").click(function(){
		var $serial_num = $("#serial-num");
		if($serial_num.val() == ""){
			$serial_num.parent().prev().text("手机验证码不能为空!");
		}
		$("form").submit();
	});
	
	countDown();
	
});