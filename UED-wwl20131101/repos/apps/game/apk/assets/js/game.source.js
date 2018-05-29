/*
 * Created with Sublime Text 3.
 * User: snowdrop
 * Date: 2014-04-10
 * Time: 15:45:18
 * Contact: dongandhuang@gmail.com
 */
;(function(iCat, $) {
    iCat.app('Gapk', function() {
        iCat.PathConfig();
        
        //客户端页面跳转的处理
        function skipPage(data, type,viewType,id,gameId) {
            if (!data) return;
            var infpage = data.split(','),
                version = GameApk.ApiVersion;
            if(!(window.gamehall||navigator.gamehall)){
                location.href = infpage[1];
                return;
            }
            switch(version){
                case 'v1':
                    openV1Page(infpage);
                    break;
                case 'v2':
                    openV2Page(infpage,type);
                    break;
                case 'v3':
                    openV3Page(type,viewType,id,gameId,infpage);
                    break;
            }
        }
        //v1(特征：phoneGap以及参数类型完全依赖data-infopage这个参数的length）
        function openV1Page(infpage){
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
            startActivity(cfg);
        }
        //v2 (部分含有phoneGap，跳转类型依赖与服务端传递的data-type)
        function openV2Page(infpage,type){
            var cfg={type:'',args:{}},len=infpage.length;
            if(type==0){//list
                var oldObj={title: infpage[0],url: infpage[1]};
                cfg.args=window.gamehall?{}:oldObj;//去除掉phoneGap之后就只用传newAgs就好了
                cfg.type='list';
                if(len==2){ //单标签list
                    cfg.args.newArgs={
                        title: infpage[0],
                        url: infpage[1]
                    };
                } else { //多标签list
                    var items = [];
                    for (var i = 0; i < len; i += 2) {
                        var obj = {};
                        if (i == 0) {
                            obj.title = '热门';
                        } else {
                            obj.title = infpage[i];
                        }
                        obj.viewType = 'Webview';
                        obj.url = infpage[i + 1];
                        items.push(obj);
                    }
                    cfg.args.newArgs={
                        title:infpage[0],
                        items:items
                    }
                }
            }
            else if(type==1){ //detail
                var oldObj={
                    title: "",url: "",gameid: "",downurl: "",packagename: "",filesize: "",
                    sdkinfo: "",resolution: ""
                };
                cfg.args=window.gamehall?{}:oldObj;
                cfg.type='detail';
                if(len==2){
                    cfg.args.newArgs={
                        title: infpage[0],
                        url: infpage[1],
                    }
                } else{
                    cfg.args.newArgs={
                        title: infpage[0],
                        url: infpage[1],
                        gameId: infpage[2]
                    }
                }
            }
            else if(type==2){//从礼包列表打开本地化的礼包详情页
                var oldObj={
                    title: "",url: "",gameid: "",downurl: "",packagename: "",filesize: "",
                    sdkinfo: "",resolution: ""
                };
                cfg.args=window.gamehall?{}:oldObj;
                cfg.type='detail';
                cfg.args.newArgs={
                    title: '礼包详情',
                    url: infpage[1],
                    viewType: 'GiftDetailView',
                    gameId: infpage[2],
                    giftId: infpage[3]
                }
            }
            else if(type==-1||type==3){ //外链
                cfg.type='browser';
                cfg.args={
                   url:type==-1?infpage[0]:infpage[1]
                };
            }
            startActivity(cfg);
        }
        //v3(去除phoneGap,部分页面都本地化了)
        function openV3Page(type,viewType,id,gameId,infpage){
            var cfg={}; cfg.args={ },source='';
            //only for 1.5.1 tongji
            //strategy
            if(viewType=='WebView'){
                source='strategy'+id;
            } else if(viewType=='GameDetailView'){//suggest field
                source='gltj'+id;
            }
            cfg.args.newArgs={
                viewType:viewType,
                param:{
                    contentId:id,
                    gameId:gameId,
                    url:viewType=='WebView'?infpage[1]:'',
                    title:viewType=='WebView'?infpage[0]:'',
                    source:source
                }
            }
            if(type==0){
                cfg.type='list';
            } else if(type==1||type==2){
                cfg.type='detail';
            }
            // alert(type,viewType,id,gameId);
            startActivity(cfg);
        }
        //根据类型打开客户端的页面 'list'、'detail'、'localbrowser'
        function startActivity(cfg){
            var sucCal=errCal = function() {},
                gamehall=window.gamehall?window.gamehall:navigator.gamehall;
                // alert(JSON.stringify(cfg.args));
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


        var o = {
            openMore: function() {
                $('.wrap').delegate('.J_openBtnWrap', 'click', function() {
                    $(this).toggleClass('open')
                        .siblings('p').toggleClass('hidden');
                });
                return this;
            },

            slidePic: function() {
                var slideWrap = $('#J_screenshot');
                var panel = $(".pic-wrap");
                if (!slideWrap[0]) return this;
                iCat.include(['/sys/lib/jquery/touchSwipe.js', './plugin/slidePic.js'], function() {
                    slideWrap.slidePic(
                        slideWrap.children().children().hasClass('J_arrow') ? {
                            slidePanel: '.pic-wrap',
                            slideItem: '.pic-wrap span',
                            specialWidth: true,
                            isTouch: false,
                        } : {
                            slidePanel: '.pic-wrap',
                            slideItem: '.pic-wrap span',
                            specialWidth: true,
                        }
                    );

                    o.displayImg();
                }, true);
                return this;
            },

            loadMore: function() {
                if (!$(".J_loadMore")[0]) return;
                var xhr, locked = false;
                $(window).scroll(function(event) {
                    var boxHeight = document.body.clientHeight,
                        visibleHeight = document.documentElement.clientHeight,
                        boxScrollTop = document.body.scrollTop;
                    var btnMore = $(".J_loadMore"),
                        hn = btnMore.attr('data-hasnext');
                    var curpage = parseInt(btnMore.attr('data-curpage'));
                    var isStrategy = false;
                    if (hn == 0 || hn == 'false') {
                        $(window).unbind('scroll');
                    } else {
                        if (locked == true) return;
                        if (Math.abs(boxHeight - visibleHeight) <= boxScrollTop ) {
                            btnMore.show();
                            locked = true;
                            $.ajax({
                                type: "POST",
                                url: btnMore.attr('data-ajaxUrl'),
                                data: {
                                    page: curpage + 1,
                                    token: token
                                },
                                dataType: 'json',
                                success: function(data) {
                                    locked = false;
                                    isNext = data.data.hasnext;
                                    btnMore.attr('data-hasnext', data.data.hasnext).attr('data-curpage', data.data.curpage);
                                    var pNode = $('.J_gameItem ul'),
                                        s = '',
                                        data_type = "";
                                    if (GameApk.ApiVersion != "v1") {
                                        data_type = 'data-type="{data-type}" data-gameId="{game_id}" data-id="{id}" data-viewType="{viewType}"';
                                    }
                                    strTemp = '<li>\
												<a ' + data_type + ' data-infpage="{profile}">\
													<span class="icon"><img src="' + GameApk.blankPic + '" data-src="{img}"></span>\
													<span class="desc">\
														<em>{name}</em>\
														<p><em>大小：{size}</em><em class="tips"></em></p>\
														<b>{resume}</b>\
													</span>\
												</a>\
											</li>',
                                    /* template-data merge */
                                    tdMerge = function(t, d, r) {
                                        //评测列表无img则不显示
                                        if (r) {
                                            if (d.img && d.img != "") {
                                                t = t.replace('<span class="pic"><img data-replace></span>', '<span class="pic"><img src="' + GameApk.blankPic + '" data-src="{img}"></span>');
                                            } else {
                                                t = t.replace('<span class="pic"><img data-replace></span>', '');
                                            }
                                        }
                                        if (!iCat.isString(t) || !/\{|\}/g.test(t)) return false;
                                        var phs = t.match(/(\{[a-zA-Z]+-[a-zA-Z]+\})|(\{[a-zA-Z]+[a-zA-Z]+\})|(\{[a-zA-Z]+_[a-zA-Z]+\})/g); //fixed bug 判断{字符-字符}
                                        if (!phs.length) return false;
                                        iCat.foreach(phs, function() {
                                            var key = this.replace(/\{|\}/g, ''),
                                                regKey = new RegExp('\{' + key + '\}'),
                                                val = d[key];
                                            t = t.replace(regKey, val !== undefined ? val : (r ? '{' + key + '}' : ''));
                                        });
                                        //游戏列表上的礼包和评测信息
                                        if (GameApk.ApiVersion != "v1" && ((d.attach && d.attach != "") || (d.device && d.device != 0))) {
                                            var tempStr = '<em class="tips">';
                                            if (d.attach) {
                                                var attachs = d.attach.split(',');
                                                for (var i = 0; i < attachs.length; i++) {
                                                    if (attachs[i] == '礼') {
                                                        tempStr += '<span class="gift"></span>';
                                                    }
                                                    if (attachs[i] == '评') {
                                                        tempStr += '<span class="comment"></span>';
                                                    }
                                                }
                                                if (d.device == 1) {
                                                    tempStr += '<span class="grip"></span>';
                                                }
                                            } else if (d.device == 1) {
                                                tempStr += '<span class="grip"></span>';
                                            }
                                            tempStr += "</em>";
                                            t = t.replace('<em class="tips"></em>', tempStr);
                                        }
                                        return t;
                                    };
                                    if ($('.J_giftItem')[0]) {
                                        pNode = $('.J_giftItem ul');
                                        strTemp = '<li>\
													<a ' + data_type + ' data-infpage="{data-infpage}">\
														<span class="icon"><img src="' + GameApk.blankPic + '" data-src="{img}"></span>\
														<span class="desc">\
															<em>{title}</em>\
															<p><em >{giftNum}</em></p>\
														</span>\
													</a>\
												</li>';
                                    } else if ($('.J_stratgyItem')[0]) {
                                        isStrategy = true;
                                        pNode = $('.J_stratgyItem ul');
                                        strTemp = '<li>\
													<a ' + data_type + ' data-infpage="{data-infpage}">\
														<div class="intro">\
															<span class="pic"><img data-replace></span><span class="title">\
																<b>{title}</b>\
																<em>{create_time}</em>\
															</span>\
														</div>\
														<div class="content">\
															<span class="desc">{resume}</span>\
														</div>\
													</a>\
												</li>';
                                    }
                                    iCat.foreach(data.data.list, function(v, i) {
                                        s += tdMerge(strTemp, v, isStrategy);
                                    });
                                    pNode.append(s);
                                    o.lazyLoad(document.body, 100);
                                    if (data.data.hasnext == 0 || data.data.hasnext == 'false') {
                                        btnMore.html('<span class="bottom">到底了，去其它页面看看吧</span>').show();
                                        $(window).unbind('scroll');
                                    } else {
                                        btnMore.hide();
                                    }
                                },
                                error: function() {
                                    locked = false;
                                }
                            })
                        }
                    }
                });
                return this;
            },

            openPage: function(argument) {
                //1.5.3之前的旧接口
                $('body').delegate('a[data-infpage]', 'click', function() {
                    var data = $(this).attr('data-infpage') || '',
                        dataType=parseInt($(this).attr('data-type'), 10),
                        viewType=$(this).attr('data-viewType')||'',
                        id=$(this).attr('data-id'),gameId=$(this).attr('data-gameId');
                    if (GameApk.ApiVersion == "v1") {
                        skipPage(data);
                    } else {
                        skipPage(data,dataType,viewType,id,gameId);
                    }
                });

                //1.5.3之后调用客户端的新接口
                $('body').delegate('a[data-action]','click',function(){
                    if(!window.gamehall) return;
                    var action=$(this).attr("data-action"),
                        data={};
                    if(action=='gamehall.forum'){//进入论坛
                        data.url=$(this).attr("data-href");
                        data.needLogin=true;
                    }
                    window.gamehall.onEvent(action,JSON.stringify(data));
                    
                });
            },

            displayImg: function() {
                var screenshot = $("#J_screenshot");
                if (!screenshot[0]) return this;
                o.startimgActivity(screenshot,true);
            },
             //displayImg without slidpic and touchswipe
            bigImg:function(){
                var screenshot = $("#J_screenshot_off");
                if (!screenshot[0]) return this;
                o.startimgActivity(screenshot,false);
            },
            startimgActivity:function(container,isTouch){
                var imgs=container.find('img'),
                    srcArr = [];
                imgs.each(function(index) {
                    srcArr[index] = $(this).attr('data-bigpic');
                    $(this).attr('data-index', index);
                });
                var clickHandler=function(){
                    var currentIndex = $(this).attr('data-index'),
                        data = [];
                    data = data.concat(currentIndex, srcArr);
                    // alert(data.join('|'));
                    var gamehall = (window.gamehall) ? window.gamehall : navigator.gamehall;
                    if (gamehall) {
                        gamehall.startimagescanactivity(
                            function() {
                                iCat.log('正在为您跳转页面');
                            },
                            function() {}, JSON.stringify({
                                url: data.join('|')
                            })
                        );
                    }
                };
                if(isTouch){
                    imgs.swipe({
                        click:clickHandler
                    })
                } else{
                    imgs.click(clickHandler);
                }
            },

            lazyLoad: function(pNode, t) {
                var imgs = $(pNode).find('img[src$="blank.gif"],img[src$="blank1.gif"]'),
                    _fn = function(o) {
                        var src = o.getAttribute('data-src');
                        iCat.__IMAGE_CACHE = iCat.__IMAGE_CACHE || {};
                        if (!src) return;

                        if (!iCat.__IMAGE_CACHE[src]) {
                            var oImg = new Image();
                            oImg.src = src;
                            oImg.onload = function() {
                                o.src = src;
                                iCat.__IMAGE_CACHE[src] = true;
                                $(o).attr('data-src', src);
                                oImg = null;
                            };
                        } else {
                            o.src = src;
                            $(o).attr('data-src', src);
                        }
                    };

                iCat.foreach(imgs, function(i, v) {
                    t ? setTimeout(function() {
                        _fn(i);
                    }, v * t) : _fn(v);
                });
            },
            openclose: function(){
                var J_expand = $(".J_expand"),
                    arrowDown=$(J_expand.children('p')[0]),
                    arrowUp=$(J_expand.children('p')[1]),
                    J_content=$(".J_content");
                J_expand.bind('click',function(){
                    if(arrowDown.hasClass('hidden')){
                        arrowDown.removeClass('hidden');
                        arrowUp.addClass('hidden');
                        J_content.removeClass('h-auto');
                    } else{
                        arrowUp.removeClass('hidden');
                        arrowDown.addClass('hidden');
                        J_content.addClass('h-auto');
                    }
                });
            }

        };
        return {
            init: function() {
                o.slidePic();
                o.openMore().loadMore();
                o.openPage();
                o.bigImg();
                o.lazyLoad(document.body, 100);
                o.openclose();
            }
        };
    });
})(ICAT, jQuery);
