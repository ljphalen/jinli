<?php
include 'common.php';
$date = date('Ymd');
$vers = array(
    'all_h5nav'    => 'H5导航',
    'all_localnav' => '本地化',
    'all_nav_news' => '新闻二级页面',
    'all_nav_fun'  => '段子二级页面',
    'all_nav_pic'  => '美图二级页面',
);



for($i=0;$i<=30;$i++) {

}

$date = date('Ymd',strtotime("-1 day")) ;
foreach ($vers as $key => $name) {
    $out = Gionee_Service_LocalNavList::run_all_stat_data($date, $key,0);
    echo $name . ':' . Common::jsonEncode($out) . "\n";
    $out = Gionee_Service_LocalNavList::run_all_stat_data($date, $key,1);
    echo $name . ':' . Common::jsonEncode($out) . "\n";
}


?>