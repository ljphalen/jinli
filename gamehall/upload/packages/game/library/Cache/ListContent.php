<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 列表数据缓存托管
 * @author huyuke
 *
 */
class Cache_ListContent {

    /*请配合计划任务构建列表数据, 两次计划任务间隔不能超过LIST_CONTENT_EXPIRE*/
    const LIST_CONTENT_EXPIRE = 86400; //one day

    const KEY_CONTENT_KEY_MANAGER = '_KeyMgr';
    const KEY_MANAGER_CURRENT_KEY = 'currentKey';
    const KEY_MANAGER_BUILDING_START_TIME = 'buildingTime';

    const KEY_A = '_A';
    const KEY_B = '_B';

    private $mContentKeyPrefix = null;

    /*保存当前可用内容缓存key的key, 保存重建标识*/
    private $mContentKeyManager = null;

    /*当前可用缓存key*/
    private $mCurrentContentKey = null;

    /*用于重建数据的key*/
    private $mContentRebuildkey = null;

    private $mIsBuilding = false;

    /**
     * @param string $contentKey, 列表内容的缓存key
     */
    public function Cache_ListContent($contentKey) {
        if (!$contentKey) {
            return;
        }
        $this->mContentKeyPrefix = $contentKey;

        $this->mContentKeyManager = $contentKey . self::KEY_CONTENT_KEY_MANAGER;
        $redis = $this->getCache();
        $this->mCurrentContentKey = $redis->hGet($this->mContentKeyManager, 
                self::KEY_MANAGER_CURRENT_KEY);

    }

    /** 开始构建列表内容, 构建前必须调用
     * 注意需要在该方法返回成功后开始构建内容
     */
    public function buildContentBegin() {
        if (!$this->mContentKeyManager) {
            return false;
        }
        if($this->isBuilding()) {
            return false;
        }
        $this->setBuildingFlag(true);
        $this->mIsBuilding = true;

        $this->mContentRebuildkey = $this->getContentNewKey();
        $this->clearOldBuildingData();
        return true;
    }

    /** 构建列表数据结束时必须调用
     */
    public function buildContentFinish() {
        if (!$this->mContentKeyManager) {
            return false;
        }
        if (true != $this->mIsBuilding) {
            return false;
        }
        $this->enableNewContent();
        $this->clearOldContent();
        $this->setBuildingFlag(false);
        $this->mIsBuilding = false;
        return true;
    }

    /**
     * 批量读取list中的内容
     * @param array $idList, 数组, 包含需要加载的列表项唯一标识
     * @return array $listContent, 内容列表
     */
    public function getContent($idList) {
        if (count($idList) < 1) {
            return array();
        }

        $listKeys = array();
        foreach($idList as $id) {
            $listKeys[] = strval($id);
        }

        $redis = $this->getCache();
        $contentItems = array();
        $cacheData = $redis->hMget($this->mCurrentContentKey, $listKeys);
        foreach($cacheData as $item) {
            $item = json_decode($item, true);
            if(is_array($item)) {
                $contentItems[] = $item;
            }
        }
        return $contentItems;
    }

    /**
     * 保存列表数据
     * @param string $itemIdKey, 可作为列表项唯一标识的字段名, 如'game_id', 'gift_id', 'id'
     * @param array $listContent, 可以是列表的部分内容, 也可以是完整的列表;
     */
    public function storeListContent($itemIdKey, $listContent) {
        $keyToStore = $this->mCurrentContentKey;
        if ($this->mIsBuilding) {
            $keyToStore = $this->mContentRebuildkey;
        }

        if(!$keyToStore) {
            return false;
        }

        if (!is_array($listContent)) {
            return false;
        }

        foreach($listContent as $item) {
            if (!isset($item[$itemIdKey])) {
                return false;
            }
        }

        $cacheList = $this->makeListContentCacheData($listContent, $itemIdKey);
        return self::storeListContentInternal($keyToStore, $cacheList);
    }

