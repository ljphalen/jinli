<?php

/**刷新客户端开服列表缓存*/
class Async_Task_Adapter_UpdateOpenListCache extends Async_Task_Base {
    
    public function update(Async_Task_Message_Task $task) {
        Game_Api_WebRecommendOpen::updateOpenListCache();
        $this->logger->debug("刷新客户端开服列表缓存");
    }
    
}

