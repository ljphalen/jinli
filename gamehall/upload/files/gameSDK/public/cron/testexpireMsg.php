<?php
include 'common.php';
$time = time();
$tite = '您有1testA券即将过期！';
$curr_time = date('Y-m-d',strtotime("+1 day"));
$msg = $curr_time.'日24点过期';
$data = array(
        'type' =>  102,
        'top_type' =>  100,
        'totype' =>  1,
        'title' =>   $tite,
        'msg' =>  $msg,
        'status' =>  0,
        'start_time' =>  $time,
        'end_time' =>  strtotime('2050-01-01'),
        'create_time' =>  $time,
        'sendInput' =>  '5DE57E28813244F7900242219D0DCD5B',
);
//Game_Service_Msg::addApiMsg($data);

$gameId = 12;
$gameInfo = Resource_Service_GameData::getGameAllInfo($gameId);
$title = "您获得".$gameInfo['name']."限量礼包";
$desc ="恭喜，您成功下载安装" . $gameInfo['name']."，获得".$gameInfo['name']."限量礼包，请尽快使用！";

$message = array(
        'type' =>  109,
        'top_type' =>  100,
        'totype' =>  1,
        'title' =>  $title,
        'msg' =>  $desc,
        'status' =>  0,
        'start_time' =>  $time,
        'end_time' =>  strtotime('2050-01-01 23:59:59'),
        'create_time' =>  $time,
        'sendInput' =>  'E1099068D82F47C58BFB2F340485C664',
);
return Common::getQueue()->push('game_client_msg',$message);
echo CRON_SUCCESS;