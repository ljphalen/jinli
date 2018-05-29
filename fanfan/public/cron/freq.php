<?php
include 'common.php';

ini_set('memory_limit', '512M');
$out = Widget_Service_Freq::run();
echo $out;
error_log($out, 3, '/tmp/fanfan_freq_' . date('Ymd'));
