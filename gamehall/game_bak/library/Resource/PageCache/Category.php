<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Resource_PageCache_Category
 * @author fanch
 *
 */
class Resource_PageCache_Category{

    const PARAMS_PAGE = 'page';
    const PARAMS_PERPAGE = 'perPage';
    const PARAMS_ID = 'id';
    const PARAMS_PID = 'pid';
    const PARAMS_IMEICRC = 'imcrc';
    const PARAMS_FILTER = 'filter';
    const PARAMS_SORTTYPE = 'sortType';
    //clientVersioon 用来分辨搜索结果相同，而输出的格式化内容不同的值
    const PARAMS_CLIENTVERSION = 'clientVersion';
    //method 用来分辨搜索结果相同，而输出的格式化内容不同的值
    const PARAMS_ACTIONSTR = 'action';
    //intersrc 用来分辨搜索结果相同，而输出的格式化内容不同的值
    const PARAMS_INTERSRC = 'intersrc';

    const SEARCH_ALLGAMES = 100;
    const SEARCH_NEWGAMES = 101;
    const SEARCH_GUESSGAMES = "caini";

    const SORTTYPE_HOT = 'hot';
    const SORTTYPE_NEW = 'new';

    const CACHE_PAGE_EXPIRE = 1800;
    const CACHE_VERSION_EXPIRE = 86400;

    /**
     * 根据不同分页数据参数获取数据
     * @param $params
     * @return array
     */
    public static function getSearchList($params) {
        return self::getSearchListData($params);
    }

    /**
     * 通过请求参数获取页面格式化数据的缓存结果
     * @param $params
     * @return array
     */
    public static function getPageCache($params){
        $cacheKey = self::getCacheKey($params);
        $cacheObj = self::getApcuCache();
        $cacheData = $cacheObj->get($cacheKey);
        if($cacheData == false){
            return array();
        }
        return $cacheData;
    }

    /**
     * 保存格式化后的页面数据到缓存
     * 注意 有差异的格式化结果的需要通过差异参数值来区分结果
     * @param $params
     * @param $cacheData
     */
    public static function savePageCache($params, $cacheData){
        $cacheKey = self::getCacheKey($params);
        $cacheObj = self::getApcuCache();
        $cacheObj->set($cacheKey, $cacheData, self::CACHE_PAGE_EXPIRE);
    }

    /**
     * @param $params
     * @return array
     */
    private static function getSearchListData($params) {
        if ($params[self::PARAMS_PAGE] < 1) $params[self::PARAMS_PAGE] = 1;
        switch ($params[self::PARAMS_PID]) {
            case self::SEARCH_ALLGAMES :
                return self::getAllGames($params);
                break;
            case self::SEARCH_NEWGAMES :
                return self::getNewGames($params);
                break;
            case self::SEARCH_GUESSGAMES :
                return self::getGuessGames($params);
                break;
            default:
                return self::getCategoryGames($params);
        }
    }

    /**
     * @param $params
     * @return array
     */
    private static function getAllGames($params) {
        $games = array();
        $total = 0;
        $searchParams = array('status' => 1);
        if($params[self::PARAMS_FILTER]){
            $searchParams['id'] = array('NOT IN', $params[self::PARAMS_FILTER]);
        }
        $orderParams = array('id'=>'DESC');
        if($params[self::PARAMS_SORTTYPE]){
            $orderParams = self::getOrderParams($params);
        }
        list($total, $games) = Resource_Service_Games::getList($params[self::PARAMS_PAGE], $params[self::PARAMS_PERPAGE], $searchParams, $orderParams);
        return array($total, $games);

    }

    /**
     * @param $params
     * @return array
     */
    private static function getNewGames($params) {
        $games = array();
        $total = 0;
        $searchParams = array('status' => 1);
        if($params[self::PARAMS_FILTER]){
            $searchParams['id'] = array('NOT IN', $params[self::PARAMS_FILTER]);
        }
        $orderParams = array('online_time'=>'DESC');
        if($params[self::PARAMS_SORTTYPE]){
            $orderParams = self::getOrderParams($params);
        }
        $limit = Game_Service_Config::getValue('game_rank_newnum');
        $perPage = min($limit, $params[self::PARAMS_PERPAGE]);
        list($total, $games) = Resource_Service_Games::getList($params[self::PARAMS_PAGE], $perPage, $searchParams, $orderParams);
        $total = min($total, $limit);
        return array($total, $games);
    }

    /**
     * @param $params
     * @return array
     */
    private static function getCategoryGames($params) {
        if($params[self::PARAMS_ID]){
            return self::getLessCategoryData($params);
        }else {
            return self::getMainCategoryData($params);
        }
    }

    /**
     * @param $params
     * @return array
     */
    private static function getGuessGames($params) {
        $games = array();
        $total = 0;
        $guessData = Client_Service_Guess::getGamesByImCrc($params[self::PARAMS_IMEICRC]);
        if ($guessData) {
            list($total, $games) = self::getGuessData($params, $guessData);
        } else {
            list($total, $games) = self::getDefaultData($params);
        }
        return array($total, $games);
    }

