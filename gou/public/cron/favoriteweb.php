<?php 
include 'common.php';
/**
 * 处理收藏数据
 */
$page = 1;
$perpage = 100;


//处理收藏网页数据
$params = array('type'=>4, 'item_id'=> '',  'id'=>array('>', '507260'));

do {
    $web_list = array();
    list($total, $web_list) = User_Service_Favorite::getList($page, $perpage, $params, array('id'=>'ASC'));
    foreach ($web_list as $key=>$value) {
        $item_id = getItemId($value['url']);
        $ret = User_Service_Favorite::update(array('item_id'=>$item_id), $value['id']);
    }
    
} while ($total > 0);

echo CRON_SUCCESS;


function getItemId($url){
    if (!$url) return false;
    return hexdec(substr(sha1(html_entity_decode($url)), 0, 15));
}
?>