(function($){
	var time = 120, tid;
	var mobile;

	// 获取验证码
	$('#js_checkcode').on('click',function(){
		mobile = $('#inp-mobile').val();
		if(!mobile){
			$('.toast-wrap').show();
			$('.toast-wrap p').html("请输入正确的手机号");
			setTimeout(function(){
				$('.toast-wrap').hide();
				$('.toast-wrap p').html('');
			},1000);
			return;
		}
		checkcode();
		ddd
	});

	$('#js_recheckcode').on('click',function(){
		mobile = $('#inp-mobile').val();
		$(this).addClass('none');
		$('#js_countdown').removeClass('none');
		checkcode();
	});

	$('.step2 .inp-txt').on('keyup',function(){
		if($(this).val().length == 4){
			$('#js_btnsmt').addClass('inp-btn-act');
		} else {
			$('#js_btnsmt').removeClass('inp-btn-act');
		}
	});

	$('#js_btnsmt').on('click',function(){
		var that = $(this);
		var ref = $('#ref').val(),
			mobile = $('#inp-mobile').val(),
			smscode = $('.step2 .inp-txt').val();

		if(that.hasClass('inp-btn-act')){
			if(!mobile){
				$('.toast-wrap').show();
				$('.toast-wrap p').html("请输入正确的手机号");
				setTimeout(function(){
					$('.toast-wrap').hide();
					$('.toast-wrap p').html('');
				},1000);
				return;
			}

			$.ajax({
				url: '/user/index/verifyrndcode',
				type: 'post',
				data: {"f": ref, "mobile": mobile, "smscode": smscode, "token": token},
				dataType: 'json',
				success: function(data){
					if(data.success){
						if(data.data.redirect){
							location.href = data.data.redirect;
						}
					} else {
						$('.toast-wrap').show();
						$('.toast-wrap p').html(data.msg);
						setTimeout(function(){
							$('.toast-wrap').hide();
							$('.toast-wrap p').html('');
						},1000);
					}
				}
			});
		}
	});
	
	function checkcode(){
		$.ajax({
			url: '/user/index/sendrndcode',
			type: 'post',
			data: {"mobile": mobile, "token": token},
			dataType: 'json',
			success: function(data){
				$('.step1').hide();
				$('.step2').removeClass('none');
				$('.step3').removeClass('none');
				$('.step2 .inp-txt').focus();
				countdown(time);
				if(data.success){
					//time = data.data.interval;
					//console.log(time);
				} else {
					$('.toast-wrap').show();
					$('.toast-wrap p').html(data.msg);
					setTimeout(function(){
						$('.toast-wrap').hide();
						$('.toast-wrap p').html('');
					},1000);
				}
			}
		});
	}

	function countdown(time){
		if(time <= 0){
			clearTimeout(tid);
			$('#js_countdown').addClass('none');
			$('#js_recheckcode').removeClass('none');
			return;
		}
		$('#js_countdown em').html(--time);
		tid = setTimeout(function(){
			countdown(time);
		},1000);
	}
})(Zepto);