<?php
include 'common.php';

define('OPERATING_MSG',1);
define('TEMPORARY_MSG',2);

function getMsg($msgId){
    return  Game_Service_Msg::getMsg($msgId);
}

function getPushMsg($msgId){
    return Client_Service_PushMsg::get($msgId);
}

function getEffectMsg($msgId, $msgType){
    $time = Common::getTime();
    if($msgType == OPERATING_MSG){
        $msgInfo = getMsg($msgId);
        if(!$msgInfo['operate_status']){
            return array(false, '');
        }
    } else if($msgType == TEMPORARY_MSG){
        $msgInfo = getPushMsg($msgId);
        if(!$msgInfo['status']){
            return array(false, '');
        }
    }

    if($msgInfo['start_time'] <= $time){
        return array(true, '');
    }
    
    return array(false, $msgId);
}

function getPushKey($msgType){
    if($msgType == OPERATING_MSG){
        $pushKey = 'game_client_operating_msg';
    } else if($msgType == TEMPORARY_MSG){
        $pushKey = 'game_client_temporary_msg';
    }
    return $pushKey;
}

function pushNotBegingMsgQueue($notBegingMsgIds, $msgType){
    $pushKey = getPushKey($msgType);
    foreach($notBegingMsgIds as $k=>$v){
        if(!$v){
            continue;
        }
        Common::getQueue()->push($pushKey, $v);
    }
}

function pushNotBegingMsg($notBegingOperatMsgIds, $notBegingTempMsgIds){
    pushNotBegingMsgQueue($notBegingOperatMsgIds, OPERATING_MSG);
    pushNotBegingMsgQueue($notBegingTempMsgIds, TEMPORARY_MSG);
}

function getAppointAcountSendMsg($sendMsg = array(), $msgInfo){
    $maps = Game_Service_Msg::getsMaps(array('msgid'=>$msgInfo['id']));
    foreach($maps as $k=>$v){
        $msgInfo['sendInput'] = $v['uid'];
        $appointAcountMsg = Api_Push_Msg::getOperatingAppointAcountMsg($msgInfo);
        array_push($sendMsg, $appointAcountMsg);
    }
    return $sendMsg;
}

function getSendMsgBody($msgIds){
    $appointSendMsg = array();
    foreach($msgIds as $key=>$value){
        if(!$value){
            continue;
        }
        $msgInfo = getMsg($value);
        if($msgInfo['totype'] == Game_Service_Msg::TARGET_ALL_ACOUNT){
            $appointSendMsg[] = Api_Push_Msg::getOperatingAllAcountMsg($msgInfo);
        } else if($msgInfo['totype'] == Game_Service_Msg::TARGET_SPECIFIED_ACCOUNT){
            $appointSendMsg = getAppointAcountSendMsg($appointSendMsg, $msgInfo);
        }
    }
    return $appointSendMsg;
}

function getTempSendMsgBody($tempMsgIds){
    $tempSendMsg = array();
    foreach($tempMsgIds as $key=>$value){
        if(!$value){
          continue;
        }
        $pushInfo = getPushMsg($value);
        $tempSendMsg[] = Api_Push_Msg::getOperatingAllAcountMsg($pushInfo,true);
    }
    return $tempSendMsg;
}

$notBegingOperatMsgIds = array();
while($msgId =  json_decode(Common::getQueue()->pop('game_client_operating_msg'),TRUE)){
    list($result,$notBegingOperatMsgId) = getEffectMsg($msgId , OPERATING_MSG);
    $notBegingOperatMsgIds[] = $notBegingOperatMsgId;
    if($result){
        $msgIds[] = $msgId;
    }
}

$notBegingTempMsgIds = array();
while($tempMsgId =  json_decode(Common::getQueue()->pop('game_client_temporary_msg'),TRUE)){
    list($ret,$notBegingTempMsgId) = getEffectMsg($tempMsgId , TEMPORARY_MSG);
    $notBegingTempMsgIds[] = $notBegingTempMsgId;
    if($ret){
        $tempMsgIds[] = $tempMsgId;
    }
}

echo "<pre>";
print_r($msgIds);

$sendMsg = $appointSendMsg = $tempSendMsg = array();
$appointSendMsg = getSendMsgBody($msgIds);
$tempSendMsg = getTempSendMsgBody($tempMsgIds);
$sendMsg = array_merge($appointSendMsg, $tempSendMsg);

foreach($sendMsg as $k=>$v){
     print_r($v);
     if(!$v){
        continue;
     }
     Api_Push_Msg::pushMsg($v);
}

pushNotBegingMsg($notBegingOperatMsgIds, $notBegingTempMsgIds);
echo CRON_SUCCESS;

exit;
