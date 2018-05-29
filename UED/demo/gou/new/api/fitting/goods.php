<?php $page = isset($_GET["page"])? $_GET["page"] : 1;?>
{
    "success": true,
    "msg": "",
    "data": {
        "list": [{
            "price": "11.00",
            "img": "http:\/\/img01.taobaocdn.com\/bao\/uploaded\/i1\/10497024330121619\/T1mCptFj4bXXXXXXXX_!!0-item_pic.jpg_460x460.jpg",
            "link": "http:\/\/gou.gionee.com\/mall\/detail?id=2891&cid=0&t_bi=_4243639497",
            "descrip": "\u8f7b\u677e\u718a\u8f66\u8f7d\u624b\u673a\u5145\u7535\u5668"
        },
        {
            "price": "128.00",
            "img": "http:\/\/img03.taobaocdn.com\/bao\/uploaded\/i3\/15710026716996555\/T1rkOsFcVaXXXXXXXX_!!0-item_pic.jpg_460x460.jpg",
            "link": "http:\/\/gou.gionee.com\/mall\/detail?id=2889&cid=0&t_bi=_4243639497",
            "descrip": "\u6728\u8d28\u79fb\u52a8\u7535\u6e90"
        },
        {
            "price": "88.00",
            "img": "http:\/\/img02.taobaocdn.com\/bao\/uploaded\/i2\/12683024937486316\/T1PNhBFe4dXXXXXXXX_!!0-item_pic.jpg_460x460.jpg",
            "link": "http:\/\/gou.gionee.com\/mall\/detail?id=2887&cid=0&t_bi=_4243639497",
            "descrip": "\u96c5\u5170\u4ed5AL-103\u5e26\u7ebf\u63a7\u5c0f\u97f3\u7bb1"
        },
        {
            "price": "299.00",
            "img": "http:\/\/img04.taobaocdn.com\/bao\/uploaded\/i4\/T1ITn9FohdXXXXXXXX_!!0-item_pic.jpg_460x460.jpg",
            "link": "http:\/\/gou.gionee.com\/mall\/detail?id=2879&cid=0&t_bi=_4243639497",
            "descrip": "jabra\u00a0clear\u84dd\u7259\u8033\u673a"
        },
        {
            "price": "76.00",
            "img": "http:\/\/gou.gionee.com\/attachs\/Mallgoods\/201312\/52bae9e517a79.jpg",
            "link": "http:\/\/gou.gionee.com\/mall\/detail?id=2871&cid=0&t_bi=_4243639497",
            "descrip": "\u8ff7\u4f60\u91d1\u5143\u5b9d\u97f3\u54cd"
        },
        {
            "price": "269.00",
            "img": "http:\/\/gou.gionee.com\/attachs\/Mallgoods\/201312\/52b83c318259c.jpg",
            "link": "http:\/\/gou.gionee.com\/mall\/detail?id=2855&cid=0&t_bi=_4243639497",
            "descrip": "\u6613\u62c9\u7f50\u5145\u7535\u5b9d6600\u6beb\u5b89"
        }],
        "hasnext": <?php echo $page==5? "false" : "true";?>,
        "curpage": <?php echo $page;?>
    }
}