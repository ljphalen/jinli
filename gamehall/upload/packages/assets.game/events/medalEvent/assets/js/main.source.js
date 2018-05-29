(function($) {
	$.fn.scrollLoading = function(options) {
		var defaults = {
			attr: "data-src",
			container: $(window),
			callback: $.noop
		};
		var params = $.extend({}, defaults, options || {});
		params.cache = [];
		$(this).each(function() {
			var node = this.nodeName.toLowerCase(), url = $(this).attr(params["attr"]);
			//重组
			var data = {
				obj: $(this),
				tag: node,
				url: url
			};
			params.cache.push(data);
		});
		
		var callback = function(call) {
			if ($.isFunction(params.callback)) {
				params.callback.call(call.get(0));
			}
		};
		//动态显示数据
		var loading = function() {
			
			var contHeight = params.container.height();
			if (params.container.get(0) === window) {
				contop = $(window)[0].scrollY;
			} else {
				contop = params.container.offset().top;
			}		
			
			$.each(params.cache, function(i, data) {
				var o = data.obj, tag = data.tag, url = data.url, post, posb;
				
				if (o) {
					post = o.offset().top - contop, posb = post + o.height();
					if ((post >= 0 && post < contHeight) || (posb > 0 && posb <= contHeight)) {
						if (url) {
							//在浏览器窗口内
							if (tag === "img") {
								//图片，改变src
								callback(o.attr("src", url));		
							} else {
								o.load(url, {}, function() {
									callback(o);
								});
							}		
						} else {
							// 无地址，直接触发回调
							callback(o);
						}
						data.obj = null;	
					}
				}
			});	
		};
		
		//事件触发
		//加载完毕即执行
		loading();
		//滚动执行
		params.container.bind("scroll", loading);
	};
	$.init = function(){

		(function(){
			var file
				,gameList
				,status
				,currentVersion = Com.getClientVersion()
			;
			if(!$('.game-list .J_openDetailView').length) return;
			Com.onEvent('gamestatus.listener');
			if(!Com.isVersionBiggerorEqual(currentVersion,'1.5.8'))
				return $('.J_openDetailView').find('.dow').hide();
			
			$('.game-list .J_openDetailView').each(function(){
				if(!$(this).attr('data-packagename')) return;
				file = getDowData($(this));
				gameList = Com.dowloadStatus({
					list:[file]
				});

				if(!gameList)
					return;
				gameList = gameList.list[0]
				status = gameList.status;
				
				if( status==111||status==108 ) 
					return $(this).hide();
				
				$(this).attr('data-status',status);
				$(this).find('input').val(gameStatus[status][0]);
				
			});
			
		}());

		var Roll=document.getElementById("roll")
			,RollCopy=document.getElementById("copyRoll")
			,activity =document.getElementById("rolls")
			,validate
		;
		if($('#roll li').length>5){
			RollCopy.innerHTML = Roll.innerHTML;
			//如果是单数 改为双数
			if($('#roll li').length%2){
				$('#roll').append('<li>'+$('#roll li').eq(0).html()+'</li>');
				$('#copyRoll li').eq(0).remove();
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
	}
})(Zepto);

var action={
	network:'gamehall.hasnetwork',
	alert:'gamehall.alert',
	logout:'gamehall.clearlogin',
	finish:'gamehall.finish',
	islogin:'gamehall.islogin',
	login:'gamehall.account',
	account:'gamehall.account.info',
};
var Const={
	ADDRESS_NULL:'请填写收货地址',
	CONSIGNEE_NULL:'请填写收货人姓名',
	PHONE_NULL:'手机号码不能为空',
	PHONE_ERROR:'手机号码格式错误',
	LOGOUT:'未登录',
	CHANGE_TEXT:'兑换',
	CHANGED_TEXT:'已兑换',
	CHANGE_ING_TEXT:'兑换中...',
	CHANGE_TEXT_NOW:'立即兑换',
	PHONE_REG:/^1[34578]{1}[0-9]{9}$/,
	MILLSECONDS:3000,

}
var changeInfo={
	unchangeAble:1,
	changeAble:2,
	changed:3
}
var prizeType={
	entity:1,
	acoin:2,
	point:3
}
function getDowData($that){
	var file = {};
	file['source'] = $that.attr('data-source')
	file['iconUrl'] = $that.attr('data-iconUrl')
	file['gameId'] = $that.attr('data-gameId')
	file['gameSize'] = $that.attr('data-gameSize')
	file['gameName'] = $that.attr('data-gameName')
	file['downUrl'] = $that.attr('data-downUrl')
	file['packageName'] = $that.attr('data-packageName');
	return file;
}
//客户端页面跳转的处理
function openPage(data) {
    if (!data) return;
    if(!(window.gamehall)){
    	location.href = data.url;
        return;
    }
    switch(apiVersion){
        case 'v1':
            openV1Page(data);
            break;
        case 'v2':
            openV2Page(data);
            break;
        case 'v3':
            openV3Page(data);
            break;
    }
}
//v1(特征：phoneGap以及参数类型完全依赖data-infopage这个参数的length）
function openV1Page(data){
    var cfg={};
    if(data.type=='list'){
        cfg={
            type:'list',
            args:{
                title: data.title,
                url: data.url
           }
        };
    } else{
        cfg={
            type:'detail',
            args:{
                title: data.title,
                url: data.url,
                gameid: data.gameId,
                downurl: data.downurl,
                packagename: data.packagename,
                filesize: data.filesize,
                sdkinfo: data.sdkinfo,
                resolution: data.resolution
            }
        }
    }
    startActivity(cfg);
}
//v2 (部分含有phoneGap，跳转类型依赖与服务端传递的data-type)
function openV2Page(data){
    var cfg={type:'',args:{}};
    if(data.type=='list'){//list
        var oldObj={title: data.title,url: data.url};
        cfg.args=window.gamehall?{}:oldObj;//去除掉phoneGap之后就只用传newAgs就好了
        cfg.type='list';
        //单标签list
        cfg.args.newArgs={
            title: data.title,
            url: data.url
        };
       // to do  多标签list
    }
    else if(data.type=='detail'){ 
        var oldObj={
            title: "",url: "",gameid: "",downurl: "",packagename: "",filesize: "",
            sdkinfo: "",resolution: ""
        };
        cfg.args=window.gamehall?{}:oldObj;
        cfg.type='detail';
        cfg.args.newArgs={
            title: data.title,
            url: data.url,
            gameId: data.gameId
        }
    }
    else if(data.type=='giftDetail'){//从礼包列表打开本地化的礼包详情页
        var oldObj={
            title: "",url: "",gameid: "",downurl: "",packagename: "",filesize: "",
            sdkinfo: "",resolution: ""
        };
        cfg.args=window.gamehall?{}:oldObj;
        cfg.type='detail';
        cfg.args.newArgs={
            title: '礼包详情',
            url: data.url,
            viewType: 'GiftDetailView',
            gameId: data.gameId,
            giftId: data.giftId
        }
    }
    else if(data.type=='browser'){ //外链
        cfg.type='browser';
        cfg.args={
           url:data.url
        };
    }
    startActivity(cfg);
}

//type: list detail browser
//type,viewType,id,gameId,title,url,source
function openV3Page(data){
    var cfg={}; cfg.args={ };
    var viewType=data.viewType;

    cfg.args.newArgs={
        viewType:viewType,
        param:{
            contentId:data.id,
            gameId:data.gameId,
            url:viewType=='WebView'?data.url:'',
            title:viewType=='WebView'?data.title:'',
            source:data.source,
            'package':data['package']
        }
    }
    cfg.type=data.type;
    console.log(JSON.stringify(cfg.args.newArgs));
    startActivity(cfg);
}
//根据类型打开客户端的页面 'list'、'detail'、'localbrowser'
function startActivity(cfg){
	if(!window.gamehall) return;
    var sucCal=errCal = function() {},
        gamehall=window.gamehall;
    switch(cfg.type){
        case 'list':
            gamehall.startlistactivity(sucCal,errCal,JSON.stringify(cfg.args));
            break;
        case 'detail':
            gamehall.startdetailsactivity(sucCal,errCal,JSON.stringify(cfg.args));
            break;
        case 'browser':
            gamehall.startlocalbrowser(sucCal,errCal,JSON.stringify(cfg.args));
            break;
    }
}

 function getUrlParam(name){
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
    var r = window.location.search.substr(1).match(reg);  //匹配目标参数
    if (r != null) return unescape(r[2]); return null; //返回参数值
}
function getClientVersion(){
    var version='1.0.0';
    var spParam=getUrlParam('sp');
    if(spParam){
        version=spParam.split('_')[1];
    }
    return version;
}
function getApiVersionAndBranch(versionNumber){
    var apiVersion='v1',
        branch='v1';
    var currentVersion=versionNumber;
    
    if(compareVersion(currentVersion,'1.5.1')){//1.5.1 客户端接口本地化
        apiVersion='v3';
        branch='v3';
        if(compareVersion(currentVersion,'1.5.2')){//1.5.2  网页实现的列表页面样式修改
            branch='v3_1';
        }
        if(compareVersion(currentVersion,'1.5.5')){//1.5.5  去掉shadow-网页实现的列表页面样式修改
            branch="v3_2";
        }
    }else{
        if(compareVersion(currentVersion,'1.4.8')){//1.4.7以上 phoneGap接口调整
            apiVersion='v2';
            branch='v2';
        }else{
            apiVersion='v1';
            branch='v1';
        }
    }
    
    return{
        version:apiVersion,
        branch:branch
    }
}

function compareVersion(sourceVersion,targetVersion){
    if(sourceVersion.indexOf(targetVersion)>-1){
        return true;
    }
    var srcArr=sourceVersion.split('.'),
        targetArr=targetVersion.split('.');
    var len=Math.max(srcArr.length,targetArr.length);

    for(var i=0;i<len;i++){
        if(srcArr[i]===undefined){
            return false;
        }
        if(targetArr[i]===undefined){
            return true;
        }
        if(srcArr[i]>targetArr[i]){
            return true;
        }else if(srcArr[i]<targetArr[i]){
            return false;
        }
    }
    return false;
}
//打开客户端页面
function onEventAction(action,data){
	if(window.gamehall){
		var ret=window.gamehall.onEvent(action,JSON.stringify(data));
	}else{
		if(action=action.alert){
			// alert(data.alertStr);
		}
	}
}
//从客户端取数据或状态
function getValueAction(action,data){
	if(window.gamehall){
		var ret=window.gamehall.getValue(action,JSON.stringify(data));
		ret=JSON.parse(ret);
		return ret;
	} 
}
function getLoginInfo(){
	var accountInfo;
	var loginStatus=getValueAction(action.islogin,{});
	if(loginStatus){
		accountInfo={};
		accountInfo=getValueAction(action.account,{});
	}else{
		accountInfo=null;
	}
	return accountInfo;
}
function openClientPage(pageType,ele){
	var id=ele.attr('data-id')||'',
		gameId=ele.attr('data-gameId')||'',
		title=ele.attr('data-title')||'',
		url=ele.attr('data-url')||'',
		source=ele.attr('data-source')||'',
		packageName=ele.attr('data-packagename')||'';

	var data={
		viewType:pageType,
		type:'list',
		id:id,
		gameId:gameId,
		title:title,
		url:url,
		source:source,
		'package':packageName
	};
	data.downurl=ele.attr('data-downurl')||'';
	if(pageType=='GameDetailView'&&apiVersion=='v1'){
		data.type='detail';
		data.packagename=ele.attr('data-packagename')||'';
		data.filesize=ele.attr('data-filesize')||'';
		data.sdkinfo=ele.attr('data-sdkinfo')||'';
		data.resolution=ele.attr('data-resolution')||'';
	}
	console.log(data);
	openPage(data);

}
function showDialog(dialogContainer){
	var dialogWrapper=dialogContainer.parent('.J_dialog');
	dialogContainer.siblings('.dialog-container').addClass('hidden');
	dialogContainer.removeClass('hidden');
	dialogWrapper.removeClass('hidden');
	$("body").bind('touchmove', function(e){e.preventDefault();e.stopPropagation();}, false);
}
function clearLogin(){
	onEventAction(action.finish,{});
	onEventAction(action.logout,{});
	onEventAction(action.login,{});
}
function postData(ajaxUrl, data, sucCallback, errorCallback) {
	$.ajax({
		url: ajaxUrl,
		dataType: 'json',
		type: "POST",
		data: data,
		timeout: 10000,
		success: function(data) {
			sucCallback(data);
			setTimeout(function(){
				postLock=false;
			},Const.MILLSECONDS);
		},
		error: function() {
			postLock=false;
		}
	})
}
var Memory = {
	list:[]
}
var currentPrizeEle=null;
var puuid=getUrlParam('puuid');
var uname=getUrlParam('uname');
var sp=getUrlParam('sp');
var apkVersion=getClientVersion();
var apiVersion=getApiVersionAndBranch(apkVersion).version;
var haveChance=compareVersion(apkVersion,clientVersion);
var postLock=false;
var Com = Common;
var gameStatus={
	100:['下载','startDownload'],
	101:['下载中',''],
	102:['继续','resumeDownload'],
	103:['安装中',''],
	104:['安装','installGame'],
	105:['启动','openGame'],
	106:['升级','startDownload'],
	107:['重试','resumeDownload'],
	109:['下载','startDownload'],
	108:['下载',''],
	110:['升级','startDownload'],
	111:['隐藏','']
};
$(function(){

	$.init();
	$("img[data-src]").scrollLoading({
        callback:function(){
            $(this).removeAttr('data-src');
        }
    });

	function checkAualified(callback,param){
		var currentVersion = Com.getClientVersion();
		if(!Com.isVersionBiggerorEqual(currentVersion,'1.5.5')) return;
		if(!haveChance){//版本号小于后台配置的版本号 提示更新
			if(sp!=null&&window.gamehall){
				showDialog($(".J_update"));
			}else{
				return;
			}
			
		}else{
			var accountInfo=getLoginInfo();
			if(accountInfo==null){//未登录，则先登录
				clearLogin();
			}else{
				callback(param);
			}
		}
	}
	
	$("body").on('click', '.J_openWebview', function(event) {
		
		if($(this).hasClass('J_checkStatus')){
			var that = $(this)
				,callback=function(){
				openClientPage('WebView',that);
			}
			checkAualified(callback)
		}else{
			openClientPage('WebView',$(this));
		}
	});

	$("body").on('click', '.J_openDetailView img', function(event) {
		var that = $(this).parents('.J_openDetailView')
			,sp=getUrlParam('sp')
		;
		openClientPage('GameDetailView',that);
	});

	$(".J_update ").on('click', '.J_openDetailView', function(event) {
		openClientPage('GameDetailView',$(this));
	});
	$("body").on('click', '.J_stapHide', function(event) {
		$(".J_dialog").addClass('hidden');	
		$("body").unbind('touchmove');
	});
	//兑换状态检测
	$("body").on('click',".J_exchange",function(){
		var that=$(this)
			,stapVal = that.attr('data-value')
			,stapImgUrl = that.attr('data-bigImgSrc')
		;
		currentPrizeEle=that;
		var callback=function(that){

			var status=that.attr('data-exchangeStatus');
			var number=that.attr('data-prizenum')
			var type=that.attr('data-prizeType'),data;
			if(type==prizeType.entity){
				data={
					type:'list',
					viewType:'WebView',
					id:'',
					gameId:'',
					title:that.attr('data-title'),
					url:that.attr('data-url'),
					source:that.attr('data-source')
				}
			}
			if(type!= 1&&number==0) return;//虚拟物品 乘于零时不做任何反应
			if(number==0&&status!=3) return; //实物未对换为零时不做任何反应
			if(status==changeInfo.unchangeAble){//不可兑换
				$.get('client/festival_activity/click',{
					puuid:puuid
					,uname:uname
					,sp:sp
					,exchange:0
				});
				if(stapImgUrl){
					$('.J_stap').find('.J_poster').attr('src',stapImgUrl)
				}
				$('.J_stap').find('.J_msg').html(stapVal);
				return showDialog($('.J_stap'));
				
			}else if(status==changeInfo.changeAble){//可兑换

				$.get('client/festival_activity/click',{
						puuid:puuid
						,uname:uname
						,sp:sp
						,exchange:1
					});

				if(type==prizeType.entity){
					onEventAction(action.finish,{});
					openPage(data);
				}else {
					
					var num=that.attr('data-propNum');
					$(".J_num").html(num);
					$(".J_award").children('img').attr('src', that.attr('data-bigImgSrc'));
					showBtnContainer();

					showDialog($(".J_award"));
				}
			}else if(status==changeInfo.changed){//已兑换
				if(type==prizeType.entity){
					openPage(data);
				}else {
					return;
				}
			}
		}
		checkAualified(callback,that);
	});
	
	//虚拟物品兑换 及重试
	$("body").on('click', '.J_virtual_exchange,.J_vritual_retry', function(event) {
		if(postLock) return;
		
		if(!currentPrizeEle.length) return;
		var data=getVirtualData();
		currentPrizeEle.children('.btn').html(Const.CHANGE_ING_TEXT);

		postLock=true;
		postData($(this).attr("data-ajaxUrl"),data,dlcCallback);
	});
	//虚拟物品兑换-取消重试
	$("body").on('click', '.J_cancel_virtual_retry', function(event) {
		if(!currentPrizeEle.length) return;
		currentPrizeEle.children('.btn').html(Const.CHANGE_TEXT);
		$(".J_dialog").addClass('hidden');
		$("body").unbind('touchmove');
	});

	$('body').on('click','.j_dow',function(e){
		var $gameId =  $(this).parents('.J_openDetailView')
			,games = getDowData($gameId)
			,status = $gameId.attr('data-status')
			,file = {list:[games]}
		;

		Com.removeLetter(file.list,'gameSize');
		Memory.list.push(games);
		e.stopPropagation();
		if(gameStatus[status][1])
			Com.onEvent(gameStatus[status][1],file);
		
	})

	//实物兑换 及重试
	$("body").on('click', '.J_entity_exchange,.J_entity_retry', function(event) {
		if(postLock) return ;

		var data=getEntityData();
		if(checkEntityDataValidate(data)){
			var btn=$(".J_entity_exchange");
			btn.html(Const.CHANGE_ING_TEXT);
			$(".J_error_tips").addClass('invisible');
			showInutError();
			postLock=true;
			postData(btn.attr("data-ajaxUrl"),data,entityCallback);
		}
	});
	//实物兑换 取消重试
	$("body").on('click', '.J_cancel_entity_retry', function(event) {
		$(".J_entity_exchange").html(Const.CHANGE_TEXT_NOW);
		$(".J_dialog").addClass('hidden');
		$("body").unbind('touchmove');
	});
})
function dlcCallback(data){
	if (data.success == 'false' || !data.success) {
		showFail();
		return;
	}
	data = data.data;
	if(data.isLogin=="false"){
		clearlogin();
	}else{
		var status=parseInt(data.status,10);
		if(status){
			showSuccess();
			setTimeout(function(){
				window.location.reload();
			},Const.MILLSECONDS);
		}else{
			showFail();
		}
	}
}
function setButStatus(ac,data){

	var statusList = Com.dowloadStatus({
			list:Memory.list
		})
		,list,$id,status,taskData = [],packageName,game,len
		,notGame = false; 
	;
	list = statusList.list;
	len = list.length;
	for(var i=0;i<len;i++){
		status = list[i].status;
		packageName = list[i].packageName;
		$id = $('[data-packagename="'+packageName+'"]');
		$id.attr('data-status',status);
		$id.find('input').val(gameStatus[status][0]);
	}
}
function entityCallback(data){
	showDialog($(".J_award"));
	if (data.success == 'false' || !data.success) {
		showFail();
		return;
	}
	data = data.data;
	if(data.isLogin=="false"){
		clearlogin();
	}else{
		var status=parseInt(data.status,10);
		if(status){
			showSuccess();
			setTimeout(function(){
				var search=location.search;
				var href=$(".J_entity_exchange").attr('data-redirecturl');
				location.href=href+search;
			},Const.MILLSECONDS);
		}else{
			showFail();
		}
	}
}
function getVirtualData(){
	var data={
		token:token,
		festivalId:festivalId,
		prizeType:currentPrizeEle.attr('data-prizeType'),
		prizeId:currentPrizeEle.attr('data-prizeId'),
		puuid:puuid,
		uname:uname,
		sp:sp,
		contact:'',
		phone:'',
		address:''
	};
	return data;
}

function getEntityData(){
	var ele=$(".J_entity_exchange");
	var data={
		token:token,
		festivalId:festivalId,
		prizeType:ele.attr('data-prizeType'),
		prizeId:ele.attr('data-prizeId'),
		puuid:puuid,
		uname:uname,
		sp:sp,
		contact:$("#contact").val().trim(),
		phone:$("#phone").val().trim(),
		address:$("#address").val().trim()
	};
	return data;
}
function checkEntityDataValidate(data){
	if(data.contact==''){
		showErrorTips(Const.CONSIGNEE_NULL);
		showInutError($("#contact"));
		return false;
	}
	if(data.phone==''){
		showErrorTips(Const.PHONE_NULL);
		showInutError($("#phone"));
		return false;
	}else if(!Const.PHONE_REG.test(data.phone)){
		showErrorTips(Const.PHONE_ERROR);
		showInutError($("#phone"));
		return false;
	}
	if(data.adress==''){
		showErrorTips(Const.ADDRESS_NULL);
		showInutError($("#address"));
		return false;
	}

	return true;
}
function showErrorTips(msg){
	var tipEle=$(".J_error_tips");
	tipEle.html(msg);
	tipEle.removeClass('invisible');
}
function showInutError(ele){
	$("#contact").removeClass('error');
	$("#phone").removeClass('error');
	$("#address").removeClass('error');
	if(ele!=undefined&&ele.length){
		ele.addClass('error');
	}
}

function showBtnContainer(){
	$(".J_ex_fail").addClass('hidden');
	$(".J_ex_success").addClass('hidden');
	$(".J_num_tips").removeClass('hidden');
	$(".J_btn_container").removeClass('hidden');
}
function showSuccess(){
	$(".J_ex_fail").addClass('hidden');
	$(".J_ex_success").removeClass('hidden');
	$(".J_num_tips").addClass('hidden');
	$(".J_btn_container").addClass('hidden');
}
function showFail(){
	$(".J_ex_fail").removeClass('hidden');
	$(".J_ex_success").addClass('hidden');
	$(".J_num_tips").addClass('hidden');
	$(".J_btn_container").addClass('hidden');
}
function setGameStatus(ac,data){
	if(ac=='gamehall.gameStatusChange'){
		setButStatus(ac,data);
	}
}
