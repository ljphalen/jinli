<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 开服列表
 * Game_Cache_GameOpen
 * @author wupeng
 */
class Game_Cache_GameOpen extends Cache_Base {
	public $expire = 86400;
	private $clientPageList;
	private $clientDataList;
	private $dataList;
	private $gameOpenList;
	
	public function __construct() {
	    $api = Util_CacheKey::getApi(Util_CacheKey::WEBGAME, Util_CacheKey::WEBGAME_OPENLIST);
	    $pageListKey = Util_CacheKey::getKey($api, array('page'));
	    $dataListKey = Util_CacheKey::getKey($api, array('data'));
	    $clientListKey = Util_CacheKey::getKey($api, array('client'));
	    $gameListKey = Util_CacheKey::getKey($api, array('game'));
	    $rowKey = 'id';
	    $this->clientPageList = new Util_Api_ListPage($pageListKey);
	    $this->clientDataList = new Util_Api_ListContent($clientListKey);
	    $this->dataList = new Util_Api_ListContent($dataListKey, $rowKey);
	    $this->gameOpenList = new Util_Api_ListContent($gameListKey);
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
	
	public function getOpenInfo($openId) {
	    return $this->dataList->getContent($openId);
	}
	
	public function getClientPageInfo() {
	    return $this->clientPageList->getPageInfo();
	}
	
	public function updateDataList($data) {
	    $indexList = $this->dataList->storeListContent($data);
	}
	
	public function updateGameOpenList($data) {
	    $indexList = $this->gameOpenList->storeListContent($data);
	}
	
	public function getGameOpenList($gameId) {
	    return $this->gameOpenList->getContent($gameId);
	}
	
	public function getDataList() {
	    return $this->dataList->getContentList();
	}
	
	private static $instance;
	public static function getInstance() {
	    if(! self::$instance) {
	        self::$instance = new Game_Cache_GameOpen();
	    }
	    return self::$instance;
	}
	
}
