<?php 
include 'common.php';
/**
 * 处理收藏数据
 */

$page = 1;
$perpage = 1000;
//商品
$i = 0;
do {
    echo $page." page doing goods.\n";
    
    $params = array('type'=>2, 'id'=>array('<', '576377'));
    //$params = array('type'=>2, 'id'=>array('<', '10000'));
 	list($total, $list) = User_Service_Favorite::getList($page, $perpage, $params, array('id'=>'ASC'));
	foreach ($list as $key=>$value) {
	    if($value['data']) {
	        $d = json_decode($value['data'], true);
	        if($d['price']) User_Service_Favorite::update(array('price'=>$d['price']), $value['id']);
	    } 	    
	    $i++;
	    if (($i % 100) == 0) {
	        echo "done with ". $i."\n";
	    }
	}
	$page++;
	//sleep(1);
} while ($total>=(($page -1) * $perpage));

echo CRON_SUCCESS;
?>