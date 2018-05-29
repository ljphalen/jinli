<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * Cache key constants
 *
 * @package utility
 */
class Util_CacheKey {
	/* Rules for cache hash key and field name
	 * 1. Hash key and filed name must be constant defined in this file.
	 * 2. Hash key must be module name, such as Gift, Home ...
	 */


	const INVALID_KEY = '';
	const KEY_SEPARATOR = '_';
	const CLASS_NAME = 'className';
	const METHOD_NAME = 'methodName';
	
	const HOME = 'home';
	
	const HOME_SLIDE_AD = 'SlideAd';//轮播图
	const HOME_TEXT_AD  = 'TextAd';//文字公告
	const HOME_RECOMMEND_LIST = 'RecomendList';//推荐列表
	const HOME_DAILY_RECOMMEND = 'DailyRecommend';//每日一荐

	const CLIENT_BEHAVIOUR = 'ClientBehaviour_';

	//游戏sdk活动送A券缓存
	const SDK_TICKET_LOGIN = 'sdkTicketForLogin';
	const SDK_TICKET_CONSUME = 'sdkTicketForConsume';
	const HOME_H5_INDEX = 'Home_H5';
	
	/**
	 * @param array $api, such as array(Util_CacheKey::CLASS_NAME => 'Gift', Util_CacheKey::METHOD_NAME => 'myGiftList')
	 * @param string $version, such as 1.5.6, 1,5,7 ...
	 * @param $pageIndex, such as 1, 2, 3 ...
	 * @return string name of cache key, such as Gift::myGiftList_1.5.6_1
	 */
	public static function getCacheKeyForPage($api, $version, $pageIndex = 0) {
		if((!is_array($api)) || (!$version)) {
			return self::INVALID_KEY;
		}
		if ((!$api[self::CLASS_NAME]) || (!$api[self::METHOD_NAME])) {
			return self::INVALID_KEY;
		}

		$keyName = $api[self::CLASS_NAME] . '::' . $api[self::METHOD_NAME];
		if ($pageIndex) {
			$keyName = $keyName . self::KEY_SEPARATOR . $pageIndex;
		}
		$keyName = $keyName . self::KEY_SEPARATOR . $version;

		return $keyName;
	}
	
	public static function getCacheKeyForCommon($api, $version = ''){
		if(!is_array($api)) {
			return self::INVALID_KEY;
		}
		
		if ((!$api[self::CLASS_NAME]) || (!$api[self::METHOD_NAME])) {
			return self::INVALID_KEY;
		}
		$keyName = $api[self::CLASS_NAME] . '::' . $api[self::METHOD_NAME];
		if($version){
			$keyName = $keyName . self::KEY_SEPARATOR . $version;
		}
		return $keyName;
		
	}
	
	
	/** start Add by wupeng 2015/04/24*/
	/**
	 * 取缓存key前缀
	 * @param unknown $className
	 * @param unknown $method
	 * @return multitype:unknown
	 */
	public static function getApi($className, $method) {
	    return array(Util_CacheKey::CLASS_NAME => $className, Util_CacheKey::METHOD_NAME => $method);
	}
	
	/**
	 * 获取缓存key
	 * @param unknown $api
	 * @param unknown $args
	 * @return string
	 */
	public static function getKey($api, $args) {
	    if(!is_array($api)) {
	        return self::INVALID_KEY;
	    }
		if ((!$api[self::CLASS_NAME]) || (!$api[self::METHOD_NAME])) {
			return self::INVALID_KEY;
		}
		$keyName = $api[self::CLASS_NAME] . '::' . $api[self::METHOD_NAME];
		if($args) {
		    $keyName = $keyName . self::KEY_SEPARATOR . implode(self::KEY_SEPARATOR, $args);
		}
        return $keyName;
	}
	
	/**
	 * 取缓存数据
	 * @param unknown $api
	 * @param unknown $args
	 */
	public static function getCache($api, $args) {
	    $key = self::getKey($api, $args);  
	    $cache = Common::getCache();
	    return $cache->get($key);
	}
    
	/**
	 * 更新缓存数据
	 * @param unknown $api
	 * @param unknown $args
	 * @param unknown $data
	 * @param unknown $expireTime
	 */
	public static function updateCache($api, $args, $data, $expireTime = 3600) {
	    $key = self::getKey($api, $args);
	    $cache = Common::getCache();
	    $cache->set($key, $data, $expireTime);
	}
	
	/** end Add by wupeng 2015/04/24*/
	
	
}
