var action={
    network:'gamehall.hasnetwork',
    alert:'gamehall.alert',
    listener:'gamehall.gamestatus.listener',
    getGameStatus:'gamehall.getGameStatus',
    startDownLoad:'gamehall.startDownload',
    pauseDownLoad:'gamehall.pauseDownload',
    resumeDownLoad:'gamehall.resumeDownload',
    openGame:'gamehall.openGame',
    upgradeGame:'gamehall.upgrade',
    installGame:'gamehall.installGame',
    statusChange:'gamehall.gameStatusChange',
    finish:'gamehall.finish',
    startRewardDownload:'gamehall.startRewardDownload',
};
var gameStatus={
    100:'J_download_start,下载,'+action.startDownLoad,
    102:'J_download_resume,继续,'+action.resumeDownLoad,
    101:'J_download_pause,暂停,'+action.pauseDownLoad,
    103:'J_download_installing,安装中,',
    104:'J_download_install,安装,'+action.installGame,
    105:'J_download_open,打开,'+action.openGame,
    106:'J_download_upgrade,升级,'+action.startDownLoad,
    107:'J_download_fail,重试,'+action.resumeDownLoad,
    108:'hidden,,',//隐藏下载按钮
    109:'J_download_reward,有奖下载,'+action.startRewardDownload,
    110:'J_download_rewardUpgrade,有奖升级,'+action.startRewardDownload,
    111:'J_remove,,',//过滤整个游戏 显示在页面
}

