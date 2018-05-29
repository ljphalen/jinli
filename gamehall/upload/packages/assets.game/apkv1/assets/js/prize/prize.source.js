//九宫格抽奖
var lottery = {
    index: -1, //当前转动到哪个位置
    count: 0, //总共有多少个位置
    timer: 0, //setTimeout的ID，用clearTimeout清除
    speed: 180, //初始转动速度
    times: 0, //转动次数
    cycle: 30, //转动基本次数：即至少需要转动多少次再进入抽奖环节
    prize: -1, //中奖位置,
    click: false,
    callBack: function() {}, //中奖之后的回调函数
    init: function(id) {
        if ($("#" + id).find("li.prize-unit").length > 0) {
            $lottery = $("#" + id);
            $units = $lottery.find(".prize-unit");
            this.obj = $lottery;
            this.count = $units.length;
        };
    },
    roll: function() {
        var index = this.index;
        var count = this.count;
        var lottery = this.obj;
        $(lottery).find("#prize-grid" + index).removeClass("active");
        index += 1;
        if (index > count - 1) {
            index = 0;
        };
        $(lottery).find("#prize-grid" + index).addClass("active");
        this.index = index;
        return false;
    },
    setPrize: function(index) {
        this.prize = index;
    },
    getPrize: function() {
        if (this.prize == -1) {
            return undefined;
        } else {
            return this.prize;
        }
    },
    stop: function() {
        this.callBack();
    },
    reset: function() {
        clearTimeout(this.timer);
        $("#prize-btn").removeClass('prize-btn-active');
        $("#prize_wrap").find("#prize-grid" + this.index).removeClass("active");
        this.click = false;
        this.speed = 180;
        this.index = -1;
        this.prize = -1;
    }
};
function roll() {
    lottery.times += 1;
    lottery.roll();
    //确定了中奖目标索引后再让其转动到对应位置
    if (lottery.times > lottery.cycle + 10 && lottery.prize == lottery.index) {
        clearTimeout(lottery.timer);
        lottery.stop();
        lottery.prize = -1;
        lottery.times = 0;
        click = false;
    } else {
        if (lottery.times == lottery.cycle) {
            if (lottery.getPrize() == undefined) {
                lottery.cycle += 10;
            }
        }
        // console.log(lottery.times+'^^^^^^'+lottery.speed+'^^^^^^^'+lottery.prize);
        lottery.timer = setTimeout(roll, lottery.speed);
    }
    return false;
}

//转盘抽奖
var turnplate = {
    prize: prize || [], //大转盘奖品名称
    colors: ["#f9f5cf", "#f9e4af", "#f9f5cf", "#f9e4af", "#f9f5cf", "#f9e4af", "#f9f5cf", "#f9e4af"],
    x: 211, //圆心x坐标
    y: 211, //圆心Y坐标
    outsideRadius: 192, //大转盘外圆的半径
    textRadius: 170, //大转盘奖品位置距离圆心的距离
    insideRadius: 90, //大转盘内圆的半径
    startAngle: 0, //开始角度
    bRotate: false, //false:停止;true:旋转

};
var rotateStart = function() {
    $('#wheelcanvas').stopRotate();
    $('#wheelcanvas').rotate({
        angle: 0,
        animateTo: 1800,
        duration: 10000
    });
}

var rotateTimeOut = function() {
    /*var startAngles = $("#wheelcanvas").getRotateAngle();
    $('#wheelcanvas').rotat
        angle: startAngles,
        animateTo: 1800,
        duration: 5000,
        callback: function() {
            turnplate.bRotate = !turnplate.bRotate;
        }
    });*/
    if(turnplate.bRotate){
        turnplate.bRotate=false;
    }
    $("#wheelcanvas").stopRotate();
    
};

