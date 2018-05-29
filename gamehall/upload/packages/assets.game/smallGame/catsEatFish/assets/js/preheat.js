var Com = Common;
$(function(){
	var width = Com.client().width*2
	var tour = function(){
		$('#finshSmall').animate({left:width},width*5,function(){
			$(this).css({left:'-160px'})
			tour();
		});
		$('#finshBig').animate({right:width},width*5,function(){
			$(this).css({right:'-160px'})
		});
	}
	tour();

	var interval = setInterval(function(){
		var date
			,format = []
		;
		preheatData.timeStart += 1;
		date = Com.getTwoTimeDifference(preheatData.timeStart,preheatData.timeEnd);
		if(date.day>=1){
			format[0] = '<span>'+date.day+'</span>';
			format[1] = '天';
			format[2] = '<span>'+date.hours+'</span>';
			format[3] = '小时';
			format[4] = '<span>'+date.minute+'</span>';
			format[5] = '分';
		}else if(date.hours>=1){
			format[0] = '<span>'+date.hours+'</span>';
			format[1] = '小时';
			format[2] = '<span>'+date.minute+'</span>';
			format[3] = '分';
			format[4] = '<span>'+date.second+'</span>';
			format[5] = '秒';
		}else if(date.minute >=1){
			format[0] = '<span>'+date.minute+'</span>';
			format[1] = '分';
			format[2] = '<span>'+date.second+'</span>';
			format[3] = '秒';
		}else if(date.second>=1){
			format[0] = '0';
			format[1] = '分';
			format[2] = '<span>'+date.second+'</span>';
			format[3] = '秒';
		}else{
			file.newArgs = {
				viewType:'webview',
		   		param:{
			        url:preheatData.activityUrl,
			        title:'活动说明'
			    }
			};
			Com.openPage({file:file});
			Com.onEvent('finish');
		}
		$('#date').html(format.join(''));
	},1000);
	$('body').bind('touchmove',function(){},false);
})