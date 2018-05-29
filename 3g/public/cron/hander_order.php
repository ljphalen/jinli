<?php
include 'common.php';
$out = '';

$path = '/data/3g_log/hander_order/';
if (!is_dir($path)) {
    mkdir($path, 0777, true);
}
while (true) {
    $out = User_Service_Order::run(5);
    $msg = date('m/d/y H:i:s') . Common::jsonEncode($out) . "\n";
    error_log($msg, 3, $path . date('Ymd'));
    sleep(3);

}