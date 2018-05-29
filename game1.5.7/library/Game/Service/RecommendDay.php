<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Game_Service_RecommendDay
 * @author wupeng
 */
class Game_Service_RecommendDay{
	
	
	const STATUS_OPEN = 1;         //开启状态
	const STATUS_CLOSE = 0;        //关闭状态
	
	const CACHE_EXPIRE = 3600;

	const SHOW_NUM = 4;

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllRecommendDay() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
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
		$ret = self::_getDao()->getList($start, $limit, $params, $sort);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getRecommendDay($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
    
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateRecommendDay($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteRecommendDay($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * @param unknown $params
	 * @return boolean|Ambigous <boolean, unknown, number>
	 */
	public static function deleteRecommendDayList($idList) {
		if (!is_array($idList)) return false;
		return self::_getDao()->deletes("id", $idList);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addRecommendDay($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateListSort($idList, $sortList) {
        if (!is_array($idList) || !is_array($sortList)) return false;
        foreach ($idList as $id) {
           self::_getDao()->update(array('sort' => $sortList[$id]), $id);
        }
        return true;
	}
	
	/**================start客户端api*/
	/**
	 * 获取首页每日一荐缓存数据
	 */
	public static function getIndexRecommendCacheData($systemVersion){
		$api = Util_CacheKey::getApi(Util_CacheKey::HOME, Util_CacheKey::HOME_DAILY_RECOMMEND);
	    $data = Util_CacheKey::getCache($api, func_get_args());
	    if($data === false) {
	        $data = self::getRecommendDataByHour();
	        Util_CacheKey::updateCache($api, func_get_args(), $data);
	    }
	    return $data;
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
		$api = Util_CacheKey::getApi(Util_CacheKey::HOME, Util_CacheKey::HOME_DAILY_RECOMMEND);

        $oldSystemVersion = Yaf_Registry::get("androidVersion");
		$systemVersionList = array(Game_Service_RecommendNew::GREATE_SYSTEM_VERSION, Game_Service_RecommendNew::LESS_SYSTEM_VERSION);
		foreach ($systemVersionList as $systemVersion) {
            Yaf_Registry::set("androidVersion", $systemVersion);
            $data = self::getIndexRecommendCacheData($systemVersion);
            if (! $data || ! isset($data[0])) continue;
            $gv = $data[0];
            $updateFlg = false;
            $extalInfo = $newData[$gv['gameId']];
            if (! $extalInfo) return;
            if ($gv['score'] != $extalInfo['score'] || $gv['attach'] != $extalInfo['attach'] ||
                $gv['freedl'] != $extalInfo['freedl'] || $gv['reward'] != $extalInfo['reward']) {
                    $updateFlg = true;
                    $gv['attach'] = $extalInfo['attach'];
                    $gv['freedl'] = $extalInfo['freedl'];
                    $gv['reward'] = $extalInfo['reward'];
                    $gv['score'] = $extalInfo['score'];
            }
            if ($updateFlg) {
                $data[0] = $gv;
                $args = array($systemVersion);
                Util_CacheKey::updateCache($api, $args, $data);
            }
		}
        Yaf_Registry::set("androidVersion", $oldSystemVersion);
	}
	
	private static function getRecommendByHour() {
	    $startTime = strtotime(date('Y-m-d H:00:00'));
	    $endTime = strtotime(date('Y-m-d H:00:00', strtotime("+1 hours")));
	    $params['start_time'] = array('<=', $startTime);
	    $params['end_time'] = array('>=', $endTime);
	    $params['status'] = Game_Service_RecommendDay::STATUS_OPEN;
	    $params['game_status']=Resource_Service_Games::STATE_ONLINE;
	     
	    $orderBy = array('sort'=>'desc', 'id'=>'desc');
	    $recommend = self::_getDao()->getBy($params, $orderBy);
	    return $recommend;
	}
	
	private static function getRecommendDataByHour() {
	    $data = array();
	    $val = self::getRecommendByHour();
	    if($val) {
	        $gameInfo = Resource_Service_GameData::getBasicInfo($val['game_id']);
	        $extalInfo = Resource_Service_GameData::getExtraInfo($val['game_id']);
	        if($gameInfo){
	            $data[] = array(
	                'listItemType'=>'DailyRecommend',
	                'title'=>html_entity_decode($val['title'], ENT_QUOTES),
	                'resume'=>html_entity_decode($val['content'], ENT_QUOTES),
	                'viewType'=>'GameDetailView',
	                'ad_id'=>$val['id'],
	                'gameId'=>$val['game_id'],
	                'img'=>$gameInfo['img'],
	                'name'=>html_entity_decode($gameInfo['name'], ENT_QUOTES),
	                'size'=>$gameInfo['size'],
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
	    return $data;
	}
	/**================end客户端api*/

	/**
	 * 获取首页每日一荐缓存数据
	 */
	public static function updateIndexRecommendCacheData() {
	    $systemVersionList = array(Game_Service_RecommendNew::GREATE_SYSTEM_VERSION, Game_Service_RecommendNew::LESS_SYSTEM_VERSION);
	    foreach ($systemVersionList as $systemVersion) {
	        Yaf_Registry::set("androidVersion", $systemVersion);
	        self::updateIndexRecommendCacheDataByVersion($systemVersion);
	    }
	}
	
	private static function updateIndexRecommendCacheDataByVersion($systemVersion) {
	    $api = Util_CacheKey::getApi(Util_CacheKey::HOME, Util_CacheKey::HOME_DAILY_RECOMMEND);
	    $data = self::getRecommendDataByHour();
	    Util_CacheKey::updateCache($api, func_get_args(), $data);
	}

	/**
	 * 更新游戏状态字段(游戏打开关闭的时候需要调用)
	 * @param unknown $gameId
	 * @param unknown $status
	 */
	public static function updateGameStatus($gameId, $status) {
	    $params = array('game_id' => $gameId);
	    $data = array('game_status' => $status);
	    $result = self::_getDao()->updateBy($data, $params);
	    if($result) {
	        $recommend = self::getRecommendByHour();
	        if($recommend && $recommend['game_id'] == $gameId) {
	             self::updateIndexRecommendCacheData();
	        }
	    }
	    return $result;
	}
    
	/***
	 * 后台添加修改的时候判断是否有重复时间段的记录
	 * @param unknown $startTime
	 * @param unknown $endTime
	 * @param unknown $editId
	 * @return string
	 */
	public static function getShowGameCountsByTime($startTime, $endTime, $editId) {
        return self::_getDao()->getDailyGamesCounts($startTime, $endTime, $editId);
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
		if(isset($data['game_id'])) $tmp['game_id'] = $data['game_id'];
		if(isset($data['game_status'])) $tmp['game_status'] = $data['game_status'];
		if(isset($data['content'])) $tmp['content'] = $data['content'];
		if(isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
		if(isset($data['end_time'])) $tmp['end_time'] = $data['end_time'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['sort'])) $tmp['sort'] = $data['sort'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		return $tmp;
	}

	/**
	 * 
	 * @return Game_Dao_RecommendDay
	 */
	private static function _getDao() {
		return Common::getDao("Game_Dao_RecommendDay");
	}
	
}
