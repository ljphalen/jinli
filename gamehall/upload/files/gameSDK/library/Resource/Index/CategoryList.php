<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 分类游戏列表索引数据处理文件
 * Class Resource_Index_CategoryList
 * 不包含猜你喜欢分类的索引数据
 * @author fanch
 */
class Resource_Index_CategoryList{

    //分类列表前缀
    const CATLIST_PREFIX = "catList";
    //每页数据
    const CATLIST_PAGE_SIZE = 10;
    //初始化每页数据
    const CATLIST_SCAN_PERPAGE = 100;
    //列表索引缓存时间
    const CATLIST_EXPIRE = 86400; //1天有效期

    //全部游戏|父类ID
    const CATLIST_GAMES_PARENT_ID = 100;
    //主分类默认|最新排序
    const CATLIST_PARENT_ORDERBY_DEFAULT='parentDefault';
    //主分类最热排序
    const CATLIST_PARENT_ORDERBY_HOT='parentHot';
    //子分类排序
    const CATLIST_SUB_ORDERBY = 'subDefault';
    //全部游戏|最新游戏-最新排序
    const CATLIST_GAMES_ORDERBY_NEW='gamesNew';
    //全部游戏|最新游戏-最热排序
    const CATLIST_GAMES_ORDERBY_HOT='gamesHot';

    /**
     * 初始化分类索引数据
     */
    public static function initCategoryIdx(){
        $category = self::getAllCategory();
        foreach($category as $item) {
            list($parentId, $categoryId) = $item;
            self::buildCategoryIdx($parentId, $categoryId);
        }
        self::buildAllGameIdx();
    }

    /**
     * 更新游戏分类索引
     * 1 游戏修改
     * 2 新增游戏
     * @param $gameId
     * @param $categoryData
     */
    public static function updateCategoryIdx($gameId, $categoryData) {
        self::removeCategoryIdxItem($gameId);
        foreach ($categoryData as $item) {
            self::buildCategoryIdx($item['parentId'], $item['categoryId']);
        }
        self::buildAllGameIdx();
    }

    /**
     * 删除游戏关联的分类索引数据
     * 1 游戏下线
     * @param $gameId
     */
    public static function removeCategoryIdxItem($gameId){
        $idxKeys = self::getCatIdxKeys($gameId);
        if($idxKeys) {
            foreach ($idxKeys as $item) {
                self::removeIdxContentItem($item, $gameId);
                self::saveCatIdxVersion($item);
            }
            self::delIdxKeysByGameId($gameId);
        }
    }

    /**
     * 更新游戏关联的索引Key对应版本
     * 1 礼包变更
     * 2 A券活动
     * 3 用户评分
     * @param $gameId
     */
    public static function updateCategoryIdxVersion($gameId){
        $idxKeys = self::getCatIdxKeys($gameId);
        if($idxKeys) {
            foreach ($idxKeys as $item) {
                self::saveCatIdxVersion($item);
            }
        }
    }


    /**
     * 构建全部游戏索引数据
     * 1 计划任务刷新游戏下载量
     *
     */
    public static function buildAllGameIdx() {
        $parentId= self::CATLIST_GAMES_PARENT_ID;
        $categoryId = 0;
        //最新排序
        $orderBy = self::getGameAllIdxOrderBy(self::CATLIST_GAMES_ORDERBY_NEW);
        $gameIds = self::getAllGameIds($orderBy);
        self::saveIdxData($parentId, $categoryId, self::CATLIST_GAMES_ORDERBY_NEW, $gameIds);
        //最热排序
        $orderBy = self::getGameAllIdxOrderBy(self::CATLIST_GAMES_ORDERBY_HOT);
        $gameIds = self::getAllGameIds($orderBy);
        self::saveIdxData($parentId, $categoryId, self::CATLIST_GAMES_ORDERBY_HOT, $gameIds);

    }

    /**
     * 获取索引key的数据版本
     * @param $idxKey
     * @return int
     */
    public static function getCatIdxVersion($idxKey){
        $cache = self::getCache();
        $data = $cache->hGet(Util_CacheKey::CATLIST_DATA_VER, $idxKey);
        $data = ($data) ? $data : 0;

        $allAcoupon = Resource_Service_GameListData::getGameAllAcoupon();
        $version = strval($data) . ':' . strval($allAcoupon);
        return $version ;
    }

    /**
     * 创建生成索引key
     * @param $categoryId
     * @param $parentId
     * @param $orderBy
     * @return string
     */
    public static function createCatIdxkey($parentId, $categoryId, $orderBy){
        $idxKey = sprintf("%s:%d_%d_%s",self::CATLIST_PREFIX, $parentId, $categoryId, $orderBy);
        return $idxKey;
    }

    /**
     * 分类索引数据创建
     * @param $parentId
     * @param $categoryId
     */
    public static function buildCategoryIdx($parentId, $categoryId){
        //主分类
        self::saveParentCatIdx($parentId);
        //子分类
        if($categoryId) {
            self::saveSubCatIdx($parentId, $categoryId);
        }
    }

