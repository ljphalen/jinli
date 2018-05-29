<?php

/**
 * Class Async_Task_Adapter_CategoryList
 * 游戏分类列表索引，异步任务
 * @auth fanch
 */
class Async_Task_Adapter_CategoryList extends Async_Task_Base {

    public function updateIndex(Async_Task_Message_Task $task) {
        $gameId = $task->getParams();
        $data = $this->getCategoryData($gameId);
        if($data) {
            Resource_Index_CategoryList::updateCategoryIdx($gameId, $data);
        }
        $this->logger->debug("异步刷新游戏分类列表索引");
    }

    public function removeIndex(Async_Task_Message_Task $task) {
        $gameId = $task->getParams();
        Resource_Index_CategoryList::removeCategoryIdxItem($gameId);
        $this->logger->debug("异步删除游戏分类列表索引");
    }

    public function updateAllGameIndex(Async_Task_Message_Task $task) {
        Resource_Index_CategoryList::buildAllGameIdx();
        $this->logger->debug("异步刷新游戏分类列表索引");
    }

    private function getCategoryData($gameId){
        $result = array();
        $data = Resource_Service_GameCategory::getsBy(array('game_id'=>$gameId, 'status'=>1, 'game_status'=>1));
        if($data) {
            foreach ($data as $item) {
                $result[] = array('categoryId' => $item['category_id'], 'parentId' => $item['parent_id']);
            }
        }
        return $result;
    }
}

