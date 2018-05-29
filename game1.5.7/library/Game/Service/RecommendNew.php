<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Game_Service_RecommendNew
 * @author wupeng
 */
class Game_Service_RecommendNew{
	
	const SHOW_NUM = 8;
	const CACHE_EXPIRE =600;
	const GAME_OPEN_STATUS = 1;
	const GAME_CLOSE_STATUS = 0;
	const RECOMMEND_OPEN_STATUS = 1;
	const RECOMMEND_CLOSE_STATUS = 0;
	const RECOMMEND_INVALID_STATUS = -1;

	const PER_PAGE = 10;
	
	const  LESS_SYSTEM_VERSION = 'Android4.1.0';
	const  GREATE_SYSTEM_VERSION = 'Android4.2.0';
	
	const  CLIENT_VERSION_156 = '1.5.6';
	const  CLIENT_VERSION_157 = '1.5.7';
	
	
    /**关闭1.5.7版本推荐标识key*/
    const CONFIG_KEY = "closed_1.5.7_recommend";
    
    /**是否使用1.5.7版本推荐*/
    public static function closed_1_5_7_recommend() {
	    $status = Game_Service_Config::getValue(Game_Service_RecommendNew::CONFIG_KEY);
        return $status ? true : false;
    }
    
	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllRecommendnew() {
		return array(self::getDao()->count(), self::getDao()->getAll());
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $sort = array()) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList($start, $limit, $params, $sort);
		$total = self::getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getRecommendList($page = 1, $limit = 10, $params = array(), $sort = array()) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList($start, $limit, $params, $sort);
		$total = self::getDao()->count($params);
		return array($total, $ret);
	}

	//======================================start 客户端api调用
	public static function getRecommendListCacheData($page, $device, $clientVersion, $systemVersion){
		if(!$page || !$device){
			return false;
		}
		if(Common::isAfterVersion($clientVersion, self::CLIENT_VERSION_157)) {
		    $clientVersion = self::CLIENT_VERSION_157;
		}else{
		    $clientVersion = self::CLIENT_VERSION_156;
		}
		
		$systemVersionResult = Common::compareSysytemVersion($systemVersion);
		if($systemVersionResult){
			$systemVersion = self::GREATE_SYSTEM_VERSION;
		}else{
			$systemVersion = self::LESS_SYSTEM_VERSION;
		}
		
		$modeGroup = Resource_Service_Pgroup::getModeGroupCacheData();
		$mode = self::getModeGroupByMode($modeGroup, $device);

		return self::getRecommendListPageDataByDevice($page, $mode, $clientVersion, $systemVersion);
	}
	
	public function getRecommendListPageDataByDevice($page, $mode, $clientVersion, $systemVersion) {
	    $api = Util_CacheKey::getApi(Util_CacheKey::HOME, Util_CacheKey::HOME_RECOMMEND_LIST);
	    $data = Util_CacheKey::getCache($api, func_get_args());
	    if($data === false) {
	        $data = self::getRecommendListByHour($page, $mode, $clientVersion);
	        Util_CacheKey::updateCache($api, func_get_args(), $data);
	    }
		return $data;
	}
    
    /**
     * 后台有数据修改后主动更新客户端缓存
     */
    public static function updateRecomendListCacheData(){
        $maxPage = self::getClientRecommendPageSize();
        $clientVersionList = array(self::CLIENT_VERSION_157, self::CLIENT_VERSION_156);
        $systemVersionList = array(self::GREATE_SYSTEM_VERSION,self::LESS_SYSTEM_VERSION);
        $modeGroup = Resource_Service_Pgroup::getModeGroupCacheData();
        foreach ($modeGroup as $ke => $mode) {
            for ($page = 1; $page <= $maxPage; $page ++) {
                foreach ($clientVersionList as $clientVersion) {
                	foreach ($systemVersionList as $systemVersion){
                	    Yaf_Registry::set("androidVersion", $systemVersion);
                		self::updateRecommendListPageDataByDevice($page, $mode['id'], $clientVersion, $systemVersion);
                	}
                }
            }
        }
        
        if($maxPage == 0) {
            self::deleteRecomendListCacheData($modeGroup, $clientVersionList, $systemVersionList);
        }
    }
    
    private static function getClientRecommendPageSize() {
        $params['status'] = self::RECOMMEND_OPEN_STATUS;
        $startTime = strtotime(date('Y-m-d H:00:00'));
        $endTime = strtotime(date('Y-m-d H:00:00', strtotime("+1 hours")));
        $params['start_time'] = array('<=', $startTime);
        $params['end_time'] = array('>=', $endTime);
        
        $result = self::getsBy($params);
        $num = count($result);
        $maxPage = ceil($num /self::PER_PAGE);
        return $maxPage;
    }
    
    
    /**
     * 清除缓存
     * @param unknown $modeGroup
     * @param unknown $clientVersionList
     * @param unknown $systemVersionList
     */
    private static function deleteRecomendListCacheData($modeGroup, $clientVersionList, $systemVersionList){
        $maxPage = 10;
        foreach ($modeGroup as $ke => $mode) {
            for ($page = 1; $page <= $maxPage; $page ++) {
                foreach ($clientVersionList as $clientVersion) {
                    foreach ($systemVersionList as $systemVersion){
                        self::updateRecommendListPageDataByDevice($page, $mode['id'], $clientVersion, $systemVersion);
                    }
                }
            }
        }
    }
    
    
    /**
     * 前端请求游戏附加属性的同时检查首页缓存数据是否需要更新
     * @param unknown $newExtrainfo
     */
    public static function checkGameExtrainfo($newExtrainfo) {
        $newData = array();
        foreach ($newExtrainfo as $extra) {
            $newData[$extra['gameId']] = $extra;
        }
        if (! $newData) return;
        $modeGroup = Resource_Service_Pgroup::getModeGroupCacheData();
        $maxPage = self::getClientRecommendPageSize();
        $api = Util_CacheKey::getApi(Util_CacheKey::HOME, Util_CacheKey::HOME_RECOMMEND_LIST);
        foreach ($modeGroup as $ke => $mode) {
            for ($page = 1; $page <= $maxPage; $page ++) {
                foreach ($clientVersionList as $clientVersion) {
                    foreach ($systemVersionList as $systemVersion){
                        $data = self::getRecommendListPageDataByDevice($page, $mode['id'], $clientVersion, $systemVersion);
                        if (! $data['list']) continue;
                        $updateFlg = false;
                        foreach ($data['list'] as $key => $value) {
                            $gameList = $value['gameItems'];
                            if (! $gameList) continue;
                            foreach ($gameList as $gk => $gv) {
                                $extalInfo = $newData[$gv['gameId']];
                                if (! $extalInfo) continue;
                                if ($gv['score'] != $extalInfo['score'] || $gv['attach'] != $extalInfo['attach'] ||
                                    $gv['freedl'] != $extalInfo['freedl'] || $gv['reward'] != $extalInfo['reward']) {
                                    $updateFlg = true;
                                    $gv['attach'] = $extalInfo['attach'];
                                    $gv['freedl'] = $extalInfo['freedl'];
                                    $gv['reward'] = $extalInfo['reward'];
                                    $gv['score'] = $extalInfo['score'];
                                    $data['list'][$key]['gameItems'][$gk] = $gv;
                                }
                            }
                        }
                        if ($updateFlg) {
                            $args = array($page, $mode['id'], $clientVersion, $systemVersion);
                            Util_CacheKey::updateCache($api, $args, $data);
                        }
                    }
                }
            }
        }
    }
	
	private function updateRecommendListPageDataByDevice($page, $mode, $clientVersion, $systemVersion) {
	    $api = Util_CacheKey::getApi(Util_CacheKey::HOME, Util_CacheKey::HOME_RECOMMEND_LIST);
	    $data = Util_CacheKey::getCache($api, func_get_args());
        $data = self::getRecommendListByHour($page, $mode, $clientVersion, $systemVersion);
        Util_CacheKey::updateCache($api, func_get_args(), $data);
	}
	
	private function getRecommendListByHour($page, $mode, $clientVersion) {
	    //推荐专题
	    if ($page < 1) $page = 1;
	    $status = Game_Service_Config::getValue('client_picture_status');
	    
	    if($clientVersion == self::CLIENT_VERSION_157) {
	        $interval = Game_Service_Config::getValue('client_picture_space157');
	        if (! $interval)
                $interval = Game_Service_Config::getValue('client_picture_space');
	    }else{
	        $interval = Game_Service_Config::getValue('client_picture_space');
	    }
	    
	    $params['pgroup'] = array('in', array($mode, 0));
	    $params['status'] = self::RECOMMEND_OPEN_STATUS;

	    $startTime = strtotime(date('Y-m-d H:00:00'));
	    $endTime = strtotime(date('Y-m-d H:00:00', strtotime("+1 hours")));
	    $params['start_time'] = array('<=', $startTime);
	    $params['end_time'] = array('>=', $endTime);

	    $orderBy = array('sort'=>'desc', 'id'=>'desc');
	    list($total,  $recommendListResult) = self::getRecommendList($page, self::PER_PAGE, $params, $orderBy);
	    $recommendListData = self::formatRecommendList($recommendListResult);
	    $hasnext = ceil($total / self::PER_PAGE) > $page ? true : false;
	    //插图广告开关
	    if($status){
	        //获取插图广告
	        $recAds = Client_Service_InsertAdData::getInsertPicAd();
	        //当前页面广告位置
	        list($adUsedCount, $previousPageGamesAfterLastAd) = Client_Service_InsertAdData::getAdPos($page,  $interval);
	        //当前页面的广告
	        $adsUnused = Client_Service_InsertAdData::getCurrpageAd($adUsedCount, $recAds, $interval);
	        //插入当前数据
	        $insertData['subjectGames'] = $recommendListData;
	        $insertData['adUnusedArr'] = $adsUnused;
	        $insertData['interval'] = $interval;
	        $insertData['previousPageGamesAfterLastAd'] = $previousPageGamesAfterLastAd;
	        $pageData = Client_Service_InsertAdData::insertAdData($page, $insertData);
	    }
	    $data = array('hasnext'=>$hasnext, 'curpage'=>$page);
	    $data['list']  = $status ? $pageData : $recommendListData;
	    return $data;
	}
	
	private function formatRecommendList($recommendListResult) {
    	if(!is_array($recommendListResult)) {
    		return false;
    	}
    	$temp = array();
    	$webRoot = Common::getWebRoot().'/Api/Local_Home/recommendGameList?recommendId=';
    	foreach ($recommendListResult as $key => $val) {
    		$temp[$key]['listItemType'] = 'ChannelBanner';
    		$temp[$key]['title'] = html_entity_decode($val['title'], ENT_QUOTES);
    		$temp[$key]['viewType'] = 'ListView';
    		$temp[$key]['param']['url'] = $webRoot;
    		$temp[$key]['param']['contentId'] = $val['id'];
    		$temp[$key]['param']['gameId'] = '';
    		$temp[$key]['param']['title'] = html_entity_decode($val['title'], ENT_QUOTES);
    		
    		$gameParams['recommend_id'] = $val['id'] ;
    		$gameParams['game_status'] = self::GAME_OPEN_STATUS ;
    		list($gameTotal, $gameList) = Game_Service_RecommendGames::getRecommendGameList(1, Game_Service_RecommendNew::SHOW_NUM, $gameParams);
            $gameInfoListData =  self::fillRecommendListGameInfoData($gameList);
    		$temp[$key]['gameItems'] = $gameInfoListData;
    		$temp[$key]['total'] = $gameTotal;
    	}
    	return $temp;
    }
    
    private function fillRecommendListGameInfoData($gameList){
    	if(!is_array($gameList)){
    		return false;
    	}
    	foreach ($gameList as $ke=>$va){
    		$gameInfo = Resource_Service_GameData::getBasicInfo($va['game_id']);
    		$extalInfo = Resource_Service_GameData::getExtraInfo($va['game_id']);
    		if($gameInfo){
    			$gameData[] = array(
    					'viewType'=>'GameDetailView',
    					'ad_id'=>$va['recommend_id'],
    					'gameId'=>$va['game_id'],
    					'img'=>$gameInfo['img'],
    					'name'=>html_entity_decode($gameInfo['name'], ENT_QUOTES),
    					'size'=>$gameInfo['size'].'M',
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
    	return $gameData;
    }
    
    private function getModeGroupByMode($modeGroup, $device){
    	if(!$device){
    		return false;
    	}
    	if(!is_array($modeGroup)){
    		return false;
    	}
    	 
    	$findFlag = 0;
    	foreach ($modeGroup as $key=>$val){
    		$deviceArr = explode(',', $val['p_title']) ;
    		if(is_array($deviceArr) && in_array($device, $deviceArr)){
    			$mode = $val['id'];
    			$findFlag = 1;
    			break;
    		}
    	}
    	if($findFlag == 0){
    		$mode = 1;
    	}
    	return $mode;
    }
    
    /**
     * 推荐列表查看更多游戏
     * @param unknown $recommendId
     * @param unknown $page
     * @param unknown $device
     */
    public static function getClientRecommendGamesList($recommendId, $page) {
        $params['id'] = $recommendId;
        $params['status'] = self::RECOMMEND_OPEN_STATUS;
        
        $startTime = strtotime(date('Y-m-d H:00:00'));
        $endTime = strtotime(date('Y-m-d H:00:00', strtotime("+1 hours")));
        $params['start_time'] = array('<=', $startTime);
        $params['end_time'] = array('>=', $endTime);
        
        $recommendListResult = self::getBy($params);
        
        if ($page < 1) $page = 1;
        $totalCount = 0;
        $hasnext = false;
        $pageData = array();
        if($recommendListResult) {
            $gameParams['recommend_id'] = $recommendId;
            $gameParams['game_status'] = self::GAME_OPEN_STATUS ;
        
            list($total, $gameList) = Game_Service_RecommendGames::getRecommendGameList($page, self::PER_PAGE, $gameParams);
            $pageData =  self::fillRecommendListGameInfoData($gameList);
            $totalCount = ceil($total / self::PER_PAGE);
            $hasnext = $totalCount > $page ? true : false;
        }
        $data = array('list'=>$pageData, 'hasnext'=>$hasnext, 'curpage'=>$page, 'totalCount'=>$totalCount);
        return $data;
    }
    
    //======================================end 客户端api调用
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getRecommendnew($id) {
		if (!intval($id)) return false;
		return self::getDao()->get(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getBy($params, $orderBy = array('id'=>'ASC')) {
		if (!is_array($params)) return false;
		return self::getDao()->getBy($params, $orderBy);
	}
	

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getsBy($params, $orderBy = array('id'=>'ASC')) {
		if (!is_array($params)) return false;
		return self::getDao()->getsBy($params,$orderBy);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateRecommendnew($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteRecommendnew($id) {
	    return self::deleteRecommendnewList(array($id));
	}
	
	/**
	 * 
	 * @param unknown $params
	 * @return boolean|Ambigous <boolean, unknown, number>
	 */
	public static function deleteRecommendnewList($idList) {
		if (!is_array($idList)) return false;
        Common_Service_Base::beginTransaction();
        $ret = Game_Service_RecommendGames::deleteRecommendGames($idList);
		if (!$ret) {
		    Common_Service_Base::rollBack();
		    return false;
		}
		$ret = self::getDao()->deletes("id", $idList);
		if (!$ret) {
		    Common_Service_Base::rollBack();
		    return false;
		}
		Common_Service_Base::commit();
		return true;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addRecommendnew($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$ret = self::getDao()->insert($data);
		if($ret) {
		        return self::getDao()->getLastInsertId();
		}
		return false;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateListSort($idList, $sortList) {
	    if (!is_array($idList) || !is_array($sortList)) return false;
	    Common_Service_Base::beginTransaction();
	    foreach ($idList as $id) {
	        $ret = self::getDao()->update(array('sort' => $sortList[$id]), $id);
	        if (!$ret) {
	            Common_Service_Base::rollBack();
	            return false;
	        }
	    }
	    Common_Service_Base::commit();
	    return true;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['content'])) $tmp['content'] = $data['content'];
		if(isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
		if(isset($data['end_time'])) $tmp['end_time'] = $data['end_time'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['sort'])) $tmp['sort'] = $data['sort'];
		if(isset($data['pgroup'])) $tmp['pgroup'] = $data['pgroup'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		return $tmp;
	}
    
	/**
	 * 
	 * @return Game_Dao_RecommendNew
	 */
	private static function getDao() {
		return Common::getDao("Game_Dao_RecommendNew");
	}
	
}