    /**
     * 保存游戏分类的索引数据
     * @param $categoryId
     * @param $parentId
     * @param $orderBy
     * @param $idxData
     */
    private static function saveIdxData($parentId, $categoryId, $orderBy, $idxData){
        //根据索引条件创建索引key
        $idxKey = self::createCatIdxkey($parentId, $categoryId, $orderBy);
        //保存分类索引数据
        $result = self::saveIdxContent($idxKey, self::CATLIST_PAGE_SIZE, $idxData, self::CATLIST_EXPIRE);
        if(!$result){
            $message = "分类索引[{$parentId}-{$categoryId}]数据写入失败：" . json_encode(array('idxKey' => $idxKey, 'gameIds' => $idxData, 'orderBy' => $orderBy));
            Util_Log::debug('catListIndx', 'categoryidx.log', $message);
        }else {
            //保存key的数据版本
            self::saveCatIdxVersion($idxKey);
            //保存游戏id与key的对应关系
            self::saveIdxKeyToGames($idxData, $idxKey);
        }
    }

    /**
     * 保存idxKey数据版本
     * @param $idxKey
     */
    private static function saveCatIdxVersion($idxKey){
        $cache = self::getCache();
        $cache->hSet(Util_CacheKey::CATLIST_DATA_VER, $idxKey, time(), self::CATLIST_EXPIRE);
    }

    /**
     * 保存游戏id与索引key的对应关系
     * @param $idxData
     * @param $idxKey
     */
    private static function saveIdxKeyToGames($idxData, $idxKey){
        if(!is_array($idxData)){
            return false;
        }
        $cacheKey = Util_CacheKey::CATLIST_GAMEID_IDXKEYS;
        $cache = self::getCache();
        $saveData = array();
        foreach($idxData as $gameIds){
            foreach($gameIds as $gameId) {
                $item = self::getIdxDataByGameId($gameId, $idxKey);
                $saveData[strval($gameId)] = json_encode($item);
            }
        }
        return $cache->hMset($cacheKey, $saveData, self::CATLIST_EXPIRE);
    }

    /**
     * 获取所有游戏分类
     * @return array
     */
    private static function getAllCategory(){
        $parentCategoryType = 1;
        $subCategoryType = 10;
        $data =array();
        $params = array('id'=>array('NOT IN', array(100, 101)), 'at_type' => $parentCategoryType, 'status'=>1);
        $parentCategory = Resource_Service_Attribute::getsBy($params);
        foreach ($parentCategory as $item) {
            $data[] = array($item['id'], 0);
            $subCategory = Resource_Service_Attribute::getsBy(array('at_type' => $subCategoryType, 'parent_id' => $item['id'], 'status' => 1));
            foreach($subCategory as $subItem){
                $data[] = array($item['id'], $subItem['id']);
            }
        }
        return $data;
    }

    /**
     * 计算游戏的最新idx结果
     * @param $gameId
     * @param $idxKey
     * @return array|int
     */
    private static function getIdxDataByGameId($gameId, $idxKey){
        $cacheData = self::getCatIdxKeys($gameId);
        array_push($cacheData, $idxKey);
        $cacheData = array_unique($cacheData);
        return $cacheData;
    }


    /**
     * 删除游戏ID与idxKey的对应关系
     * @param $gameId
     * @return mixed
     */
    private static function delIdxKeysByGameId($gameId){
        $cacheKey = Util_CacheKey::CATLIST_GAMEID_IDXKEYS;
        $cache = self::getCache();
        return $cache->hDel($cacheKey, $gameId);
    }

    /**
     * 获取游戏ID与索引Key的对应关系
     * @param $gameId
     * @return array|mixed
     */
    private static function getCatIdxKeys($gameId){
        $cacheKey = Util_CacheKey::CATLIST_GAMEID_IDXKEYS;
        $cache = self::getCache();
        $cacheData = $cache->hGet($cacheKey, $gameId);
        return $cacheData ? json_decode($cacheData, true) : array();
    }

    /**
     * @param $categoryId
     * @param $parentId
     * @return array
     */
    private static function getCatIdxParams($parentId, $categoryId){
        $params = array('parent_id' => $parentId, 'game_status' => 1, 'status' => 1);
        if($categoryId){
            $params['category_id'] = $categoryId;
        }
        return $params;
    }

    /**
     * @param $orderBy
     * @return array
     */
    private static function getCatIdxOrderBY($orderBy){
        switch($orderBy){
            case self::CATLIST_PARENT_ORDERBY_DEFAULT:
            case self::CATLIST_SUB_ORDERBY:
                return array('sort' => 'DESC', 'game_id' => 'DESC');
                break;
            case self::CATLIST_PARENT_ORDERBY_HOT :
                return array('downloads' => 'DESC', 'game_id' => 'DESC');
                break;
        }
    }

