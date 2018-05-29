var Com = Common;
$(function(){
	var file = {}
		dataSp = Com.getUrlParam('sp')
	;
	var interval = setInterval(function(){
		var date
			,format = []
		;
		holidayData.timeStart += 1;
		date = Com.getTwoTimeDifference(holidayData.timeStart,holidayData.timeEnd);
		if(date.day>=1){
			format[0] = date.day;
			format[1] = '天';
			format[2] = date.hours;
			format[3] = '小时';
			format[4] = date.minute;
			format[5] = '分';
		}else if(date.hours>=1){
			format[0] = date.hours;
			format[1] = '小时';
			format[2] = date.minute;
			format[3] = '分';
			format[4] = date.second;
			format[5] = '秒';
		}else if(date.minute >=1){
			format[0] = date.minute;
			format[1] = '分';
			format[2] = date.second;
			format[3] = '秒';
		}else if(date.second>=1){
			format[0] = '0';
			format[1] = '分';
			format[2] = date.second;
			format[3] = '秒';
		}else{
			clearInterval(interval);
			file.newArgs = {
				viewType:'webview',
		   		param:{
			        url:holidayData.activityUrl,
			        title:'活动说明'
			    }
			};
			Com.openPage({file:file});
			Com.onEvent('finish');
		}
		$('#date').html(format.join(''));
		//console.log('相差'+date.day+'天'+date.hours+"小时"+date.minute+'分'+date.second+'秒');
	},1000);
	$('#update').tap(function(){
		var file = {
			newArgs:{
				viewType:'GameDetailView',
		   		param:{
					title:'客户端下载',
					url:holidayData.clientGameDetailUrl,
					gameId: holidayData.clientGameId,
				    downUrl: holidayData.downurl,
				    packageName:holidayData.packagename,
				    filesize: holidayData.filesize,
				    sdkinfo: 'android1.6',
				    resolution:dataSp.split('_')[4]
				}
			}
		};
		Com.openPage({file:file});
		Com.onEvent('finish');
	})

})