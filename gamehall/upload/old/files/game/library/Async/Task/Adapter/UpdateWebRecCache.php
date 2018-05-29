<?php

/**刷新网游推荐缓存*/
class Async_Task_Adapter_UpdateWebRecCache extends Async_Task_Base {
    
    public function update(Async_Task_Message_Task $task) {
        Game_Api_WebRecommendBanner::updateClientBannerCacheData();
        Game_Api_WebRecommendList::updateClientRecommendCacheData();
        $this->logger->debug("刷新网游缓存");
    }
    
    public function updateBanner(Async_Task_Message_Task $task) {
        Game_Api_WebRecommendBanner::updateClientBannerCacheData();
        $this->logger->debug("刷新网游轮播图缓存");
    }

    public function updateList(Async_Task_Message_Task $task) {
        Game_Api_WebRecommendList::updateClientRecommendCacheData();
        $this->logger->debug("刷新网游列表缓存");
    }
    
}

