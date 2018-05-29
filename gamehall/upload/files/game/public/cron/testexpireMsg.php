<?php
include 'common.php';
/*
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
$page = 1;
do {
    //$params['status'] =  1;
    $params = array();
    list($total, $gifts) = Client_Service_Gift::getList($page , 100, $params);
    foreach($gifts as $key=>$value){
        if(!$value['vip_level']){
            Client_Service_Gift::updateGiftDatabase(array('vip_level'=>1), $value['id']);
        }
    }
    $page++;
} while ($total > (($page -1) * 100));

*/

$giftInfo = array(
        'id' =>'156',
        'name' =>'模拟测试',
        'game_id' =>'1',
        //'dev_email' => '741347507@qq.com',
        'dev_email' => 'lichanghua@gionee.com',
);
//$giftInfo = Client_Service_Gift::getBy(array('id'=>156));
echo "<pre>";
print_r($giftInfo);

$giftTotal = 50;
$remainGiftNum = 7;


grabGiftWarningEmail($giftInfo, $giftTotal, $remainGiftNum);


 function grabGiftWarningEmail($giftInfo, $giftTotal, $remainGiftNum) {
    if($giftTotal >= 50){
        $giftNum = Game_Service_Config::getValue('game_gift_num');
        $num =  intval($giftTotal*($giftNum/100));
        echo $num;
        if($remainGiftNum == 0){
            $tmp['remainGiftNum'] = array($remainGiftNum,'剩余数量为0');
            echo "<pre>";
            print_r($tmp);
            giftEmaiToQueue($giftInfo, $remainGiftNum,1);
        } else if($giftNum && $remainGiftNum == intval($num) ){
            $tmp['remainGiftNum'] = array($remainGiftNum,$num,'剩余数量为后台配置的数量');
            echo "<pre>";
            print_r($tmp);
            giftEmaiToQueue($giftInfo, $remainGiftNum,2);
        }
    } else if($giftTotal >= 10 && $giftTotal < 50){
        if($remainGiftNum == 5 ||  $remainGiftNum == 0){
            $tmp['remainGiftNum'] = array($remainGiftNum,'剩余数量等于5个');
            echo "<pre>";
            print_r($tmp);
            giftEmaiToQueue($giftInfo, $remainGiftNum,3);
        }
    }else if($giftTotal < 10){
        if($remainGiftNum == 0){
            $tmp['remainGiftNum'] = array($remainGiftNum,'总数小于10剩余数量等于0个');
            echo "<pre>";
            print_r($tmp);
            giftEmaiToQueue($giftInfo, $remainGiftNum,3);
        }
    }
}


function giftEmaiToQueue($giftInfo, $remainGiftNum, $conditionCode){
    if($conditionCode ==1 || $conditionCode ==2){
        $email = array(
                'yunying' =>html_entity_decode(Game_Service_Config::getValue('game_gift_eamil')),
                'dev' =>$giftInfo['dev_email'],
        );
    } else {
        $email = array(
                'yunying' =>html_entity_decode(Game_Service_Config::getValue('game_gift_eamil')),
        );
    }

    echo "<pre>";
    print_r($email);

    $gameInfo = Resource_Service_GameData::getBasicInfo($giftInfo['game_id']);
    $mailInfo = array('gift_id'=>$giftInfo['id'],
            'game_id'=>$giftInfo['game_id'],
            'game_name'=>$gameInfo['name'],
            'package'=>$gameInfo['package'],
            'gift_name'=>$giftInfo['name'],
            'remain_gifts'=>$remainGiftNum,
            'conditionCode'=>$conditionCode);

    foreach($email as $key=>$val){
        $mailInfo['email'] = $val;
        $mailInfo['sendtype'] = $key;
        Common::getQueue()->push('game_gift',$mailInfo);
    }
    
    echo "<pre>";
    print_r($mailInfo);
    
    return;
}



echo CRON_SUCCESS;