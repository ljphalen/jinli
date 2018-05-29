<?php
include 'common.php';

$cache = Cache_Factory::getCache();
$lockName = Util_CacheKey::LOCK_EXTRA_SYS_MSG_CRON;
$lock = $cache->get($lockName);
if($lock){
    $cache->set($lockName, 0, 5*60);
    exit("nothing to do.\n");
}
$cache->set($lockName, 1, 5*60);

while($data =  json_decode(Common::getQueue()->pop('game_client_msg'),TRUE)){
	$msgId = Game_Service_Msg::addApiMsg($data);
	$msgIds[] = $msgId;
}

echo "<pre>";
print_r($msgIds);

$i= 0;
foreach($msgIds as $key=>$value){
      $msgInfo = Game_Service_Msg::getMsg($value);
      $sendMsg = Api_Push_Msg::assembleRequestData($msgInfo);
      print_r($sendMsg);
      if($sendMsg){
          $ret = Api_Push_Msg::pushMsg($sendMsg);
          if($ret['r'] == Api_Push_Msg::TOKEN_INVALID_CODE && $i <3){   //token无效，重新取三次放弃
              Api_Push_Msg::againGetPushToken();
              Api_Push_Msg::pushMsg($sendMsg);
              $i++;
          }
      }
      
}

echo CRON_SUCCESS;

$cache->set($lockName, 0, 5*60);
exit;
