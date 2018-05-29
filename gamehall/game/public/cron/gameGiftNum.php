<?php
include 'common.php';


$params = array('status' => Client_Service_Gift::GIFT_STATE_OPENED, 
		        'game_status'=>Client_Service_Gift::GAME_STATE_ONLINE);
$params['effect_start_time'][0] = array('>=',  strtotime('- 1 day'));
$params['effect_start_time'][1] = array('<=', Common::getTime());
$giftInfo = Client_Service_Gift::getsBy($params);
foreach ($giftInfo as $key=>$val){
	Client_Service_Gift::updataGiftNumCacheByGiftId($val['id']);
}
