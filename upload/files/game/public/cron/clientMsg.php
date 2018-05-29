<?php
include 'common.php';
while($data =  json_decode(Common::getQueue()->pop('game_client_msg'),TRUE)){
	$msgId = Game_Service_Msg::addApiMsg($data);
	$msgIds[] = $msgId;
}

echo "<pre>";
print_r($msgIds);

foreach($msgIds as $key=>$value){
      $msgInfo = Game_Service_Msg::getMsg($value);
      $sendMsg = Api_Push_Msg::assembleRequestData($msgInfo);
      print_r($sendMsg);
      if($sendMsg){
          Api_Push_Msg::pushMsg($sendMsg);
      }
      
}
