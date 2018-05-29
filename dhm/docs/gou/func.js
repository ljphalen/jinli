(function () {
    getParameterByName = function (name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    };
    var dt = {"data": {}, "type": "send", to: ""};
    getTmallHkGoodsDetail = function () {

        var id = getParameterByName("id");
        $.ajax({
            url: "http://hws.m.taobao.com/cache/wdetail/5.0/",
            data: {id: id},
            type: "get",
            dataType: "json",
            success: function (data) {
                dt.data = data;
                dt.from = "tmall";
                dt.to   = "dhm_goods_tab";
                return sendDataToBackground(dt);
            }
        });

    };

    getJDGoodsDetail = function () {

        var data = {};
        var dom = $("#jd-price");
        if (dom === undefined || dom.length == 0) {
            return false;
        }
        var price_str = $(dom).text().replace(/\￥/g,"");
        var price = "";
        if(price_str.split("~")[1]==undefined){
            price = {"min": price_str, "max": price_str}
        }else{
            price = {"min": price_str.split("~")[0], "max": price_str.split("~")[1]}
        }
        data.price = price;

        $("#name>h1").children().remove();
        var title = $("#name>h1").text()
        data.title  =title;

        var imageCont = $("#spec-list > div > ul > li > img");
        var images = [];
        var sernum = Math.floor(Math.random()*5);
        $.each(imageCont,function(k,v){
            var uri = $(v).data("url");
            images[k]= "http://img1"+sernum+".360buyimg.com/n0/"+uri;
        })
        data.images = images

        data.detail = $("#parameter2").html().replace(/\<\/a\>/g,"").replace(/\<a.*k\"\>/g,"");

        data.url ="http://item.m.jd.com/product"+location.pathname
        dt.from = "jd";
        dt.to   = "dhm_goods_tab";
        dt.data = data;
        return sendDataToBackground(dt);

    };
    var getAmazonPrice= function (dom) {
        if (dom == undefined || dom.length == 0) {
            console.log("get price error");
            return false;
        }
        var pstr = $(dom).html();
        pstr = pstr.replace(/^[\w|\-]+/g,"").replace(/\s+/g,"");
        var price = 0;
        if(pstr.split("-")[1]==undefined){
            pstr = getCleanPrice(pstr);
            price = {"min": pstr, "max": pstr}
        }else{
            var split_str = pstr.split("-");
            price = {"min": getCleanPrice(split_str[0]), "max":  getCleanPrice(split_str[1])}
        }

        return price;
    };
    var getCleanPrice=function(price){
        price = price.replace(/[\W|\-]+/g,"");
        return price;
    };
    getAmazonCNGoodsDetail = function (domain) {
        var price = getAmazonPrice($("#priceblock_ourprice"));
        if (!price) {
            price = getAmazonPrice($("#priceblock_saleprice"));
        }
        if (!price) {
            price = getAmazonPrice($("#ags_price_local"));
        }
        if (!price) {
            price = getAmazonPrice($("b.priceLarge"));
        }
        if (!price) {
            price = {"min": 0, "max": 0}
        }else if(/co\.jp/g.test(domain)===false){
            price.min = price.min/100;
            price.max = price.max/100;
        }
        console.log(price);

        dt.to = "dhm_mall_tab";
        var dp = /\/dp\/(.*)\/ref/g;
        var gp = /\/gp\/product\/(.*)\/ref/g;
        var reg = dp.exec(location.href);
        if(reg===null){
            reg = gp.exec(location.href);
        }
        var id = "";
        if(reg!==null){
            id = reg[1];
        }
        var url = "http://"+domain+"/gp/aw/d/"+id;
        dt.data = {"url": url , "price": price};
        return sendDataToBackground(dt);

    };

    getYmaTouGoodsDetail = function () {
        var dom = $("#proDetail > div > div.productInfoBox > div.proInfo > dl.price > dd > span");
        if (dom === undefined || dom.length == 0) {
            return false;
        }
        var price_str = $(dom).html();
        var price = "";
        if(price_str.split("~")[1]==undefined){
            price = {"min": price_str, "max": price_str}
        }else{
            price = {"min": price_str.split("~")[0], "max": price_str.split("~")[1]}
        }
        dt.to = "dhm_mall_tab";
        var url ="http://m.ymatou.com/"+location.pathname
        dt.data = {"url": url, "price": price};
        return sendDataToBackground(dt);
    }

    //获取口袋通价格
    getKouDaiTongDetail = function () {
        var dom = $("body > div.container.main.clearfix > div.content > div.goods-summary.clearfix > div.goods-info.pull-right > div.goods-price.clearfix > strong");
        if (dom === undefined || dom.length == 0) {
            return false;
        }
        $('.goods-rmb').remove();
        var price_str = $.trim($(dom).html());
        var price = "";
        if(price_str.split("~")[1]==undefined){
            price = {"min": price_str, "max": price_str}
        }else{
            price = {"min": price_str.split("~")[0], "max": price_str.split("~")[1]}
        }
        dt.to = "dhm_mall_tab";
        var alias = getParameterByName('alias');
        var url ="http://wap.koudaitong.com/v2/showcase/goods?alias="+alias
        dt.data = {"url": url, "price": price};
        return sendDataToBackground(dt);
    }


    var decodeHtml = function (html) {
        var txt = document.createElement("textarea");
        txt.innerHTML = html;
        return txt.value;
    }

    getEbayDetail = function () {
        var dom = $("#convbinPrice");
        if (dom === undefined || dom.length == 0) {
            dom = $("#convbidPrice");
        }
        if (dom === undefined || dom.length == 0) {
            return false;
        }
        $(dom).children().remove();
        var price_str = decodeHtml($(dom).text());
        price_str = price_str.replace(/[a-zA-Z\,\@]+/g,"").trim();
        var price = "";
        if(price_str.split("~")[1]==undefined){
            price = {"min": price_str, "max": price_str}
        }else{
            price = {"min": price_str.split("~")[0], "max": price_str.split("~")[1]}
        }
        dt.to = "dhm_mall_tab";
        var itm = getParameterByName('itm');
        var regex = /\/itm\/.*\/([0-9]+)/g;
        var res   = regex.exec(location.pathname);
        if(res!=null){
            itm = res[1];
        }
        var url ="http://m.ebay.com/itm/"+itm;
        dt.data = {"url": url, "price": price};
        return sendDataToBackground(dt);
    }

    getSaSaDetail = function () {
        var dom = $("div.content > div:nth-child(2) > big");
        if (dom === undefined || dom.length == 0) {
            return false;
        }
        var price_str = $(dom).html();
        price_str = price_str.replace(/[A-Z\¥\$]+/g,"").trim();
        var price = "";
        if(price_str.split("~")[1]==undefined){
            price = {"min": price_str, "max": price_str}
        }else{
            price = {"min": price_str.split("~")[0], "max": price_str.split("~")[1]}
        }
        dt.to = "dhm_mall_tab";
        var itemno = getParameterByName('itemno');
        var url ="https://web1.sasa.com/SasaWeb/m/sc/sasa/product_detail.jsp?itemno="+itemno;
        dt.data = {"url": url, "price": price};
        return sendDataToBackground(dt);
    };

    getGMarketDetail = function () {
        var dom = $("#trCostPrice > td > div > strong");
        if (dom === undefined || dom.length == 0) {
            return false;
        }

        var price_str = $(dom).text();
        price_str = price_str.replace(/[\￦\,]+/g,"").trim();
        var price = "";
        if(price_str.split("~")[1]==undefined){
            price = {"min": price_str, "max": price_str}
        }else{
            price = {"min": price_str.split("~")[0], "max": price_str.split("~")[1]}
        }
        dt.to = "dhm_mall_tab";
        var goodscode = getParameterByName("goodscode");
        var url = "http://mitem.gmarket.co.kr/Item?goodscode="+goodscode;
        dt.data = {"url": url, "price": price};
        return sendDataToBackground(dt);
    };

    getLotteiMallDetail = function () {
        var dom = $("div.price > div.price2");
        if (dom === undefined || dom.length == 0) {
            dom = $("dd.d2 > span.price >strong");
        }
        if (dom === undefined || dom.length == 0) {
            return false;
        }
        $(dom).children().remove();
        var price_str = $(dom).text();
        price_str = price_str.replace(/[\￦\,]+/g,"").trim();
        var price = "";
        if(price_str.split("~")[1]==undefined){
            price = {"min": price_str, "max": price_str}
        }else{
            price = {"min": price_str.split("~")[0], "max": price_str.split("~")[1]}
        }
        dt.to = "dhm_mall_tab";
        var goods_no = getParameterByName('goods_no');
        var url = "http://m.lotteimall.com/goods/viewGoodsDetail.lotte?goods_no="+goods_no;
        dt.data = {"url": url, "price": price};
        return sendDataToBackground(dt);
    };

    getTheBodyShopDetail = function () {
        var dom = $("div.quantity > p.price");
        if (dom === undefined || dom.length == 0) {
            return false;
        }
        var price_str = $(dom).text();
        price_str = price_str.replace(/[\D]+/g,"").trim();
        var price = "";
        if(price_str.split("~")[1]==undefined){
            price = {"min": price_str, "max": price_str}
        }else{
            price = {"min": price_str.split("~")[0], "max": price_str.split("~")[1]}
        }
        dt.to = "dhm_mall_tab";
        var url = location.href;
        dt.data = {"url": url, "price": price};
        return sendDataToBackground(dt);
    };

    getBonjourDetail = function () {
        var dom = $("p.ProductDetailsRed");
        if (dom === undefined || dom.length == 0) {
            return false;
        }
        var price_str = $(dom).text();
        reg = /[\$\￥]+([0-9\.]*)/g
        price_arr = reg.exec(price_str);
        console.log(price_arr)
        price_str = price_arr[1];
        var price = "";
        if(price_str.split("~")[1]==undefined){
            price = {"min": price_str, "max": price_str}
        }else{
            price = {"min": price_str.split("~")[0], "max": price_str.split("~")[1]}
        }
        dt.to = "dhm_mall_tab";
        var url = location.href;
        dt.data = {"url": url, "price": price};
        return sendDataToBackground(dt);
    }

    getColourmixDetail = function () {
        var dom = $("span.txtSale>span.str_price");
        if (dom === undefined || dom.length == 0) {
            return false;
        }
        var price_str = $(dom).html();
        price_str = price_str.replace(/[A-Z\$\s]/g,"");
        var price = "";
        if(price_str.split("~")[1]==undefined){
            price = {"min": price_str, "max": price_str}
        }else{
            price = {"min": price_str.split("~")[0], "max": price_str.split("~")[1]}
        }
        dt.to = "dhm_mall_tab";
        var url = location.href;
        dt.data = {"url": url, "price": price};
        return sendDataToBackground(dt);
    }

    getWatsonsDetail = function () {
        var dom = $("li.price_grey");
        if (dom === undefined || dom.length == 0) {
            return false;
        }
        var price_str = $(dom).html();
        price_str = price_str.replace(/[A-Z\$\s]/g,"");
        var price = "";
        if(price_str.split("~")[1]==undefined){
            price = {"min": price_str, "max": price_str}
        }else{
            price = {"min": price_str.split("~")[0], "max": price_str.split("~")[1]}
        }
        dt.to = "dhm_mall_tab";
        reg = /\/p\/([\w\_]*)/g
        var prod = reg.exec(location.pathname);
        var url = "http://www.watsons.com.hk/p/"+prod[1];
        dt.data = {"url": url, "price": price};
        return sendDataToBackground(dt);
    }

    getCosmeDetail = function () {
        var dom = $("div.sectionH3 >dl>dd>strong");
        if (dom === undefined || dom.length == 0) {
            return false;
        }
        var price_str = $(dom).html();
        price_str = price_str.replace(/[\,]/g,"");
        var price = "";
        if(price_str.split("~")[1]==undefined){
            price = {"min": price_str, "max": price_str}
        }else{
            price = {"min": price_str.split("~")[0], "max": price_str.split("~")[1]}
        }
        dt.to = "dhm_mall_tab";
        var url = location.href;
        dt.data = {"url": url, "price": price};
        return sendDataToBackground(dt);
    }


    var sendDataToBackground = function () {
        console.log(dt);
        chrome.runtime.sendMessage(dt, function (response) {
            console.log(response.farewell);
            return true;
        });
    }
    dealDhmGoodsInfo = function (request) {

        switch(request.from){
            case "jd":
                return dealJDGoods(request.data);
            case "tmall":
            default :
                return dealTmallGoods(request.data);
        }


        //---------end price-------------
    };

    var dealJDGoods = function (data) {
        console.log($("#addForm input[name=title]"));
        $("#addForm input[name=title]").val(data.title);
        $("#addForm input[name=url]").val(data.url);
        $("#addForm input[name=mall_from]").val("京东");
        $("#pre_content").val(data.detail);

        var inner = "";
        $.each(data.images, function (k, v) {
            url = v.replace(/n0/,"n4");
            if(k<3){

                inner += '<input type="checkbox" name="image[]" value="' + v + '" checked="checked" /><img src="' + url + '" alt=""/>';
            }else{

                inner += '<input type="checkbox" name="image[]" value="' + v + '" /><img src="' + url + '" alt=""/>';
            }
        });
        $("td#img_box").html(inner);

        inner = "";
        $.each(data.images, function (k, v) {
            url = v.replace(/n0/,"n4");
            if(k<1) {
                inner += '<input type="radio" name="cover_img" value="' + v + '" checked="checked" /><img src="' + url + '" alt=""/>';
            }else{
                inner += '<input type="radio" name="cover_img" value="' + v + '" /><img src="' + url + '" alt=""/>';
            }
        });
        $("td#img_cover_box").html(inner);

        $("#addForm input[name=min_price]").val(data.price.min);
        $("#addForm input[name=max_price]").val(data.price.max);

    };

    var dealTmallGoods = function (data) {
        var itemInfoModel = data.data.itemInfoModel;
        $("#addForm input[name=title]").val(itemInfoModel.title);
        $("#addForm input[name=url]").val(itemInfoModel.itemUrl);
        $("#addForm input[name=mall_from]").val("天猫");

        //---------start images -----------
        var imgs = itemInfoModel.picsPath;

        var inner = "";
        $.each(imgs, function (k, v) {
            if(k<3){
                inner += '<input type="checkbox" name="image[]" value="' + v + '_540x540.jpg" checked="checked" /><img src="' + v + '_80x80.jpg" alt=""/>';
            }else  {
                inner += '<input type="checkbox" name="image[]" value="' + v + '_540x540.jpg" /><img src="' + v + '_80x80.jpg" alt=""/>';
            }
        });
        $("td#img_box").html(inner);

        inner = "";
        $.each(imgs, function (k, v) {
            if(k<1){
            inner += '<input type="radio" name="cover_img" value="' + v + '_540x540.jpg" checked="checked"/><img src="' + v + '_80x80.jpg" alt=""/>';
            }else{
                inner += '<input type="radio" name="cover_img" value="' + v + '_540x540.jpg" /><img src="' + v + '_80x80.jpg" alt=""/>';
            }
        });
        $("td#img_cover_box").html(inner);


        //---------end  images-------------

        //---------start desc--------------
        var props = data.data.props
        var desc = "";
        $.each(props, function (k, p) {
            desc += "<li>" + p.name + ": " + p.value + "</li>"
        });

        $('#pre_content').val(desc);
        //---------end desc----------------


        //---------start price-------------
        var apiStack = JSON.parse(data.data.apiStack[0].value);
        var price = apiStack.data.itemInfoModel.priceUnits[0].price;
        var rangeprice = price.split("-");
        if (rangeprice.length > 1) {
            $("#addForm input[name=min_price]").val(rangeprice[0]);
            $("#addForm input[name=max_price]").val(rangeprice[1]);
        } else {
            $("#addForm input[name=min_price]").val(rangeprice[0]);
            $("#addForm input[name=max_price]").val(rangeprice[0]);
        }
    };

    addImageCheckbox = function (data) {
        var inner = "";
        $.each(data, function (k, v) {
            if (k < 3) {
                inner += '<input type="checkbox" name="goods_img[]" value="' + v + '" checked="checked" /><img src="' + v + '" alt=""/>';
            } else {
                inner += '<input type="checkbox" name="goods_img[]" value="' + v + '" /><img src="' + v + '" alt=""/>';
            }
        });
        $("td#img_box").html(inner);
    };
    dealDhmMallInfo = function (data) {
        $("#addForm input[name=min_price]").val(data.price.min);
        $("#addForm input[name=max_price]").val(data.price.max);
        $("#addForm input[name=url]").val(data.url);
    }

})();