    /**
     * @param $params
     * @param $guessData
     * @return array
     */
    private static function getGuessData($params, $guessData) {
        $games = array();
        $total = 0;
        $searchParams = array('status' => 1);
        $orderParams = self::getOrderParams($params);
        $gameIds = explode(',', $guessData['game_ids']);
        $searchParams['id'] = array('IN', $gameIds);
        if ($params[self::PARAMS_FILTER]) {
            $searchParams['id'] = array(array('IN', $gameIds), array('NOT IN', $params[self::PARAMS_FILTER]));
        }
        list($total, $games) = Resource_Service_Games::getList($params[self::PARAMS_PAGE], $params[self::PARAMS_PERPAGE], $searchParams, $orderParams);
        return array($total, $games);
    }

    /**
     * @param $params
     * @return array
     */
    private static function getDefaultData($params) {
        $games = array();
        $total = 0;
        $orderParams = self::getOrderParams($params);
        $defaultParams = array('game_status' => 1, 'status' => 1);
        if ($params[self::PARAMS_FILTER]) {
            $defaultParams['game_id'] = array('NOT IN', $params[self::PARAMS_FILTER]);
        }
        list($total, $games) = Client_Service_Game::geGuesstList($params[self::PARAMS_PAGE], $params[self::PARAMS_PERPAGE], $defaultParams, $orderParams);
        return array($total, $games);
    }


    /**
     * @param $params
     * @return array
     */
    private static function getMainCategoryData($params) {
        $games = array();
        $total = 0;
        $searchParams=array(
            'parent_id' => $params[self::PARAMS_PID],
            'game_status' => 1,
            'status' => 1
        );
        if ($params[self::PARAMS_FILTER]) {
            $searchParams['game_id'] = array('NOT IN', $params[self::PARAMS_FILTER]);
        }
        $orderParams = array('sort'=>'DESC','game_id'=>'DESC');
        if ($params[self::PARAMS_SORTTYPE] == self::SORTTYPE_HOT) {
            $orderParams = array('downloads' => 'DESC', 'game_id' => 'DESC');
        }
        list($total, $games) = Resource_Service_GameCategory::getListByMainCategory($params[self::PARAMS_PAGE], $params[self::PARAMS_PERPAGE], $searchParams, $orderParams);
        return array($total, $games);
    }

    /**
     * @param $params
     * @return array
     */
    private static function getLessCategoryData($params) {
        $games = array();
        $total = 0;
        $searchParams = array(
            'category_id' => $params[self::PARAMS_ID],
            'parent_id' => $params[self::PARAMS_PID],
            'game_status' => 1,
            'status' => 1
        );

        if ($params[self::PARAMS_FILTER]) {
            $searchParams['game_id'] = array('NOT IN', $params[self::PARAMS_FILTER]);
        }
        $orderParams = array('sort'=>'DESC','game_id'=>'DESC');
        if ($params[self::PARAMS_SORTTYPE] == self::SORTTYPE_HOT){
            $orderParams = array('downloads' => 'DESC', 'game_id' => 'DESC');
        }
        list($total, $games) = Resource_Service_GameCategory::getList($params[self::PARAMS_PAGE], $params[self::PARAMS_PERPAGE], $searchParams, $orderParams);
        return array($total, $games);
    }

    /**
     * @param $params
     * @return array
     */
    private static function getOrderParams($params){
        $orderParams = array();
        //最新 按照上线时间倒序
        if($params[self::PARAMS_SORTTYPE] == self::SORTTYPE_NEW){
            $orderParams = array('online_time'=>'DESC');
        }
        //最热 按照下载总量倒序
        if($params[self::PARAMS_SORTTYPE] == self::SORTTYPE_HOT){
            $orderParams = array('downloads'=>'DESC','id'=>'DESC');
        }
        return $orderParams;
    }

    /**
     * 设置缓存版本值
     * @return boolean
     */
    public static function setCacheVersion(){
        $versionKey = self::getVersionKey();
        $cacheObj = self::getRedisCache();
        $cacheObj->set($versionKey, time(), self::CACHE_VERSION_EXPIRE);
        return true;
    }

    /**
     * 获取分类数据版本
     * @return string
     */
    private static function getVesionData(){
        $versionKey = self::getVersionKey();
        $cacheObj = self::getRedisCache();
        $cacheData = $cacheObj->get($versionKey);
        if($cacheData == false){
            $cacheData = time();
            $cacheObj->set($versionKey, $cacheData, self::CACHE_VERSION_EXPIRE);
        }
        return $cacheData;
    }

    /**
     * 获取分页分类缓存key
     * @param $params
     * @return string
     */
    private static function getCacheKey($params){
        ksort($params);
        $cacheStr =  md5(json_encode($params) .  self::getVesionData());
        $cacheKey = Util_CacheKey::CACHE_CATPAGE_PREFIX . dechex(crc32($cacheStr));
        return $cacheKey;
    }

    /**
     * 分类数据版本key
     * @return string
     */
    private static function getVersionKey(){
        $cacheKey = Util_CacheKey::CACHE_CATPAGE_VER;
        return  $cacheKey;
    }

    /**
     * 获取Redis缓存对象
     * @return object
     * @throws Exception
     */
    private static function getRedisCache(){
        return Cache_Factory::getCache();
    }

    /**
     * 获取Apcu缓存对象
     * @return object
     * @throws Exception
     */
    private static function getApcuCache(){
        return Cache_Factory::getCache(Cache_Factory::ID_LOCAL_APCU);
    }
}