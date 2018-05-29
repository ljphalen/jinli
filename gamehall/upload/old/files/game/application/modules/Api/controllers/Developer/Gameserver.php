<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 开发者平台对接接口-游戏开服信息
 * @author wupeng
 *
 */
class Developer_GameserverController extends Api_BaseController{
    const ADD_OPEN_INFO_TAG = 'ADD_OPEN_INFO';
    const LOG_FILE = 'open_game.log';
    
    public function syncOpenAction(){
        $inputData = $this->getInput(array('appId', 'serverId', 'serverName', 'openType', 'openTime', 'status', 'sign'));
        Util_Log::info(self::ADD_OPEN_INFO_TAG, self::LOG_FILE, array('同步单条开服信息：', $inputData));
        $this->checkSign($inputData);
        $result = self::checkResultData($inputData);
        if($result) {
            $this->output(-1, $result);
        }
        $gameOpen = self::formatData($inputData);
        if(! $gameOpen['game_id']) {
            $this->output(-1, '游戏不存在');
        }
        $searchParams = array('game_id'=> $gameOpen['game_id'], 'server_id' => $gameOpen['server_id']);
        $oldInfo = Game_Service_GameOpen::getGameOpenBy($searchParams);
        if($oldInfo) {
            $openId = $oldInfo['id'];
            $gameOpen['id'] = $openId;
            $updateData = Game_Manager_WebRecommendList::getUpdateParams($gameOpen, $oldInfo);
            if($updateData) {
                Game_Service_GameOpen::updateGameOpen($updateData, $openId);
            }
        }else{
            Game_Service_GameOpen::addGameOpen($gameOpen);
        }
        $this->checkCache($gameOpen);
        $this->output(0, '同步成功');
    }

    public function syncMutilOpenAction(){
        $inputData = $this->getInput(array('list', 'sign'));
        Util_Log::info(self::ADD_OPEN_INFO_TAG, self::LOG_FILE, array('同步多条开服信息：', $inputData));
        $this->checkSign($inputData);
        $result = array();
        $list = $inputData['list'];
        foreach ($list as $data) {
            $msg = self::checkResultData($data);
            if(! $msg) {
                $gameOpen = self::formatData($data);
                if(! $gameOpen['game_id']) {
                    $msg = '游戏不存在';
                }else{
                    Game_Service_GameOpen::addGameOpen($gameOpen);
                }
            }
            $result[] = array('appId' => $data['appId'], 'server_id' => $data['serverId'], 'result' => $msg?0:1, 'resultMsg' => $msg);
        }
        Async_Task::execute('Async_Task_Adapter_UpdateOpenListCache', 'update');
        $this->output(0, '', $result);
    }
    
    private function checkSign($data) {
        $verifyResult = $this->verifySign($data);
        if(! $verifyResult) {
            Util_Log::info(self::ADD_OPEN_INFO_TAG, self::LOG_FILE, array('验证未通过：', $data));
            $this->output(-1, '非法请求.');
        }
    }
    
    private function checkResultData($data){
        if(! isset($data['appId']) || !$data['appId']) {
            return 'appId空';
        }
        if(! isset($data['serverId']) || !$data['serverId']) {
            return 'serverId空';
        }
        if(! isset($data['serverName']) || !$data['serverName']) {
            return 'serverName空';
        }
        if(! isset($data['openType']) || !$data['openType']) {
            return 'openType空';
        }
        if(! isset($data['openTime']) || !$data['openTime']) {
            return 'openTime空';
        }
        if(! isset($data['status']) || !$data['status']) {
            return 'status空';
        }
        if(! ($data['openType'] == 1 || $data['openType'] == 2)) {
            return 'openType = 1或2';
        }
        if(! ($data['status'] == 1 || $data['status'] == 2)) {
            return 'status = 1或2';
        }
        if(Util_String::strlen($data['serverName']) > 10) {
            return 'serverName(1-10个字符)';
        }
    }
    
    private function formatData($data) {
        if($data['status'] == 2) {
            $data['status'] = 0;
        }
        $game = Resource_Service_Games::getBy(array('appId' => $data['appId']));
        $gameOpen = array(
            'game_id'=> $game ? $game['id'] : 0,
            'server_id' => $data['serverId'],
            'server_name' => $data['serverName'],
            'open_type' => $data['openType'],
            'open_time' => $data['openTime'],
            'status' => $data['status'],
            'update_time' => Common::getTime(),
            'game_status' => resource_service_games::STATE_ONLINE,
        );
        return $gameOpen;
    }
    
    private function verifySign($data){
        $pubKey = Common::getConfig("siteConfig", "rsaPubFile");
    	$rsa = new Util_Rsa();
    	$result = $rsa->verifySign($data, $data['sign'], $pubKey);
    	return $result;
    }
    
    private function checkCache($open) {
        $curDate = Util_TimeConvert::floor(time(), Util_TimeConvert::RADIX_DAY);
        $startTime = Util_TimeConvert::addDays(-4, $curDate);
        $endTime = Util_TimeConvert::addDays(5, $curDate);
        if($open['open_time'] >= $startTime && $open['open_time'] <= $endTime) {
            Async_Task::execute('Async_Task_Adapter_UpdateOpenListCache', 'update');
        }
        if(! $open['id']) return;
        $openId = $open['id'];
        $flg = Game_Api_WebRecommendList::openIsShowing($openId);
        if($open['status'] == Game_Service_GameOpen::STATUS_CLOSE) {
            $data = array('open_status' => Game_Service_GameOpen::STATUS_CLOSE);
            $searchParams = array('open_id' => $openId);
            Game_Service_GameWebRecOpen::updateGameWebRecOpenBy($data, $searchParams);
        }
        if($flg) {
            Async_Task::execute('Async_Task_Adapter_UpdateWebRecCache', 'updateList');
        }
    }
    
}