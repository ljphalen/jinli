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
	const HOME_DATA = 'data';//首页数据
	const HOME_LIST = 'list';//首页数据

	const CLIENT_BEHAVIOUR = 'gameCliBehav_';

	//游戏sdk活动送A券缓存
	const SDK_TICKET_LOGIN = 'sdkTicketForLogin';
	const SDK_TICKET_CONSUME = 'sdkTicketForConsume';
	const HOME_H5_INDEX = 'Home_H5';
	
	const RECOMMEND = 'recommend';//后台编辑首页推荐相关
	const RECOMMEND_INFO = 'info_';//后台首页推荐临时数据

	const SUBJECT = 'subject';//后台编辑专题相关
	const SUBJECT_INFO = 'info_';//后台专题临时数据

	const WEBGAME = 'webGame';//
	const WEBGAME_INFO = 'editInfo';//后台网游推荐临时数据
	const WEBGAME_BANNER = 'banner';//客户端接口-轮播图
	const WEBGAME_BUTTON = 'button';//客户端接口-导航按钮
	const WEBGAME_LIST = 'list';//客户端接口-推荐列表
	const WEBGAME_OPENDATA = 'opendata';//开服列表
	const WEBGAME_OPENLIST = 'openlist';//开服列表
	const WEBGAME_DATA = 'data';//网游数据

	const SINGLEGAME = 'singlegame';//
	const SINGLEGAME_INFO = 'editInfo';//后台单机频道推荐临时数据
	const SINGLEGAME_BANNER = 'banner';//客户端接口-轮播图
	const SINGLEGAME_LIST = 'list';//客户端接口-推荐列表
	const SINGLEGAME_DATA = 'data';//单机数据

	const VIPCENTER = 'vipcenter';//
	const VIPCENTER_GAMELIST = 'gameList';//用户中心游戏
	const VIPCENTER_RANK = 'rank';//vip排行榜
	const VIPCENTER_MAXRANK = 'maxrank';//最大排行
	
	
    const GAMES_EXTRA = 'gamesExtra';
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

    const FESTIVAL_TOUCH_GAME = "festival_touch_game";

	
	const GAME_PACEKAGE_RELATION_GAMEID = 'game_package_relation_gameId';
	const GAME_PACKAGE_DIFF_INFO  ='game_diff_info_';
	
	const GIFT = 'gift';
	const GIFT_LIST  = 'giftList';
	const MY_GIFT_ID  = 'myGiftId';
	const GAME_GIFT_LIST = 'giftListForNew';
	const PRIVILEGE_GIFT_LIST = 'privilegeGiftList';
	
	const GIFT_LIST_VERSION ='giftListVersion';
	
	const GIFT_LESS_THAN_VERSION ='lessThan1.6.1';
	const GIFT_MORE_THAN_VERSION ='moreThan1.6.1';
	
	const INDEX ='index';
	const RECOMEMNT_LIST_VERSION_FOR_OLD = 'recomendListVersionForOld';
	const RECOMMENT_LIST_FOR_OLD = 'recomendLitForOld';
	const RECOMMENT_GAMEIDS = 'recommendGameIds';

    const LOCK_CATLIST_INDEX_CRON = 'lock_catList_index_cron';
    const LOCK_GAMELIST_DATA_CRON = 'lock_gameList_data_cron';

	const LOCK_FREE_DOWNLOAD_SYNC_STATE = 'game_cron_freedl_custatus_lock';
	const LOCK_FREE_DOWNLOAD_PROCESS = 'game_cron_freedl_process_lock';
	const LOCK_SCAN_GAMEDATA = 'game_cron_scan_gamedata_lock';
	const LOCK_UNLOGIN_CRON = 'game_cron_ulogin_lock';
    const LOCK_EXTRA_DAY_CRON = 'lock_extra_day_cron';
    const LOCK_EXTRA_HOUR_CRON = 'lock_extra_hour_cron';
    const LOCK_EXTRA_FREEDL_CRON = 'lock_extra_freedl_cron';
    const LOCK_EXTRA_SYS_MSG_CRON = 'game_cron_sys_msg_lock';
    const LOCK_LOGIN_GAME_ACOUPON = 'lock_game_login_acoupon';

	const SUFFIX_OF_RECOMMEND_TABLE = 'gameguess_table';

	const LOGIN_LAST_TIME = 'game_cron_ulogin_last_time';

    //A券活动中标识全部游戏
    const TASK_GAME_ALL_ACOUPON = 'task_game_all_acoupon';
    //分类页面缓存
    const CACHE_CATPAGE_PREFIX = "catPage:";
    const CACHE_CATPAGE_VER = "catPageVer";
    
    const HASH_TIME = "hash_time";
    
    //活动配置缓存
    const CACHE_ACTIVITY_CFG = "activity_cfg_";
    
    // 奖品发放
    const CACHE_ACTIVITY_LOG = "activity_log_";
    
    const CACHE_RANKLIST = 'clientrank_';

    
    const CRACK_GAME_LIST ='crack_game_list_';

    // 单机列表缓存 
    const SINGLE_LIST = "single_list_";
    
    const GAME_LIST_CONTENT_KEY = 'gameListData';
    //分类列表数据版本
    const CATLIST_DATA_VER = "catList_dataVer";
    //游戏id与索引key对应关系
    const CATLIST_GAMEID_IDXKEYS = "catList_gameId_idxKeys";
    //排行榜列表数据版本
    const RANKLIST_DATA_VER = "rankList_dataVer";
    //游戏属性版本
    const ATTRIBUTE_VERSION = 'attribute_version';
    const ATTRIVUTE_DATA  = 'attribute_data';
    //闪屏推荐
    const BEST_TJ_VERSION = 'bestTj_version';
    const BEST_TJ_INFO    = 'bestTj_info';


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
	
	
	public static function getApi($className, $method) {
	    return array(Util_CacheKey::CLASS_NAME => $className, Util_CacheKey::METHOD_NAME => $method);
	}

	public static function getKey($api, $args = array()) {
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
	
	public static function getCache($api, $args) {
	    $key = self::getKey($api, $args);  
	    $cache = Cache_Factory::getCache();
	    return $cache->get($key);
	}
    
	public static function updateCache($api, $args, $data, $expireTime = 86400) {
	    $key = self::getKey($api, $args);
	    $cache = Cache_Factory::getCache();
	    $result = $cache->set($key, $data, $expireTime);
	    if(! $result) {
	        Util_Log::info('Util_CacheKey', 'cache.log', $key);
	    }
	}

	public static function deleteCache($api, $args) {
	    $key = self::getKey($api, $args);
	    $cache = Cache_Factory::getCache();
	    return $cache->delete($key);
	}

	public static function getHCache($api, $args, $key) {
	    $hash = self::getKey($api, $args);
	    $cache = Cache_Factory::getCache();
	    $data = $cache->hGet($hash, $key);
	    if ($data === false) return false;
	    return json_decode($data, true);
	}

	public static function getAllHCache($api, $args) {
	    $hash = self::getKey($api, $args);
	    $cache = Cache_Factory::getCache();
	    $list = $cache->hGetAll($hash);
	    foreach ($list as $index => $data) {
	        $list[$index] = json_decode($data, true);
	    }
	    return $list;
	}
	
	public static function updateHCache($api, $args, $key, $data, $expireTime = 86400) {
	    $hash = self::getKey($api, $args);
	    $cache = Cache_Factory::getCache();
	    $data = json_encode($data);
	    $cache->hSet($hash, $key, $data, $expireTime);
	}
	
	public static function deleteHCache($api, $args, $key) {
	    $hash = self::getKey($api, $args);
	    $cache = Cache_Factory::getCache();
	    return $cache->hDel($hash, $key);
	}

	public static function getUserInfoKey($uuid) {
		return self::USER_KEY_PREFIX . $uuid . self::USER_KEY_SUFFIX;
	}
	
}
