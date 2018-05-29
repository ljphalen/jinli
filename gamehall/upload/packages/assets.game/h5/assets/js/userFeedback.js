$(function(){
	var setAlert =function(config,type){
		for(var i in config){
			$('#alert [name="'+i+'"]').html(config[i])
		}
		if(type=="ok"){
			$('#alert [name="ok"]').attr('href','javascript:history.go(-1)');
			$('#alert [name="icon"]').removeClass('failure');
			$('#alert [name="icon"]').addClass('success');
			$('#alert [name="info"]').css('color','#aaaaaa');
		}
		$('[name="alertRadio"]').show();
	}
	$('#alert [name="ok"]').attr('href','javascript:')
	$('#alert [name="ok"]').click(function(){
		$('[name="alertRadio"]').hide();
	})
	
	$('#send').click(function(){
		var data = {
			phone:$('#phone').val(),
			opinion:$('#opinion').val(),
			qq:$('#qq').val()
		},valiText;
		if(!Validata.empty(data.opinion)){
			setAlert({info:'请输入反馈意见'});
			return;
		}
		if(Validata.len(data.opinion)<10){
			setAlert({info:'请输入十个字母或五个汉字'});
			return;
		}
		if(Validata.empty(data.phone)){
			
			if(!Validata.phone(data.phone)){
				setAlert({info:'请输入格式正确的手机号码'});
				return;
			}
		}
		if(!Validata.empty(data.qq)){
			setAlert({info:'请输入QQ或邮箱'});
			return;
		}
		if(Validata.number(data.qq)){
			if(data.qq.length<5){
				setAlert({info:'请输入格式正确的QQ或邮箱'});
				return
			}
		}else if(!Validata.email(data.qq)){
			setAlert({info:'请输入格式正确的QQ或邮箱'});
			return;
		}
		var form = new Model({
			url:'/feedback/addPost'
			,data:data
		});
		form.save(
			function(data){
				if(!data.success){
					setAlert({
						info:data.msg
					},'ok');
					return;
				}
				setAlert({
					title:'反馈成功'
					,info:'我们的客服人员会尽快答复或者联系您'
				},'ok');
			}
		);
	});
	
})