    /**
     * 更新单条数据, 如果不存在, 则新增
     * @param string $itemIdKey, 可作为列表项唯一标识的字段名, 如'game_id', 'gift_id', 'id'
     * @param array $itemContent, 新的列表项内容
     */
    public function storeListItem($itemIdKey, $itemContent) {
        if(!$this->mCurrentContentKey) {
            return false;
        }
        $itemId = $itemContent[$itemIdKey];
        if (!isset($itemId)) {
            return false;
        }
        $itemId = strval($itemId);

        $value = json_encode($itemContent);
        $redis = $this->getCache();
        $redis->hSet($this->mCurrentContentKey, $itemId, $value);
        if ($this->isBuilding()) {
            $buildingKey = $this->getContentNewKey();
            $redis->hSet($buildingKey, $itemId, $value);
        }
        return true;
    }

    /**
     * 删除列表内容的某项, 支持多条删除
     * @param mix $contentId, 要删除的项id值, 即storeListContent()方法中$itemIdKey对应的内容值
     */
    public function removeFromContent($contentId) {
        if(!$this->mCurrentContentKey) {
            return false;
        }
        $redis = $this->getCache();
        $contentIds = $contentId;
        if(!is_array($contentIds)) {
            $contentIds = array($contentId);
        }
        foreach($contentIds as $id) {
            $id = strval($id);
            $redis->hDel($this->mCurrentContentKey, $id);
        }
        return true;
    }



    /*清理上次被中断的重建数据*/
    private function clearOldBuildingData() {
        $redis = $this->getCache();
        $redis->delete($this->mContentRebuildkey);
    }

    private function clearOldContent() {
        $redis = $this->getCache();
        $redis->delete($this->mCurrentContentKey);
        $this->mCurrentContentKey = $this->mContentRebuildkey;
    }

    private function isBuilding() {
        $redis = $this->getCache();
        $buildingTime = $redis->hGet($this->mContentKeyManager, self::KEY_MANAGER_BUILDING_START_TIME);
        if (!$buildingTime) {
            return false;
        }

        $buildingPeriod = Common::getTime() - $buildingTime;
        /*时间太过长久, 可判定上次重建被中断*/
        $redundancyTime = 300;
        if ($buildingPeriod >= (self::LIST_CONTENT_EXPIRE - $redundancyTime)) {
            return false;
        }

        return true;
    }

    private function setBuildingFlag($building) {
        $buildingTime = 0;
        if ($building) {
            $buildingTime = Common::getTime();
        }
        $redis = $this->getCache();
        return $redis->hSet($this->mContentKeyManager, self::KEY_MANAGER_BUILDING_START_TIME, $buildingTime);
    }

    private function getContentNewKey() {
        $contentKeyA = $this->mContentKeyPrefix . self::KEY_A;
        $contentKeyB = $this->mContentKeyPrefix . self::KEY_B;

        if ($contentKeyA != $this->mCurrentContentKey) {
            return $contentKeyA;
        } else {
            return $contentKeyB;
        }
    }

    private function enableNewContent() {
        $expire = 2*self::LIST_CONTENT_EXPIRE;
        $redis = $this->getCache();
        $redis->hSet($this->mContentKeyManager, self::KEY_MANAGER_CURRENT_KEY,
                        $this->mContentRebuildkey, $expire);
    }

    private function storeListContentInternal($contentKey, $listContent) {
        $redis = $this->getCache();
        return $redis->hMset($contentKey, $listContent, 2*self::LIST_CONTENT_EXPIRE);
    }

    private function makeListContentCacheData($originalList, $itemIdKey) {
        $cacheList = array();

        foreach($originalList as $item) {
            $itemId = strval($item[$itemIdKey]);
            $cacheList[$itemId] = json_encode($item);
        }
        return $cacheList;
    }

    private function getCache() {
        return Cache_Factory::getCache(Cache_Factory::ID_REMOTE_REDIS);
    }
}
