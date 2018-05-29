var Com = Common
	,dataSp = Com.getUrlParam('sp')
	,source = Com.getUrlParam('source')
	,puuid  = Com.getUrlParam('puuid')
;
var Memory = {
	file:{
		score:0
		,sp:dataSp
		,source:source
		,puuid:puuid
		,token:token
		,grant:{

		}
	}
}

var endInterval = function(){
	$('#open').show();
	$('#time span').hide();
	$('#time').attr('value','end');
	$('#plate img').hide();
	$('#getIntegral h1').html('0');
	$('#getIntegral ul').html('');
	clearInterval(inter)
}
var score = function(val){
	
	if(!gameData.props[val]){
		Memory.file.score = 0
		endInterval();
		gameM.send({
			data:Memory.file
		});
		return;
	}
	var html = ['<li><h2>+',gameData.props[val],'</h2></li>'].join('');
	Memory.file.score = Memory.file.score+gameData.props[val];
	console.log(Memory.file.grant[val]);
	if(Memory.file.grant[val])
		Memory.file.grant[val]++;
	else
		Memory.file.grant[val] = 1;

	$('#getIntegral h1').html(Memory.file.score);
	$('#getIntegral ul').append(html);
	
}
var isCode = function(data){
	data.name = '挑战成功';
	switch(data.code){
		case -2 :
			data.msg ='您非法修改了数据，不能在本活动周期内继续参与';
			break;
		case -1 :
			data.msg ='您今天的机会已用完，请明天继续接受挑战';
			break;
		case 0 :
			isLoad('挑战失败');
			return 'over';
		case 1 :
			data.msg ='活动不再记分范围内，可以无限试玩。';
			break;
		case 2 :
			data.name = '挑战失败';
			data.msg ='请再接再厉,今天还有'+data.remainTimes+'次机会';
			break;
		case 3 :
			data.msg ='今天还有'+data.remainTimes+'次机会';
			break;
		case 4 :
			data.msg ='今日积分已达到上限';
			break;
		case 5 :
			data.msg ='你今天的机会用完了，分享获得额外一次机会';
			break;
	}
	if(data.code==5){
		$('#shareBut').show();
		$('#comeAgain').hide();
	}else{
		$('#shareBut').hide();
		$('#comeAgain').show();
	}
}
var isLoad = function(name,e){
		
		if(name)
			$('#loadWindow [name="name"]').html(name).show();
		else
			$('#loadWindow [name="name"]').hide();

		$('#loadWindow').show();
		$('[name="window"]').show();
		
		if(e) e.stopPropagation();
		return;
	
}
var ajaxInit = function(e){
	Memory.file.score = 0;
	for(var i in Memory.file.grant){
		Memory.file.grant[i] = 0;
	}
	if(e){
		gameData.fish = e.fish;
		gameData.remainTimes = e.remainTimes;
		if(e.grant>gameData.historyScore&&e.code!=1){
			$('[name="load"] font span').html(e.grant);
			gameData.historyScore = e.grant;
		}
	}
}
var gameM = new Model({
	url:gameData.submitUrl
	,succ:function(e){

		ajaxInit(e.data);
		if(!e.success) return alert(e.msg||'非法请求');
		if(isCode(e.data)=='over') return;

		var form = new Form('#window');
		form.setHtml(e.data);
		$('#window').show();
		$('[name="window"]').show();

	}
});
var shareM = new Model({
	url:gameData.share.ajax
	,succ:function(e){
		if(e.data.share){
			gameData.share.status = e.data.share;
			gameData.remainTimes = e.data.remainTimes;
		}
	}
});
$(function(){
	var fishWidth = 130      //鱼的宽度
		,fishtop = 150       //鱼的启始位置 absolute
		,client = Common.client() 
		,fishLeft = (client.width- fishWidth) /2  
		,catHeight = $('#cat').height()
		,fishEnd = client.height - catHeight /1.55
		,fishBottomStyle = {
			left:fishLeft
			,top:fishtop
			,position:'absolute'
		}
		,fishTopStyle = {
			left:fishLeft
			,top:130
			,position:'absolute'
		}
	;

	$('#cat').load(function(){
		catHeight = $('#cat').height()
		fishEnd = client.height - catHeight /1.55
	})
	$('#open').click(function(e){
		if(!gameData.remainTimes) {
			if(!gameData.share.status){
				$('#shareWindow').show();
				e.stopPropagation();
				return;
			}
			$('#overWindow').show();
			e.stopPropagation();
			return;
		}
		
		$(this).hide();
		$('#time span').css('display','inline-block');
		$('#time').attr('value','statr');
		$('#time span:eq(0)').html('30');
		$('#plate [index="'+gameData.fish[0]+'"]').show();
		
		inter = setInterval(function(){
			var seconds = parseInt($('#time span:eq(0)').html());
			if(seconds)
				return $('#time span:eq(0)').html(--seconds);
			
			endInterval();
			gameM.send({
				data:Memory.file
			});
			
		}, 800);
	});
	
	$('#explain [name="title"]').click(function(e){
		if($('#explain').hasClass('hide'))
			$('#explain').removeClass('hide');
		else
			$('#explain').addClass('hide');
		
		e.stopPropagation();
	})
	$('body').click(function(){
		if(!$('#explain').hasClass('hide'))
			$('#explain').addClass('hide');
		
		$('#guide').hide();
		$('.window').hide();
		$('.window-bg').hide();

	})
	$('#getIntegral').css('bottom',client.height/3);
	
	$("#state").swipe({
		swipeStatus:function(event,
		 phase, direction, distance, duration,fingerCount) {

			if(phase == 'move'||$('#time').attr('value')=='end') return;
			
			var $imgId = $('#plate [index="'+gameData.fish[0]+'"]')
				,imgHtml = $imgId.clone()
				,myFish
			;
			if(direction == 'down'){
				
				$imgId.hide();
				myFish = gameData.fish[0]

				gameData.fish.shift();
				$('#plate [index="'+gameData.fish[0]+'"]').show();

				$("#state").append(imgHtml);
				var length = $("#state img").length -1
					,$id = $("#state img").eq(length)
				;
				$id.css(fishBottomStyle);
				$id.animate({
					top:fishEnd
				},1000,function(){
					$(this).remove();
				});
				score(myFish);
			}
			
			if(direction == 'up'){
				
				$imgId.hide();
				gameData.fish.shift();
				$('#plate [index="'+gameData.fish[0]+'"]').show();
				
				$("#state").append(imgHtml);
				var length = $("#state img").length -1
					,$id = $("#state img").eq(length)
				;
				$id.css(fishTopStyle);
				$id.animate({
					top:0
				},600,function(){
					$(this).remove();
				});
				return;
			}
		}
	});
	
	$('[name="load"]').click(function(){
		if(gameData.login) return;
		if(dataSp){
			Com.onEvent('account');
			Com.onEvent('finish');
			return;
		}
		window.location.href = gameData.loginUrl;
	});
	$('[name="share"]').click(function(e){
		if(gameData.source=='weixin'){
			$('#wxShare').show();
			e.stopPropagation();
			return;
		}

		console.log(JSON.stringify(gameData.share));
		if(dataSp){
			shareM.send({
				data:{
					sp:dataSp
					,source:source
					,puuid:puuid
					,token:token
				}
			});
			Com.onEvent('share',gameData.share);
			return;
		}

	});
	$('#comeAgain').click(function(){
		$('#window').hide();
		$('[name="window"]').hide();
	});
	$('#draw').click(function(e){
		if(!gameData.login) {
			isLoad(null,e)
			return;
		}
		var url = $(this).attr('data-url');
		var	file = {
			newArgs:{
				viewType:'webview',
		   		param:{
			        url:url,
			        title:'抽奖'
			    }
			}
		};
		if(dataSp){
			Com.openPage({file:file});
			return;
		}
		$('#dow').show();
		$('.window-bg').show();
		e.stopPropagation();
	});
	$('#mall').click(function(e){
		if(!gameData.login) {
			isLoad(null,e)
			return;
		}
		var url = $(this).attr('data-url');
		var	file = {
			newArgs:{
				viewType:'webview',
		   		param:{
			        url:url,
			        title:'积分商城'
			    }
			}
		};
		if(dataSp){
			Com.openPage({file:file});
			return;
		}
		$('#dow').show();
		$('.window-bg').show();
		e.stopPropagation();
		
	});
	$('#ranking').click(function(){
		var url = $(this).attr('data-url');
		var	file = {
			newArgs:{
				viewType:'webview',
		   		param:{
			        url:url,
			        title:'排行榜'
			    }
			}
		};
		if(dataSp)
			Com.openPage({file:file});
		else
			window.location.href = url
	});
	$('#dow [type="button"]').click(function(){
		if(gameData.source=='weixin'){
			window.location.href = $(this).attr('weixinhref');
			return;
		}
		window.location.href = $(this).attr('clienthref');
	});
})