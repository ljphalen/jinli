<?php
include 'common.php';
/**
 *update idx_game_client_news ntype
 */

$idxs = Client_Service_News::getGameClientNewsAll();
list(, $news) = Client_Service_News::getAllNews();
$news = Common::resetKey($news, 'out_id');
foreach($idxs as $key=>$value){
	if($value['out_id']){
		Client_Service_News::updateGameClientNewsByOutId($news[$value['out_id']]['ntype'],$news[$value['out_id']]['id'],$value['out_id']);
	}
}





echo CRON_SUCCESS;