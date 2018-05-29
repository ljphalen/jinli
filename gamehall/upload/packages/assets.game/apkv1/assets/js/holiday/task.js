(function(){
	var titleH = 0
		,nameH = 0;
	$('#download [name="gameList"] h1').each(function(){
		var height = $(this).height();
		if(height>titleH)	titleH = height;
	});
	$('#download [name="gameList"] h2').each(function(){
		var height = $(this).height();
		if(height>nameH)	nameH = height;
	});
	$('#download [name="gameList"] h1').css('height',titleH).addClass('multi-line-omit');
	$('#download [name="gameList"] h2').css('height',nameH).addClass('multi-line-omit');
}());

var Com = Common
	,gameStatus={
		100:['下载','startDownload'],
		101:['下载中',''],
		102:['继续','resumeDownload'],
		103:['安装中',''],
		104:['安装','installGame'],
		105:['启动','openGame'],
		106:['升级','startDownload'],
		107:['重试','resumeDownload'],
		109:['下载','startDownload'],
		110:['升级','startDownload']
	}
	,loadState =Validata.empty(Com.getVal('islogin'))
	,dataSp = Com.getUrlParam('sp')
	,dataPuuid = Com.getUrlParam('puuid')
	,isAjax = false
;
$(function(){
	var activity=document.getElementById("msg")
		,Roll=document.getElementById("roll")
		,RollCopy=document.getElementById("copyRoll")
		,validate
		,urlPig = $('#layerPig img').attr('src')
	;


	//设置 下载名称 与 提示 高度一至
	(function(){
		var taskData = setButStatus()
			,ajaxUrl = holidayData.autoFinishTaskUrl
		;
		if(isAjax) return;
		if(loadState =='false') return;
		if(!taskData.length) return;
		isAjax = true;
		$.post(ajaxUrl,{
				token:token,
				sp:dataSp,
				puuid:dataPuuid,
				taskData:JSON.stringify(taskData)
			},function(data){
				var data = JSON.parse(data)
					,form = new Form('#sign')
				;
				isAjax = false;
				if(!data.success){
					$('#ajaxTitle').show();
					$('#ajaxTitle [name=msg]').html(data.msg||'非法请求');
					return;
				}
				if(!data.data.status){
					$('#ajaxTitle').show();
					$('#ajaxTitle [name=msg]').html(data.msg||'自动完程任务失败');
					return;
				}
				if(data.data.taskStatus){
					$('#sign .same-day').removeClass('same-day').addClass('signed');
					$('#sign [name="todayFinished"]').html('完成');
				}
				for(var i in data.data.reward){
					$('#prize [name="pig"]').eq(i-1).attr('data-status',data.data.reward[i]);
				}
				form.setHtml(data.data);
		});
	}());
	var pigMove = function(){
		var html = [
			'<div name="pigMove"><div class="layer-bg-black"></div>'
			,'<div class="img-container">'
			,'<div class="img-wrapper">'
			,'<img name="poster" src=',urlPig,'>'
			,'</div>'
			,'</div>'
			,'<div class="btn-container">努力追猪中...</div></div>'
		].join('')
		$('#layerPig').html(html);
		$('#layerPig').show();
		return {
			remove:function(){
				$('#layerPig').find('[name="pigMove"]').remove();
			}
		}
	}
	
	//添加监听 gamestatus.listener
	Com.onEvent('gamestatus.listener');
	if($('#roll p').length>10){
		RollCopy.innerHTML = Roll.innerHTML;
		//如果是单数 改为双数
		if($('#roll p').length%2){
			$('#roll').append('<p>'+$('#roll p').eq(0).html()+'</p>');
			$('#copyRoll p').eq(0).remove();
		}
		
		setInterval(function(){
			if(RollCopy.offsetTop-activity.scrollTop<=0
				||validate==activity.scrollTop){
				activity.scrollTop-=Roll.offsetHeight+56;
			}else{
				validate =activity.scrollTop;
				activity.scrollTop++;
			}
		},80);
	}
	function handler(e) {
        e.preventDefault();
        e.stopPropagation();
    }
	var showWindow = function(config){
		if(config.top){
			$(config.id).css({'top':config.top});
			$("body").unbind('touchmove');
		}

		$(config.id).show();

		$(config.id).find('[name="msg"]').html(config.data.title);
		$(config.id).find('[name="poster"]').attr('src',config.data.prizeImg);
	}
	var showValidate = function(){

		if(!Com.isVersionBiggerorEqual(dataSp.split('_')[1],holidayData.clientVersion)){
			$("body").bind('touchmove', handler, false);
			$('#source').show();
			return;
		}

		if(loadState=='false'){
			$("body").bind('touchmove', handler, false);
			$('#load').show();
			return;
		}
		return true;
	}
	$('#download').on('click','[name="gameList"] [name="dow"]',function(){
		var $gameId =  $(this).parents('[name="gameList"]')
			,packageName = $gameId.attr('data-package')
			,status = $gameId.attr('data-status')
			,games = Com.getDataStatus(holidayData.game,'packageName',packageName)
			,file = {list:[games]}
		;

		if(!showValidate()) return;

		Com.removeLetter(file.list,'gameSize');
		if(gameStatus[status][1]){
			Com.onEvent(gameStatus[status][1],file);
			if(status == 105)
				$(this).val('已完成');
		}
	});
	$('#download').on('click','[name="gameList"] [name="gameDetails"]',function(){
		var packageName = $(this).parents('[name="gameList"]').attr('data-package')
			,game = Com.getDataStatus(holidayData.game,'packageName',packageName)
		;

		if(!showValidate())	return;
		var file = {
			newArgs:{
				viewType:'GameDetailView',
		   		param:{
					title:'',
					url:'http://game.gionee.com',
					gameId: game.gameId,
				    downUrl: game.downUrl,
				    packageName:game.packagename,
				    filesize: game.gameSize,
				    sdkinfo: 'android1.6',
				    resolution:dataSp.split('_')[4]
				}
			}
		};
		Com.openPage({file:file});
	});
	$('#prize').on('click','[name="pig"]',function(){
		var status = parseInt($(this).attr('data-status'))
			,index = $('#prize [name="pig"]').index(this)
			,ajaxUrl = $(this).attr('data-ajaxUrl')
			,condition = $(this).attr('data-condition')
			,$id = $(this)
			,url = $(this).attr('data-ajaxUrl')
			,prize
			,rmPigMove
		;
		if(isAjax) return;
		if(!showValidate()) return;

		$("body").bind('touchmove', handler, false);
		
		if(!status){
			$('#unfinishedTask').show();
			$('#unfinishedTask [name="msg"]').html(condition);
			return;
		}

		if(status=='2'){
			$('#successPrize').show();
			return;
		}
		rmPigMove = pigMove();
		var interval = setInterval(function(){
			if(prize){
				rmPigMove.remove();
				clearInterval(interval);
				isAjax = false;
				if(!prize.success){
					return;
				}
				if( prize.success==0||prize.data.status==0 ){
					$('#ajaxTitle').show();
					$('#ajaxTitle [name=msg]').html(prize.msg);
					return;
				}
				switch(parseInt(prize.data.prizeType)){
					case 3:
					prize.id = '#layerEntity';
					prize.top = document.body.scrollTop + 'px';
					break;
					case 1:
					prize.id = '#voucher';
					break;
					case 2:
					prize.id = '#integral';
					break;
					case 0:
					prize.id = '#layerNot';
					break;
				}
				showWindow(prize);
				$id.attr('data-status','2');
			}
		},3200);
		isAjax = true;
		$.post(ajaxUrl,{
			token:token,
			sp:dataSp,
			puuid:dataPuuid
		},function(data){
			prize = JSON.parse(data);
			holidayData.prizeId = prize.data.prizeId;
			holidayData.prizeItemId = prize.data.prizeItemId;
			
		});
		return;
	});
	$('#layerEntity [name="ok"]').tap(function(){
		var form = new Form('#layerEntity')
			,file = form.getVals()
			$error = $('#layerEntity [name="error"]')
		;
		if(isAjax) return;
		if(!Validata.empty(file.name)){
			$error.html('请输入收货人');
			$error.show();
			return;
		}
		if(!Validata.empty(file.phone)){
			$error.html('请输入手机号码');
			$error.show();
			return;
		}
		if(!Validata.phone(file.phone)){
			$error.html('请输入正确的手机号码');
			$error.show();
			return;
		}
		if(!Validata.empty(file.address)){
			$error.html('请输入地址');
			$error.show();
			return;
		}
		if(file.address.length<=6){
			$error.html('地址长度应大于六位');
			$error.show();
			return;
		}
		$error.hide();
		file.token = token;
		file.sp = dataSp;
		file.puuid = dataPuuid;
		file.prizeId = holidayData.prizeId;
		file.prizeItemId = holidayData.prizeItemId;
		isAjax = true;
		$.post(holidayData.ajaxUrlEntity,file,function(data){
			isAjax = false;
			data = JSON.parse(data);
			$('#layerEntity').hide();
			if(!data.success){
				$('#ajaxTitle').show();
				$('#ajaxTitle [name=msg]').html(data.msg);
				return;
			}
			if(!data.data.status){
				$('#ajaxTitle').show();
				$('#ajaxTitle [name=msg]').html(data.msg);
				return;
			}
			$('#layerEntitySuccess').show();
			
		});
	});
	$('#load [name="ok"]').tap(function(){
		Com.onEvent('account');
		Com.onEvent('finish');
	});
	$('input[name="but-hide"]').click(function(){
		$(this).parents('.layer').hide();
		$("body").unbind('touchmove');
	});
	$('#ajaxTitle [name="ok"]').tap(function(){
		$(this).parents('.layer').hide();
		$("body").unbind('touchmove');
	})
	$('#voucher [name="ok"]').tap(function(){
		var	file = {
			newArgs:{
				viewType:'webview',
		   		param:{
			        url:$('#voucher').attr('data-url'),
			        title:'A券'
			    }
			}
		};
		Com.openPage({file:file});
		$("body").unbind('touchmove');
	});
	$('#source [name="ok"]').tap(function(){
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
	});
	$('#integral [name="ok"]').tap(function(){
		Com.onEvent('mypoint'); 
		$("body").unbind('touchmove');
	});
	$('#selectInfo').click(function(){
		var	file = {
			newArgs:{
				viewType:'webview',
		   		param:{
			        url:$(this).attr('data-url'),
			        title:'活动说明'
			    }
			}
		};
		Com.openPage({file:file});
	});
})
function setButStatus(){
	var statusList = Com.dowloadStatus({
			list:holidayData.game
		})
		,list,$id,status,taskData = [],packageName,game,len
		,notGame = false; 
	;
	if(!statusList) return taskData;

	list = statusList.list;
	len = list.length;
	for(var i=0;i<len;i++){
		status = list[i].status;
		packageName = list[i].packageName;
		$id = $('[data-package="'+packageName+'"]');
		$id.attr('data-status',status);
		
		game = Com.getDataStatus(holidayData.game,'packageName',packageName);
		if((status==105&&game.taskType ==2&&loadState=='true')||game.taskStatus==1)
			$id.find('input').val('已完成');
		else
			$id.find('input').val(gameStatus[status][0]);
		
		if(status == 105&&loadState=='true'&&game.taskStatus==0){
			notGame = true;
		}

		if(status==105&&game.taskType!=1&&game.taskStatus==0){
			taskData.push({
				taskId:game.taskId,
				taskType:game.taskType,
				gameId:game.gameId,
				packageName:packageName
			});
		}
		
		/* 隐藏 已前下载的游戏
		if(game.taskStatus == 3) 
			$('[data-package="'+game.packageName+'"]').parents('[name="dowFrame"]').hide();*/
	}
	if(notGame){
		$.post(holidayData.getTaskStatusUrl,{
				'puuid':dataPuuid,
				'token':token,
				'sp':dataSp
			},function(data){
				var data = JSON.parse(data)
					,form = new Form('#sign')
				;
				data = data.data;
				if(!parseInt(data.taskStatus))
					return;
				for(var i in data.reward){
					$('#prize [name="pig"]').eq(i-1).attr('data-status',data.reward[i]);
				}

				$('#download [name="dow"]').val('已完成');
				$('#sign .same-day').removeClass('same-day').addClass('signed');
				$('#sign [name="todayFinished"]').html('完成');
				form.setHtml(data);
		});
	}
	return taskData;
}
function setGameStatus(ac,data){
	if(ac=='gamehall.gameStatusChange'){
		setButStatus();
	}
}