<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 缓存工厂
 * @author rainkid
 *
 */
class Cache_FactoryNew {
    
    static $sCacheInstances;

    const ID_REMOTE_REDIS = 1;
    const ID_LOCAL_APCU = 2;
    const ID_REMOTE_MEMCACHE = 3;
    
    const NAME_REDIS = 'Cache_Redis';
    const NAME_MEMCACHE = 'Cache_Memcache';
    const NAME_APCU = 'Cache_Apcu';

    private static $sIdToName = array(
                                   self::ID_REMOTE_REDIS => self::NAME_REDIS,
                                   self::ID_LOCAL_APCU => self::NAME_APCU,
                                   self::ID_REMOTE_MEMCACHE => self::NAME_MEMCACHE
                                  );

    private static $sIdToConfig = array(
                                   self::ID_REMOTE_REDIS => 'redisConfig'
                                  );
    /**
     * 
     * 统一获取cache接口
     * @param string $which: unique identification of Cache
     * @return object Cache instance
     */
    static public function getCache($which = self::ID_REMOTE_REDIS) {
        if(!array_key_exists($which, self::$sIdToName)) {
            $which = self::ID_REMOTE_REDIS;
        }

        $cacheName = self::$sIdToName[$which];
        $config = array();
        if(array_key_exists($which, self::$sIdToConfig)) {
            $configFile = self::$sIdToConfig[$which];
            $config = Common::getConfig($configFile);
        }
        $cacheId = md5($cacheName . json_encode($config));

        $instanceExists = (isset(self::$sCacheInstances[$cacheId]) &&
                is_object(self::$sCacheInstances[$cacheId])) ? true : false;

        if($instanceExists) {
            return self::$sCacheInstances[$cacheId];
        }

        if (!class_exists($cacheName)) {
            throw new Exception('empty class name');
        }
        self::$sCacheInstances[$cacheId] = new $cacheName($config);
        return self::$sCacheInstances[$cacheId];
    }
}
