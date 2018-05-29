<?php
include 'common.php';

//set the order between $start_time to $end_time
$start_time = '-35 days';
$end_time = '+1 days';

echo PHP_EOL;
echo "|________________Start_____________|";
echo PHP_EOL;

//Gou_Service_PartnerOrder::getPartnerOrder($start_time, $end_time, 'DZDP');
Gou_Service_PartnerOrder::getPartnerOrder($start_time, $end_time);

echo PHP_EOL;
echo "|________________End_______________|";
echo PHP_EOL;
die;