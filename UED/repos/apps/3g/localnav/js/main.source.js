$(function () {

    var itemWrap, parentWrap, tid, total, wli, wul, idx,
        eventTap = 'touchstart' in window ? 'tap' : 'click';

    function namespace(name) {
        if (!name) {
            return window;
        }

        var nms = name.split('.'), tmp = window, ns, i = 0;

        for (; i < nms.length; i++) {
            ns = nms[i];
            tmp[ns] = tmp[ns] || {};
            tmp = tmp[ns];
        }

        return tmp;
    }

    namespace('local.navtive.event');
    namespace('local.navtive.card');

    /**
     * 客户端事件统计方法
     * @param  {[type]} eventId    事件唯一标识
     * @param  {[type]} eventLabel 事件唯一名称
     * @param  {[type]} eventOpt   可选：事件参数
     * @return {[type]}            null
     */
    local.navtive.event.log = function (eventId, eventLabel, eventOpt) {

        eventId = eventId || '';
        eventLabel = eventLabel || '';
        eventOpt = eventOpt || '';

        if (window.HtmlStatisticJSInterface && HtmlStatisticJSInterface.onH5Event) {
            return HtmlStatisticJSInterface.onH5Event(eventId, eventLabel, eventOpt);
        } else {
            console.log(eventId, eventLabel, eventOpt);
        }
    };

    local.navtive.card.env = function (url) {
        var status = /^dev.demo|demo.*/.test(location.href);

        if (status) {
            return 'http://3g.3gtest.gionee.com' + url;
        }

        return url;
    };

    if ($("#js-slide-news")[0]) {
        itemWrap = $("#js-slide-news li");
        parentWrap = $("#js-slide-news ul");
        total = itemWrap.size();
        wli = itemWrap.width();
        wul = wli * total;

        if (wli > 0) {
            itemWrap.css({'width': wli + 'px'});
            parentWrap.css({'width': wul + 'px'});
            itemWrap.first().addClass('active');
        }

        if (total > 1) {
            slideNewsLoop();
        }
    }

    function slideNewsLoop(data) {
        if (data && data.length > 0) {
            clearTimeout(tid);
            $("#js-slide-news ul").html('');
            $.each(data, function (index, item) {
                $("#js-slide-news ul").append('<li>\
					<a class="pic-wrap" href="' + item.link + '"><img class="pic" src="' + item.img + '" /></a>\
					<a class="txt" href="#">' + item.title + '</a>\
				</li>');
            });
        }
        if ($("#js-slide-news li").size() > 1) {
            tid = setTimeout(function () {
                idx = itemWrap.index($('#js-slide-news li.active')) + 1;
                if (total - 1 >= idx) {
                    parentWrap.css({'margin-left': -idx * wli + 'px'});
                    itemWrap.removeClass('active');
                    itemWrap.eq(idx).addClass('active');
                } else {
                    parentWrap.css({'margin-left': 0 + 'px'});
                    itemWrap.removeClass('active');
                    itemWrap.eq(0).addClass('active');
                }
                slideNewsLoop();

            }, 3000);
        }
    }

    local.navtive.card.total = 0;

    local.navtive.card.ajax = function (opt) {
        var localData, total = -1;

        localData = 'initData' in window ? initData : null;

        window.localStorage.removeItem('h5-card-'+opt.mid);
        window.localStorage.removeItem('h5-timestamp-'+opt.mid);

        if(localData){
            total = localData ? Math.ceil(localData.data.list.length / opt.num) : -1;
            // 客户端事件统计上报, 自动触发的不上报
            if(local.navtive.card.total > 0){
                local.navtive.event.log('card_refreshTheH5', opt.mid);
            }

            local.navtive.card.total++;

            renderHtml(localData);
        }

        function renderHtml(data) {
            var arr = [], list = data.data.list, i, cur;

            opt.el.data('pos', ++opt.pos);

            if (local.navtive.card.total > total) {
                local.navtive.card.total = 1;
            }

            if (data.data.img.length > 0) {
                slideNewsLoop(data.data.img);
            }

            for (i = 0; i < opt.num; i++) {
                cur = i + opt.num * (local.navtive.card.total - 1);
                arr.push(list[cur]);
            }

            opt.callback(arr);
        }
    };

    setTimeout(function () {
        $("#js-news-btn").trigger(eventTap);
        $("#js-fun-btn").trigger(eventTap);
        $("#js-mito-btn").trigger(eventTap);
    }, 1000);

    $("#js-news-btn").on(eventTap, function () {
        var that = $(this), mid = $(this).data('mid'), pos = $(this).data('pos') || 1, url = '/front/browser/moreall';

        local.navtive.card.ajax({
            el: that,
            url: url,
            mid: mid,
            pos: pos,
            num: 5,
            callback: function (data) {
                var arr = [];
                $.each(data, function (index, item) {
                    arr.push('<li><a href="' + item.link + '"><span class="txt">' + item.title + '</span></a></li>');
                });
                $('#js-news-list').html(arr.join(''));
            }
        });
    });

    $("#js-fun-btn").on(eventTap, function () {
        var that = $(this), mid = $(this).data('mid'), pos = $(this).data('pos') || 1, url = '/front/browser/moreall';
        local.navtive.card.ajax({
            el: that,
            url: url,
            mid: mid,
            pos: pos,
            num: 1,
            callback: function (data) {
                $('.fun-piece-box .piece').html(data[0]['text']);
            }
        });
    });

    $("#js-mito-btn").on(eventTap, function () {
        var that = $(this), mid = $(this).data('mid'), pos = $(this).data('pos') || 1, url = '/front/browser/moreall';
        local.navtive.card.ajax({
            el: that,
            url: url,
            mid: mid,
            pos: pos,
            num: 5,
            callback: function (data) {
                that.data('pos', ++pos);
                $.each(data, function (index, item) {
                    $('.mito-pic-box li').eq(index).find('a').attr('href', item.link);
                    $('.mito-pic-box li').eq(index).find('img').attr('src', item.img);
                });
            }
        });
    });

    var tmp = 0, eTime, stockTid, isStockEnd;

    // 股票模块
    if($('.mod-stock-box')[0]){
        init_sina_stock();
    }

    function init_sina_stock(){
        var d = new Date(), sTime, step;
        
        sTime = d.getTime();
        eTime = eTime || 0;

        isStockEnd = d.getHours() >= 9 && d.getHours() < 15 ? false : true;
        step = sTime - eTime > 3000 ? true : false;


        if(isStockEnd){
            req_sina_stock();
            cancelAnimationFrame(stockTid);
        } else {
            if(step){
                eTime = sTime;
                if($('#J_hqsina')[0]){
                    $('#J_hqsina').remove();
                }
                req_sina_stock();
            }

            stockTid = requestAnimationFrame(arguments.callee);
        }
        
    }

    function req_sina_stock(){
        var _script = document.createElement('script');
        _script.id = 'J_hqsina';
        _script.type = 'text/javascript';
        _script.src = 'http://hq.sinajs.cn/rn='+(+new Date)+'&list=s_sh000001,s_sz399001,s_sz399006';
        document.body.appendChild(_script);
        _script.onload = function(data){
            stock(hq_str_s_sh000001,hq_str_s_sz399001,hq_str_s_sz399006);
        }
    }

    function stock(s1,s2,s3){
        console.log(s1,s2,s3);
        var hq_str_s_sh000001 = (s1 || '').split(',');
        var hq_str_s_sz399001 = (s2 || '').split(',');
        var hq_str_s_sz399006 = (s3 || '').split(',');
        var st_c1_status = hq_str_s_sh000001[3] == 0 ? 'hq_str_eq' : (hq_str_s_sh000001[3] > 0 ? 'hq_str_up' : 'hq_str_dn');
        var st_c2_status = hq_str_s_sz399001[3] == 0 ? 'hq_str_eq' : (hq_str_s_sz399001[3] > 0 ? 'hq_str_up' : 'hq_str_dn');
        var st_c3_status = hq_str_s_sz399006[3] == 0 ? 'hq_str_eq' : (hq_str_s_sz399006[3] > 0 ? 'hq_str_up' : 'hq_str_dn');

        $('.st_c1 span:nth-child(2)').html((+hq_str_s_sh000001[1]).toFixed(2)).addClass(st_c1_status);
        $('.st_c1 span:nth-child(3)').html((hq_str_s_sh000001[3] > 0 ? '+' : '')+(+hq_str_s_sh000001[2]).toFixed(2)+'&nbsp;'+(hq_str_s_sh000001[3] > 0 ? '+' : '')+hq_str_s_sh000001[3]+'%').addClass(st_c1_status);
        $('.st_c2 span:nth-child(2)').html((+hq_str_s_sz399001[1]).toFixed(2)).addClass(st_c2_status);
        $('.st_c2 span:nth-child(3)').html((hq_str_s_sz399001[3] > 0 ? '+' : '')+(+hq_str_s_sz399001[2]).toFixed(2)+'&nbsp;'+(hq_str_s_sz399001[3] > 0 ? '+' : '')+hq_str_s_sz399001[3]+'%').addClass(st_c2_status);
        $('.st_c3 span:nth-child(2)').html((+hq_str_s_sz399006[1]).toFixed(2)).addClass(st_c3_status);
        $('.st_c3 span:nth-child(3)').html((hq_str_s_sz399006[3] > 0 ? '+' : '')+(+hq_str_s_sz399006[2]).toFixed(2)+'&nbsp;'+(hq_str_s_sz399006[3] > 0 ? '+' : '')+hq_str_s_sz399006[3]+'%').addClass(st_c3_status);
    }

    $('.pic').each(function (index, item) {
        var url = $(item).data('src'), picImg = new Image();
        if (url) {
            picImg.src = url;
            picImg.onload = function () {
                $(item).removeAttr('data-src');
                setTimeout(function () {
                    $(item).attr("src", url);
                }, 200);
            }

            picImg.onerror = function () {
                $(item).addClass('default');
            }
        } else {
            $(item).addClass('default');
        }
    });
});
