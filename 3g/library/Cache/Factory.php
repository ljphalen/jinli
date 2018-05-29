<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 缓存工厂
 * @author rainkid
 *
 */
class Cache_Factory {

    static $instances;

    /**
     *
     * 统一获取cache接口
     *
     * @param array  $cacheInfo
     * @param string $cacheType
     */
    static public function getCache($cacheConfig, $i = 0) {
        if (isset(self::$instances['redis'][$i]) && is_object(self::$instances['redis'][$i])) return self::$instances['redis'][$i];
        self::$instances['redis'][$i] = new Cache_Redis($cacheConfig, $i);
        return self::$instances['redis'][$i];
    }
}