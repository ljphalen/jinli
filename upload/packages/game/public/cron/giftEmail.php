<?php
include 'common.php';

//获取队列的key值
while( $data =  json_decode(Common::getQueue()->pop('game_gift'),TRUE)){
	if($data){
		$body = '礼包ID：'.$data['gift_id'].'，游戏ID：'.$data['game_id'].'，游戏名称：'.$data['game_name'].'，礼包名称：'.$data['gift_name'].'，剩余的数量：'.$data['remain_gifts'].'个，请及时添加。';
		$ret  = Common::sendEmail($data['emailSubject'],  $body,  $data['email']);
	}
}

echo CRON_SUCCESS;
exit;