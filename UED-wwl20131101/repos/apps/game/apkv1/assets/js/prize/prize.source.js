var lottery={
    index:-1,    //当前转动到哪个位置
    count:0,    //总共有多少个位置
    timer:0,    //setTimeout的ID，用clearTimeout清除
    speed:180,  //初始转动速度
    times:0,    //转动次数
    cycle:30,   //转动基本次数：即至少需要转动多少次再进入抽奖环节
    prize:-1,   //中奖位置
    callBack:function(){},//中奖之后的回调函数
    init:function(id){
        if ($("#"+id).find("li.prize-unit").length>0) {
            $lottery = $("#"+id);
            $units = $lottery.find(".prize-unit");
            this.obj = $lottery;
            this.count = $units.length;
        };
    },
    roll:function(){
        var index = this.index;
        var count = this.count;
        var lottery = this.obj;
        $(lottery).find("#prize-grid"+index).removeClass("active");
        index += 1;
        if (index>count-1) {
            index = 0;
        };
        $(lottery).find("#prize-grid"+index).addClass("active");
        this.index=index;
        return false;
    },
    setPrize:function(index){
        this.prize=index;
    },
    getPrize:function(){
        if(this.prize==-1){
            return undefined;
        } else{
            return this.prize;
        }
    },
    stop:function(){
        this.callBack();
    },
    reset:function(){
    	clearTimeout(this.timer);
        $("#prize-btn").removeClass('prize-btn-active');
        $("#prize_wrap").find("#prize-grid"+this.index).removeClass("active");
        click=false;
        this.speed=180;
        this.index=-1;
        this.prize=-1;
    }
};

function roll(){
    lottery.times += 1;
    lottery.roll();
    //确定了中奖目标索引后再让其转动到对应位置
    if (lottery.times > lottery.cycle+10 && lottery.prize==lottery.index) {
        clearTimeout(lottery.timer);
        lottery.stop();
        lottery.prize=-1;
        lottery.times=0;
        click=false;
    }else{
        if(lottery.times==lottery.cycle) {
            if(lottery.getPrize()==undefined){
                lottery.cycle+=10;
            }
        }
        // console.log(lottery.times+'^^^^^^'+lottery.speed+'^^^^^^^'+lottery.prize);
        lottery.timer = setTimeout(roll,lottery.speed);
    }
    return false;
}
var click=false;

