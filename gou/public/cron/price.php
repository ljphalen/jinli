<?php
/**
 * update favorite reduce price status
 */
include 'common.php';

//不叠加执行
$cache = Common::getCache();
$do    = $cache->get("cron_spider_favorite_goods");
if ($do == "doing") exit("spider doing.\n");
$cache->set("cron_spider_favorite_goods", "doing");

echo "Start Time" . date('H:i:s') . "\n";
$api                      = new Api_Miigou_Data();
$start_time               = microtime(true);

$condition['type']        = 2;
$condition['create_time'] = array("<", strtotime(date("Y-m-d", strtotime("-1Day"))));
$sort                     = array('item_id' => 'ASC');

$j                        = 0;
$item_id                  = 0;
$page                     = 1;
$per_page                 = 1000;
$flags                    = 1;//是否继续标志

/**
 * +--------------------+
 * |item_id为判断条件,    |
 * |每次取固定条数 无需分页 |
 * +--------------------+
 */
while ($flags){
    $i = 0;
    $page++;
    $count_time           = microtime(true);

    $data                 = array();
    $condition['item_id'] = array('>', $item_id);
    list(,$list)          = User_Service_Favorite::getField(1,$per_page,"distinct(item_id)",$condition,$sort);
    if(empty($list)){
        $flags = 0;
        continue ;
    }
    array_walk($list, function (&$v) {
        $v     = $v['item_id'];
    });
    $item_id   = end($list);//拿出本次数据最大的ID作为下次的条件
    foreach (array_unique($list) as $key => $value) {
        $info  = Third_Service_Goods::get($value);
        $i++;
        if ($info) $api->goodsInfo($info['goods_id'], $info['id'], Client_Service_Spider::channel($info['channel_id']));
        if ($i % 2 == 1) sleep(1);
    }
    $j += $i;
    echo $page . " Rows $i Time Used: " . sprintf('%.3f', (microtime(true) - $count_time)) . "s\n";
}

$cache->set("cron_spider_favorite_goods", "done");

$end_time = (microtime(true) - $start_time);
$str_time = sprintf("%dh %dmin %ds", floor($end_time / 3600), floor(($end_time % 3600) / 60), $end_time % 60);

echo "END Time".date('H:i:s')."\n";
echo "Total $j Rows Time Used: ".$str_time."\n";

echo CRON_SUCCESS;
echo PHP_EOL;
exit;
