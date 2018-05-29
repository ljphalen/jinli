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
        $api = Util_CacheKey::getApi(Util_CacheKey::HOME, Util_CacheKey::HOME_DAILY_RECOMMEND);
        $data = Util_CacheForApi::getCache($api, func_get_args());
        if($data === false) {
            $data = self::getClientDayData();
            Util_CacheForApi::updateCache($api, func_get_args(), $data);
        }
        return $data;
    }
    
    private static function getClientDayData() {
        $day = self::getDayData();
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
	    if($day) {
	        $gameInfo = Resource_Service_GameData::getBasicInfo($day['game_id']);
	        $extalInfo = Resource_Service_GameData::getExtraInfo($day['game_id']);
	        if($gameInfo){
	            $data[] = array(
	                'listItemType'=>'DailyRecommend',
	                'title'=>html_entity_decode($day['title'], ENT_QUOTES),
	                'resume'=>html_entity_decode($day['content'], ENT_QUOTES),
	                'viewType'=>'GameDetailView',
	                'ad_id'=>$day['id'],
	                'gameId'=>$day['game_id'],
	                'img'=>$gameInfo['img'],
	                'name'=>html_entity_decode($gameInfo['name'], ENT_QUOTES),
	                'size'=>$gameInfo['size'],
	                'link'=>$gameInfo['link'],
	                'package'=>$gameInfo['package'],
	                'category'=>$gameInfo['category'],
	                'hot'=>$gameInfo['hot'],
	                'score'=>$extalInfo['score'],
	                'freedl'=>$extalInfo['freedl'],
	                'reward'=>$extalInfo['reward'],
	                'attach'=>$extalInfo['attach']
	                
	            );
	        }
	    }
	    return $data;
    }
    
    /**服务端主动刷新缓存*/
    public static function updateClientDayCacheData() {
        $systemVersionList = Game_Api_Util_RecommendListUtil::$systemVersionList;
        $day = self::getDayData();
        $api = Util_CacheKey::getApi(Util_CacheKey::HOME, Util_CacheKey::HOME_DAILY_RECOMMEND);
        
        $keys = Util_CacheForApi::getValidKeys($api);
        
//         $keys = array();
//         $keys[] = array('args' => array("Android4.2.0", "1.5.8"));
//         $keys[] = array('args' => array("Android4.1.0", "1.5.8"));
//         $keys[] = array('args' => array("Android4.2.0", "1.5.7"));
        
        foreach ($keys as $key => $params) {
            $args = $params['args'];
            if(count($args) != 2) {
                Util_CacheForApi::updateCache($api, $args, array());
                continue;
            }
            Yaf_Registry::set("androidVersion", $args[0]);
            Yaf_Registry::set("apkVersion", $args[1]);
            $data = self::makeClientDayData($day);
            Util_CacheForApi::updateCache($api, $args, $data);
        }
    }

    /**
     * 前端请求游戏附加属性的同时检查首页缓存数据是否需要更新
     */
    public static function checkGameExtrainfo($newExtrainfo) {
        $newData = Common::resetKey($newExtrainfo, 'gameId');
        $api = Util_CacheKey::getApi(Util_CacheKey::HOME, Util_CacheKey::HOME_DAILY_RECOMMEND);
        $keys = Util_CacheForApi::getValidKeys($api);
        foreach ($keys as $key => $params) {
            $args = $params['args'];
            $data = Util_CacheForApi::getCache($api, $args);
            if(! $data || ! isset($data[0])) continue;
            $updateData = $data[0];
            $updateFlg = self::updateCacheByGameExtraInfo($updateData, $newData);
            if($updateFlg) {
                Util_CacheForApi::updateCache($api, $args, array($updateData));
            }
        }
    }
    
    private static function updateCacheByGameExtraInfo(&$data, $newData) {
        $updateFlg = false;
        $extalInfo = $newData[$data['gameId']];
        if (! $extalInfo) return $updateFlg;
        if ($data['score'] != $extalInfo['score'] || $data['attach'] != $extalInfo['attach'] ||
            $data['freedl'] != $extalInfo['freedl'] || $data['reward'] != $extalInfo['reward']) {
                $updateFlg = true;
                $data['attach'] = $extalInfo['attach'];
                $data['freedl'] = $extalInfo['freedl'];
                $data['reward'] = $extalInfo['reward'];
                $data['score'] = $extalInfo['score'];
        }
        return $updateFlg;
    }

    public static function gameIsShowing($gameId) {
        $recommend = self::getDayData();
        if ($recommend && $recommend['game_id'] == $gameId) {
            return true;
        }
        return false;
    }
	
}
