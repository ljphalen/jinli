<?php
include 'common.php';

define('POINST_MESSAGE', 108);
define('SYSTEM_MESSAGE', 100);
define('SPECIFY_USER', 1);
define('ONE_DAY', 1);
define('TWO_DAY', 2);
define('THREE_DAY', 3);


$currentTime = date('Y-m-d', Common::getTime());

$seasonTimeRange = Common::getSeasonTimeRange();

// $seasonTimeRange['endTime'] = '2015-05-06';

//每个季度的最后一天
$seasonEndDay = date('Y-m-d', strtotime($seasonTimeRange['endTime']));

//过期提醒的前三天
$messageRemindThirdDay = date('Y-m-d', strtotime('-3 day',  strtotime($seasonEndDay)));

//过期提醒的前二天
//$messageRemindSencodDay = date('Y-m-d', strtotime('-2 day',  strtotime($seasonEndDay)));

//过期提醒的前一天
//$messageRemindFirstDay = date('Y-m-d', strtotime('-1 day',  strtotime($seasonEndDay)));

//过期消息提醒
if($currentTime == $messageRemindThirdDay ){
	sendPointMessgae(THREE_DAY);
}

//每个季度最后一下天
if($currentTime == $seasonEndDay){
	clearUserPoint();
}

//清除用户积分
function clearUserPoint(){
	$page = 1;
	do {
		//用户积分大于零 
		$params['points'] =  array('>',  0);
		list($total, $userInfo) = Account_Service_User::getUserInfoList($page , 100, $params);
		foreach($userInfo as $key=>$value){
			$userPointData['points'] = 0 ;
			$userParams['id'] = $value['id'];
			Account_Service_User::updateUserInfo($userPointData, $userParams);
		}
		$page++;
	} while ($total > (($page -1) * 100));
}

//发送积分过期消息
function sendPointMessgae($day = 3){
	$pointMsgSwitch = Game_Service_Config::getValue('msg_pointexpire');
	if(!$pointMsgSwitch) return false;
	$page = 1;
	do {
		//用户积分大于零
		$params['points'] =  array('>',  0);
		list($total, $userInfo) = Account_Service_User::getUserInfoList($page , 100, $params);
		foreach($userInfo as $key=>$value){
			saveMessageToCache($value['uuid'], $day);
			echo "send messgae :{$value['uuid']}\r\n";
		}
		$page++;
	} while ($total>(($page -1) * 100));

}

function saveMessageToCache($uuid, $day){
	$time = Common::getTime();
	$message = array(
			'type' =>  POINST_MESSAGE,
			'top_type' => SYSTEM_MESSAGE,
			'totype' => SPECIFY_USER,
			'title' =>  '积分即将过期',
			'msg' =>  '你的积分'.$day.'天后过期，请及时使用',
			'status' =>  0,
			'start_time' =>  $time,
			'end_time' =>  strtotime('2050-01-01 23:59:59'),
			'create_time' =>  $time,
			'sendInput' =>  $uuid,
	);
     Common::getQueue()->push('game_client_msg',$message);
}
echo CRON_SUCCESS;
exit;