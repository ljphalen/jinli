$(function () {
    //判断是否为店铺抓取页面（专用标志）
    var is_gou_shop_page  =  $("#page_shop_flag");
    var is_dhm_goods_page =  $("#dhm_goods_table");
    var is_dhm_mall_page  =  $("#search_dhm_mall_btn");
    var is_send_data = false;
    var is_rev_data = false;

    var uid = getParameterByName("data-uid");
    var message = {"type": "sign", "data": {}};
    var s = getParameterByName("s");


    //淘宝搜索页面
    if (uid != "" && is_send_data === false) {
        var img = $("div.product-img>a[data-uid=" + uid + "]>img");

        //分页获取图片
        if (img.length < 1) {
            page_str = $(".pagination").attr("bx-config");
            pager = eval("(" + page_str + ")");
            var count = parseInt(pager.count);
            console.log(count);
            if (s == "")s = 0;
            //没有获取到图片
            if (count - s < 20) {
                console.log("page count 1");
                alert("无图片");
            }
            s += 20;
            //跳转到下一页
            if (s <= count) {
                url = window.location.href + "&s=" + s;
                window.location.href = url;
            }
        }

        $.each(img, function (k, v) {
            message.data[k] = v.src;
        });
        //send images to background javascript
        message.type = "send";
        message.to = "gou_shop_tab";
        chrome.runtime.sendMessage(message, function (response) {
            is_send_data = true;
            console.log(response.farewell);
        });
    }

    //发送信号
    if (is_gou_shop_page.length > 0) {
        message.type = "page";
        message.tab = "gou_shop_tab";
        console.log(message);
        chrome.runtime.sendMessage(message, function (response) {
            console.log(response.farewell);
        });
    }
    //发送信号
    if (is_dhm_goods_page.length > 0) {
        message.type = "page";
        message.tab = "dhm_goods_tab";
        console.log(message)
        chrome.runtime.sendMessage(message, function (response) {
            console.log(response.farewell);
        });
    }

    if (is_dhm_mall_page.length > 0) {
        message.type = "page";
        message.tab = "dhm_mall_tab";
        console.log(message)
        chrome.runtime.sendMessage(message, function (response) {
            console.log(response.farewell);
        });
    }


    chrome.extension.onMessage.addListener(function (request) {
        if ($.isEmptyObject(request.data) || is_rev_data === true) {
            //window.close();
            return false;
        }
        message.type = "finish";
        chrome.runtime.sendMessage(message);
        is_rev_data = true;
        switch (request.to) {
            case "gou_shop_tab":
                addImageCheckbox(request.data)
                return false;
            case "dhm_goods_tab":
                dealDhmGoodsInfo(request);
                return false;
            case "dhm_mall_tab":
                dealDhmMallInfo(request.data);
                return false;
            case "ok":
                window.close();
                return false;
            default :
                return false;
        }
    });
});


