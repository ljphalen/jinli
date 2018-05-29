<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 网游推荐
 * Game_Cache_GameWebRec
 * @author wupeng
 */
class Game_Cache_GameWebRec extends Cache_Base {
	public $expire = 86400;
	private $dataList;
	private $controll;
	private $api;
	private $cacheObject = array();
	
	public function __construct() {
	    $this->api = Util_CacheKey::getApi(Util_CacheKey::WEBGAME, Util_CacheKey::WEBGAME_LIST);
	    $dataListKey = Util_CacheKey::getKey($this->api, array('data'));
	    $controllKey = Util_CacheKey::getKey($this->api, array('controll'));
	    $rowKey = 'id';
	    $this->dataList = new Util_Api_ListContent($dataListKey, $rowKey);
	    $this->controll = new Util_Api_ListControll($controllKey);
	}
	
	public function updateClientPageList($keyParams, $pageData, $pageList) {
	    $clientPageList = $this->getListPage($keyParams);
	    $clientDataList = $this->getListContent($keyParams);
	    $clientPageList->storeListContent($pageList);
	    $clientDataList->storeListContent($pageData);
	    if(! $pageList) {
	        $flag = ! $this->controll->itemKeyExists($keyParams);
	    }else{
	        $flag = true;
	    }
	    if($flag) {
	        $this->controll->storeListItem($keyParams);
	    }
	}
	
	public function getClientPageList($page, $keyParams) {
	    if($page <=0) return array();
	    $clientPageList = $this->getListPage($keyParams);
	    $idList = $clientPageList->getContent($page);
	    if(! $idList) {
	        return $idList;
	    }
	    $clientDataList = $this->getListContent($keyParams);
	    $dataList = $clientDataList->getContent($idList);
	    return $dataList;
	}
	
	public function getClientPageInfo($keyParams) {
	    $flg = $this->controll->itemKeyExists($keyParams);
	    if(! $flg) {
	        return false;
	    }
	    $clientPageList = $this->getListPage($keyParams);
	    return $clientPageList->getPageInfo();
	}
	
	public function updateDataList($data) {
	    $indexList = $this->dataList->storeListContent($data);
	}
	
	public function getDataList() {
	    return $this->dataList->getContentList();
	}
	
	public function getData($id) {
	    return $this->dataList->getContent($id);
	}
	
	public function getValidListArgs() {
	    return $this->controll->getValidList();
	}
	
	private function getListPage($keyParams) {
	    array_unshift($keyParams, 'page');
	    $pageListKey = Util_CacheKey::getKey($this->api, $keyParams);
	    $clientPageList = $this->cacheObject[$pageListKey];
	    if(! $clientPageList) {
	        $clientPageList = new Util_Api_ListPage($pageListKey, 0);
	        $this->cacheObject[$pageListKey] = $clientPageList;
	    }
	    return $clientPageList;
	}
	
	private function getListContent($keyParams) {
	    array_unshift($keyParams, 'client');
	    $pageListKey = Util_CacheKey::getKey($this->api, $keyParams);
	    $clientPageList = $this->cacheObject[$pageListKey];
	    if(! $clientPageList) {
	        $clientPageList = new Util_Api_ListContent($pageListKey);
	        $this->cacheObject[$pageListKey] = $clientPageList;
	    }
	    return $clientPageList;
	}
	
	private static $instance;
	public static function getInstance() {
	    if(! self::$instance) {
	        self::$instance = new Game_Cache_GameWebRec();
	    }
	    return self::$instance;
	}
	
}
