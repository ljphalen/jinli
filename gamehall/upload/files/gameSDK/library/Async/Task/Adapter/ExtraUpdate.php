<?php

/**
 * Class Async_Task_Adapter_SubjectStatus
 * 更新游戏附加属性异步任务
 * @auth fanch
 */
class Async_Task_Adapter_ExtraUpdate extends Async_Task_Base {
    
    public function gameRewardAcoupon(Async_Task_Message_Task $task) {
        $data = Resource_Service_GameExtraCache::getAllExtra();
        foreach ($data as $key => $item) {
            $gameId = $key;
            Resource_Service_GameExtraCache::refreshGameRewardAcoupon($gameId);
        }
        $this->logger->debug("刷新游戏附加属性");
    }
}

