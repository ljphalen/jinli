<?php
if (! defined('BASE_PATH')) exit('Access Denied!');

/**
 * 排行榜游戏列表索引数据处理文件 Class Resource_Index_CategoryList 不包含猜你喜欢分类的索引数据
 *
 * @author fanch
 */
class Resource_Index_RankList {
    
    //列表前缀
    const PREFIX = "rankList";
    //每页数据
    const PAGE_SIZE = 10;
    //初始化每页数据
    const MAX_TOTAL = 1000;
    //列表索引缓存时间
    const EXPIRE = Cache_ListContent::LIST_CONTENT_EXPIRE; //1天有效期
    //游戏id的冗余查询量
    const REDUNDANCY_NUM = 20;
    //排行榜游戏总数缺省值
    const DEFAULT_NUM = 50;
    
    //周榜
    const RANK_TYPE_WEEK = 'weekRank';
    //月榜
    const RANK_TYPE_MONTH = 'monthRank';
    //新游榜
    const RANK_TYPE_NEW = 'newRank';
    //上升最快
    const RANK_TYPE_UP = 'upRank';
    //网游榜
    const RANK_TYPE_ONLINE = 'onlineRank';
    //单机榜
    const RANK_TYPE_PC = 'pcRank';
    //网游活跃榜
    const RANK_TYPE_OLACTIVE = 'olactiveRank';
    //游戏飙升榜
    const RANK_TYPE_SOARING = 'soaringRank';
    
    private static $allRankType = array(
                    'weekRank',
                    'monthRank',
                    'newRank',
                    'upRank',
                    'onlineRank',
                    'pcRank',
                    'olactiveRank',
                    'soaringRank',
    );
    
    public static function buildRankListIdx($rankType) {
        if (! $rankType) {
            return false;
        }
        $gameIds = array();
        switch ($rankType) {
            case self::RANK_TYPE_WEEK :
                $gameIds = self::getWeekGameIds();
                break;
            case self::RANK_TYPE_MONTH :
                $gameIds = self::getMonthGameIds();
                break;
            case self::RANK_TYPE_NEW :
                $gameIds = self::getNewGameIds();
                break;
            case self::RANK_TYPE_UP :
                $gameIds = self::getUpGameIds();
                break;
            case self::RANK_TYPE_ONLINE :
                $gameIds = self::getOnlineGameIds();
                break;
            case self::RANK_TYPE_PC :
                $gameIds = self::getPCGameIds();
                break;
            case self::RANK_TYPE_OLACTIVE :
                $gameIds = self::getOlactiveGameIds();
                break;
            case self::RANK_TYPE_SOARING :
                $gameIds = self::getSoaringGameIds();
                break;
            default :
                return false;
        }
        echo 'gameIds count :' . count($gameIds);
        if (!$gameIds) {
        	return false;
        }
        self::saveIdxData($rankType, $gameIds);
    }
    
    public static function getIdxKey($rankType) {
        return sprintf("%s:%s", self::PREFIX, $rankType);
    }
    
    public static function removeIdxItem($gameId) {
        foreach (self::$allRankType as $rankType) {
            $idxKey = self::getIdxKey($rankType);
            $obj = self::getIdxContent($idxKey);
            return $obj->removeFromIndex($gameId);
        }
    }
    
    /**
     * 获取索引key的数据版本
     * @param $idxKey
     * @return int
     */
    public static function getIdxVersion($idxKey){
        $cache = Cache_Factory::getCache(Cache_Factory::ID_REMOTE_REDIS);
        $idxVersion = $cache->hGet(Util_CacheKey::RANKLIST_DATA_VER, $idxKey);
        $idxVersion = ($idxVersion) ? $idxVersion : 0;
    
        $allAcoupon = Resource_Service_GameListData::getGameAllAcoupon();
        $version = strval($idxVersion) . ':' . strval($allAcoupon);
        return $version ;
    }
    
    private static function getWeekGameIds() {
        $setTotal = Game_Service_Config::getValue('game_rank_weeknum');
        $setTotal = $setTotal ? $setTotal : self::DEFAULT_NUM;
        $setTotal = min($setTotal, self::MAX_TOTAL);
        $limit = $setTotal + self::REDUNDANCY_NUM;
        $dayId = Client_Service_WeekNewRank::getLastDayId();
        $params = array(
                        'day_id' => $dayId 
        );
        list (  , $biGames ) = Client_Service_WeekNewRank::getList(1, $limit, $params);
        return self::getGameIds($biGames, $setTotal); 
    }
    
    private static function getMonthGameIds() {
        $setTotal = Game_Service_Config::getValue('game_rank_monthnum');
        $setTotal = $setTotal ? $setTotal : self::DEFAULT_NUM;
        $limit = $setTotal + self::REDUNDANCY_NUM;
        $dayId = Client_Service_MonthRank::getLastDayId();
        $params = array(
                        'day_id' => $dayId
        );
        list (  , $biGames ) = Client_Service_MonthRank::getList(1, $limit, $params);
        return self::getGameIds($biGames, $setTotal);
    }
    
