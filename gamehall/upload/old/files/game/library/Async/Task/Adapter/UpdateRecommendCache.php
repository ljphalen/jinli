<?php

/**刷新首页推荐缓存*/
class Async_Task_Adapter_UpdateRecommendCache extends Async_Task_Base {
    
    public function update(Async_Task_Message_Task $task) {
        Game_Api_Recommend::updateClientRecommendCacheData();
        Game_Api_RecommendBanner::updateClientBannerCacheData();
        Game_Api_RecommendDay::updateClientDayCacheData();
        Game_Api_RecommendText::updateClientTextCacheData();
        $this->logger->debug("刷新首页缓存");
    }
    
}

