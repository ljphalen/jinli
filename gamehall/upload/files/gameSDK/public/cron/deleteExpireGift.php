<?php
include 'common.php';
//删除过期礼包的激活码
$page = 1;
do {
    $parmas = array();
    $currTime = Common::getTime() - 3 * 30 * 24 * 3600;
    $parmas['use_end_time'] = array('<', $currTime);
    list($total, $giftsList) = Client_Service_Gift::getList($page, 100, $parmas);
    foreach($giftsList as $key=>$value){
        Client_Service_Giftlog::deleteBy(array('gift_id'=>$value['id'],'log_type'=>Client_Service_Giftlog::GRAB_GIFT_LOG));
    }
    $page++;
} while ($total>(($page -1) * 100));
