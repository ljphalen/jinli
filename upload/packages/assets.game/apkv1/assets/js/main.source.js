;(function(iCat){
    iCat.app('GameApk',function(){
        //获取版本分支
        function getVersion() {
            var search = window.location.search,
                spParam = "";
            if (!search) return {
                version: 'v1',
                sp: spParam,
                branch:'v1'
            };
            else {
                search = search.substring(1); //截除问号
                var items = search.split('&');
                for (var i = 0; i < items.length; i++) {
                    if (!items[i]) {
                        continue;
                    }
                    if (items[i].split('=')[0] == 'sp') {
                        spParam = items[i].split('=')[1]; //取得sp参数
                        break;
                    };
                }
            }
            if (!spParam) return {
                version: 'v1',
                sp: spParam,
                branch:'v1'
            };
            else if (spParam.split('_').length < 2 || spParam.split('_')[1] == '') return {
                version: 'v1',
                sp: spParam,
                branch:'v1'
            };
            else {
                var vNum = spParam.split('_')[1].split('.');
                //又增加了新的分支客户端1.5.1（此版本逐渐本地化了）
                if(vNum[0] > 1||(vNum[0] == 1 && vNum[1] > 5)||(vNum[0] == 1 && vNum[1] == 5 && vNum[2] >= 1)){
                    if(vNum[0] == 1 && vNum[1] == 5 && vNum[2] == 1){
                        return {
                            version:'v3',
                            sp:spParam,
                            branch:'v3'
                        };
                    } else { 
                        if(vNum[2]>=5||(vNum[0] > 1||(vNum[0] == 1 && vNum[1] > 5))){//1.5.5版本及以上，去掉shadow
                            return {
                                version:'v3',
                                sp:spParam,
                                branch:'v3_2'
                            }
                        }
                        //客户端版本大于等于1.5.2时，视觉又做了新的修改，但是低于1.5.2的版本不做此修改
                        return {
                            version:'v3',
                            sp:spParam,
                            branch:'v3_1'
                        }
                    }
                }
                //大于1.4.7的版本为V2，小于等于则为v1
                if (vNum[0] > 1 || (vNum[0] == 1 && vNum[1] > 4) || (vNum[0] == 1 && vNum[1] == 4 && vNum[2] > 7)) {
                    return {
                        version: 'v2',
                        sp: spParam,
                        branch:'v2'
                    };
                }
                return {
                    version: 'v1',
                    sp: spParam,
                    branch:'v1'
                };
            }
        }
        return {
            spParam: getVersion().sp,
            ApiVersion: getVersion().version, //v1 页面调用旧版接口及旧版视觉
            branch:getVersion().branch,
            blankPic: iCat.PathConfig.picPath + 'blank.gif',
            loadingPic: iCat.PathConfig.picPath + 'loading_img.gif',

            init:function(){
                /*iCat.DebugMode=true;
                iCat.DemoMode=true;*/
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