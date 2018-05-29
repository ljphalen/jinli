<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Resource_PageCache_CatList
 * @author fanch
 *
 */
class Resource_PageCache_CatList{

    const APCU_CACHE_EXPIRE = 1800; //30分钟

    public static function getPage($page, $parentId, $categoryId, $orderBy){
        $buildFlag = false;
        $cache = self::getApcuCache();
        $idxKey = Resource_Index_CategoryList::createCatIdxkey($parentId, $categoryId, $orderBy);
        $cacheKey = self::getCacheKey($idxKey, $page);
        $cacheData = $cache->get($cacheKey);
        $dataVersion = Resource_Index_CategoryList::getCatIdxVersion($idxKey);
        if(!$cacheData) {
            $buildFlag = true;
        } else {
            if($cacheData['version'] != $dataVersion){
                $buildFlag = true;
            }
        }
        if($buildFlag){
            $cacheData = self::buildCacheData($page, $idxKey, $dataVersion);
            $cache->set($cacheKey, $cacheData, self::APCU_CACHE_EXPIRE);
        }
        return $cacheData;
    }

    /**
     * @param $page
     * @param $idxKey
     * @param $cacheData
     * @param $dataVersion
     * @return mixed
     */
    private static function buildCacheData($page, $idxKey, $dataVersion) {
        $data = array();
        list($pageData, $hasNext, $total) = Resource_Service_GameListData::getPage($idxKey, $page);
        $data['list'] = Resource_Service_GameListFormat::output($pageData);
        $data['hasNext'] = $hasNext;
        $data['total'] = $total;
        $data['version'] = $dataVersion;
        return $data;
    }

    /**
     * 获取缓存Key
     * @param $idxKey
     * @param $page
     * @return string
     */
    private static function getCacheKey($idxKey, $page){
        $verStr = 'v3';
        $apkVer = Yaf_Registry::get("apkVersion");
        if (strnatcmp($apkVer, '1.4.8') < 0){
            $verStr = 'v1';
        } else if (strnatcmp($apkVer, '1.5.1') < 0){
            $verStr = 'v2';
        }
        return sprintf("%s-%s-%s", $idxKey, $verStr, $page);
    }

    /**
     * 获取Apcu缓存对象
     * @return object
     * @throws Exception
     */
    private static function getApcuCache(){
        return Cache_Factory::getCache(Cache_Factory::ID_LOCAL_APCU);
    }
}