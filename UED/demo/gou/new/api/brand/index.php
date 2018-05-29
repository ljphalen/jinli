<?php $page = isset($_GET["page"])? $_GET["page"] : 1;?>
{
    "success": true,
    "msg": "",
    "data": {
        "top" : {
            "banner_img": "http://a.gou.3gtest.gionee.com/attachs/gou_brand/201403/533917ba88c16.jpg",
            "brand_img": "http://gou.3gtest.gionee.com/attachs/gou",
            "id": "4",
            "logo_img": "http://gou.3gtest.gionee.com/attachs/gou"
        },
        "list": [
            {
                "id": "4",
                "is_top": "0",
                "banner_img": "http://a.gou.3gtest.gionee.com/attachs/gou_brand/201403/533917ba88c16.jpg",
                "brand_img": "http://a.gou.3gtest.gionee.com/attachs/gou_brand/201403/5338d122b7a6f.jpg",
                "logo_img": "http://a.gou.3gtest.gionee.com/attachs/gou_brand/201403/5338d12725a1f.jpg"
            },
            {
                "id": "3",
                "is_top": "1",
                "banner_img": "",
                "brand_img": "http://a.gou.3gtest.gionee.com/attachs/gou_brand/201403/5338ca06a242b.png",
                "logo_img": "http://a.gou.3gtest.gionee.com/attachs/gou_brand/201403/5338ca37e6c56.png"
            },
            {
                "id": "6",
                "is_top": "1",
                "banner_img": "http://a.gou.3gtest.gionee.com/attachs/gou_brand/201403/533917ba88c16.jpg",
                "brand_img": "http://a.gou.3gtest.gionee.com/attachs/gou_brand/201403/533917be8b2ed.jpg",
                "logo_img": "http://a.gou.3gtest.gionee.com/attachs/gou_brand/201403/533917c1c44d4.jpg"
            },
            {
                "id": "1",
                "is_top": "1",
                "banner_img": "http://a.gou.3gtest.gionee.com/attachs/gou_brand/201403/5338c7e7e41f4.png",
                "brand_img": "http://a.gou.3gtest.gionee.com/attachs/gou_brand/201403/5338c7b1969dd.jpg",
                "logo_img": "http://a.gou.3gtest.gionee.com/attachs/gou_brand/201403/5338c7bacb92b.png"
            },
            {
                "id": "5",
                "is_top": "1",
                "banner_img": "http://a.gou.3gtest.gionee.com/attachs/gou_brand/201403/5338c7e7e41f4.png",
                "brand_img": "http://a.gou.3gtest.gionee.com/attachs/gou_brand/201403/5338ff9ddc401.jpg",
                "logo_img": "http://a.gou.3gtest.gionee.com/attachs/gou_brand/201403/5338ffa4f2ec8.jpg"
            },
            {
                "id": "6",
                "is_top": "1",
                "banner_img": "http://a.gou.3gtest.gionee.com/attachs/gou_brand/201403/5338c7e7e41f4.png",
                "brand_img": "http://a.gou.3gtest.gionee.com/attachs/gou_brand/201403/5338ff9ddc401.jpg",
                "logo_img": "http://a.gou.3gtest.gionee.com/attachs/gou_brand/201403/5338ffa4f2ec8.jpg"
            }
        ],
        "hasnext": <?php echo ($page==2? 'false' : 'true');?>,
        "curpage": <?php echo $page;?>
    }
}