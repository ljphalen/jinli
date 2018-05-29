<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 列表索引, 维护列表的ID顺序
 * @author huyuke
 *
 */
class Cache_ListIndex {

    const LIST_INDEX_EXPIRE_DEFAULT = 7200; //two hours

    const KEY_INDEX_LIST_SIZE = 'indexSize';
    const KEY_INDEX_PAGE_SIZE = 'pageSize';

    const KEY_BUILDING = 'building';
    const KEY_INDEX_BUILDING_TIME = 'buildingTime';

    const MAX_LIST_LEN = 3000;
    const SEGMENT_SIZE = 50;

    /*当前可用缓存key*/
    private $mCurrentContentKey = null;

    private $mCurrentIndexKey = null;
    private $mBuildingKey = null;

    private $mPageSize = null;

    private $mIsBuilding = false;

    private $mLastPosInBuilding = -1;

    /**
     * @param string $contentKey, 列表内容的缓存key
     * @param string $indexName, 列表索引的缓存key
     */
    public function Cache_ListIndex($contentKey, $indexName) {
        if (!$contentKey || !$indexName) {
            return;
        }

        $this->mCurrentIndexKey = $indexName;
        $this->mBuildingKey = $indexName . self::KEY_BUILDING;

        $contentManagerKey = $contentKey . Cache_ListContent::KEY_CONTENT_KEY_MANAGER;
        $redis = $this->getCache();
        $this->mCurrentContentKey = $redis->hGet($contentManagerKey,
                Cache_ListContent::KEY_MANAGER_CURRENT_KEY);
    }

    /**
     * 读取list中的分页数据
     * @param int $pageIndex, 页面索引, 1为第1页
     * @return array($pageItems, $hasNext, $indexSize),包含页面数据,是否有下一页的数组,总个数
     */
    public function getPage($pageIndex) {
        $pageIndex = intval($pageIndex);
        if($pageIndex < 1) {
            $pageIndex = 1;
        }

        list($pageKeys, $hasNext, $indexSize) = $this->getKeysInPage($pageIndex);

        if (count($pageKeys) < 1) {
            return array(array(), false, 0);
        }

        $redis = $this->getCache();
        $cacheData = $redis->hMget($this->mCurrentContentKey, $pageKeys);

        $pageItems = array();
        foreach($cacheData as $item) {
            $item = json_decode($item, true);
            if(is_array($item)) {
                $pageItems[] = $item;
            }
        }

        return array($pageItems, $hasNext, $indexSize);
    }

    /** 构建索引数据开始时必须调用
     * 注意需要在该方法返回成功后开始构建内容
     */
    public function buildIndexBegin($pageSize) {
        $this->debug('buildIndexBegin', $this->mCurrentIndexKey . ' build is start');
        if (intval($pageSize) < 1) {
            return false;
        }
        $redis = $this->getCache();
        if(!$redis->exists($this->mCurrentContentKey)) {
            //content not exists
            return false;
        }
        $this->mPageSize = intval($pageSize);
        if($this->isBuilding()) {
            return false;
        }
        $this->clearOldBuildingData();
        $this->resetMember();
        $this->setBuildingFlag(true);
        $this->mIsBuilding = true;
        return true;
    }

    /** 构建索引数据结束时必须调用
     */
    public function buildIndexFinish() {
        $this->debug('buildIndexFinish', $this->mCurrentIndexKey . ' build is finish');
        if (true != $this->mIsBuilding) {
            return false;
        }
        $this->setBuildingFlag(false);
        $this->enableNewIndex();
        $this->mIsBuilding = false;
        $this->resetMember();
        $this->debug('buildIndexFinish', 'build is finish return');
        return true;
    }

    /**
     * 重建列表索引, 即内容id的排序, 可以分批添加id列表, 最终结果按添加的时间先后顺序排列
     * 注意：
     * 1 为了保证索引更新性能, 索引长度不能超过MAX_LIST_LEN, 超过的部分会被丢弃
     * 2 此方法属于耗时操作, 请在后台进程运行
     *
     * @param string $itemKeyList, 按特定顺序排列的ID列表, 如游戏id列表, 礼包id列表
     * @param int $expire, 不能大于列表内容的缓存有效期
     */
    public function buildListIndex($itemKeyList,
                                   $expire = self::LIST_INDEX_EXPIRE_DEFAULT) {
        $this->debug('buildListIndex', $this->mCurrentIndexKey . ' building');
        if (!$this->allowBuild($expire)) {
            $this->debug('buildListIndex', 'build is not allowed');
            return false;
        }

        $startPos = $this->mLastPosInBuilding + 1;
        $newTotalSize = $startPos + count($itemKeyList);

        if ($newTotalSize > self::MAX_LIST_LEN) {
            $itemKeyList = $this->cutRedundance($startPos, $itemKeyList);
            if (count($itemKeyList) < 1) {
                return true;
            }
        }

        if (count($itemKeyList) < 1) {
            return false;
        }

        $endPos = $startPos + count($itemKeyList) - 1;

        $startPage = $this->getPageKey($startPos, $this->mPageSize);

        $cacheData = $this->makePagesData($itemKeyList, $this->mPageSize, $startPage, true);

        $this->mLastPosInBuilding = $endPos;

        $endPage = $this->getPageKey($endPos, $this->mPageSize);

        $redis = $this->getCache();
        $cacheData[self::KEY_INDEX_LIST_SIZE] = $endPos + 1;
        $cacheData[self::KEY_INDEX_PAGE_SIZE] = $this->mPageSize;
        return $redis->hMset($this->mBuildingKey, $cacheData, 2*$expire);
    }

