require([
   './pro','./ajax'
], function(){
    // 隐藏Android工具条
    
    setTimeout(window.scrollTo(0,1),10);

    // baidu report
    function parseAjaxUrl(url){
        var reg = /^dev.|demo.*/g;
        return reg.test(location.host) === true ? '/3g/' + url + '.php' : url;
    }

    function report2baidu(act,label){
        var _hmt = window._hmt || [];
        _hmt.push(['_trackEvent', 'YX-畅聊数据', act, label]);
    }

    // 拨号盘展开、收起操作
    // $('.call-bottom-bar-span').on('click', function(){
    //     var isExpand = $('.call-bottom-bar-span i').hasClass('btn-call-arrow-down'); // 箭头向下，拨号盘向上
    //     if(isExpand){
    //         $(this).removeClass('done');
    //         $('.call-panel-box').addClass('none'); 
    //         $('.call-bottom-bar-span i').removeClass('btn-call-arrow-down').addClass('btn-call-arrow-up');

    //     } else {
    //         report2baidu('YX-拨号展开','YX-拨号展开次数');
    //         $(this).addClass('done');
    //         $('.call-panel-box').removeClass('none');
    //         $('.call-bottom-bar-span i').removeClass('btn-call-arrow-up').addClass('btn-call-arrow-down');
    //     }
    // });
    
    setTimeout(function(){
        if(location.href.indexOf('tel') > -1){
            $('#js-call-calling').trigger('tap');
        }
    },1000);


    var val = '', callStatus = 0, isCalling = false;

    // 拨号输入操作
    $('.button-panel li span').on('tap',function(){
        var oInputText = $('.call-input-text');
        if (!oInputText.hasClass('call-input-actived')){
            oInputText.addClass('call-input-actived');
        }
        
        val = val + $(this).text();

        if (val.slice(0,1) == 0 && val.length > 11){
            val = val.substring(0,11) + $(this).text();
        } else if(val.length > 10){
            val = val.substring(0,10) + $(this).text();
        }

        oInputText.attr('data-code',1);
        oInputText.val(val);
    });

    // 拨号拨打操作
    $('.call-list').delegate('.ui-item,.call-sta','tap',function(evt){
        // if(localSDK.support().code == 1 && localSDK.network().code == 1){
        //     var ret = localSDK.tel(_CONFIG_.account);
        //     if(ret.code == 1){
        //         //alert(ret.text);
        //     }
        // } else {
        //     if($(this).hasClass('.call-sta')){
        //         location.href = $(this).data('url');
        //     }
        // }
    });

    // 拨号删除操作
    $('.call-input-btn').on('tap',function(){
        var oInputText = $('.call-input-text');
        val = oInputText.val().slice(0,-1);
        oInputText.val(val);
        if(val.length == 0){
            oInputText.removeClass('call-input-actived');
        }
    });

    // 长按拨号删除操作
    $('.call-input-btn').on('longTap',function(){
        val = '';
        $('.call-input-text').removeClass('call-input-actived').val('').attr('placeholder','请输入或粘贴手机/座机号');
    });

    // 激活电话操作
    $('#js-call-activeing').on('tap',function(){
        var that = $(this);
        var pid = that.data('pid'),
            uid = that.data('uid'),
            url = that.data('url');
        if(!isCalling){
            $.ajax({
                url: url,
                type: 'post',
                data: {'pid': pid, 'uid': uid, 'token': token},
                dataType: 'json',
                success: function(res){
                    if (res.success){
                        if (res.data.redirect){
                            that.addClass('none');
                            $('.ui-panel').html('<a class="ui-button ui-block ui-normal" href="'+res.data.redirect+'"><i class="icon-call-white"></i>免费电话</a>');
                        }
                    } else {
                        var el = $('.call-tips-error');
                        showMsg(el, res.msg);
                    }
                }
            }); 
        }
        
    });

    // 删除通话记录操作
    $('#js-actionsheet-ok').on('click',function(){
        var url = $('#js-call-clear').data('url');
        if (url){
            $.get(url,function(ret){
                if (ret.success){
                    $('#js-call-clear').remove();
                    $('.call-list li').not($('.call-list li[data-fixed="true"]')).remove();
                    $('#dialog-cancel').trigger('tap');
                }
            },'json');
        }
    });

    $('#js-actionsheet-cancel').on('click',function(){
        $('#dialog-cancel').trigger('tap');
    });

    // 拨打电话的操作
    $('#js-call-calling').on('tap', function(){
        var that = $(this);
        var callee = $('.call-input-text').val();
        var cname = $('#js-input-cname').val();
        var url = $(this).data('url');
        var el = $('.call-input-tips');
        if (callee == _CONFIG_.telphone){
            showMsg(el, '不能拨打本机号码！');
            return;
        }

        if(callee == ''){
            showMsg(el, '请输入您要拨打的号码！');
            return;  
        }

        if (callee.slice(0,1) == 0){
            if (!(/^0(10|[2]\d{1}|[3-9]\d{2})(\d{7,8})/.test(callee))){
                showMsg(el, '座机号输入不正确！');
                return;
            }
        } else if(!(/^1[3|4|5|7|8][0-9]\d{8}$/).test(callee)) {
            showMsg(el, '手机号输入不正确！');
            return;
        }

        if(callStatus !== 0){
            showMsg(el, '系统繁忙，请稍后！');
            return;
        }

        if (!that.hasClass('done')){
            that.addClass('done');
        }

        if(_CONFIG_.isLogin === '1'){
            // 用户已登录
            if(localSDK.support().code == 1 && localSDK.network().code == 1){
                // 支持SDK调用，并且当前网络为WIFI，采用直拨方式
                get2call({'el': that,'url':url,'callee': callee, 'name':cname, 'type':1});
            } else {
                get2call({'el': that, 'url': url, 'callee': callee, 'name':cname, 'type':2});
            }
        } else {
            // 未登录，跳转到登录界面
            if(login_url){
                location.href = login_url;
            }
        }

        
    });

    function get2call(options){
        var that = options.el;
        var data = {
            'callee': options.callee,
            'type': options.type,
            'token': token,
            'name': options.name
        };

        $.ajax({
            url: options.url,
            type: 'post',
            data: data,
            dataType: 'json',
            success: function(res){
                isCalling = true;

                if(options.type == 1){
                    // var account = {
                    //     devaccount:"3gadmin",//开发者账号
                    //     devpw:"123123",//开发者密码
                    //     clientasscount:"test",//Client账号
                    //     clientpw:"test",//Client密码
                    //     id: 1,
                    //     name:"hankewins",
                    //     number: "18520872388",
                    //     city:"江西 九江"
                    // };
                    var ret = localSDK.tel(res.data.account);

                    that.removeClass('done');

                }

                if(options.type == 2){
                    var times = 30;
                    spinner(times);
                    function spinner(times){
                        $('.ui-button-blue').text('呼叫中'+'('+times+')');
                        times--;
                        if (times > 0){
                            tid = setTimeout(function(){
                                spinner(times);
                            },1000);
                            callStatus++;
                        } else if (times == 0){
                            $('.ui-button-blue').text('呼叫中').addClass('none');
                            that.removeClass('none');
                            callStatus = 0;
                            isCalling = false;
                         }
                    }
                    that.addClass('none').removeClass('done');
                    $('.ui-button-blue').removeClass('none');
                }
            }
        });
    }

    function showMsg(el,str,time){
        el.removeClass('none').text(str);
        setTimeout(function(){
            el.addClass('none').text('');
        },time || 3000);
    }

    $('#link-wrap a').on('tap',function(){
        var url = $(this).attr('href');
        $(this).attr('href','http://3g.gionee.com/tj/'+url);
    });

    // feedback 
    $('#js-radio-list .ui-radio ').on('click',function(){
        var that = $(this).find('input'), index = 0;
        if(!that.hasClass('checked')){
            index = $('#js-radio-list .ui-radio input').index(that);
            $('#js-radio-list .ui-radio input').removeClass('checked');
            that.addClass('checked');
            $('#js-checkbox-list .ui-list-checkbox').addClass('none');
            $('#js-checkbox-list .ui-list-checkbox').eq(index).removeClass('none');
        }
    });

    $('#js-feed-submit').on('tap',function(){
        var menu_id = $('#js-radio-list .ui-radio input:checked').val();
        var checked_index = $('#js-radio-list .ui-radio input').index('.checked');
        var checked_list = $('#js-checkbox-list .ui-list-checkbox:nth-child('+(checked_index+1)+') input:checked');
        var react = $('.ui-textarea').val();
        var contact = $('.inp-tel').val();
        var checked_arr = [];
        $.each(checked_list,function(index,item){
            checked_arr.push($(item).val());
        });

        $.ajax({
            url: parseAjaxUrl('/feedback/ajaxPost'),
            type:'POST',
            dataType: 'json',
            data: {
                'react': react, 'contact': contact, 'menu_id': menu_id, 'checked_list': checked_arr.join(), 'token': token, 'ch': '' 
            },
            success: function(data){
                if(data.success){
                    alert(data.msg);
                    if(data.data.type == 'redirect'){
                        location.href = data.data.url;
                    }
                } else {
                    alert(data.msg);
                }
            }
        });

    });

});
