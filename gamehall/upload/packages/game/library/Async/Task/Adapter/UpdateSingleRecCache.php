<?php

/**刷新单机推荐缓存*/
class Async_Task_Adapter_UpdateSingleRecCache extends Async_Task_Base {
    
    public function update(Async_Task_Message_Task $task) {
        Game_Api_SingleRecommendBanner::updateClientBannerCacheData();
        Game_Api_SingleRecommendList::updateClientRecommendCacheData();
        $this->logger->debug("刷新单机频道缓存");
    }
    
}