    /**
     * 删除列表索引的某项
     * 注意：
     * 1 此方法属于耗时操作, 请在后台进程运行
     *
     * @param int $indexId, 要删除的项id值, 即storeListContent()方法中$itemIdKey对应的内容值
     */
    public function removeFromIndex($indexId) {
        $this->debugForRemove('removeFromIndex begin');
        $redis = $this->getCache();
        $redis->setReadWriteNoSeparate(true);
        list($indexSize, $pageSize) = $this->getIndexInfo();

        $pagesToModify = $this->findPagesForRemove($indexId, $indexSize, $pageSize);
        $segmentArr = array_chunk($pagesToModify, self::SEGMENT_SIZE, false);

        $removeCount = 0;
        $startPage = 0;
        $mergeStartPage = false;

        foreach($segmentArr as $segment) {
            $indexArr = $this->getIndexFromPages($segment);

            list($indexArr, $removedSingleLoop) = $this->removeFromArr($indexArr, $indexId);
            $removeCount += $removedSingleLoop;

            if ($startPage < 1) {
                $startPage = $segment[0];
                $mergeStartPage = false;
            } else {
                $mergeStartPage = true;
            }

            $pagesData = array();
            if (count($indexArr) > 0) {
                $pagesData = $this->makePagesData($indexArr, $pageSize, $startPage, $mergeStartPage);
                $redis->hMset($this->mCurrentIndexKey, $pagesData);
                $startPage = $this->calculateNextStartPage($pagesData, $pageSize);
            }

            $this->removeEmptyPages($segment, $pagesData);
        }

        if ($removeCount > 0) {
            $indexInfo[self::KEY_INDEX_LIST_SIZE] = $indexSize - $removeCount;
            $redis->hMset($this->mCurrentIndexKey, $indexInfo);
        }
        $redis->setReadWriteNoSeparate(false);
        $this->debugForRemove('removeFromIndex finish');
        return true;
    }

    /**
     * 添加一条数据到列表索引的指定位置
     * @param int @index, 位置, 0是第1个
     * @param int $indexId, 要添加的列表项id值, 即storeListContent()方法中$itemIdKey对应的内容值
     */
    public function addIndexItem($index, $indexId) {
        return true;
    }


    private function cutRedundance($currLen, $itemKeyList) {
        $cutLen = self::MAX_LIST_LEN - $currLen;
        if ($cutLen < 0) {
            $cutLen = 0;
        }
        return array_slice($itemKeyList, 0, $cutLen);
    }

    private function calculateNextStartPage($pagesData, $pageSize){
        $endPage = end($pagesData);
        $endKey = key($pagesData);
        if(count($endPage) < $pageSize) {
            $startPage= $endKey;
        } else {
            $startPage = $endKey + 1;
        }

        return $startPage;
    }

    private function removeFromArr($indexArr, $indexId) {
        $matchKeys = array_keys($indexArr, $indexId);
        $removeCount = count($matchKeys);
        foreach($matchKeys as $key) {
            unset($indexArr[$key]);
        }

        return array($indexArr, $removeCount);
    }

    private function removeEmptyPages($oldPages, $newPages) {
        $redis = $this->getCache();
        foreach ($oldPages as $page) {
            if(!isset($newPages[$page])) {
                   $redis = $this->getCache();
                   $redis->hDel($this->mCurrentIndexKey, $page);
            }
        }
    }

    private function resetMember() {
        $this->mLastPosInBuilding = -1;
    }

    private function isBuilding() {
        $redis = $this->getCache();
        $buildingTime = $redis->hGet($this->mBuildingKey, self::KEY_INDEX_BUILDING_TIME);
        if (!$buildingTime) {
            return false;
        }

        $buildingPeriod = Common::getTime() - $buildingTime;
        /*时间太过长久, 可判定上次重建被中断*/
        if ($buildingPeriod >= self::LIST_INDEX_EXPIRE_DEFAULT) {
            return false;
        }

        return true;
    }

