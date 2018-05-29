<?php
include 'common.php';
/**
 *统计
 */
$cache = Common::getCache();

//update pv to mysql
$pv  = $cache->get('ht_pv');

foreach($pv as $key=>$value) {
    if ($value) {
        if (Wifi_Service_Stat::incrementPv($value, $key)) {
            unset($pv[$key]);
        }
    }
}
echo "ht_uv update done...\n";
$cache->set('ht_pv', $pv);

$uv  = $cache->get('ht_uv');
foreach($uv as $key=>$value) {
    if ($value) {
        if (Wifi_Service_Stat::incrementUv($value, $key)) {
            unset($uv[$key]);
        }
    }
}
echo "ht_pv update done...\n";
$cache->set('ht_uv', $uv);
