<?php
include 'common.php';
/**
 * 当spider未抓取到数据时，计划任务会再次抓取4次
 */

$api = new Api_Miigou_Data();

$page = 1;
$perpage  = 100;
do {
    list($total, $list) = Third_Service_GoodsUnipid::getList($page, $perpage, array('status'=>1, 'request_count'=>array("<", "5")));
    if($list){
        foreach ($list as $key=>$value) {
            Third_Service_Goods::update(array("request_count"=>$value["request_count"]+1), $value["goods_id"]);
            $info = Third_Service_Goods::get($value["goods_id"]);
            if ($info) {
                $api->goodsInfo($info['goods_id'], $info['id'], Client_Service_Spider::channel($info['channel_id']));
                echo "third good ".$info["id"]." done\n";
                sleep(2);
            }
        }
    }
    $page++;
} while ($total>($page * $perpage));

$page = 1;
$perpage  = 100;
do {
    list(, $list) = Third_Service_Shop::getList($page, $perpage, array('status'=>1, 'request_count'=>array("<", "5")),  array("id"=>"DESC"));
    if($list){
        foreach ($list as $key=>$value) {
            Third_Service_Shop::update(array("request_count"=>$value["request_count"]+1), $value["id"]);
            $api->shopInfo($value['shop_id'], $value['id'],"favorite", Client_Service_Spider::channel($value['channel_id']));
            echo "third shop ".$value["id"]." done\n";
            sleep(2);
        }
    }
    $page++;
} while ($total>($page * $perpage));

$page = 1;
$perpage  = 100;
do {
    list(, $list) = Third_Service_Web::getList($page, $perpage, array('status'=>1,  'request_count'=>array("<", "5")),  array("id"=>"DESC"));
    if($list){
        foreach ($list as $key=>$value) {
            Third_Service_Web::update(array("request_count"=>$value["request_count"]+1), $value["id"]);
            $api->web($value['url'], $value['id']);
            echo "web ".$value["url"]." done\n";
            sleep(2);
        }
    }
    $page++;
} while ($total>($page * $perpage));
echo CRON_SUCCESS;
?>