;(function(){
    var enumMsg = {
        networkError:'网络异常，请稍候重试',
        notLogin:'未登录，请先登录',
        underPoints:'您的积分不足',
        nameError:'收货人不能为空',
        phoneError:'请输入合法的联系电话',
        addressError:'收货地址不能为空',
        postSuccess:'提交成功',
        timeOut:'请求超时',
        postError:'请求异常，请刷新重试'
    };
    var action={
        network:'gamehall.hasnetwork',
        alert:'gamehall.alert',
        logout:'gamehall.clearlogin',
        finish:'gamehall.finish',
        islogin:'gamehall.islogin',
        getPoint:'gamehall.daily.task',
        login:'gamehall.account',
        statis:'gamehall.sendstatis',
        boradcast:'gamehall.money.change',
        getServerId:'gamehall.getserverid',
    }
    var pointPrize = {
        intervalId:null,
        getUrlParam:function(name){
             var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
             var r = window.location.search.substr(1).match(reg);  //匹配目标参数
             if (r != null) return unescape(r[2]); return ''; //返回参数值
        },
        getServerId:function(){
            var version=pointPrize.getUrlParam('sp').split('_')[1];
            var serverId='';
            if(pointPrize.isVersionBiggerorEqual(version,'1.5.7')){//1.5.7增加的取serverId的接口
                serverId=pointPrize.getValueAction(action.getServerId,{url:location.href}).serverId;
            }
            return serverId;

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
        
        initPrizeScroll:function(){
            lottery.init('prize_wrap');//初始化九宫格抽奖

            var search=location.search,
                arr=$("#acoin").attr('data-infpage').split(','),
                aCoinUrl=arr[1]+search;
            $("#acoin").attr('data-infpage',arr[0]+","+aCoinUrl);

            var uname=pointPrize.getUrlParam('uname'),
                puuid=pointPrize.getUrlParam('puuid'),
                sp=pointPrize.getUrlParam('sp'),
                spArr=sp.split('_'),
                imei=spArr[spArr.length-1];

            //领积分
            $(".btn-points").click(function(){
                pointPrize.onEventAction(action.getPoint,{});
            })

            //中奖按钮处理逻辑
            $("#prize-btn").click(function(){
                if(click||$(this).hasClass('prize-btn-active')) return;
                //3.积分不足
                var pointsBtn=$("#prize-totalPoints"),
                    consumePoints=parseInt($("#prize-consumePoints").html(),10);
                if(parseInt(pointsBtn.html(),10)<consumePoints){
                    pointPrize.onEventAction(action.alert,{alertStr:enumMsg.underPoints});
                    return;
                }

                //正常抽奖
                roll();
                click=true;
                $(this).addClass('prize-btn-active');

                var obj={
                    action:'lottery',
                    object:'lottery'+prizeId//活动ID
                }
                pointPrize.onEventAction(action.statis,obj);//统计
                var ajaxUrl=$(this).attr('data-ajaxUrl');
                
                $.ajax({
                    type:"POST",
                    url:ajaxUrl,
                    dataType: 'json',
                    timeout:5000,
                    data:{
                        token: token,
                        puuid:puuid,
                        uname:uname,
                        prizeId:prizeId,
                        serverId:pointPrize.getServerId(),
                        imei:imei,
                        sp:sp
                    },
                    success:function(data){
                        if(!data||(typeof data=='string')||(!data.success)){//非法请求
                            lottery.reset();
                            var msg=enumMsg.postError;
                            if(data&&data.msg){
                                msg=data.msg;
                            }
                            pointPrize.onEventAction(action.alert,{alertStr:msg});
                            return;                     
                        }
                        var d=data.data;
                        if(d.isLogin!=undefined&&d.isLogin==false){//未登录
                            lottery.reset();
                            pointPrize.onEventAction(action.finish,{});
                            pointPrize.onEventAction(action.logout,{});
                            pointPrize.onEventAction(action.login,{});
                            return;
                        }
                        
                        if(d.underPoints&&d.underPoints==false){//积分不足
                            lottery.reset();
                            pointPrize.onEventAction(action.alert,{alertStr:enumMsg.underPoints});
                            return;
                        }
                        if(d.prizeType!=undefined){
                            pointPrize.onEventAction(action.boradcast,{});
                            var statObj={
                                action:'visit',
                                object:'lotterypop',//奖项ID
                                intersrc:'prize'+d.configId
                            },popDialogID;
                            if(d.prizeType==0){//未中奖
                                statObj={
                                    action:'visit',
                                    object:'lotterypop',//奖项ID
                                    intersrc:'lose'
                                };
                                popDialogID='.J_noPrize';

                            } else if(d.prizeType==1){//实物
                                popDialogID='.J_entity';
                                $("#name").val(d.receivingName);
                                $("#phone").val(d.receivingPhone);
                                $("#address").val(d.receivingAddress);
                                $("#submit").attr('data-ticket',d.ticket);
                                $("#submit").attr('data-logId',d.logId);

                            } else if(d.prizeType==2){//A券
                                popDialogID='.J_acoin';
                                $("#indate").html(d.indate+'天');

                            } else if(d.prizeType==3){//积分
                                popDialogID='.J_points';
                            }
                            var dialog=$(popDialogID);
                            dialog.find('img').attr('src',d.prizeImg);
                            dialog.find('#prizeName').html(d.prizeName);

                            //设置九宫格以决定最后中奖状态
                           lottery.setPrize(parseInt(d.prizeIndex,10)-1);
                            //弹框提示
                            lottery.callBack=function(){
                                pointPrize.getBillboard();
                                pointsBtn.html(d.totalPoints);
                                dialogShow(dialog);
                                pointPrize.onEventAction(action.statis,statObj);
                            }
                        }
                    },
                    error:function(xhr,textStatus){
                        var msg=enumMsg.networkError;
                        if(textStatus=='timeout'){
                            msg=enumMsg.timeOut;
                        }
                        lottery.reset();
                        pointPrize.onEventAction(action.alert,{alertStr:enumMsg.networkError});
                    }
                })
            });

            
            //不抽了
            $('body').delegate('#close','click',function(){
                var dialog=$(this).parent('div').parent('div');
                dialogHide(dialog);
                lottery.reset();

            });

            //查看A券 关闭弹框
            $('body').delegate('#acoin','click',function(){
                var dialog=$(this).parent('div').parent('div');
                dialogHide(dialog);
                lottery.reset();

            });

            //继续抽奖
            $('body').delegate("#continue",'click',function(){
                var dialog=$(this).parent('div').parent('div');
                dialogHide(dialog);
                setTimeout(function(){
                    lottery.reset();
                    $("#prize-btn").trigger('click');
                },300);
            });
            

            //抽中实物提交收货地址信息
            $('body').delegate("#submit",'click',function(){
                var name=$("#name").val().trim(),
                    phone=$("#phone").val().trim(),
                    address=$("#address").val().trim(),
                    postUrl=$(this).attr('data-ajaxUrl'),
                    ticket=$(this).attr('data-ticket'),
                    logId=$(this).attr('data-logId');
                if($.isEmptyObject(name)){//收货人不能为空
                    pointPrize.onEventAction(action.alert,{alertStr:enumMsg.nameError});
                    return;
                }
                var reg=/^1[34578]{1}[0-9]{9}$/;
                if(!reg.test(phone)){//电话号码必须合法
                    pointPrize.onEventAction(action.alert,{alertStr:enumMsg.phoneError});
                    return;
                }
                if($.isEmptyObject(address)){//收货地址不能为空
                    pointPrize.onEventAction(action.alert,{alertStr:enumMsg.addressError});
                    return;
                }
                $(this).addClass('hidden');
                $(".loading-btn").removeClass('hidden');
                $.ajax({
                    type:"POST",
                    url:postUrl,
                    dataType: 'json',
                    data:{
                        token: token,
                        name:name,
                        phone:phone,
                        address:address,
                        ticket:ticket,
                        logId:logId
                    },
                    success:function(data){
                        if(data.success){
                            pointPrize.onEventAction(action.alert,{alertStr:enumMsg.postSuccess});
                            var dialog=$('.J_entity');
                            dialogHide(dialog);
                            
                        } else{
                            pointPrize.onEventAction(action.alert,{alertStr:enumMsg.networkError});
                        }
                        lottery.reset();
                        $("#submit").removeClass('hidden');
                        $(".loading-btn").addClass('hidden');
                    },
                    error:function(){
                        lottery.reset();
                        pointPrize.onEventAction(action.alert,{alertStr:enumMsg.networkError});
                        $("#submit").removeClass('hidden');
                        $(".loading-btn").addClass('hidden');
                    }
                })
            });

            function handler(e){
                e.preventDefault();
            }

            function dialogShow(dialog){
                $("body").bind('touchmove',handler,false);
                var scrollTop=(3-document.body.scrollTop)+'px';
                dialog.show().animate({
                    display: 'block',
                    bottom: scrollTop,
                }, 300, function() {
                    $(".J_dialog").removeClass('invisible');
                });
            }
            function dialogHide(dialog){
                $("body").unbind('touchmove');

                var height=dialog.height();
                dialog.animate({
                    display: 'block',
                    bottom: -height,
                }, 300, function() {
                    $(this).hide();
                    $(".J_dialog").addClass('invisible');
                });
            }
        },

        //获取文字中奖公告
        getBillboard:function(){
            var ajaxUrl=$("#rolling").attr('data-ajaxUrl');
            $.ajax({
                type:"POST",
                url:ajaxUrl,
                dataType: 'json',
                data:{
                    prizeId: prizeId,
                    token:token
                },
                success:function(data){
                    var list=data.data.list;
                    if(list&&list.length>0){
                        var html='<ul>';
                        for(var i=0,len=list.length;i<len;i++){
                            html+='<li class="roll">'+list[i]+'</li>';
                        }
                        html+="</ul>";
                        $("#rolling").html(html);
                        $("#rolling").removeClass('invisible');
                        pointPrize.marquee();
                    }
                }
            });
        },
        //文字跑马灯
        marquee:function(){
            var obj=$("#rolling");
            var scrolltime=500;//移动频度(毫秒)越大越慢 
            var stoptime=3500;//间断时间(毫秒) 
            function start(){ 
                $(obj).find("ul:first").animate({ 
                    marginTop:"-32px" 
                },scrolltime,function(){ 
                    $(this).css({marginTop:"0px"}).find("li:first").appendTo(this); 
                }); 
            }
            if(pointPrize.intervalId!=null){
                clearInterval(pointPrize.intervalId);
            }
            pointPrize.intervalId=setInterval(start,stoptime) ;
        },
        //设置弹出层的高度及top位置
        setDialogHeight:function(){
            $(".J_dialog").height($(document).height());
            var elementArr=['.J_noPrize','.J_points','.J_acoin','.J_entity'];
            for(var i=0,len=elementArr.length;i<len;i++){
                var ele=$(elementArr[i]);
                ele.css('bottom',-ele.height());
                ele.removeClass('invisible').hide();
            }
        },
        //通过viewType接口打开一个新的webview界面
        openPageByViewType:function(){
            $('body').delegate('[data-infpage]', 'click', function() {
                var infpage = $(this).attr("data-infpage").split(',');
                if(!(window.gamehall||navigator.gamehall)){
                    location.href = infpage[1];
                    return;
                }
                var type=$(this).attr('data-type'),
                    viewType=$(this).attr("data-viewType"),
                    source=$(this).attr("data-source")||'',
                    url=infpage[1],
                    title=infpage[0];
                var cfg={};
                    cfg.args={ },cfg.type='list';
                cfg.args.newArgs={
                    viewType:viewType,
                    param:{
                        url:url,
                        title:title,
                        source:source
                    }
                }
                var sucCal=errCal = function() {},
                    gamehall=window.gamehall?window.gamehall:navigator.gamehall;
                gamehall.startlistactivity(sucCal,errCal,JSON.stringify(cfg.args));
            });
            
        },
        //打开客户端页面
        onEventAction:function(action,data){
            if(window.gamehall){
                var ret=window.gamehall.onEvent(action,JSON.stringify(data));
            }else{
                if(action=='gamehall.alert'){
                    alert(data.alertStr);
                }
            }
        },
        //从客户端取数据或状态
        getValueAction:function(action,data){
            if(window.gamehall){
                var ret=window.gamehall.getValue(action,JSON.stringify(data));
                ret=JSON.parse(ret);
                return ret;
            } 
        },

        checkLoginStatus:function(){
            if(typeof isLogin=='undefined') return;
            if(isLogin!=undefined&&(isLogin=='false'||isLogin==false)){
                this.onEventAction(action.finish,{});
                this.onEventAction(action.logout,{});
                this.onEventAction(action.login,{});
            }
        },

        init: function() {
            this.checkLoginStatus();
            this.setDialogHeight();
            this.initPrizeScroll();
            this.getBillboard();
            this.openPageByViewType();
        }
    };
    $(function() {
        FastClick.attach(document.body);
        pointPrize.init(); 
    })
})();