//设置旋转停止 item:奖品位置; callback 抽奖完毕的回调函数;
var rotateFn = function(item, callback) {
    var angles = (item - 2) * (360 / turnplate.prize.length) - (360 / (turnplate.prize.length * 2));
    if (angles < 270) {
        angles = 270 - angles;
    } else {
        angles = 360 - angles + 270;
    }
    var startAngles = $("#wheelcanvas").getRotateAngle();
    $('#wheelcanvas').rotate({
        angle: startAngles,
        animateTo: angles + 1800,
        duration: 8000,
        callback: function() {
            turnplate.bRotate = !turnplate.bRotate;
            callback();
        }
    });
};
var getPixelRatio = function(context) {
  var backingStore = context.backingStorePixelRatio ||
    context.webkitBackingStorePixelRatio ||
    context.mozBackingStorePixelRatio ||
    context.msBackingStorePixelRatio ||
    context.oBackingStorePixelRatio ||
    context.backingStorePixelRatio || 1;
   return (window.devicePixelRatio || 1) / backingStore;
};
function drawRouletteWheel() {
    var canvas = document.getElementById("wheelcanvas");
    if (!canvas) return;
    if (canvas.getContext) {

        //根据奖品个数计算圆周角度
        var arc = Math.PI / (turnplate.prize.length / 2);
        var ctx = canvas.getContext("2d");
        var ratio = getPixelRatio(ctx);

        //在给定矩形内清空一个矩形
        ctx.clearRect(0, 0, (turnplate.x * 2)*ratio, (turnplate.y * 2)*ratio);
        //strokeStyle 属性设置或返回用于笔触的颜色、渐变或模式  
        ctx.strokeStyle = "#fff9e3";

        //font 属性设置或返回画布上文本内容的当前字体属性
        ctx.font = '15px Microsoft YaHei';
        for (var i = 0; i < turnplate.prize.length; i++) {
            var angle = turnplate.startAngle + (i - 2) * arc;

            ctx.fillStyle = "#fff9e3";

            ctx.beginPath();
            ctx.arc(turnplate.x, turnplate.y, (turnplate.outsideRadius), angle, angle + arc, false);
            ctx.arc(turnplate.x, turnplate.y, turnplate.insideRadius - 30, angle + arc, angle, true);
            ctx.stroke();



            ctx.fill();
            ctx.closePath();

            ctx.fillStyle = turnplate.colors[i];
            ctx.beginPath();
            //arc(x,y,r,起始角,结束角,绘制方向) 方法创建弧/曲线（用于创建圆或部分圆）    
            ctx.arc(turnplate.x, turnplate.y, (turnplate.outsideRadius - 33), angle, angle + arc, false);
            ctx.arc(turnplate.x, turnplate.y, turnplate.insideRadius - 30, angle + arc, angle, true);
            ctx.stroke();
            ctx.fill();
            //锁画布(为了保存之前的画布状态)
            ctx.save();

            //----绘制奖品开始----
            ctx.fillStyle = "#a45d00";
            var text = turnplate.prize[i];
            var line_height = 17;
            //translate方法重新映射画布上的 (0,0) 位置
            ctx.translate(211 + Math.cos(angle + arc / 2) * turnplate.textRadius, 211 + Math.sin(angle + arc / 2) * turnplate.textRadius);

            //rotate方法旋转当前的绘图
            ctx.rotate(angle + arc / 2 + Math.PI / 2);

            /** 下面代码根据奖品类型、奖品名称长度渲染不同效果，如字体、颜色、图片效果。(具体根据实际情况改变) **/
            if (text.length > 6) { //奖品名称长度超过一定范围 
                text = text.substring(0, 6) + "||" + text.substring(6);
                var texts = text.split("||");
                for (var j = 0; j < texts.length; j++) {
                    ctx.fillText(texts[j], -ctx.measureText(texts[j]).width / 2, j * line_height);
                }
            } else {
                //在画布上绘制填色的文本。文本的默认颜色是黑色
                //measureText()方法返回包含一个对象，该对象包含以像素计的指定字体宽度
                ctx.fillText(text, -ctx.measureText(text).width / 2, 0);
            }
            //添加对应图标
            var img = document.getElementById("img" + (i + 1));
            ctx.drawImage(img, -(35*ratio), 15*ratio, 70*ratio, 70*ratio);
            //把当前画布返回（调整）到上一个save()状态之前 
            ctx.restore();
            
        }
        $('#wheelcanvas').css('width',"100%");
        $('#wheelcanvas').css('height',"auto")
    }
}


