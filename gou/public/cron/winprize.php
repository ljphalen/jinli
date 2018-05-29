<?php
include 'common.php';
/**
 * 砍价结束后给用户发push
 */
list(, $goods) = Cut_Service_Goods::getsBy(array('status' => 1, 'end_time' => array('<', Common::getTime())), array('id' => 'ASC'));
if ($goods) {
    $goods_ids = array_keys(Common::resetKey($goods, 'id'));
    //更新商品活动状态
    Cut_Service_Goods::updateBy(array('status' => 2), array('id' => array('IN', $goods_ids)));

//    Common::log($goods_ids, 'winprize.log');

    //获取需要push的第一名玩家的UID
    $uids = array_keys(Common::resetKey($goods, 'uid'));
    $uids = array_filter($uids);
    $uids = array_unique($uids);
    $baidu_users = User_Service_Uid::getsBy(array('uid' => array('IN', $uids)));
    $baidu_uids = array_keys(Common::resetKey($baidu_users, 'baidu_uid'));
    $baidu_uids = array_filter($baidu_uids);
    $baidu_uids = array_unique($baidu_uids);

    //push
    foreach($baidu_uids as $buid){
//        Common::log($buid, 'winprize.log');
        $custom_content = array('url' => Common::getWebRoot() . '/apk/winprize/awardlist');
        $title = '上购物大厅拼速度，赢免费大奖！';
        $content = '恭喜您成为本期速度之王，赢得免费大奖，快去领取>>';
        Api_Baidu_Push::pushMessage($buid, $title, $content, 2, $custom_content);
    }
}

//设置活动结束时间大于5天的状态为10(失效)
$five_ago_time = strtotime(date("Y-m-d", strtotime('-4 days')));
list(, $goods) = Cut_Service_Goods::getsBy(array('status' => array("IN", array(2, 3)), 'end_time' => array('<', $five_ago_time)));
if($goods){
    $goods_ids = array_keys(Common::resetKey($goods, 'id'));
    //更新商品活动状态
    Cut_Service_Goods::updateBy(array('status' => 10), array('id' => array('IN', $goods_ids)));
}

?>