    private function setBuildingFlag($building) {
        $buildingTime = 0;
        if ($building) {
            $buildingTime = Common::getTime();
            $this->mCurrentIndexKey = $this->mBuildingKey;
        } else {
            $this->mCurrentIndexKey = substr($this->mBuildingKey, 0,
                    strlen($this->mBuildingKey) - strlen(self::KEY_BUILDING));
        }
        $redis = $this->getCache();
        $redis->hSet($this->mBuildingKey, self::KEY_INDEX_BUILDING_TIME, $buildingTime);
    }

    /*清理上次被中断的重建数据*/
    private function clearOldBuildingData() {
        $redis = $this->getCache();
        $redis->delete($this->mBuildingKey);
    }

    private function enableNewIndex() {
        $redis = $this->getCache();
        $redis->rename($this->mBuildingKey, $this->mCurrentIndexKey);
    }

    private function makePagesData($itemKeyList, $pageSize, $startPage, $mergeStartPage) {
        if ($mergeStartPage) {
            $indexInStartPage = $this->getIndexFromPages(array($startPage));
            $itemKeyList = array_merge($indexInStartPage, $itemKeyList);
        }

        $pageKey = $startPage;
        $pages = array_chunk($itemKeyList, $pageSize, false);
        foreach($pages as $page) {
            $pagesData[$pageKey] = json_encode($page);
            $pageKey += 1;
        }

        return $pagesData;
    }

    private function allowBuild($expire) {
        if (true != $this->mIsBuilding) {
            return false;
        }

        $redis = $this->getCache();
        if(!$redis->exists($this->mCurrentContentKey)) {
            //content not exists
            return false;
        }

        if ($expire > (Cache_ListContent::LIST_CONTENT_EXPIRE+3600)) {
            return false;
        }

        return true;
    }

    private function findPagesForRemove($indexId, $indexSize, $pageSize) {
        $lastPage = $this->getPageKey($indexSize - 1, $pageSize);

        $found = false;
        $pagesToModify = array();
        for($page = 1; $page <= $lastPage; $page++) {
            if (!$found) {
                $listIndex = array_flip($this->getIndexFromPages(array($page)));
                if (isset($listIndex[$indexId])) {
                    $found = true;
                }
            }
            if ($found) {
                $pagesToModify[] = $page;
            }
        }

        return $pagesToModify;
    }

    private function getPageKey($position, $pageSize) {
        return intval(floor($position/$pageSize) + 1);
    }

    private function getIndexInfo() {
        $redis = $this->getCache();
        $infoKeys = array(self::KEY_INDEX_LIST_SIZE, 
                          self::KEY_INDEX_PAGE_SIZE
                        );
        $indexInfo = $redis->hMget($this->mCurrentIndexKey, $infoKeys);

        $indexSize = intval($indexInfo[self::KEY_INDEX_LIST_SIZE]);
        $pageSize = intval($indexInfo[self::KEY_INDEX_PAGE_SIZE]);
        return array($indexSize, $pageSize);
    }

    private function getIndexFromPages($pageArr) {
        $redis = $this->getCache();
        $indexInPages = $redis->hMget($this->mCurrentIndexKey, $pageArr);
        $indexs = array();
        foreach($indexInPages as $index) {
            $indexArr = json_decode($index, true);
            if (count($indexArr) > 0) {
                $indexs = array_merge($indexs, $indexArr);
            }
        }
        return $indexs;
    }

    private function getKeysInPage($pageIndex) {
        $listIndex = $this->getIndexFromPages(array($pageIndex));

        if (count($listIndex) < 1) {
            return array(array(), false);
        }

        $keysInPage = array();
        foreach($listIndex as $key) {
            $keysInPage[] = strval($key);
        }

        list($indexSize, $pageSize) = $this->getIndexInfo();
        $hasNext = ($indexSize > $pageIndex * $pageSize) ? true : false;

        return array($keysInPage, $hasNext, $indexSize);
    }

    private function getCache() {
        $redis = Cache_Factory::getCache(Cache_Factory::ID_REMOTE_REDIS);
        return $redis;
    }

    private function debug($methodName, $message) {
        $tag = $methodName;
        $logFile = 'ListIndex';
        $message['indexName'] = $this->mCurrentIndexKey;
        Util_Log::debug($tag, $logFile, $message);
    }

    private function debugForRemove($description) {
        $message['description'] = $description;
        $redis = $this->getCache();
        $indexCacheData = $redis->hGetAll($this->mCurrentIndexKey);
        $this->debug('removeFromIndex', $indexCacheData);
    }
}