;(function() {
    var enumMsg = {
        networkError: '网络异常，请稍候重试',
        notLogin: '未登录，请先登录',
        underPoints: '您的积分不足',
        nameError: '收货人不能为空',
        phoneError: '请输入合法的联系电话',
        addressError: '收货地址不能为空',
        postSuccess: '提交成功',
        timeOut: '请求超时',
        postError: '请求异常，请刷新重试'
    };
    var action = {
        network: 'gamehall.hasnetwork',
        alert: 'gamehall.alert',
        logout: 'gamehall.clearlogin',
        finish: 'gamehall.finish',
        islogin: 'gamehall.islogin',
        getPoint: 'gamehall.daily.task',
        login: 'gamehall.account',
        statis: 'gamehall.sendstatis',
        boradcast: 'gamehall.money.change',
        getServerId: 'gamehall.getserverid',
    }
    var pointPrize = {
        intervalId: null,
        getUrlParam: function(name) {
            var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
            var r = window.location.search.substr(1).match(reg); //匹配目标参数
            if (r != null) return unescape(r[2]);
            return ''; //返回参数值
        },

        getServerId: function() {
            var version = pointPrize.getUrlParam('sp').split('_')[1];
            var serverId = '';
            if (pointPrize.compareVersion(version, '1.5.7')) { //1.5.7增加的取serverId的接口
                serverId=pointPrize.getValueAction(action.getServerId,{url:location.href}).serverId;
            }
            return serverId;
        },

        compareVersion: function(sourceVersion, targetVersion) {
            if (sourceVersion.indexOf(targetVersion) > -1) {
                return true;
            }
            var srcArr = sourceVersion.split('.'),
                targetArr = targetVersion.split('.');
            var len = Math.max(srcArr.length, targetArr.length);

            for (var i = 0; i < len; i++) {
                if (srcArr[i] === undefined) {
                    return false;
                }
                if (targetArr[i] === undefined) {
                    return true;
                }
                if (srcArr[i] > targetArr[i]) {
                    return true;
                } else if (srcArr[i] < targetArr[i]) {
                    return false;
                }

            }
            return false;
        },

        initPrizeScroll: function() {
            lottery.init('prize_wrap'); //初始化九宫格抽奖
            drawRouletteWheel(); //初始化转盘抽奖

            var search = location.search,
                arr = $("#acoin").attr('data-infpage').split(',');
            if (arr.length > 2) {
                aCoinUrl = arr[1] + search;
                $("#acoin").attr('data-infpage', arr[0] + "," + aCoinUrl);
            }


            var uname = pointPrize.getUrlParam('uname'),
                puuid = pointPrize.getUrlParam('puuid'),
                sp = pointPrize.getUrlParam('sp'),
                spArr = sp.split('_'),
                imei = spArr[spArr.length - 1],
                ajaxUrl = $("#prize-btn").length ? $("#prize-btn").attr('data-ajaxUrl') : $(".J_pointer").attr('data-ajaxUrl');

            //领积分
            $(".btn-points").click(function() {
                pointPrize.onEventAction(action.getPoint, {});
            })

            //九宫格中奖按钮处理逻辑
            $("#prize-btn").click(function() {
                if (lottery.click || $(this).hasClass('prize-btn-active')) return;

                var init = function() {
                    //正常抽奖
                    roll();
                    lottery.click = true;
                    $("#prize-btn").addClass('prize-btn-active');
                }
                

                var successCallback = function(d, dialog, statObj) {
                    //设置九宫格以决定最后中奖状态
                    lottery.setPrize(parseInt(d.prizeIndex, 10) - 1);
                    //弹框提示
                    lottery.callBack = function() {
                        pointPrize.getBillboard();
                        $("#prize-totalPoints").html(d.totalPoints);
                        dialogShow(dialog);
                        pointPrize.onEventAction(action.statis, statObj);
                    }
                }

                getPrizeInfo(init, successCallback);
            });

            //转盘抽奖处理逻辑
            $("body").on('click', '.J_pointer', function(event) {
                if (turnplate.bRotate || $(this).hasClass('prize-btn-active')) return;
                var init = function() {
                    //正常抽奖
                    rotateStart();
                    turnplate.bRotate = !turnplate.bRotate;
                    $(".J_pointer").addClass('prize-btn-active');
                }

                var successCallback = function(d, dialog, statObj) {
                    var item = parseInt(d.prizeIndex, 10);
                    var callbackEvent = function() {
                        pointPrize.getBillboard();
                        $("#prize-totalPoints").html(d.totalPoints);
                        dialogShow(dialog);
                        pointPrize.onEventAction(action.statis, statObj);
                    }
                    rotateFn(item, callbackEvent);
                }

                
                getPrizeInfo(init, successCallback);

            });
            
            //重置抽奖信息
            function reset(){
                if($("#prize-btn").length){
                    lottery.reset();
                }else{
                    rotateTimeOut();
                    $(".J_pointer").removeClass('prize-btn-active');
                }
            }

            function getPrizeInfo(init, successCallback) {
                //积分不足
                var pointsBtn = $("#prize-totalPoints"),
                    consumePoints = parseInt($("#prize-consumePoints").html(), 10);
                if (parseInt(pointsBtn.html(), 10) < consumePoints) {
                    pointPrize.onEventAction(action.alert, {
                        alertStr: enumMsg.underPoints
                    });
                    $("#prize-btn,.J_pointer").removeClass('prize-btn-active');
                    return;
                }

                init();

                var obj = {
                    action: 'lottery',
                    object: 'lottery' + prizeId //活动ID
                }
                pointPrize.onEventAction(action.statis, obj); //统计
                $.ajax({
                    type: "POST",
                    url: ajaxUrl,
                    dataType: 'json',
                    timeout: 5000,
                    data: {
                        token: token,
                        puuid: puuid,
                        uname: uname,
                        prizeId: prizeId,
                        serverId: pointPrize.getServerId(),
                        imei: imei,
                        sp: sp
                    },
                    success: function(data) {
                        if (!data || (typeof data == 'string') || (!data.success)) { //非法请求
                            reset();
                            var msg = enumMsg.postError;
                            if (data && data.msg) {
                                msg = data.msg;
                            }
                            pointPrize.onEventAction(action.alert, {
                                alertStr: msg
                            });
                            return;
                        }
                        var d = data.data;
                        if (d.isLogin != undefined && d.isLogin == false) { //未登录
                            reset();
                            pointPrize.onEventAction(action.finish, {});
                            pointPrize.onEventAction(action.logout, {});
                            pointPrize.onEventAction(action.login, {});
                            return;
                        }

                        if (d.underPoints && d.underPoints == false) { //积分不足
                            reset();
                            pointPrize.onEventAction(action.alert, {
                                alertStr: enumMsg.underPoints
                            });
                            return;
                        }
                        if (d.prizeType != undefined) {
                            pointPrize.onEventAction(action.boradcast, {});
                            var statObj = {
                                    action: 'visit',
                                    object: 'lotterypop', //奖项ID
                                    intersrc: 'prize' + d.configId
                                },
                                popDialogID;
                            if (d.prizeType == 0) { //未中奖
                                statObj = {
                                    action: 'visit',
                                    object: 'lotterypop', //奖项ID
                                    intersrc: 'lose'
                                };
                                popDialogID = '.J_noPrize';

                            } else if (d.prizeType == 1) { //实物
                                popDialogID = '.J_entity';
                                $("#name").val(d.receivingName);
                                $("#phone").val(d.receivingPhone);
                                $("#address").val(d.receivingAddress);
                                $("#submit").attr('data-ticket', d.ticket);
                                $("#submit").attr('data-logId', d.logId);

                            } else if (d.prizeType == 2) { //A券
                                popDialogID = '.J_acoin';
                                $("#indate").html(d.indate + '天');

                            } else if (d.prizeType == 3) { //积分
                                popDialogID = '.J_points';
                            }
                            var dialog = $(popDialogID);
                            dialog.find('img').attr('src', d.prizeImg);
                            dialog.find('#prizeName').html(d.prizeName);


                            successCallback(d, dialog, statObj);

                        }
                    },
                    error: function(xhr, textStatus) {
                        var msg = enumMsg.networkError;
                        if (textStatus == 'timeout') {
                            msg = enumMsg.timeOut;
                        }
                        reset();
                        pointPrize.onEventAction(action.alert, {
                            alertStr: enumMsg.networkError
                        });
                    }
                })
            }


            //不抽了
            $('body').delegate('#close', 'click', function() {
                var dialog = $(this).parent('div').parent('div');
                dialogHide(dialog);
                reset();
            });

            //查看A券 关闭弹框
            $('body').delegate('#acoin', 'click', function() {
                var dialog = $(this).parent('div').parent('div');
                dialogHide(dialog);
                reset();
            });

            //继续抽奖
            $('body').delegate("#continue", 'click', function() {
                var dialog = $(this).parent('div').parent('div');
                dialogHide(dialog);
                setTimeout(function() {
                    reset();
                    if($("#prize-btn").length){
                        $("#prize-btn").trigger('click');
                    }else{
                        $(".J_pointer").trigger('click');
                    }
                    
                }, 300);
            });


            //抽中实物提交收货地址信息
            $('body').delegate("#submit", 'click', function() {
                var name = $("#name").val().trim(),
                    phone = $("#phone").val().trim(),
                    address = $("#address").val().trim(),
                    postUrl = $(this).attr('data-ajaxUrl'),
                    ticket = $(this).attr('data-ticket'),
                    logId = $(this).attr('data-logId');
                if ($.isEmptyObject(name)) { //收货人不能为空
                    pointPrize.onEventAction(action.alert, {
                        alertStr: enumMsg.nameError
                    });
                    return;
                }
                var reg = /^1[34578]{1}[0-9]{9}$/;
                if (!reg.test(phone)) { //电话号码必须合法
                    pointPrize.onEventAction(action.alert, {
                        alertStr: enumMsg.phoneError
                    });
                    return;
                }
                if ($.isEmptyObject(address)) { //收货地址不能为空
                    pointPrize.onEventAction(action.alert, {
                        alertStr: enumMsg.addressError
                    });
                    return;
                }
                $(this).addClass('hidden');
                $(".loading-btn").removeClass('hidden');
                $.ajax({
                    type: "POST",
                    url: postUrl,
                    dataType: 'json',
                    data: {
                        token: token,
                        name: name,
                        phone: phone,
                        address: address,
                        ticket: ticket,
                        logId: logId
                    },
                    success: function(data) {
                        if (data.success) {
                            pointPrize.onEventAction(action.alert, {
                                alertStr: enumMsg.postSuccess
                            });
                            var dialog = $('.J_entity');
                            dialogHide(dialog);

                        } else {
                            pointPrize.onEventAction(action.alert, {
                                alertStr: enumMsg.networkError
                            });
                        }
                        reset();
                        $("#submit").removeClass('hidden');
                        $(".loading-btn").addClass('hidden');
                    },
                    error: function() {
                        reset();
                        pointPrize.onEventAction(action.alert, {
                            alertStr: enumMsg.networkError
                        });
                        $("#submit").removeClass('hidden');
                        $(".loading-btn").addClass('hidden');
                    }
                })
            });

            function handler(e) {
                e.preventDefault();
            }

            function dialogShow(dialog) {
                $("body").bind('touchmove', handler, false);
                var scrollTop = document.body.scrollTop + 'px';
                dialog.show().animate({
                    display: 'block',
                    bottom: '0px',
                }, 300, function() {
                    $(".J_dialog").removeClass('invisible');
                });
            }

            function dialogHide(dialog) {
                $("body").unbind('touchmove');

                var height = dialog.height();
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
        getBillboard: function() {
            var ajaxUrl = $("#rolling").attr('data-ajaxUrl');
            $.ajax({
                type: "POST",
                url: ajaxUrl,
                dataType: 'json',
                data: {
                    prizeId: prizeId,
                    token: token
                },
                success: function(data) {
                    var list = data.data.list;
                    if (list && list.length > 0) {
                        var html = '<ul>';
                        for (var i = 0, len = list.length; i < len; i++) {
                            html += '<li class="roll">' + list[i] + '</li>';
                        }
                        html += "</ul>";
                        $("#rolling").html(html);
                        $("#rolling").removeClass('invisible');
                        pointPrize.marquee();
                    }
                }
            });
        },
        //文字跑马灯
        marquee: function() {
            var obj = $("#rolling");
            var scrolltime = 500; //移动频度(毫秒)越大越慢 
            var stoptime = 3500; //间断时间(毫秒) 
            function start() {
                $(obj).find("ul:first").animate({
                    marginTop: "-32px"
                }, scrolltime, function() {
                    $(this).css({
                        marginTop: "0px"
                    }).find("li:first").appendTo(this);
                });
            }
            if (pointPrize.intervalId != null) {
                clearInterval(pointPrize.intervalId);
            }
            pointPrize.intervalId = setInterval(start, stoptime);
        },
        //设置弹出层的高度及top位置
        setDialogHeight: function() {
            $(".J_dialog").height($(document).height());
            var elementArr = ['.J_noPrize', '.J_points', '.J_acoin', '.J_entity'];
            for (var i = 0, len = elementArr.length; i < len; i++) {
                var ele = $(elementArr[i]);
                ele.css('bottom', -ele.height());
                ele.removeClass('invisible').hide();
            }
        },
        //通过viewType接口打开一个新的webview界面
        openPageByViewType: function() {
            $('body').delegate('[data-infpage]', 'click', function() {
                var infpage = $(this).attr("data-infpage").split(',');
                if (!(window.gamehall || navigator.gamehall)) {
                    location.href = infpage[1];
                    return;
                }
                var type = $(this).attr('data-type'),
                    viewType = $(this).attr("data-viewType"),
                    source = $(this).attr("data-source") || '',
                    url = infpage[1],
                    title = infpage[0];
                var cfg = {};
                cfg.args = {}, cfg.type = 'list';
                cfg.args.newArgs = {
                    viewType: viewType,
                    param: {
                        url: url,
                        title: title,
                        source: source
                    }
                }
                var sucCal = errCal = function() {},
                    gamehall = window.gamehall ? window.gamehall : navigator.gamehall;
                gamehall.startlistactivity(sucCal, errCal, JSON.stringify(cfg.args));
            });

        },
        //打开客户端页面
        onEventAction: function(action, data) {
            if (window.gamehall) {
                var ret = window.gamehall.onEvent(action, JSON.stringify(data));
            } else {
                if (action == 'gamehall.alert') {
                    alert(data.alertStr);
                }
            }
        },
        //从客户端取数据或状态
        getValueAction: function(action, data) {
            if (window.gamehall) {
                var ret = window.gamehall.getValue(action, JSON.stringify(data));
                ret = JSON.parse(ret);
                return ret;
            }
        },

        checkLoginStatus: function() {
            if (typeof isLogin == 'undefined') return;
            if (isLogin != undefined && (isLogin == 'false' || isLogin == false)) {
                this.onEventAction(action.finish, {});
                this.onEventAction(action.logout, {});
                this.onEventAction(action.login, {});
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

    window.onload = function() {
        var $imgs = $('.dial-container .imgs img')
            ,length = $imgs.length
            ,num=0
        ;
        if(!$('.dial-container').length)
            pointPrize.init();
        
        $imgs.each(function(){
            if(this.complete){
                num++
                if(num==length)
                    pointPrize.init();
                return;
            }
            $(this).load(function(){
                num++;
                if(num==length)
                    pointPrize.init();
            })
        });
        
        // FastClick.attach(document.body);

    }
})();