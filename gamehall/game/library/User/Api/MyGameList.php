<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 用户玩的游戏
 * User_Api_MyGameList
 * @author wupeng
 */
class User_Api_MyGameList {

    public static function getList($uuid) {
        $api = Util_CacheKey::getApi(Util_CacheKey::VIPCENTER, Util_CacheKey::VIPCENTER_GAMELIST);
        $args = func_get_args();
        $logList = Util_Api_Cache::getCache($api, $args);
        if($logList === false) {
            $logList = self::getDBData($uuid);
            Util_Api_Cache::updateCache($api, $args, $logList);
        }
        $gameIdList = array_keys($logList);
        $data = self::reInitGameInfo($gameIdList, $logList);
        $count = count($data);
        if($count > 4) {
            $data = array_slice($data, 0, 4);
        }elseif ($count < 4) {
            $idList = Game_Api_WebRecommendList::getFirstListGameList();
            if($idList) {
                $gameIdList = array_merge($gameIdList, $idList);
                $gameIdList=array_unique($gameIdList);
            }
            $data = self::reInitGameInfo($gameIdList, $logList);
            if(count($data) > 4) {
                $data = array_slice($data, 0, 4);
            }
        }
        return $data;
    }

    private static function getDBData($uuid) {
        $searchParams = array(User_Service_GameLog::F_UUID => $uuid);
        $logList = User_Service_GameLog::getTopListBy($searchParams, 10);
        $logList = Common::resetKey($logList, User_Service_GameLog::F_GAME_ID);
        return $logList;
    }
    
    /**服务端主动刷新缓存*/
    public static function updateClientDayCacheData($uuid) {
        $data = self::getDBData($uuid);
        $api = Util_CacheKey::getApi(Util_CacheKey::VIPCENTER, Util_CacheKey::VIPCENTER_GAMELIST);
        $args = func_get_args();
        Util_Api_Cache::updateCache($api, $args, $data);
    }

    private static function reInitGameInfo($data, $logList) {
        if(! $data) return $data;
        $list = Resource_Service_GameListData::getList($data);
        $gameList = Resource_Service_GameListFormat::output($list);
        foreach ($gameList as $key => $gameInfo) {
            $gameid = $gameInfo['gameid'];
            if(! $logList[$gameid]) {
                continue;
            }
            $gameInfo['timeStamp'] = $logList[$gameid][User_Service_GameLog::F_LOGIN_TIME];
            $gameList[$key] = $gameInfo;
        }
        return $gameList;
    }
	
}
