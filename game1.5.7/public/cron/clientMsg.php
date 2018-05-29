<?php
include 'common.php';

//获取队列的key值
while($data =  json_decode(Common::getQueue()->pop('game_client_msg'),TRUE)){
	Game_Service_Msg::addApiMsg($data);
	echo CRON_SUCCESS;
}