var subject={
    gameList:{},
    lazyload:function(pNode, t){
        var imgs = $(pNode).find('img[src$="blank.gif"]'),
        _fn = function(o) {
            var src = o.getAttribute('data-src');
            var obj={}
            obj.__IMAGE_CACHE = obj.__IMAGE_CACHE || {};
            if (!src) return;

            if (!obj.__IMAGE_CACHE[src]) {
                var oImg = new Image();
                oImg.src = src;
                oImg.onload = function() {
                    o.src = src;
                    obj.__IMAGE_CACHE[src] = true;
                    $(o).attr('data-src', src);
                    oImg = null;
                };
            } else {
                o.src = src;
                $(o).attr('data-src', src);
            }
        };
    imgs.each(function(i, v) {
         t ? setTimeout(function() {
            _fn(v);
        }, i * t) : _fn(v);
    });
    },
    getUrlParam:function(name){
         var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
        var r = window.location.search.substr(1).match(reg);  //匹配目标参数
        if (r != null) return unescape(r[2]); return null; //返回参数值
    },
    skipPage:function(){
        //打开专题详情
        $('body').delegate('.J_subjectDetail', 'click', function() {
            var type=$(this).attr('data-type'),
                id=$(this).attr('data-subjectid'),
                url=$(this).attr('data-url'),
                title=$(this).attr('data-title'),
                source=$(this).attr('data-source');
                viewType=type=='custom'?'WebView':'TopicDetailView';
            subject.openPage(0,viewType,id,'',title,url,source);
            if(type=='custom'){
                subject.onEventAction(action.finish,{});
            }
        });

        //跳转到详情页
        $('body').delegate('.J_gotoDetail','click',function(){
            var container=$(this).siblings('.J_btn'),
                source=container.attr('data-source'),
                id=subject.getUrlParam('id'),
                title=container.attr('data-name'),
                gameId=container.attr('data-gameId');
            subject.openPage(1,'GameDetailView',id,gameId,title,'',source);
        });
    },
    openPage:function(type,viewType,id,gameId,title,url,source){
        var cfg={}; cfg.args={ };
        if(viewType=='GameDetailView'){
            if(source==''){
                source='gltj'+id;
            }
        } 
        cfg.args.newArgs={
            viewType:viewType,
            param:{
                contentId:id,
                gameId:gameId,
                url:viewType=='WebView'?url:'',
                title:viewType=='WebView'?title:'',
                source:source
            }
        }
        if(type==0){
            cfg.type='list';
        } else if(type==1||type==2){
            cfg.type='detail';
        } else if(type ==3){
            cfg={};
            cfg.type='browser';
            cfg.args={
               url:type==-1?title:url
            };
        }
        this.startActivity(cfg);
    },
    startActivity:function(cfg){
        var sucCal=errCal = function() {},
            gamehall=window.gamehall?window.gamehall:navigator.gamehall,
            data=JSON.stringify(cfg.args);
        switch(cfg.type){
            case 'list':
                gamehall.startlistactivity(sucCal,errCal,data);
                break;
            case 'detail':
                gamehall.startdetailsactivity(sucCal,errCal,data);
                break;
            case 'browser':
                gamehall.startlocalbrowser(sucCal,errCal,data);
                break;
        }
    },
    onEventAction:function(action,data){
        if(window.gamehall){
            window.gamehall.onEvent(action,JSON.stringify(data));
        }
    },
    getValueAction:function(action,data){
        if(window.gamehall){
            var ret=window.gamehall.getValue(action,JSON.stringify(data));
            ret=JSON.parse(ret);
            return ret;
        } 
    },
    addListener:function(){
        this.onEventAction(action.listener,{});

        $('body').delegate('.J_btn[data-action]','click',function(){
            var action=$(this).attr('data-action'),
                gameId=$(this).attr('data-gameId'),obj={};
                obj.list=[];
            if(action!=undefined&&action!=''){
               var list=subject.gameList.list;
               for(var i=0,len=list.length;i<len;i++){
                    if(gameId==list[i].gameId){
                        obj.list.push(list[i]);
                        break;
                    }
               }
               subject.onEventAction(action,obj);
            }
         });
    },
    getGameInfoFromPage:function(){
        var btnList=$(".J_btn"),
            list=[],data={};
        btnList.each(function(index, el) {

            var gameId=$(this).attr('data-gameId'),
                gameName=$(this).attr('data-gameName'),
                source=$(this).attr('data-source'),
                downurl=$(this).attr('data-downUrl'),
                packageName=$(this).attr('data-packageName'),
                gameSize=$(this).attr('data-gameSize'),
                iconUrl=$(this).attr('data-iconUrl'),
                freedl=$(this).attr('data-freedl');
            list[index]={};
            list[index].gameId=gameId;
            list[index].gameName=gameName;
            list[index].source=source;
            list[index].downUrl=downurl;
            list[index].packageName=packageName;
            list[index].gameSize=gameSize;
            list[index].iconUrl=iconUrl;
            list[index].freedl=freedl;
            //for 有奖下载
            if($(this).attr('data-reward')){
                list[index].reward={};
                list[index].reward.rewardTypeCount=$(this).attr('data-rewardTypeCount');
                list[index].reward.remindDes=$(this).attr('data-remindDes');
                list[index].reward.rewardStatisId=$(this).attr('data-rewardStatisId');
                console.log(list[index]);
            }
        });
        
        data.list=list;
        this.gameList=data;
    },

    getGameStatusFromClient:function(){
        var gameStatusList=this.getValueAction(action.getGameStatus,this.gameList);
        this.setGameStatus(gameStatusList);
    },
    setGameStatus:function(statusList){
        if(!statusList) return;
        var list=statusList.list;
        for(var i=0,len=list.length;i<len;i++){
            var element=$(".J_btn[data-packageName='"+list[i].packageName+"']");
            if(!element.length) continue;

            var statusConfig=gameStatus[list[i].status].split(',');
            var className=statusConfig[0],
                text=statusConfig[1],
                action=statusConfig[2];
            if(className=='J_remove'){//将游戏直接过滤
                element.parent('li.single').remove();//删除单个游戏
                var parent=element.parent('div.game-list');//删除多个游戏
                if(parent.siblings('div.game-list').length==0){
                    parent.parent('li.multiple').remove();//如果只有一个元素，除了将当前游戏删除还要删除父元素
                }else{
                    parent.remove();
                }
                
            }else{
                element.attr('class', 'J_btn btn '+className);
                element.html(text);
                if(action){
                    element.attr('data-action',action);
                }else{
                    element.removeAttr('data-action');
                }
            }
        }
    },
    
    
    init:function(){
        this.addListener();
        this.lazyload(document.body,100);
        this.skipPage();
        this.getGameInfoFromPage();
        this.getGameStatusFromClient();
    }
}

$(function() {
    subject.init();
});