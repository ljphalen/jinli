<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class CrawController extends Api_BaseController {


    //
    private function getD() {
        if(ENV == "develop") {
            return "http://dev.findjoy.cn/";
        } else {
            return "http://www.miigou.com/";
        }
    }

    public function get_dianqiAction() {

        $tmall_danqi = array(
           array(
               "url"=>"http://ald.taobao.com/recommend.htm?appId=03507&areaId=440300&size=15&page=1&type=1&catId=2",
               "category_id"=>Craw_Service_Const::DQ_A,
           ),
            array(
                "url"=>"http://ald.taobao.com/recommend.htm?appId=03507&areaId=440300&size=15&page=1&type=1&catId=5",
                "category_id"=>Craw_Service_Const::DQ_B,
            ),
            array(
                "url"=>"http://ald.taobao.com/recommend.htm?appId=03507&areaId=440300&size=15&page=1&type=1&catId=1",
                "category_id"=>Craw_Service_Const::DQ_C,
            ),
            array(
                "url"=>"http://ald.taobao.com/recommend.htm?appId=03507&areaId=440300&size=15&page=1&type=1&catId=8",
                "category_id"=>Craw_Service_Const::DQ_D,
            ),
            array(
                "url"=>"http://ald.taobao.com/recommend.htm?appId=03507&areaId=440300&size=15&page=1&type=1&catId=6",
                "category_id"=>Craw_Service_Const::DQ_E,
            ),
            array(
                "url"=>"http://ald.taobao.com/recommend.htm?appId=03507&areaId=440300&size=15&page=1&type=1&catId=7",
                "category_id"=>Craw_Service_Const::DQ_F,
            ),
            array(
                "url"=>"http://ald.taobao.com/recommend.htm?appId=03507&areaId=440300&size=15&page=1&type=1&catId=3",
                "category_id"=>Craw_Service_Const::DQ_G,
            ),
            array(
                "url"=>"http://ald.taobao.com/recommend.htm?appId=03507&areaId=440300&size=15&page=1&type=1&catId=17",
                "category_id"=>Craw_Service_Const::DQ_H,
            ),
            array(
                "url"=>"http://ald.taobao.com/recommend.htm?appId=03507&areaId=440300&size=15&page=1&type=1&catId=10",
                "category_id"=>Craw_Service_Const::DQ_I,
            ),
            array(
                "url"=>"http://ald.taobao.com/recommend.htm?appId=03507&areaId=440300&size=15&page=1&type=1&catId=11",
                "category_id"=>Craw_Service_Const::DQ_J,
            ),
            array(
                "url"=>"http://ald.taobao.com/recommend.htm?appId=03507&areaId=440300&size=15&page=1&type=1&catId=9",
                "category_id"=>Craw_Service_Const::DQ_K,
            ),
            array(
                "url"=>"http://ald.taobao.com/recommend.htm?appId=03507&areaId=440300&size=15&page=1&type=1&catId=4",
                "category_id"=>Craw_Service_Const::DQ_L,
            ),
            array(
                "url"=>"http://ald.taobao.com/recommend.htm?appId=03507&areaId=440300&size=15&page=1&type=1&catId=12",
                "category_id"=>Craw_Service_Const::DQ_M,
            ),
            array(
                "url"=>"http://ald.taobao.com/recommend.htm?appId=03507&areaId=440300&size=15&page=1&type=1&catId=13",
                "category_id"=>Craw_Service_Const::DQ_N,
            ),
            array(
                "url"=>"http://ald.taobao.com/recommend.htm?appId=03507&areaId=440300&size=15&page=1&type=1&catId=14",
                "category_id"=>Craw_Service_Const::DQ_O,
            ),
            array(
                "url"=>"http://ald.taobao.com/recommend.htm?appId=03507&areaId=440300&size=15&page=1&type=1&catId=16",
                "category_id"=>Craw_Service_Const::DQ_P,
            ),
            array(
                "url"=>"http://ald.taobao.com/recommend.htm?appId=03507&areaId=440300&size=15&page=1&type=1&catId=15",
                "category_id"=>Craw_Service_Const::DQ_Q,
            ),
        );

        $craw = "http://127.0.0.1:8090/api/tmall/list";
        $callback = $this->getD()."api/craw/dianqi";

        foreach($tmall_danqi as $value){

            $curl = new Util_Http_Curl($craw);
            $curl->setData(array(
                "callback"=>sprintf("%s?category_id=%d&channel_id=%d", $callback, $value["category_id"], Craw_Service_Const::CHL_TMALL),
                "url"=>$value["url"],
            ));
            $result = $curl->send("POST");
            sleep(2);
        }
    }

    public function dianqiAction() {
        $category_id = $this->getInput("category_id");
        $channel_id = $this->getInput("channel_id");

        $data = html_entity_decode($this->getPost("data"));
        $data = json_decode(str_replace('\"', '"', $data), true);

        $params = $data["params"];
        $list = $data["list"]["data"];

        foreach ($list as $key=>$value) {
            $item = array(
                'category_id'=>$category_id,
                'channel_id'=>$channel_id,
                "item_id"=>$value["id"],
                "title"=>$value["txt"],
                "price"=>$value["price"],
                "img"=>$value["img"],
                "origi_price"=>$value["originalprice"],
                "sale_num"=>$value["realMonthSellNum"],
                "sort"=>$value["_pos_"],
            );
            $ret = Craw_Service_Goods::addGoods($item);
            if ($ret) {
                Common::log($item['item_id'] ." update done.", "craw.log");
            }
        }
    }



    
    public function get_nanzhuangAction() {
        $tmall_nanzhaung = array(
            array(
                "url"=>"http://ald.taobao.com/recommend.htm?&appId=03363&pageNo=1&pageSize=200&typeId=nanzhuang&catId=catlevel2_50000436",
                "category_id"=>Craw_Service_Const::NZ_A,
            ),
            array(
                "url"=>"http://ald.taobao.com/recommend.htm?&appId=03363&pageNo=1&pageSize=200&typeId=nanzhuang&catId=catlevel2_50010158",
                "category_id"=>Craw_Service_Const::NZ_B,
            ),
            array(
                "url"=>"http://ald.taobao.com/recommend.htm?&appId=03363&pageNo=1&pageSize=200&typeId=nanzhuang&catId=catlevel2_50011159",
                "category_id"=>Craw_Service_Const::NZ_C,
            ),
            array(
                "url"=>"http://ald.taobao.com/recommend.htm?&appId=03363&pageNo=1&pageSize=200&typeId=nanzhuang&catId=catlevel2_50011123",
                "category_id"=>Craw_Service_Const::NZ_D,
            ),
            array(
                "url"=>"http://ald.taobao.com/recommend.htm?&appId=03363&pageNo=1&pageSize=200&typeId=nanzhuang&catId=catlevel2_3035",
                "category_id"=>Craw_Service_Const::NZ_E,
            ),
            array(
                "url"=>"http://ald.taobao.com/recommend.htm?&appId=03363&pageNo=1&pageSize=200&typeId=nanzhuang&catId=catlevel2_50010159",
                "category_id"=>Craw_Service_Const::NZ_F,
            ),
            array(
                "url"=>"http://ald.taobao.com/recommend.htm?&appId=03363&pageNo=1&pageSize=200&typeId=nanzhuang&catId=catlevel2_50010167",
                "category_id"=>Craw_Service_Const::NZ_G,
            ),
            array(
                "url"=>"http://ald.taobao.com/recommend.htm?&appId=03363&pageNo=1&pageSize=200&typeId=nanzhuang&catId=catlevel2_50010160",
                "category_id"=>Craw_Service_Const::NZ_H,
            ),
            array(
                "url"=>"http://ald.taobao.com/recommend.htm?&appId=03363&pageNo=1&pageSize=200&typeId=nanzhuang&catId=catlevel2_50010402",
                "category_id"=>Craw_Service_Const::NZ_I,
            ),
            array(
                "url"=>"http://ald.taobao.com/recommend.htm?&appId=03363&pageNo=1&pageSize=200&typeId=nanzhuang&catId=catlevel2_50000557",
                "category_id"=>Craw_Service_Const::NZ_J,
            ),
        );

        $craw = "http://127.0.0.1:8090/api/tmall/list";
        $callback = $this->getD()."api/craw/nanzhuang";

        foreach($tmall_nanzhaung as $value){

            $curl = new Util_Http_Curl($craw);
            $curl->setData(array(
                "callback"=>sprintf("%s?category_id=%d&channel_id=%d", $callback, $value["category_id"], Craw_Service_Const::CHL_TMALL),
                "url"=>$value["url"],
            ));
            $result = $curl->send("POST");
            sleep(1);
        }
    }

    public function nanzhuangAction() {
        $category_id = $this->getInput("category_id");
        $channel_id = $this->getInput("channel_id");

        $data = html_entity_decode($this->getPost("data"));
        $data = json_decode(str_replace('\"', '"', $data), true);

        $params = $data["params"];
        $list = $data["list"]["data"];

        foreach ($list as $key=>$value) {
            $item = array(
                'category_id'=>$category_id,
                'channel_id'=>$channel_id,
                "item_id"=>$value["itemId"],
                "title"=>$value["itemTitle"],
                "price"=>$value["promotionPrice"],
                "img"=>$value["pictUrl"],
                "origi_price"=>$value["reservePrice"],
                "sale_num"=>$value["sold"],
                "sort"=>$value["_pos_"],
            );
            $ret = Craw_Service_Goods::addGoods($item);
            if ($ret) {
                Common::log($item['item_id'] ." update done.", "craw.log");
            }
        }
    }



    public function get_xiangbaoAction() {
        $tmall_xiangbao = array(
            array(
                "url"=>"http://ald.taobao.com/recommend.htm?appId=03367&pageNo=1&pageSize=50&typeId=xiangbao&catId=53690009",
                "category_id"=>Craw_Service_Const::XB_A,
            ),
            array(
                "url"=>"http://ald.taobao.com/recommend.htm?appId=03367&pageNo=1&pageSize=50&typeId=xiangbao&catId=53700004",
                "category_id"=>Craw_Service_Const::XB_B,
            ),
            array(
                "url"=>"http://ald.taobao.com/recommend.htm?appId=03367&pageNo=1&pageSize=50&typeId=xiangbao&catId=55186007",
                "category_id"=>Craw_Service_Const::XB_C,
            ),
            array(
                "url"=>"http://ald.taobao.com/recommend.htm?appId=03367&pageNo=1&pageSize=50&typeId=xiangbao&catId=55758010",
                "category_id"=>Craw_Service_Const::XB_D,
            ),
            array(
                "url"=>"http://ald.taobao.com/recommend.htm?appId=03367&pageNo=1&pageSize=50&typeId=xiangbao&catId=50072921",
                "category_id"=>Craw_Service_Const::XB_E,
            ),
            array(
                "url"=>"http://ald.taobao.com/recommend.htm?appId=03367&pageNo=1&pageSize=50&typeId=xiangbao&catId=53718001",
                "category_id"=>Craw_Service_Const::XB_F,
            ),
            array(
                "url"=>"http://ald.taobao.com/recommend.htm?appId=03367&pageNo=1&pageSize=50&typeId=xiangbao&catId=51024007",
                "category_id"=>Craw_Service_Const::XB_G,
            ),
            array(
                "url"=>"http://ald.taobao.com/recommend.htm?appId=03367&pageNo=1&pageSize=50&typeId=xiangbao&catId=53724001",
                "category_id"=>Craw_Service_Const::XB_H,
            ),
        );

        $craw = "http://127.0.0.1:8090/api/tmall/list";
        $callback = $this->getD()."api/craw/xiangbao";

        foreach($tmall_xiangbao as $value){

            $curl = new Util_Http_Curl($craw);
            $curl->setData(array(
                "callback"=>sprintf("%s?category_id=%d&channel_id=%d", $callback, $value["category_id"], Craw_Service_Const::CHL_TMALL),
                "url"=>$value["url"],
            ));
            $result = $curl->send("POST");
            sleep(1);
        }
    }

    public function xiangbaoAction() {
        $category_id = $this->getInput("category_id");
        $channel_id = $this->getInput("channel_id");

        $data = html_entity_decode($this->getPost("data"));
        $data = json_decode(str_replace('\"', '"', $data), true);

        $params = $data["params"];
        $list = $data["list"]["data"];

        foreach ($list as $key=>$value) {
            preg_match('/id=(\d+)/', $value['itemUrl'], $matches);
            $item = array(
                'category_id'=>$category_id,
                'channel_id'=>$channel_id,
                "item_id"=>$matches[1],
                "title"=>$value["itemTitle"],
                "price"=>$value["promotionPrice"],
                "img"=>$value["pictUrl"],
                "origi_price"=>$value["reservePrice"],
                "sale_num"=>$value["sold"],
                "sort"=>$value["_pos_"],
            );
            $ret = Craw_Service_Goods::addGoods($item);
            if ($ret) {
                Common::log($item['item_id'] ." update done.", "craw.log");
            }
        }
    }




    public function getmuyingAction() {
        $tmall_muying = array(
            array(
                "url"=>"http://plaza.baby.tmall.com/item/getRecommendItems.do?itemNum=200&babyStage=1&cat=nursing",
                "category_id"=>Craw_Service_Const::MY_A,
            ),
            array(
                "url"=>"http://plaza.baby.tmall.com/item/getRecommendItems.do?itemNum=200&babyStage=1&cat=milk",
                "category_id"=>Craw_Service_Const::MY_B,
            ),
            array(
                "url"=>"http://plaza.baby.tmall.com/item/getRecommendItems.do?itemNum=200&babyStage=1&cat=diaper",
                "category_id"=>Craw_Service_Const::MY_C,
            ),
            array(
                "url"=>"http://plaza.baby.tmall.com/item/getRecommendItems.do?itemNum=200&babyStage=1&cat=feeding",
                "category_id"=>Craw_Service_Const::MY_C,
            ),
            array(
                "url"=>"http://plaza.baby.tmall.com/item/getRecommendItems.do?itemNum=200&babyStage=1&cat=shoes",
                "category_id"=>Craw_Service_Const::MY_E,
            ),
            array(
                "url"=>"http://plaza.baby.tmall.com/item/getRecommendItems.do?itemNum=200&babyStage=1&cat=bedwalk",
                "category_id"=>Craw_Service_Const::MY_F,
            ),
            array(
                "url"=>"http://plaza.baby.tmall.com/item/getRecommendItems.do?itemNum=200&babyStage=1&cat=nursing",
                "category_id"=>Craw_Service_Const::MY_G,
            ),
            array(
                "url"=>"http://plaza.baby.tmall.com/item/getRecommendItems.do?itemNum=200&babyStage=1&cat=toys",
                "category_id"=>Craw_Service_Const::MY_H,
            ),
        );

        $craw = "http://127.0.0.1:8090/api/tmall/list";
        $callback = $this->getD()."api/craw/muying";

        foreach($tmall_muying as $value){

            $curl = new Util_Http_Curl($craw);
            $curl->setData(array(
                "callback"=>sprintf("%s?category_id=%d&channel_id=%d", $callback, $value["category_id"], Craw_Service_Const::CHL_TMALL),
                "url"=>$value["url"],
            ));
            $result = $curl->send("POST");
            sleep(1);
        }
    }

    public function muyingAction() {
        $category_id = $this->getInput("category_id");
        $channel_id = $this->getInput("channel_id");

        $data = html_entity_decode($this->getPost("data"));
        $data = json_decode(str_replace('\"', '"', $data), true);

        $params = $data["params"];
        $list = $data["list"]["data"]["items"];

        $i=0;
        foreach ($list as $key=>$value) {
            preg_match('/id=(\d+)/', $value['pcItemUrl'], $matches);
            $item = array(
                'category_id'=>$category_id,
                'channel_id'=>$channel_id,
                "item_id"=>$matches[1],
                "title"=>$value["title"],
                "price"=>$value["price"],
                "img"=>$value["pic"],
                "origi_price"=>$value["origi_price"],
                "sale_num"=>$value["salesVolume"],
                "sort"=>$i++,
            );
            $ret = Craw_Service_Goods::addGoods($item);
            if ($ret) {
                Common::log($item['item_id'] ." muying update done.", "craw.log");
            }
        }
    }


    public function getJkjAction() {
        $jkj = array(
            array(
                'url'=>'http://jiukuaiyoucom.uz.taobao.com/?m=index&cat=fushi',
                'category_id'=>Craw_Service_Const::JKJ_A
            ),
            array(
                'url'=>'http://jiukuaiyoucom.uz.taobao.com/?m=index&cat=jujia',
                'category_id'=>Craw_Service_Const::JKJ_B
            ),
            array(
                'url'=>'http://jiukuaiyoucom.uz.taobao.com/?m=index&cat=muying',
                'category_id'=>Craw_Service_Const::JKJ_C
            ),
            array(
                'url'=>'http://jiukuaiyoucom.uz.taobao.com/?m=index&cat=meishi',
                'category_id'=>Craw_Service_Const::JKJ_D
            ),
            array(
                'url'=>'http://jiukuaiyoucom.uz.taobao.com/?m=index&cat=xiebaopeishi',
                'category_id'=>Craw_Service_Const::JKJ_E
            ),
            array(
                'url'=>'http://jiukuaiyoucom.uz.taobao.com/?m=index&cat=meizhuang',
                'category_id'=>Craw_Service_Const::JKJ_F
            ),
            array(
                'url'=>'http://jiukuaiyoucom.uz.taobao.com/?m=index&cat=shuma',
                'category_id'=>Craw_Service_Const::JKJ_G
            ),
            array(
                'url'=>'http://jiukuaiyoucom.uz.taobao.com/?m=index&cat=wenti',
                'category_id'=>Craw_Service_Const::JKJ_H
            ),
        );

        $craw = "http://127.0.0.1:8090/api/jkj/list";
        $callback = $this->getD()."api/craw/jkj";

        foreach($jkj as $value){

            $curl = new Util_Http_Curl($craw);
            $curl->setData(array(
                "callback"=>sprintf("%s?category_id=%d", $callback, $value["category_id"]),
                "url"=>$value["url"]
            ));
            $result = $curl->send("POST");
            sleep(1);
        }
    }

    public function jkjAction() {
        $category_id = $this->getInput("category_id");

        $data = html_entity_decode($this->getPost("data"));
        $data = json_decode(str_replace('\"', '"', $data), true);

        $params = $data["params"];
        $list = $data["list"];

        foreach ($list as $key=>$value) {
            $item = array(
                'category_id'=>$category_id,
                'channel_id'=>($value["chl"] == "tmall") ? Craw_Service_Const::CHL_TMALL : Craw_Service_Const::CHL_TAOBAO,
                "item_id"=>$value["item_id"],
                "title"=>$value["title"],
                "price"=>$value["price"],
                "img"=>$value["img"],
                "origi_price"=>$value["origi_price"],
                "sale_num"=>0,
                "data"=>array(
                    "url"=>$value["url"],
                    "gai"=>$value["gai"]
                )
            );
            $ret = Craw_Service_Goods::addGoods($item);
            if ($ret) {
                Common::log($item["item_id"] ." jkj update done.", "craw.log");
            }
        }
    }

    public function getJpAction() {
        $jkj = array(
            array(
                'url'=>'http://juanpi.uz.taobao.com/?m=index&cat=shangyi',
                'category_id'=>Craw_Service_Const::JP_FS_A
            ),
            array(
                'url'=>'http://juanpi.uz.taobao.com/?m=index&cat=qunku',
                'category_id'=>Craw_Service_Const::JP_FS_B
            ),
            array(
                'url'=>'http://juanpi.uz.taobao.com/?m=index&cat=taozhuang',
                'category_id'=>Craw_Service_Const::JP_FS_C
            ),
            array(
                'url'=>'http://juanpi.uz.taobao.com/?m=index&cat=neiyi',
                'category_id'=>Craw_Service_Const::JP_FS_D
            ),
            array(
                'url'=>'http://juanpi.uz.taobao.com/?m=index&cat=xiezi',
                'category_id'=>Craw_Service_Const::JP_FS_E
            ),
            array(
                'url'=>'http://juanpi.uz.taobao.com/?m=index&cat=xiangbao',
                'category_id'=>Craw_Service_Const::JP_FS_F
            ),
            array(
                'url'=>'http://juanpi.uz.taobao.com/?m=index&cat=peishi',
                'category_id'=>Craw_Service_Const::JP_FS_G
            ),
            array(
                'url'=>'http://juanpi.uz.taobao.com/?m=index&cat=zhonglaonian',
                'category_id'=>Craw_Service_Const::JP_FS_H
            ),
            //
            array(
                'url'=>'http://juanpi.uz.taobao.com/?m=index&cat=tongzhuang',
                'category_id'=>Craw_Service_Const::JP_MY_A
            ),
            array(
                'url'=>'http://juanpi.uz.taobao.com/?m=index&cat=tongxie',
                'category_id'=>Craw_Service_Const::JP_MY_B
            ),
            array(
                'url'=>'http://juanpi.uz.taobao.com/?m=index&cat=yingyouyongpin',
                'category_id'=>Craw_Service_Const::JP_MY_C
            ),
            array(
                'url'=>'http://juanpi.uz.taobao.com/?m=index&cat=xuexiwanju',
                'category_id'=>Craw_Service_Const::JP_MY_D
            ),
            array(
                'url'=>'http://juanpi.uz.taobao.com/?m=index&cat=yunmazhuangqu',
                'category_id'=>Craw_Service_Const::JP_MY_E
            ),
            //
            array(
                'url'=>'http://juanpi.uz.taobao.com/?m=index&cat=chuangpinbuyi',
                'category_id'=>Craw_Service_Const::JP_JJ_A
            ),
            array(
                'url'=>'http://juanpi.uz.taobao.com/?m=index&cat=jujiabaihuo',
                'category_id'=>Craw_Service_Const::JP_JJ_C
            ),
            array(
                'url'=>'http://juanpi.uz.taobao.com/?m=index&cat=canchuqingjie',
                'category_id'=>Craw_Service_Const::JP_JJ_D
            ),
            array(
                'url'=>'http://juanpi.uz.taobao.com/?m=index&cat=jiajujiancai',
                'category_id'=>Craw_Service_Const::JP_JJ_E
            ),
            //
            array(
                'url'=>'http://juanpi.uz.taobao.com/?m=index&cat=shuma',
                'category_id'=>Craw_Service_Const::JP_QT_A
            ),
            array(
                'url'=>'http://juanpi.uz.taobao.com/?m=index&cat=meishi',
                'category_id'=>Craw_Service_Const::JP_QT_B
            ),
            array(
                'url'=>'http://juanpi.uz.taobao.com/?m=index&cat=meizhuang',
                'category_id'=>Craw_Service_Const::JP_QT_C
            ),
            array(
                'url'=>'http://juanpi.uz.taobao.com/?m=index&cat=wenti',
                'category_id'=>Craw_Service_Const::JP_QT_D
            ),
        );

        $craw = "http://127.0.0.1:8090/api/jp/list";
        $callback = $this->getD()."api/craw/jp";

        foreach($jkj as $value){

            $curl = new Util_Http_Curl($craw);
            $curl->setData(array(
                "callback"=>sprintf("%s?category_id=%d", $callback, $value["category_id"]),
                "url"=>$value["url"]
            ));
            $result = $curl->send("POST");
            sleep(1);
        }
    }

    public function jpAction() {
        $category_id = $this->getInput("category_id");

        $data = html_entity_decode($this->getPost("data"));
        $data = json_decode(str_replace('\"', '"', $data), true);



        $params = $data["params"];
        $list = $data["list"];


        foreach ($list as $key=>$value) {
            $item = array(
                'category_id'=>$category_id,
                'channel_id'=>($value["chl"] == "tmall") ? Craw_Service_Const::CHL_TMALL : Craw_Service_Const::CHL_TAOBAO,
                "item_id"=>$value["item_id"],
                "title"=>$value["title"],
                "price"=>$value["price"],
                "img"=>$value["img"],
                "origi_price"=>$value["origi_price"],
                "sale_num"=>0,
                "data"=>array(
                    "url"=>$value["url"],
                    "gai"=>$value["gai"]
                )
            );
            $ret = Craw_Service_Goods::addGoods($item);
            if ($ret) {
                Common::log($item["item_id"] ." jkj update done.", "craw.log");
            }
        }
    }
}