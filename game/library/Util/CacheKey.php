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
	
	const HOME_SLIDE_AD = 'Slide_Ad';//轮播图
	const HOME_TEXT_AD  = 'Text_Ad';//文字公告
	const HOME_RECOMMEND_LIST = 'Recomend_List';//推荐列表
	const HOME_DAILY_RECOMMEND = 'Daily_Recommend';//每日一荐

	const CLIENT_BEHAVIOUR = 'CliBehav_';

	//游戏sdk活动送A券缓存
	const SDK_TICKET_LOGIN = 'sdkTicketForLogin';
	const SDK_TICKET_CONSUME = 'sdkTicketForConsume';
	
	const RECOMMEND = 'recommend';//后台编辑首页推荐相关
	const RECOMMEND_INFO = 'info_';//后台首页推荐临时数据

	const SUBJECT = 'subject';//后台编辑专题相关
	const SUBJECT_INFO = 'info_';//后台专题临时数据
	
	const GAME_FILTER = 'gameFilter_';
	const GAME_INFO = 'gameInfo_';
	const GAME_ATTRIBUTE = 'gameAttr_';
	const GAME_ALL_ATTRIBUTES = ':attribute:all';
	const PUSH_TOKEN = 'authToken';
	const PUSH_EXPIRED = 'expired';
	const MY_GIFT_LOGS ='_mygGiftLogs';
	const GIFT_ACTIVITY_INFO = '_gift_activity_info';
	const BEST_RECOMMEND = 'best_recommend';
	const USER_KEY_PREFIX = 'game';
	const USER_KEY_SUFFIX = '_user_info';
	
	const GAME_PACEKAGE_INFO = 'game_package_info';
	const GAME_PACKAGE_DIFF_INFO  ='game_diff_info_';
	
	const GIFT = 'gift';
	const GIFT_LIST  = 'giftList';
	const MY_GIFT_ID  = 'myGiftId';
		

	const LOCK_FREE_DOWNLOAD_SYNC_STATE = 'game_cron_freedl_custatus_lock';
	const LOCK_FREE_DOWNLOAD_PROCESS = 'game_cron_freedl_process_lock';
	const LOCK_SCAN_GAMEDATA = 'game_cron_scan_gamedata_lock';
	const LOCK_UNLOGIN_CRON = 'game_cron_ulogin_lock';

	const SUFFIX_OF_RECOMMEND_TABLE = 'gameguess_table';

	const ALL_GAME_PACKAGE = 'game-all-package';

	const GAME_UPDATE_INFO = 'game-g-u-';

	const LOGIN_LAST_TIME = 'game_cron_ulogin_last_time';

	/**
	 * @param array $api, such as array(Util_CacheKey::CLASS_NAME => 'Gift', Util_CacheKey::METHOD_NAME => 'myGiftList')
	 * @param string $version, such as 1.5.6, 1,5,7 ...
	 * @param $pageIndex, such as 1, 2, 3 ...
	 * @return string name of cache key, such as Gift::myGiftList_1.5.6_1
	 */
	public static function getCacheKeyForPage($api, $version = '', $pageIndex = 0) {
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
	    $cache = Cache_Factory::getCache();
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
	    $cache = Cache_Factory::getCache();
	    $cache->set($key, $data, $expireTime);
	}

	public static function deleteCache($api, $args) {
	    $key = self::getKey($api, $args);
	    $cache = Cache_Factory::getCache();
	    return $cache->delete($key);
	}
	
	/** end Add by wupeng 2015/04/24*/
	
	public static function getUserInfoKey($uuid) {
		return self::USER_KEY_PREFIX . $uuid . self::USER_KEY_SUFFIX;
	}

	
}
