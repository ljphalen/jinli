var activity = {
	version:null,
	init: function() {
		this.version=this.getVersion();
		if(this.version=='v1'||this.version=='v2'){//加载phoneGap
			var src="http://assets.gionee.com/apps/game/apkv1/assets/js/plugin/cordova-2.0.0.js";
			this.getScript(src,activity.bindEvent);
		}else{
			activity.bindEvent();
		}
		
	},
	getScript:function(src,func){
		var script = document.createElement('script');
        script.async = "async";
        script.src = src;
        if (func) {
           script.onload = func;
        }
        document.getElementsByTagName("head")[0].appendChild(script);
	},
	bindEvent:function(){
		var ele=document.querySelectorAll('[data-infpage]');

		for (var i=0,len=ele.length;i<len;i++){
			ele[i].addEventListener('click',function(){
				var infpage=this.getAttribute('data-infpage').split(','),
					len=infpage.length;
				if(activity.version==null||!(window.gamehall||navigator.gamehall)){
					if(len>2){//h5上则打开h5对应的游戏大厅的下载地址
						location.href=infpage[3];
					}else{
						location.href=infpage[1];
					}
				}else{
					if(activity.version=='v4'){
						if(infpage.length>2){
							var data=JSON.stringify({});
							var ret=window.gamehall.getValue('gamehall.islogin',data);
							ret=JSON.parse(ret);
							if(ret==false){
								window.gamehall.onEvent('gamehall.finish',data);
								window.gamehall.onEvent('gamehall.clearlogin',data);
								window.gamehall.onEvent('gamehall.account',data);
							}else{
								var url=this.getAttribute('data-prize-href');
								url+="&sp="+activity.getUrlParam('sp')+"&uname="+activity.getUrlParam('uname')+"&puuid="+activity.getUrlParam('puuid');
								var newInfpage=("积分抽奖,"+url).split(',');
								activity.openV3Page(newInfpage);
							}
							
						}else{
							activity.openV3Page(infpage);
						}
					}else{
						activity.openPage(infpage);
					}
					
				}
			})
		};
		
	},
	getUrlParam:function(name){
		 var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
        var r = window.location.search.substr(1).match(reg);  //匹配目标参数
        if (r != null) return unescape(r[2]); return null; //返回参数值
	},
	openPage:function(infpage){
		switch(this.version){
	        case 'v1':
	            activity.openV1Page(infpage);
	            break;
	        case 'v2':
	            activity.openV2Page(infpage);
	            break;
	        case 'v3':
	            activity.openV3Page(infpage);
	            break;
	    }
	},
	openV1Page:function(infpage){
		var cfg={},len=infpage.length;
	    if(len==2){
	        cfg={
	            type:'list',
	            args:{
	                title: infpage[0],
	                url: infpage[1]
	           }
	        };
	    } else{
	        cfg={
	            type:'detail',
	            args:{
	                title: infpage[0],
	                url: infpage[1],
	                gameid: infpage[2],
	                downurl: infpage[3],
	                packagename: infpage[4],
	                filesize: infpage[5],
	                sdkinfo: infpage[6],
	                resolution: infpage[7]
	            }
	        }
	    }
    	activity.startActivity(cfg);
	},
	openV2Page:function(infpage){
		var cfg={type:'',args:{}},len=infpage.length;
	    if(len>2){ //detail
	        var oldObj={
	            title: "",url: "",gameid: "",downurl: "",packagename: "",filesize: "",
	            sdkinfo: "",resolution: ""
	        };
	        cfg.args=window.gamehall?{}:oldObj;
	        cfg.type='detail';
            cfg.args.newArgs={
                title: infpage[0],
                url: infpage[1],
                gameId: infpage[2]
            }
	    }
	    else{ //外链
	        cfg.type='browser';
	        cfg.args={
	           url:infpage[1]
	        };
	    }
	    activity.startActivity(cfg);
	},
	openV3Page:function(infpage){
		var cfg={};cfg.args={ };
		var len=infpage.length,gameId,
	    	viewType,source,packageName;
	    if(len>2){//跳转到详情
	    	cfg.type='detail';
	    	viewType='GameDetailView';
	    	source='gltj'+infpage[2];
	    	packageName=infpage[4];
	    	gameId=infpage[2];
	    }else{//打开webview页面
	    	cfg.type='list';
	    	viewType='WebView';
	    }

	    cfg.args.newArgs={
	        viewType:viewType,
	        param:{
	            gameId:gameId,
	            url:viewType=='WebView'?infpage[1]:'',
	            title:viewType=='WebView'?infpage[0]:'',
	            source:source,
	            packageName:packageName
	        }
	    }
	    activity.startActivity(cfg);
	},
	getVersion:function(){
		var spParam=activity.getUrlParam('sp');
		if(spParam==null){
			return null;
		} else{
            if (spParam.split('_').length < 2 || spParam.split('_')[1] == ''){
            	return 'v1';
            } 
            else {
                var vNum = spParam.split('_')[1].split('.');
                //大于或等于1.5.5（详情页本地化了）
                if(vNum[0] > 1||(vNum[0] == 1 && vNum[1] > 5)||(vNum[0] == 1 && vNum[1] == 5 && vNum[2] >= 5)){
                    return 'v4'
                }
                //大于或等于1.5.1（此版本逐渐本地化了）
                if(vNum[0] > 1||(vNum[0] == 1 && vNum[1] > 5)||(vNum[0] == 1 && vNum[1] == 5 && vNum[2] >= 1)){
                    return 'v3'
                }
                //大于1.4.7的版本为V2，小于等于则为v1
                if (vNum[0] > 1 || (vNum[0] == 1 && vNum[1] > 4) || (vNum[0] == 1 && vNum[1] == 4 && vNum[2] > 7)) {
                    return 'v2';
                }
                return 'v1';
            }
		}
	},
	startActivity:function(cfg){
	    var sucCal=errCal = function() {},
	        gamehall=window.gamehall?window.gamehall:navigator.gamehall;
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
};
window.onload=function(){
	activity.init(); 
}
		


