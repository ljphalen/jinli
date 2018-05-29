<?php
if (! defined ( 'BASE_PATH' ))
    exit ( 'Access Denied!' );
/**
 * 特价接口配置
 */
return array (
        '1' => array (
                'type' => 'moonbasa',
                'name' => '梦芭莎',
                'isXml' => true,
                'H5_channel_code' => '003',
                'APK_channel_code' => '010',
                'H5_formart' => 'http://t.moonbasa.com/rd/rdm?e=-999&adtype=0&unionid=gionee&subunionid=003&other=unionid:gionee|adtype:0|adid:-999&url=%s',
                'APK_formart' => 'http://t.moonbasa.com/rd/rdm?e=-999&adtype=0&unionid=gionee&subunionid=010&other=unionid:gionee|adtype:0|adid:-999&url=%s',
                'url' => 'http://feed.moonbasa.com//Union/WapAPI.ashx'
        ),
        '2' => array (
                'type' => 'ytaow',
                'name' => '移淘',
                'isXml' => true,
                'H5_channel_code' => '1660449',
                'APK_channel_code' => '1660457',
                'H5_formart' => '%schannel=1660449',
                'APK_formart' => '%schannel=1660457',
                'url' => 'http://ly366.cn/?c=Product&a=discount',
        ),
        '3' => array (
                'type' => 'zg51',
                'name' => '掌购',
                'isXml' => false,
                'H5_channel_code' => '6008',
                'APK_channel_code' => '600803',
                'H5_formart' => '%sChannelid=6008',
                'APK_formart' => '%sChannelid=600803',
                'url' => 'http://api.zg51.com/hdfk.asp?text=json'
        ),
        '4' => array (
                'type' => 'mbaobao',
                'name' => '麦包包',
                'isXml' => false,
                'H5_channel_code' => 'h5tejia',
                'APK_channel_code' => 'tejia',
                'H5_formart' => '%s_w=1211-h5tejia',
                'APK_formart' => '%s_w=1211-tejia',
                'url' => 'http://gbmapi.mbaobao.com/gionee/v1/getPromItemList.html'
        ),
        '5' => array (
                'type' => 'paixie',
                'name' => '拍鞋网',
                'isXml' => true,
                'H5_channel_code' => 'cCsqamlubGkrKng',
                'APK_channel_code' => 'cCsqamlubGkrKng',
                'H5_formart' => 'http://m.paixie.net/track.do?xs=cCsqamlubGkrKng=&u=%s',
                'APK_formart' => 'http://m.paixie.net/track.do?xs=cCsqamlubGkrKng=&u=%s',
                'url' => 'http://www.paixie.net/api/jinli/discount'
        ),
        '6' => array (
                'type' => '100mt',
                'name' => '蜜唐网',
                'isXml' => true,
                'H5_channel_code' => 'gionee8',
                'APK_channel_code' => 'gionee7',
                'H5_formart' => '%s',
                'APK_formart' => '%s',
                'url' => 'http://www.100mt.com/api/gioneediscount.php'
        ),
        '7' => array (
                'type' => 'mmbao',
                'name' => '买卖宝',
                'isXml' => true,
                'H5_channel_code' => '56569',
                'APK_channel_code' => '56568',
                'H5_formart' => '%s&fr=56569',
                'APK_formart' => '%s&fr=56568',
                'url' => 'http://open.mmb.cn/static/open/jinli/xml/discountProduct.xml'
        ),
        '8' => array (
            'type' => 'mogujie',
            'name' => '蘑菇街',
            'isXml' => false,
            'H5_channel_code' => 'mrtjh5',
            'APK_channel_code' => 'mrtjyuzhuang',
            'H5_formart' => 'http://www.mogujie.com/cps/open/track?uid=12mse56&feedback=mrtjh5&channel=wap&target=%s&feedback=mrtjh5',
            'APK_formart' => 'http://www.mogujie.com/cps/open/track?uid=12mse56&feedback=mrtjyuzhuang&channel=wap&target=%s&feedback=mrtjyuzhuang',
            //'url' => 'http://www.mogujie.com/cps/open/loadItem?uid=12mse56&page='
            'url'=>'http://www.mogujie.com/cps/open/loadItem?uid=125ar1o&page='
        ),
        '9' => array(
            'type' => 'meilishuo',
            'name' => '美丽说',
            'isXml' => false,
            'H5_channel_code' => 'mrtjh5',
            'APK_channel_code' => 'mrtjyuzhuang',
            'H5_formart' => '%s',
            'APK_formart' => '%s',
            'url' => 'http://adsapi.meilishuo.com/'
        ),

        '10' => array(
            'type' => 'yhd',
            'name' => '1号店',
            'isXml' => true,
            'H5_channel_code' => 'h5',
            'APK_channel_code' => 'mrtj',
            'H5_formart' => '%stracker_u=103526100',
            'APK_formart' => '%stracker_u=103526100',
            'url' => 'http://union.yhd.com/api/common/getXml.do'
        ),
        '11' => array(
            'type' => 'suning',
            'name' => '苏宁',
            'isXml' => true,
            'H5_channel_code' => 'mrtjh5',
            'APK_channel_code' => 'mrtjyuzhuang',
            'H5_formart' => 'http://union.suning.com/aas/open/vistorAd.action?userId=115611&webSiteId=0&adInfoId=00&adBookId=0&subUserEx=mrtjh5&vistURL=%s',
            'APK_formart' => 'http://union.suning.com/aas/open/vistorAd.action?userId=115611&webSiteId=0&adInfoId=00&adBookId=0&subUserEx=mrtjyuzhuang&vistURL=%s',
            'url' => 'http://unionfile.suning.com/WeiGou/'
        ),
        '12' => array(
            'type' => 'gome',
            'name' => '国美',
            'isXml' => false,
            'H5_channel_code' => 'mrtjh5',
            'APK_channel_code' => 'mrtjyuzhuang',
            'H5_formart' => 'http://cps.gome.com.cn/home/JoinUnion?sid=1569&wid=1623&feedback=mrtjh5&to=%s',
            'APK_formart' => 'http://cps.gome.com.cn/home/JoinUnion?sid=1569&wid=1623&feedback=mrtjyuzhuang&to=%s',
            'url' => 'http://open.gome.com.cn/interface/cooperate/gateway'
        ),
        '13' => array(
            'type' => 'miyabaobei',
            'name' => '蜜芽宝贝',
            'isXml' => false,
            'H5_channel_code' => 'mrtjh5',
            'APK_channel_code' => 'mrtjyuzhuang',
            'H5_formart' => '%s',
            'APK_formart' => '%s',
            'url' => 'http://api.miyabaobei.com/webunion/gouwudating/iteminfo'
        ),
);
