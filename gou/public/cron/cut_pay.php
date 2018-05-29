<?php
include 'common.php';
/**
 * 砍价订单支付查询
 */

$time_diff = Common::getTime() - 20*60;
list(, $list) = Gou_Service_Order::getsBy(array('status'=>1, 'order_type'=>5, 'create_time'=>(array('<', $time_diff))),  array("id"=>"ASC"));
if($list){
    foreach ($list as $key=>$value) {
       Cut_Service_Order::cancelOrder($value['trade_no']);
    }
}
echo CRON_SUCCESS;
?>