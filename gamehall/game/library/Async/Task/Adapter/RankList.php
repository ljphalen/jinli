<?php

/**
 * Class Async_Task_Adapter_CategoryList
 * 游戏分类列表索引，异步任务
 * @auth fanch
 */
class Async_Task_Adapter_RankList extends Async_Task_Base {

    public function removeIndex(Async_Task_Message_Task $task) {
        $gameId = $task->getParams();
        Resource_Index_RankList::removeIdxItem($gameId);
        $this->logger->debug("异步删除游戏排行榜列表索引");
    }

}

