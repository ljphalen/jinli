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
     * 统一获取cache接口
     * @param $cacheConfig
     * @param string $cacheType
     * @return Cache_Interface
     * @throws Exception
     */
	static public function getCache($cacheConfig, $cacheType = 'Redis') {
		if(!in_array($cacheType, array('Memcache', 'Redis'))) $cacheType = 'Redis';
		$cacheName = 'Cache_' . $cacheType;
		$key = md5($cacheName);
		if(isset(self::$instances[$key]) && is_object(self::$instances[$key])) return self::$instances[$key];
		if (!class_exists($cacheName)) throw new Exception('empty class name');
		self::$instances[$key] = new $cacheName($cacheConfig);
		return self::$instances[$key];
	}
}