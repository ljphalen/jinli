<?php

/**
 * Class Async_Task_Adapter_GameListData
 * 游戏列表数据，异步任务
 * @auth fanch
 */
class Async_Task_Adapter_GameListData extends Async_Task_Base {
    
    public function singleExecute() {
        return true;
    }
    
    public function updteListItem(Async_Task_Message_Task $task) {
        $gameId = $task->getParams();
        $result = Resource_Service_GameListData::updateListItem($gameId);
        if(!$result){
            $message = '游戏列表数据刷新失败：'. $gameId;
            Util_Log::debug('updteListItem', 'gamelistdata.log', $message);
        }
    }

    public function removeListItem(Async_Task_Message_Task $task) {
        $gameId = $task->getParams();
        $result = Resource_Service_GameListData::removeItemData($gameId);
        if(!$result){
            $message = '游戏列表数据删除失败：'. $gameId;
            Util_Log::debug('updteListItem', 'gamelistdata.log', $message);
        }
    }

    public function updateRewardAcoupon(Async_Task_Message_Task $task) {
        Resource_Service_GameListData::refreshGameRewardAcoupon();
    }
}

