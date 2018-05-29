<?php
include 'common.php';

$email = html_entity_decode(Game_Service_Config::getValue('game_gift_eamil'));
if($email){
	$params = array('status' => 1, 'game_status'=>1);
	$params['effect_end_time'][0] = array('>=',  strtotime('- 1 day'));
	$params['effect_end_time'][1] = array('<=', Common::getTime());
	$giftInfo = Client_Service_Gift::getsBy($params);
	foreach ($giftInfo as $key=>$val){
		echo " gift_id: {$val['id']} - 过期时间: {".date('Y-m-d H:i:s',$val['effect_end_time'])."}-ok\r\n";
		Client_Service_GiftHot::updateBy(array('status'=>0), array('gift_id'=>$val['id']));
		Client_Service_Gift::updateGift(array('status'=>0), $val['id']);
		$gameInfo = Resource_Service_GameData::getGameAllInfo($val['game_id']);
		$body = '礼包ID：'.$val['id'].'，游戏ID：'.$val['game_id'].'，游戏名称：'.$gameInfo['name'].'，礼包名称：'.$val['name'].'，生效时间：'.date('Y-m-d H:i:s',$val['effect_start_time']).'-'.date('Y-m-d H:i:s',$val['effect_end_time']).'，已过期，程序自动关闭该礼包。';
		Common::sendEmail('礼包过期提示', $body, $email);
	}
}
echo CRON_SUCCESS;
exit;