<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Local_ClientrankController extends Api_BaseController{

    const APCU_CACHE_EXPIRE = 1800; //30分钟
    
	public $perpage = 10;	
	/**
	 * 本地化周榜（原来老版的下载最多）
	 */
	public function clientWeekIndexAction() {
		$data = $this->getRankPageData(Resource_Index_RankList::RANK_TYPE_WEEK);
		$this->localOutput('','',$data);
	}
	
	/**
	 * 本地化月榜
	 */
	public function clientMonthIndexAction() {
		$data = $this->getRankPageData(Resource_Index_RankList::RANK_TYPE_MONTH);
		$this->localOutput('','',$data);
	}
	
	/**
	 * 客户端的新游
	 */
	public function newRankIndexAction() {
		$data = $this->getRankPageData(Resource_Index_RankList::RANK_TYPE_NEW);
		$this->localOutput('','',$data);
	}
	
	/**
	 * 客户端的上升最快
	 */
	public function upRankIndexAction() {
		$data = $this->getRankPageData(Resource_Index_RankList::RANK_TYPE_UP);
		$this->localOutput('','',$data);
	}
	

	/**
	 * 客户端的网游排行
	 */
	public function onlineRankIndexAction() {
		$data = $this->getRankPageData(Resource_Index_RankList::RANK_TYPE_ONLINE);
		$this->localOutput('','',$data);
	}
	
	/**
	 * 客户端的单机排行
	 */
	public function pcRankIndexAction() {
		$data = $this->getRankPageData(Resource_Index_RankList::RANK_TYPE_PC);
		$this->localOutput('','',$data);
	}
	
	/**
	 * 客户端的网游活跃榜
	 */
	public function olactiveRankIndexAction() {
		$data = $this->getRankPageData(Resource_Index_RankList::RANK_TYPE_OLACTIVE);
		$this->localOutput('','',$data);
	}
	
	/**
	 * 客户端的游戏飙升榜
	 */
	public function soaringRankIndexAction() {
		$data = $this->getRankPageData(Resource_Index_RankList::RANK_TYPE_SOARING);
		$this->localOutput('','',$data);
	}
	
	private function getRankPageData($rankType) {
	    $page = intval($this->getInput('page'));
	    if ($page < 1) $page = 1;
	    $idxKey = Resource_Index_RankList::getIdxKey($rankType);
	    $cache = Cache_Factory::getCache(Cache_Factory::ID_LOCAL_APCU);
	    $cacheKey = $idxKey.'_'.$page;
	    $cacheData = $cache->get($cacheKey);
	    $dataVersion = Resource_Index_RankList::getIdxVersion($idxKey);
	    if(!$cacheData || $cacheData['version'] != $dataVersion) {
	        list($pageData, $hasNext, $total) = Resource_Service_GameListData::getPage($idxKey, $page);
	        if (!$pageData) {
	        	return array();
	        }
	        $data = Resource_Service_GameListFormat::output($pageData);
	        $cacheData = array();
	        $cacheData['outData']['list'] = $data;
	        $cacheData['outData']['hasnext'] = $hasNext;
	        $cacheData['outData']['totalCount'] = $total;
	        $cacheData['outData']['curpage'] = $page;
	        $cacheData['version'] = $dataVersion;
	        $cache->set($cacheKey, $cacheData, self::APCU_CACHE_EXPIRE);
	    }
	    return $cacheData['outData'];
	}
}
