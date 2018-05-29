<?php
/**游戏下线*/
class Async_Task_Adapter_AfterGameOff extends Async_Task_Base {

    public function update(Async_Task_Message_Task $task) {
        $gameId = $task->getParams();
        Dev_Service_Sync::afterGameOff($gameId);
        $this->logger->debug("游戏下线: " . $gameId);
    }
}