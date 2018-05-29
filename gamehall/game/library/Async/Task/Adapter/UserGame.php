<?php

/**用户的游戏*/
class Async_Task_Adapter_UserGame extends Async_Task_Base {
    
    public function login(Async_Task_Message_Task $task) {
        $loginInfo = $task->getParams();
        $params = array();
        $params['api_key'] = $loginInfo['apiKey'];
        $result = Resource_Service_Games::getBy($params);
        $gameId = $result['id'];
        $uuid = $loginInfo['uuid'];
        $data = array();
        $data[User_Service_GameLog::F_LOGIN_TIME] = $loginInfo['loginTime'];
        $login = User_Service_GameLog::getGameLog($uuid, $gameId);
        if($login) {
            User_Service_GameLog::updateGameLog($data, $uuid, $gameId);
        }else{
            $data[User_Service_GameLog::F_GAME_ID] = $gameId;
            $data[User_Service_GameLog::F_UUID] = $uuid;
            User_Service_GameLog::addGameLog($data);
        }
        User_Api_MyGameList::updateClientDayCacheData($uuid);
        $this->logger->debug($loginInfo);
    }

    public function consume(Async_Task_Message_Task $task) {
        $params = $task->getParams();
        $params = array();
        $params['api_key'] = $params['apiKey'];
        $result = Resource_Service_Games::getBy($params);
        $gameId = $result['id'];
        $uuid = $params['uuid'];
        $data = array();
        $data[User_Service_GameLog::F_CONSUME_TIME] = $params['tradeTime'];
        $info = User_Service_GameLog::getGameLog($uuid, $gameId);
        if($info) {
            User_Service_GameLog::updateGameLog($data, $uuid, $gameId);
        }else{
            $data[User_Service_GameLog::F_GAME_ID] = $gameId;
            $data[User_Service_GameLog::F_UUID] = $uuid;
            User_Service_GameLog::addGameLog($data);
        }
        User_Api_MyGameList::updateClientDayCacheData($uuid);
        $this->logger->debug($params);
    }
    
}