    /**
     * @param $orderBy
     * @return array
     */
    private static function getGameAllIdxOrderBy($orderBy){
        switch($orderBy){
            case self::CATLIST_GAMES_ORDERBY_NEW:
                 return array('online_time'=>'DESC');
                break;
            case self::CATLIST_GAMES_ORDERBY_HOT:
                return array('downloads' => 'DESC', 'id' => 'DESC');
                break;
        }
    }

    /**
     * 保存主分类的索引数据
     * @param $categoryId
     * @param $parentId
     * @return array
     */
    private static function saveParentCatIdx($parentId) {
        $categoryId = 0;
        //主分类搜索
        $params = self::getCatIdxParams($parentId, $categoryId);
        //默认|最新
        $defultOrderBy = self::getCatIdxOrderBY(self::CATLIST_PARENT_ORDERBY_DEFAULT);
        $gameIds = self::getParentCatGameIds($params, $defultOrderBy);
        self::saveIdxData($parentId, $categoryId, self::CATLIST_PARENT_ORDERBY_DEFAULT, $gameIds);
        //最热排序
        $hotOrderBy = self::getCatIdxOrderBY(self::CATLIST_PARENT_ORDERBY_HOT);
        $gameIds = self::getParentCatGameIds($params, $hotOrderBy);
        self::saveIdxData($parentId, $categoryId, self::CATLIST_PARENT_ORDERBY_HOT, $gameIds);
    }

    /**
     * 保存子类的索引
     * @param $categoryId
     * @param $parentId
     */
    private static function saveSubCatIdx($parentId, $categoryId) {
        //存储子类索引
        $params = self::getCatIdxParams($parentId, $categoryId);
        $orderBy = self::getCatIdxOrderBY(self::CATLIST_SUB_ORDERBY);
        $gameIds = self::getSubCategoryGameIds($params, $orderBy);
        self::saveIdxData($parentId, $categoryId, self::CATLIST_SUB_ORDERBY, $gameIds);
    }

    /**
     * @param $orderBy
     */
    private static function getAllGameIds($orderBy){
        $data = array();
        $params = array('status'=>1);
        $page = 1;
        $perPage = self::CATLIST_SCAN_PERPAGE;
        do {
            //只扫上线的游戏
            list($total, $games) = Resource_Service_Games::getList($page, $perPage, $params, $orderBy);
            if(empty($games)){
                break;
            }
            $games = Common::resetKey($games, 'id');
            $gameIds = array_keys($games);
            $data[] = array_unique($gameIds);
            $page++;
        } while ($total>(($page - 1) * $perPage));
        return $data;
    }

    /**
     * 获取主分类的游戏索引
     * @param $params
     * @param $orderBy
     * @return array
     */
    private static function getParentCatGameIds($params, $orderBy) {
        $data = array();
        $page = 1;
        $perPage = self::CATLIST_SCAN_PERPAGE;
        do {
            list($total, $games) = Resource_Service_GameCategory::getListByMainCategory($page, $perPage, $params, $orderBy);
            if(empty($games)){
                break;
            }
            $games = Common::resetKey($games, 'game_id');
            $gameIds = array_keys($games);
            $data[] = array_unique($gameIds);
            $page++;
        } while ($total>(($page -1) * $perPage));
        return $data;
    }

    /**
     * 获取子分类的游戏索引
     * @param $params
     * @param $orderBy
     * @return array
     */
    private static function getSubCategoryGameIds($params, $orderBy) {
        $data = array();
        $page = 1;
        $perPage = self::CATLIST_SCAN_PERPAGE;
        do {
            //只扫上线的游戏
            list($total, $games) = Resource_Service_GameCategory::getList($page, $perPage, $params, $orderBy);
            if(empty($games)){
                break;
            }
            $games = Common::resetKey($games, 'game_id');
            $gameIds = array_keys($games);
            $data[] = array_unique($gameIds);
            $page++;
        } while ($total>(($page -1) * $perPage));
        return $data;
    }

    /**
     * @param $idxKey
     * @param $pageSize
     * @param $indexData
     * @param int $expire
     * @return bool
     */
    private static function saveIdxContent($idxKey, $pageSize, $indexData, $expire){
        $obj = Resource_Service_GameListData::getIndexInstance($idxKey);
        $begin = $obj->buildIndexBegin($pageSize);
        if(!$begin){
            return false;
        }
        foreach($indexData as $item) {
            $result = $obj->buildListIndex($item, $expire);
            if ($result == false) {
                return false;
            }
        }
        $finish = $obj->buildIndexFinish();
        if (!$finish) {
            return false;
        }
        return true;

    }

    /**
     * 删除索引中的指定索引数据
     * @param $idxKey
     * @param $idxId
     * @return bool
     */
    private static function removeIdxContentItem($idxKey, $idxId){
        $obj = Resource_Service_GameListData::getIndexInstance($idxKey);
        return $obj->removeFromIndex($idxId);
    }

    /**
     * @return object
     * @throws Exception
     */
    private function getCache() {
        $redis = Cache_Factory::getCache(Cache_Factory::ID_REMOTE_REDIS);
        return $redis;
    }
}