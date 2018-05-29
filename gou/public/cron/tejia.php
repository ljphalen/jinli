<?php
include 'common.php';
$configs = Common::getConfig('tejiaConfig');
Client_Service_Source::dropAll();
echo PHP_EOL;
echo "|________________Start_____________|";
echo PHP_EOL;
foreach ($configs as $k=>$v) {
    Api_Channel_Dealer::search($k);
}
echo PHP_EOL;
echo "|________________End_______________|";
echo PHP_EOL;
die;



