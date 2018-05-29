;(function(iCat){
    iCat.app('GameApk',function(){

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
        function getApiVersionAndBranch(){
            var apiVersion='v1',
                branch='v1';
            var currentVersion=getClientVersion();
            
            if(isVersionBiggerorEqual(currentVersion,'1.5.1')){//1.5.1 客户端接口本地化
                apiVersion='v3';
                branch='v3';
                if(isVersionBiggerorEqual(currentVersion,'1.5.2')){//1.5.2  网页实现的列表页面样式修改
                    branch='v3_1';
                }
                if(isVersionBiggerorEqual(currentVersion,'1.5.5')){//1.5.5  去掉shadow-网页实现的列表页面样式修改
                    branch="v3_2";
                }
                if(isVersionBiggerorEqual(currentVersion,'1.5.9')){//1.5.9   专题，活动样式修改
                    branch="v3_3";
                }
            }else{
                if(isVersionBiggerorEqual(currentVersion,'1.4.8')){//1.4.7以上 phoneGap接口调整
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
        
        function isVersionBiggerorEqual(sourceVersion,targetVersion){
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
        return {
            spParam: getUrlParam('sp'),
            clientVersion:getClientVersion(),
            ApiVersion: getApiVersionAndBranch().version, //v1 页面调用旧版接口及旧版视觉
            branch:getApiVersionAndBranch().branch,
            loadingClass:'loading',//吃豆人动画
            blankPic: iCat.PathConfig.picPath + 'blank.gif',
            loadingPic: iCat.PathConfig.picPath + 'loading_img.gif',

            init:function(){
                iCat.DebugMode=true;
                iCat.DemoMode=true;
                var loadAssetsArr = window.gamehall ? 
                                ['jQuery', './game.js'] : 
                                ['jQuery', './plugin/cordova-2.0.0.js', './game.js'];
                if(document.querySelector('body[data-pagerole=body]')){//mvc page
                    iCat.require({
                        modName:'appmvc',isCombo: !(iCat.DemoMode || iCat.DebugMode),
                        callback:function(){
                            var c = new GameApk.controller('mc');
                            iCat.include(loadAssetsArr, function($) {
                                c.bindEvents();
                                Gapk.init();
                            }, true, !(iCat.DebugMode || iCat.DemoMode));
                        }
                    });
                } else{
                    iCat.include(loadAssetsArr,function(){
                        Gapk.init();
                    },true,!(iCat.DemoMode ||iCat.DebugMode));
                }
            }
        };
    });
    GameApk.init();
})(ICAT);