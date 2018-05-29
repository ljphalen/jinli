window.Common = {
	onEvent:function(name,file){
		file = file || {};
		window.gamehall.onEvent('gamehall.'+name,JSON.stringify(file));
	},
	getVal:function(name,file){
		file = file || {};
		return window.gamehall.getValue('gamehall.'+name,JSON.stringify(file));
	},
	getUrlParam:function(name){
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
        var r = window.location.search.substr(1).match(reg);  //匹配目标参数
        if (r != null) return unescape(r[2]); return null; //返回参数值
	},
	openPage:function(config){
		config.succ = config.succ || function(){};
		config.error = config.error || function(){};
		if(config.file.newArgs){
			if(config.file.newArgs.param){
				var param = config.file.newArgs.param;
				param.contentId = param.contentId ||'';
				param.gameId = param.gameId ||'';
				param.source = param.source || '';
			}
		}
		window.gamehall.startlistactivity(config.succ,config.error,JSON.stringify(config.file));
	},
	getUrlParamS:function(names){
		var len=names.length
			,vals = []
		;
		for( var i=0;i<len;i++ ){
			vals.push(this.getUrlParam(names[i]));
		}
		return vals;
	},
	getDataStatus:function(data,name,val){
		for(var j=0,len=data.length;j<len;j++){
			if(data[j][name]==val)
				return data[j];
		}
		return{};
	},
	dowloadStatus:function(file){
		var statusList = this.getVal('getGameStatus',file);
		if(!statusList) return;
		return JSON.parse(statusList);
	},
	removeLetter:function(list,name){
		for( var i=0,le=list.length;i<le;i++ ){
			if(list[i][name])
				list[i][name] = list[i][name].replace(/[a-zA-Z]/g,"");
		}
	},
	getTwoTimeDifference:function(start,end){
		var date1=new Date()
			,date2=new Date()
		;
		
		date1.setTime(start*1000);
		date2.setTime(end * 1000);
		
		var date3=date2.getTime()-date1.getTime();  //时间差的毫秒数
		 
		//计算出相差天数
		var days=Math.floor(date3/(24*3600*1000));
		//计算出小时数
		var leave1=date3%(24*3600*1000);    //计算天数后剩余的毫秒数
		var hours=Math.floor(leave1/(3600*1000));//计算相差分钟数
		var leave2=leave1%(3600*1000);        //计算小时数后剩余的毫秒数
		var minutes=Math.floor(leave2/(60*1000));
		 
		//计算相差秒数
		var leave3=leave2%(60*1000);      //计算分钟数后剩余的毫秒数
		var seconds=Math.round(leave3/1000);

		return {
			day:days,
			hours:hours,
			minute:minutes,
			second:seconds
		}
	},
	isVersionBiggerorEqual:function(sourceVersion,targetVersion){
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
    },
	client:function(){
		var width = $(document).width()
			,height = $(document).height()
		;
		return {
			height:height
			,width:width
		}
	}
};