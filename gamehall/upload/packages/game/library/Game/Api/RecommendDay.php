<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 首页每日一荐
 * Game_Api_RecommendDay
 * @author wupeng
 */
class Game_Api_RecommendDay {

    public static $clientVersionList = array(
        '1.5.7', '1.5.8',
    );
    
    /**获取客户端每日一荐*/
    public static function getClientDay($systemVersion, $clientVersion) {
        $systemVersionResult = Common::compareSysytemVersion($systemVersion);
        if($systemVersionResult){
            $systemVersion = Game_Service_RecommendNew::GREATE_SYSTEM_VERSION;
        }else{
            $systemVersion = Game_Service_RecommendNew::LESS_SYSTEM_VERSION;
        }
        if(Common::isAfterVersion($clientVersion, '1.5.8')) {
            $clientVersion = "1.5.8";
        }else{
            $clientVersion = "1.5.7";
        }
        $data = self::getClientDayCacheData($systemVersion, $clientVersion);
        return $data;
    }

    private static function getClientDayCacheData($systemVersion, $clientVersion) {
        $apcu = Cache_Factory::getCache(Cache_Factory::ID_LOCAL_APCU);
	    $api = Util_CacheKey::getApi(Util_CacheKey::HOME, Util_CacheKey::HOME_DAILY_RECOMMEND);
	    $key = Util_CacheKey::getKey($api, func_get_args());
        $data = $apcu->get($key);
        if ($data === false){
            $data = self::getClientDayRedisData($systemVersion, $clientVersion);
            $apcu->set($key, $data, 60);
        }
        return $data;
    }
    
    private static function getClientDayRedisData($systemVersion, $clientVersion) {
        $api = Util_CacheKey::getApi(Util_CacheKey::HOME, Util_CacheKey::HOME_DAILY_RECOMMEND);
        $args = func_get_args();
        $data = Util_Api_Cache::getCache($api, $args);
        if($data === false) {
            $data = self::getClientDayData();
            Util_Api_Cache::updateCache($api, $args, $data);
        }
        $data = self::reInitGameInfo($data);
        return $data;
    }

    private static function reInitGameInfo($data) {
        $gameData = $data[0];
        if(! $gameData) return $data;
        $gameId = $gameData['gameId'];
        if(! $gameId) return $data;
        $list = Resource_Service_GameListData::getList(array($gameId));
        $gameList = Resource_Service_GameListFormat::output($list);
        if($gameList) {
            $gameInfo = $gameList[0];
            unset($gameInfo['viewType']);
            $gameData = array_merge($gameData, $gameInfo);
        }
        $data[0] = $gameData;
        return $data;
    }
    
    private static function getClientDayData() {
        $day = self::getRecommendDailyData();
        $data = self::makeClientDayData($day);
        return $data;
    }
    
    public static function getDayData() {
        $day_id = strtotime(date("Y-m-d"));
        $searchParams = array(
            'day_id' => $day_id,
            'status' => Game_Service_RecommendDay::STATUS_OPEN,
            'game_status' => Resource_Service_Games::STATE_ONLINE,
        );
        $day = Game_Service_RecommendDay::getRecommendDayBy($searchParams);
        return $day;
    }
    
    private static function makeClientDayData($day) {
	    $data = array();
	    if(! $day) return $data;
        $data[] = array(
            'listItemType'=>'DailyRecommend',
            'title'=>html_entity_decode($day['title'], ENT_QUOTES),
            'resume'=>html_entity_decode($day['content'], ENT_QUOTES),
            'viewType'=>'GameDetailView',
            'ad_id'=>$day['id'],
            'gameId'=>$day['game_id'],
        );
	    return $data;
    }
    
    /**服务端主动刷新缓存*/
    public static function updateClientDayCacheData() {
        $day = self::reloadRecommendDailyData();
        $api = Util_CacheKey::getApi(Util_CacheKey::HOME, Util_CacheKey::HOME_DAILY_RECOMMEND);
        $keys = Util_Api_Cache::getValidKeys($api);
        foreach ($keys as $key => $args) {
            if(count($args) != 2) {
                Util_Api_Cache::updateCache($api, $args, array());
                continue;
            }
            Yaf_Registry::set("androidVersion", $args[0]);
            Yaf_Registry::set("apkVersion", $args[1]);
            $data = self::makeClientDayData($day);
            Util_Api_Cache::updateCache($api, $args, $data);
        }
    }

    public static function gameIsShowing($gameId) {
        $recommend = self::getRecommendDailyData();
        if ($recommend && $recommend['game_id'] == $gameId) {
            return true;
        }
        return false;
    }

    private static function getRecommendDailyData() {
        $api = Util_CacheKey::getApi(Util_CacheKey::HOME, Util_CacheKey::HOME_DATA);
        $text = Util_CacheKey::getHCache($api, array(), 'daily');
        if($text === false) {
            $text = self::getDayData();
            Util_CacheKey::updateHCache($api, array(), 'daily', $text);
        }
        return $text;
    }
    
    private static function reloadRecommendDailyData() {
        $api = Util_CacheKey::getApi(Util_CacheKey::HOME, Util_CacheKey::HOME_DATA);
        $text = self::getDayData();
        Util_CacheKey::updateHCache($api, array(), 'daily', $text);
        return $text;
    }
	
}