    private static function getNewGameIds() {
        $setTotal = Game_Service_Config::getValue('game_rank_cnewnum');
        $setTotal = $setTotal ? $setTotal : self::DEFAULT_NUM;
        $limit = $setTotal + self::REDUNDANCY_NUM;
        $dayId = Client_Service_NewRank::getLastDayId();
        $params = array(
                        'day_id' => $dayId
        );
        list (  , $biGames ) = Client_Service_NewRank::getList(1, $limit, $params);
        return self::getGameIds($biGames, $setTotal);
    }
    
    private static function getUpGameIds() {
        $setTotal = Game_Service_Config::getValue('game_rank_fastestnum');
        $setTotal = $setTotal ? $setTotal : self::DEFAULT_NUM;
        $limit = $setTotal + self::REDUNDANCY_NUM;
        $dayId = Client_Service_FastestRank::getLastDayId();
        $params = array(
                        'day_id' => $dayId
        );
        list (  , $biGames ) = Client_Service_FastestRank::getList(1, $limit, $params);
        return self::getGameIds($biGames, $setTotal);
    }
    
    private static function getOnlineGameIds() {
        $setTotal = Game_Service_Config::getValue('game_rank_onlineknum');
        $setTotal = $setTotal ? $setTotal : self::DEFAULT_NUM;
        $limit = $setTotal + self::REDUNDANCY_NUM;
        $dayId = Client_Service_OlgRank::getLastDayId();
        $params = array(
                        'day_id' => $dayId
        );
        list (  , $biGames ) = Client_Service_OlgRank::getList(1, $limit, $params);
        return self::getGameIds($biGames, $setTotal);
    }
    
    private static function getPCGameIds() {
        $setTotal = Game_Service_Config::getValue('game_rank_pcnum');
        $setTotal = $setTotal ? $setTotal : self::DEFAULT_NUM;
        $limit = $setTotal + self::REDUNDANCY_NUM;
        $dayId = Client_Service_SingleRank::getLastDayId();
        $params = array(
                        'day_id' => $dayId
        );
        list (  , $biGames ) = Client_Service_SingleRank::getList(1, $limit, $params);
        return self::getGameIds($biGames, $setTotal);
    }
    
    private static function getOlactiveGameIds() {
        $setTotal = Game_Service_Config::getValue('game_rank_olranknum');
        $setTotal = $setTotal ? $setTotal : self::DEFAULT_NUM;
        $limit = $setTotal + self::REDUNDANCY_NUM;
        $dayId = Client_Service_RankVersion::getRankDayId(Client_Service_RankVersion::ONLINE_ACTION_RANK);
        $params = array(
                        'day_id' => $dayId
        );
        list (  , $biGames ) = Client_Service_OlactiveRank::getList(1, $limit, $params);
        return self::getGameIds($biGames, $setTotal);
    }
    
    private static function getSoaringGameIds() {
        $setTotal = Game_Service_Config::getValue('game_rank_soaringnum');
        $setTotal = $setTotal ? $setTotal : self::DEFAULT_NUM;
        $limit = $setTotal + self::REDUNDANCY_NUM;
        $dayId = Client_Service_RankVersion::getRankDayId(Client_Service_RankVersion::SOARING_RANK);
        $params = array(
                        'day_id' => $dayId
        );
        list (  , $biGames ) = Client_Service_SoaringRank::getList(1, $limit, $params);
        return self::getGameIds($biGames, $setTotal);
    }
    
    private static function getGameIds(&$biGames, $setTotal) {
        echo '$biGames count :' . count($biGames);
        $biGameIds = Common::resetKey($biGames, 'game_id');
        $biGameIds = array_unique(array_keys($biGameIds));
        $games = Resource_Service_GameListData::getList($biGameIds);
        if (count($games) > $setTotal) {
            $games = array_slice($games, 0, $setTotal);
        }
        $games = Common::resetKey($games, 'gameid');
        $gameIds = array_keys($games);
        return $gameIds;
    }
    
    private static function saveIdxData($rankType, $gameIds) {
        $idxKey = self::getIdxKey($rankType);
        $result = self::saveIdxContent($idxKey, $gameIds);
        if(!$result){
            $message = "排行榜索引[{$rankType}]数据写入失败：" . json_encode(array('idxKey' => $idxKey, 'gameIds' => $gameIds));
            Util_Log::debug('rankListIndx', 'rankidx.log', $message);
        }else {
            self::saveIdxVersion($idxKey);
        }
    }
    
    /**
     * @param $idxKey
     * @param $pageSize
     * @param $indexData
     * @param int $expire
     * @return bool
     */
    private static function saveIdxContent($idxKey, $gameIds){
        $obj = Resource_Service_GameListData::getIndexInstance($idxKey);
        $begin = $obj->buildIndexBegin(self::PAGE_SIZE);
        if(!$begin){
            return false;
        }
        $result = $obj->buildListIndex($gameIds, self::EXPIRE);
        if ($result == false) {
            return false;
        }
        $finish = $obj->buildIndexFinish();
        if (!$finish) {
            return false;
        }
        return true;
    }
    
    private static function saveIdxVersion($idxKey){
        $cache = Cache_Factory::getCache(Cache_Factory::ID_REMOTE_REDIS);
        $cache->hSet(Util_CacheKey::RANKLIST_DATA_VER, $idxKey, time(), self::EXPIRE);
    }
}