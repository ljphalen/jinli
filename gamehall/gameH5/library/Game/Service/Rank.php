<?php
if (! defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Enter description here ...
 * @author rainkid
 */
class Game_Service_Rank {
    
    const RANK_TYPE_WEEK = 'weekRank';
    const RANK_TYPE_MONTH = 'monthRank';
    const RANK_TYPE_NEW = 'newRank';
    const RANK_TYPE_UP = 'upRank';
    const RANK_TYPE_ONLINE = 'onlineRank';
    const RANK_TYPE_PC = 'pcRank';
    
    const RANK_LIST_DEFAULT_NUM = 50;
    
    public static $sRankType = array(
                    self::RANK_TYPE_WEEK => '周榜', 
                    self::RANK_TYPE_MONTH => '月榜', 
                    self::RANK_TYPE_NEW => '新游榜', 
                    self::RANK_TYPE_UP => '上升最快榜', 
                    self::RANK_TYPE_ONLINE => '网游榜', 
                    self::RANK_TYPE_PC => '单机榜' 
    );
    
    public static function getH5OpenRankType() {
        $h5RankConf = json_decode(Game_Service_Config::getValue('h5_rank_config'), true);
        $sortArr = array();
        $h5OpenRankType = array();
        foreach ( $h5RankConf as $rankType => $rankStatus ) {
            if ($rankStatus['status'] == 1) {
                $h5OpenRankType[$rankType] = self::$sRankType[$rankType];
                $sort[] = $rankStatus['sort'];
            }
        }
        array_multisort($sort, SORT_DESC, SORT_NUMERIC, $h5OpenRankType);
        return $h5OpenRankType;
    }
    
    public static function getGameListByType($rankType, $page = 1, $limit = 10, $params = array()) {
        $rankGameIds = self::getBiRankGameIdsByType($rankType);
        
        $params['status'] = 1;
        if( $rankGameIds ){
            $params['id'] = array('IN', $rankGameIds);
        }
        
        list($total, $resourceGames) = Resource_Service_Games::search($page, $limit, $params );
        $games = array();
        foreach($resourceGames as $key=>$value) {
            $gameInfo = Resource_Service_GameData::getGameAllInfo($value['id']);
            if ($gameInfo) {
                $games[] = $gameInfo;
            }
        }
        return array($total, $games);
    }
    
    private static function getBiRankGameIdsByType($rankType) {
        $biGames = array();
        switch ($rankType) {
            case self::RANK_TYPE_WEEK :
                $biGames = self::getWeekRank();
                break;
            case self::RANK_TYPE_MONTH :
                $biGames = self::getMonthRank();
                break;
            case self::RANK_TYPE_NEW :
                $biGames = self::getNewRank();
                break;
            case self::RANK_TYPE_UP :
                $biGames = self::getUpRank();
                break;
            case self::RANK_TYPE_ONLINE :
                $biGames = self::getOnlineRank();
                break;
            case self::RANK_TYPE_PC :
                $biGames = self::getPcRank();
                break;
        }
        
        $gameIds = Common::resetKey($biGames, 'game_id');
        $gameIds = array_unique(array_keys($gameIds));
        return $gameIds;
    }
    
    private static function getWeekRank() {
        $limit = Game_Service_Config::getValue('game_rank_weeknum');
        $limit =  $limit ? $limit : self::RANK_LIST_DEFAULT_NUM;
        $dayId =  Client_Service_WeekNewRank::getLastDayId();
        $params['day_id'] = $dayId;
        list(, $biGames) = Client_Service_WeekNewRank::getList(1,$limit,$params);
        return $biGames;
    }
    
    private static function getMonthRank() {
        $limit = Game_Service_Config::getValue('game_rank_monthnum');
        $limit =  $limit ? $limit : self::RANK_LIST_DEFAULT_NUM;
        $dayId =  Client_Service_MonthRank::getLastDayId();
        $params['day_id'] = $dayId;
        list(, $biGames) = Client_Service_MonthRank::getList(1,$limit,$params);
        return $biGames;
    }
    
    private static function getNewRank() {
        $limit = Game_Service_Config::getValue('game_rank_cnewnum');
        $limit =  $limit ? $limit : self::RANK_LIST_DEFAULT_NUM;
        $dayId =  Client_Service_NewRank::getLastDayId();
        $params['day_id'] = $dayId;
        list(, $biGames) = Client_Service_NewRank::getList(1,$limit,$params);
        return $biGames;
    }
    
    private static function getUpRank() {
        $limit = Game_Service_Config::getValue('game_rank_fastestnum');
        $limit =  $limit ? $limit : self::RANK_LIST_DEFAULT_NUM;
        $dayId =  Client_Service_FastestRank::getLastDayId();
        $params['day_id'] = $dayId;
        list(, $biGames) = Client_Service_FastestRank::getList(1,$limit,$params);
        return $biGames;
    }
    
    private static function getOnlineRank() {
        $limit = Game_Service_Config::getValue('game_rank_onlineknum');
        $limit =  $limit ? $limit : self::RANK_LIST_DEFAULT_NUM;
        $dayId =  Client_Service_OlgRank::getLastDayId();
        $params['day_id'] = $dayId;
        list(, $biGames) = Client_Service_OlgRank::getList(1,$limit,$params);
        return $biGames;
    }
    
    private static function getPcRank() {
        $limit = Game_Service_Config::getValue('game_rank_pcnum');
        $limit =  $limit ? $limit : self::RANK_LIST_DEFAULT_NUM;
        $dayId =  Client_Service_SingleRank::getLastDayId();
        $params['day_id'] = $dayId;
        list(, $biGames) = Client_Service_SingleRank::getList(1,$limit,$params);
        return $biGames;
    }
}
