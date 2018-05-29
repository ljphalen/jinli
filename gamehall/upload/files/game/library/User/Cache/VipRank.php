<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * vip排行榜
 * User_Cache_VipRank
 * @author wupeng
 */
class User_Cache_VipRank {
    private $clientPageList;
    private $clientDataList;
    
    public function __construct() {
        $api = Util_CacheKey::getApi(Util_CacheKey::VIPCENTER, Util_CacheKey::VIPCENTER_RANK);
        $pageListKey = Util_CacheKey::getKey($api, array('page'));
        $clientListKey = Util_CacheKey::getKey($api, array('client'));
        $rowKey = 'uuid';
        $this->clientPageList = new Util_Api_ListPage($pageListKey);
        $this->clientDataList = new Util_Api_ListContent($clientListKey, $rowKey);
    }
    
    public function updateClientPageList($data) {
        $this->clientDataList->storeListContent($data);
        $pageList = array_keys($data);
        $this->clientPageList->storeListContent($pageList);
    }
    
    public function getClientPageList($page) {
        $result = array();
        if($page <=0) return $result;
        $idList = $this->clientPageList->getContent($page);
        if(! $idList) {
            return $result;
        }
        $result = $this->clientDataList->getContent($idList);
        return $result;
    }
	
	public function getClientPageInfo() {
	    return $this->clientPageList->getPageInfo();
	}
    
    private static $instance;
    public static function getInstance() {
        if(! self::$instance) {
            self::$instance = new User_Cache_VipRank();
        }
        return self::$instance;
    }
}
