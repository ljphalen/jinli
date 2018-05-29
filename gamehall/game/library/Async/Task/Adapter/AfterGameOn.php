<?php

/**游戏上线状态*/
class Async_Task_Adapter_AfterGameOn extends Async_Task_Base {

    public function update(Async_Task_Message_Task $task) {
        $gameId = $task->getParams();
        Dev_Service_Sync::afterGameOn($gameId);
        $this->logger->debug("游戏上线: " . $gameId);
    }

}
