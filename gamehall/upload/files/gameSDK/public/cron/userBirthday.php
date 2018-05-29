<?php
include 'common.php';
/**
 *处理用户生日礼物赠送，任务执行频率-每天执行一次。
 *fanch
 */
$page = 1;
$today = date('m-d');
$config = Game_Service_Config::getValue('Game_User_Gift_Config');
if(!$config) exit("config not exists");
$config = json_decode($config, true);
if(!$config['status']) exit("config is closed");

do {
    //只扫上线的游戏
	list($total, $users) = Account_Service_User::getUserInfoListByBirthday($page , 100, $today);
	if(empty($users)) exit("not found data\r\n");
	foreach ($users as $value){
		if(!$value['uuid']) continue;
		$isSend = Account_Service_UserGift::isSendGift($value['uuid']);
		if($isSend) continue;
		$sendResult = Account_Service_UserGift::sendGift($value['uuid'], $config);
		if($sendResult){
			echo "ok:{$value['id']}\r\n";
		} else {
			echo "fail:{$value['id']}\r\n";
		}
	}
	$page++;
} while ($total>(($page -1) * 100));

echo CRON_SUCCESS;
