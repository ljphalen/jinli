<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Client_Service_Gift extends Common_Service_Base{
    const GIFT_STATE_OPENED = 1;
    const GIFT_STATE_CLOSEED = 0;
    const GAME_STATE_ONLINE = 1;
    const GAME_STATE_OFFLINE = 0;
    const GIFT_EXPIRE = 7200;
    const GIFT_LIST_EXPIRE =120;
    const PAGE_LIMIT = 10;

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllGift() {
		return array(self::getDao()->count(), self::getDao()->getAll());
	}
	
	/**
	 * 
	 * @param int $page
	 * @param int $limit
	 * @param params $params
	 * @return multitype:unknown
	 */
	public static function getList($page, $limit, $params = array(), $orderBy= array('game_sort'=>'DESC', 'game_id'=>'DESC', 'sort'=>'DESC', 'effect_start_time' => 'DESC','id' => 'DESC')) {
		if(intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList(intval($start), intval($limit), $params, $orderBy);
		$total = self::getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function getGiftByGameIds($params) {
		if (!isset($params)) return false;
		return self::getDao()->getGiftByGameIds($params);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getOnlineGifts() {
		$params = array('status' => 1, 'game_status'=>1);
		$params['effect_start_time'] = array('<=', Common::getTime());
		$params['effect_end_time'] = array('>=', Common::getTime());
		return self::getDao()->getsBy($params);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getBy($params) {
		return self::getDao()->getBy($params);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getsBy($params, $orderBy= array('game_sort'=>'DESC', 'game_id'=>'DESC', 'sort'=>'DESC', 'effect_start_time' => 'DESC','id' => 'DESC')) {
		return self::getDao()->getsBy($params, $orderBy);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getGift($id) {
		if (!intval($id)) return false;
		return self::getDao()->get(intval($id));
	}

	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getGiftByGift($gift_id) {
		if (!intval($gift_id)) return false;
		return self::getDao()->getBy(array('gift_id'=>$gift_id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getGiftByGameId($game_id) {
		if (!intval($game_id)) return false;
		return self::getDao()->getBy(array('game_id'=>intval($game_id)));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateGiftDatabase($data, $id) {
		if (!is_array($data)) return false;
		$data = self::cookData($data);
		return self::getDao()->update($data, intval($id));
	}
		
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateGift($data, $id) {
		$result = self::updateGiftDatabase($data, $id);
		if ($result) {
			self::updataGiftBaseInfoCache($data, $id);
			self::updataGiftNumCacheByGiftId($id);
		}
		return $result;
	}
	/**
	 * 
	 * @param unknown_type $status
	 * @param unknown_type $game_id
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updateGiftGameId($status,$game_id) {
		if (!$game_id) return false;
		return self::getDao()->updateBy(array('game_status'=>intval($status)), array('game_id'=>intval($game_id)));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateGiftStatus($giftIds, $status) {
		if (!is_array($giftIds)) {
			return false;
		}

	    foreach($giftIds as $key=>$id) {
			self::getDao()->update(array('status'=>$status), $id);		
		    //更新热门礼包游戏id
			Client_Service_GiftHot::updateBy(array('status'=>$status), array('gift_id'=>$id));
			self::updateGiftStatusInCache($status, $id);
			self::updataGiftNumCacheByGiftId($id);
		}
		return true;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateGiftStatusByGameId($ids, $status) {
		if (!is_array($ids)) return false;
		foreach($ids as $key=>$value) {
			self::getDao()->updateBy(array('status'=>$status), array('game_id'=>$value));
		}
		return true;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateGameSortByGameId($sorts) {
		if (!is_array($sorts)) return false;
		foreach($sorts as $key=>$value) {
			self::getDao()->updateBy(array('game_sort'=>$value), array('game_id'=>$key));
		}
		return true;
	}
	
	/**
	 *
	 * @param unknown $data
	 * @param unknown $params
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updateBy($data, $params) {
		if (!is_array($data)) return false;
		$data = self::cookData($data);
		return self::getDao()->updateBy($data, $params);
	}
	
	/**
	 *
	 * @param unknown_type $data
	 * @param unknown_type $sorts
	 * @return boolean
	 */
	public static function batchSortByGift($sorts) {
		foreach($sorts as $key=>$value) {
			self::getDao()->update(array('sort'=>$value), $key);
		}
		return true;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function replaceGift($data) {
		if (!is_array($data)) return false;
		return self::getDao()->replace($data);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteGift($id) {
		return self::getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * @param unknown_type $code
	 * @param unknown_type $id
	 * @return Ambigous <boolean, number>
	 */
	public static function deleteGiftActivationCode($code,$id) {
		return self::getDao()->deleteBy(array('activation_code'=>$code),$id);
	}
	
	
	
	public static function addGiftBaseInfo($data){
		if (!is_array($data)) return false;
		$data = self::cookData($data);
		$ret  =  self::getDao()->insert($data);
		if (!$ret) return $ret;
		$giftId = self::getDao()->getLastInsertId();	
		return $giftId;
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param array $giftBaseInfo
	 */
	public static function updateGiftBaseInfo($giftBaseInfo, $giftId) {
		if (!is_array($giftBaseInfo)) {
			return false;
		}
		//开始事务
		$trans = parent::beginTransaction();
		try {
			//更新礼包
			$queryRemainKey = array('gift_id'=>$giftId, 
					                'status' => Client_Service_Giftlog::KEY_STATE_FREE);
			$remainGiftNum  = Client_Service_Giftlog::getBy($queryRemainKey);
			if(!$remainGiftNum) {
				$giftBaseInfo['status'] = 0;
			}

			$ret = self::updateGiftDatabase($giftBaseInfo, $giftId);
			if (!$ret) {
				parent::rollBack();
				throw new Exception("Update Gift fail.", -201);
			}

			//更新热门礼包游戏id
			$hotGiftInfo = array('game_id'=>$giftBaseInfo['game_id'], 
					             'status'=>$giftBaseInfo['status']);
			$queryForUpdate = array('gift_id'=>$giftId);
			$ret = Client_Service_GiftHot::updateBy($hotGiftInfo, $queryForUpdate);
			if (!$ret) {
				parent::rollBack();
				throw new Exception("Update GiftHot fail.", -202);
			}

			//事务提交
			if($trans) {
				$result = parent::commit();
				if ($result) {
					self::updataGiftBaseInfoCache($giftBaseInfo, $giftId);
					self::updataGiftNumCacheByGiftId($giftId);
				}
				return $result;
			}
		} catch (Exception $e) {
			parent::rollBack();
			return false;
		}
		return true;
	}
	
	/**
	 * 更新缓存数据
	 * @param int $giftId
	 */
	public static function updateGiftDataToCache($giftId){
	    if(!$giftId){
	        return false;
	    }
	    $giftInfo = self::getBy(array(
	            'id' => $giftId,
	            'status' => self::GIFT_STATE_OPENED
	    ));
	    if(!$giftInfo){
	        return false;
	    }
	    self::updataGiftBaseInfoCache($giftInfo, $giftId);
	    self::updataGiftNumCacheByGiftId($giftId);
	}
	
	
	/**
	 *更新游戏ID关联的礼包数
	 */
	public static function updataGiftNumCacheByGameId($gameId){
		if(!$gameId){
			return false;
		}
		$cache = Cache_Factory::getCache();
		$cacheHash = $gameId.'gameRelevanceGiftInfo' ;
		$cacheRelevanceGiftInfoKey      = 'giftNum';

		$params['game_id'] = $gameId ;
		$params['status'] = 1 ;
		$params['game_status']=1;
		$params['effect_start_time'] = array('<=', Common::getTime());
		$params['effect_end_time'] = array('>=', Common::getTime());
		$gifts = self::getDao()->getsBy($params);
		$giftNum = 0;
		foreach ( $gifts as $val){
			$remainGift = self::getGiftRemainNum($val['id']);
			if($remainGift > 0){
				$giftNum++;
			}
		}
		$result = $cache->hSet($cacheHash, $cacheRelevanceGiftInfoKey, $giftNum, Client_Service_Gift::GIFT_EXPIRE);
		return $result;
	
	}
	
	/**
	 *更新游戏ID关联的礼包数
	 */
	public static function updataGiftNumCacheByGiftId($giftId){
		if(!$giftId){
			return false;
		}
		
		$giftInfo = self::getBy(array('id'=>$giftId));
		if(!$giftInfo){
			return false;
		}
		$gameId = $giftInfo['game_id'];
		$cache = Cache_Factory::getCache();
		$cacheHash = $gameId.'gameRelevanceGiftInfo' ;
		$cacheRelevanceGiftInfoKey      = 'giftNum';
		
		$params['game_id'] = $gameId ;
		$params['status'] = 1 ;
		$params['game_status']=1;
		$params['effect_start_time'] = array('<=', Common::getTime());
		$params['effect_end_time'] = array('>=', Common::getTime());
		$gifts = self::getDao()->getsBy($params);
		$giftNum = 0;
		foreach ( $gifts as $val){
			$remainGift = self::getGiftRemainNum($val['id']);
			if($remainGift > 0){
				$giftNum++;
			}
		}
		$result = $cache->hSet($cacheHash, $cacheRelevanceGiftInfoKey, $giftNum, Client_Service_Gift::GIFT_EXPIRE);
		return $result;
	
	}
	
	/**
	 * 通过游戏ID 关联的礼包数
	 */
	public static function getGiftNumByGameId($gameId){
		if(!$gameId){
			return false;
		}
		
		$cache = Cache_Factory::getCache();
		$cacheHash = $gameId.'gameRelevanceGiftInfo' ;
		$cacheRelevanceGiftInfoKey      = 'giftNum';
		$giftNum = $cache->hGet($cacheHash, $cacheRelevanceGiftInfoKey);
        if($giftNum === false){
        	$params['game_id'] = $gameId ;
        	$params['status'] = self::GIFT_STATE_OPENED ;
        	$params['game_status']= self::GAME_STATE_ONLINE;
        	$params['effect_start_time'] = array('<=', Common::getTime());
        	$params['effect_end_time'] = array('>=', Common::getTime());
        	$ret = self::getDao()->getsBy($params);
        	$giftNum  = count($ret);
        	$cache->hSet($cacheHash, $cacheRelevanceGiftInfoKey, $giftNum, Client_Service_Gift::GIFT_EXPIRE);
        }		
		return $giftNum;
	}
	
	/**
	 * 更新礼包激活码总数量缓存
	 * @param unknown_type $giftNum
	 */
	public static function updateGiftTotalCache($giftId, $giftTotal){
		if(!$giftId || !$giftTotal){
			return false;
		}
    	$cache = Cache_Factory::getCache();
    	$cacheHash = $giftId.'_gift_info' ;
    	$cacheGiftTotalKey      = 'giftActivityCodeTotal';
    	$ret = $cache->hSet($cacheHash, $cacheGiftTotalKey, $giftTotal, Client_Service_Gift::GIFT_EXPIRE);   
    	return $ret;	
	}
	
	/**
	 * 更新礼包剩余激活码数量缓存
	 * @param unknown_type $giftNum
	 */
	public static function updateGiftRemainNumCache($giftId, $giftRemainNum){
		if(!$giftId || !$giftRemainNum){
			return false;
		}
		$cache = Cache_Factory::getCache();
		$cacheHash = $giftId.'_gift_info' ;
		$cacheGiftTotalKey      = 'giftActivityCodeRemainNum';
		$ret =  $cache->hSet($cacheHash, $cacheGiftTotalKey, $giftRemainNum, Client_Service_Gift::GIFT_EXPIRE);
		return $ret;
	}
	
	/**
	 * 获取礼包激活码总数量
	 * @param unknown_type $giftId
	 * @return boolean|unknown
	 */
	public static function getGiftTotal($giftId){
		if(!$giftId){
			return false;
		}
		$cache = Cache_Factory::getCache();
		$cacheHash = $giftId.'_gift_info' ;
		$cacheGiftTotalKey   = 'giftActivityCodeTotal';
		$giftTotal = $cache->hGet($cacheHash, $cacheGiftTotalKey);
		//礼包初始化
		if($giftTotal === false){
			$giftTotal = intval(Client_Service_Giftlog::getGiftlogCount($giftId));
			$cache->hSet($cacheHash, $cacheGiftTotalKey, $giftTotal, Client_Service_Gift::GIFT_EXPIRE);
		}
		$cacheKey = 'id';
		if(!$cache->hExists($cacheHash, $cacheKey)){
			$GiftInfo = Client_Service_Gift::getGift($giftId);
			Client_Service_Gift::updataGiftBaseInfoCache($GiftInfo, $giftId);
		}
		return $giftTotal;
	}
	
	
	/**
	 * 获取礼包剩余礼包激活码数量
	 * @param unknown_type $giftId
	 */
	public static function getGiftRemainNum($giftId){
		if(!$giftId){
			return false;
		}
		$cache = Cache_Factory::getCache();
		$cacheHash = $giftId.'_gift_info' ;
		$cacheRemainNumKey      = 'giftActivityCodeRemainNum';
		$giftRemainNum = $cache->hGet($cacheHash, $cacheRemainNumKey);	
		if($giftRemainNum === false){
			$giftRemainNum = intval(Client_Service_Giftlog::getGiftlogByStatus(0,$giftId));
			$cache->hSet($cacheHash, $cacheRemainNumKey, $giftRemainNum, Client_Service_Gift::GIFT_EXPIRE);
		}
		$cacheKey = 'id';
		if(!$cache->hExists($cacheHash, $cacheKey)){
			$GiftInfo = Client_Service_Gift::getGift($giftId);
			Client_Service_Gift::updataGiftBaseInfoCache($GiftInfo, $giftId);
		}
		return $giftRemainNum;
	}
	
	/**
	 * 删除激活码的缓存
	 * @param unknown_type $giftId
	 * @return boolean
	 */
	public static function deleteGiftInfoCache($giftId){
		if(!$giftId){
			return false;
		}
		$cache = Cache_Factory::getCache();
		$cacheHash = $giftId.'_gift_info' ;
		return  $cache->delete($cacheHash);
	}
		
	public static function updateGiftStatusInCache($status, $giftId) {
		if(!$giftId){
			return false;
		}
		if (self::GIFT_STATE_OPENED != $status && self::GIFT_STATE_CLOSEED != $status) {
			return false;
		}

		$cache = Cache_Factory::getCache();
		$cacheHash = $giftId.'_gift_info' ;
		$cacheKey = 'id';
		if($cache->hExists($cacheHash, $cacheKey) ){
			$cache->hSet($cacheHash, 'status', $status, Client_Service_Gift::GIFT_EXPIRE);
			return true;
		}
		return false;
	}

	
	/**
	 * 礼包信息存入缓存
	 */
	public static function updataGiftBaseInfoCache($data , $giftId){
		if(!$giftId || !is_array($data)){
			return false;
		}
		$cache = Cache_Factory::getCache();
		$cacheHash = $giftId.'_gift_info' ;
		$data['giftActivityCodeRemainNum'] = self::getGiftEffectActivationCodeByGiftId($giftId);
		$data['giftActivityCodeTotal'] =  self::getGiftActivationCodeTotalByGiftId($giftId);
		$cache->hMset($cacheHash, $data);
		$cache->expire($cacheHash, Client_Service_Gift::GIFT_EXPIRE);
	}
	
	
	public static function getGiftEffectActivationCodeByGiftId($giftId){
		$effectActivationCode = Client_Service_Giftlog::getGiftlogByStatus(self::GIFT_STATE_CLOSEED, $giftId);
		return intval($effectActivationCode);
		
	}
	
	public static function getGiftActivationCodeTotalByGiftId($giftId){
		$activationCodeTotal = Client_Service_Giftlog::getGiftlogCount($giftId);
		return intval($activationCodeTotal);
	}
	
	
	
	
	
	/**
	 * 获取礼包的基本的缓存信息
	 */
	public static  function getGiftBaseInfo($giftId){		
		if(!$giftId){
			return false;
		}
		$cache = Cache_Factory::getCache();
		$cacheHash = $giftId.'_gift_info' ;
		$cacheKey      = 'name';
		if($cache->hExists($cacheHash, $cacheKey) ){
			$giftInfo = $cache->hGetAll($cacheHash);
		} else {
			$data = self::getGift($giftId);
			self::updataGiftBaseInfoCache($data, $giftId);
			$giftInfo = $cache->hGetAll($cacheHash);
		}
		return $giftInfo;
	}
	
	
	/**
	 * @param 缓存的激活码减少
	 */
	 public static function reduceRemainActivitionCodeCache($giftId, $currentRemains) {
	 	 if( $giftId < 1 || $currentRemains < 1 ){
	 	 	return false;
	 	 } 
		 $cache = Cache_Factory::getCache();
		 $cacheHash = $giftId.'_gift_info' ;
		 $cacheGiftTotalKey      = 'giftActivityCodeRemainNum';
		 $currentRemains = $cache->hIncrBy($cacheHash, $cacheGiftTotalKey, -1);
		 return $currentRemains;	 
	}
	
	/**
	 * @param 缓存的激活码的总数减少
	 */
	public static function reduceTotalActivitionCodeCache($giftId, $currentTotal) {
		if( $giftId < 1 || $currentTotal < 1 ){
			return false;
		}
		$cache = Cache_Factory::getCache();
		$cacheHash = $giftId.'_gift_info' ;
		$cacheGiftTotalKey      = 'giftActivityCodeTotal';
		$currentTotal = $cache->hIncrBy($cacheHash, $cacheGiftTotalKey, -1);
		return $currentTotal;
	}
	
	
	/**
	 * 更新礼包列表缓存
	 */
	public static function updateGiftListToCache($gameId){
		self::updateGiftListToCacheForAllGame();
		self::updateGiftListToCacheForGameId($gameId);
	}
	
	/**
	 * 更新礼包列表缓存,所有游戏
	 */
	public static function updateGiftListToCacheForAllGame(){
		
		$dbTotal = self::getGiftListTotal();
		$totalKeyName = self::getGiftListTotalKeyName();
		$cache = Cache_Factory::getCache();
		$cacheTotal = $cache->get($totalKeyName);
		for ( $page = 1 ; $page<= ( ceil ($dbTotal / self::PAGE_LIMIT) ); $page++ ){
			$dataKeyName = self::getGiftListKeyName($page);
			$pageData = self::getGiftListFromDb($page);
			$cache->set($dataKeyName,  $pageData, Client_Service_Gift::GIFT_LIST_EXPIRE);
		}
		$cache->set($totalKeyName, $dbTotal,  Client_Service_Gift::GIFT_LIST_EXPIRE);

	}
	/**
	 * 更新礼包列表缓存,针对某一个游戏
	 */
	public static function updateGiftListToCacheForGameId($gameId){
	
		$dbTotal = self::getGiftListTotal(gameId);
		$totalKeyName = self::getGiftListTotalKeyName (gameId );
		$cache = Cache_Factory::getCache();
		$cacheTotal = $cache->get($totalKeyName);
		for ( $page = 1 ; $page<= ( ceil ($dbTotal / self::PAGE_LIMIT) ); $page++ ){
			$dataKeyName = self::getGiftListKeyName($page, $gameId);
			$pageData = self::getGiftListFromDb($page, $gameId);
			$cache->set($dataKeyName,  $pageData, Client_Service_Gift::GIFT_LIST_EXPIRE);
		}
		$cache->set($totalKeyName, $dbTotal,  Client_Service_Gift::GIFT_LIST_EXPIRE);
	
	}
	
	public function getGiftListTotalKeyName($gameId = 0) {
		$totalKeyName = Util_CacheKey::GIFT . '::' . Util_CacheKey::GIFT_LIST.'_'.$gameId.'_total';
		return $totalKeyName;
	}
	
	public function getGiftListKeyName($page, $gameId = 0) {
		$totalKeyName = Util_CacheKey::GIFT . '::' . Util_CacheKey::GIFT_LIST.'_'.$page.'_'.$gameId;
		return $totalKeyName;
	}
	
	
	
	public static function getGiftListTotal($gameId = 0){
		//礼包列表
		$parmas['status'] = Client_Service_Gift::GIFT_STATE_OPENED ;
		$parmas['game_status'] = Client_Service_Gift::GAME_STATE_ONLINE;
		$currTime = Util_TimeConvert::floor(Common::getTime(),
											Util_TimeConvert::RADIX_DAY);
		$parmas['effect_start_time'] = array('<=', $currTime);
		$parmas['effect_end_time']   = array('>=', $currTime);
		if($gameId){
			$parmas['game_id'] = $gameId;
		}
		$orderBy = array('game_sort'=>'DESC',
						 'sort'=>'DESC',
						 'effect_start_time' => 'DESC');
		list($total,) = Client_Service_Gift::getList(1,
												self::PAGE_LIMIT,
												$parmas,
												$orderBy);
		return $total;
	}
	
	private function getGiftListFromDb($page, $gameId=0) {
		//礼包列表
		$parmas['status'] = Client_Service_Gift::GIFT_STATE_OPENED ;
		$parmas['game_status'] = Client_Service_Gift::GAME_STATE_ONLINE;
		$currTime = Util_TimeConvert::floor(Common::getTime(),
				    Util_TimeConvert::RADIX_DAY);
		$parmas['effect_start_time'] = array('<=', $currTime);
		$parmas['effect_end_time']   = array('>=', $currTime);
		if($gameId){
			$parmas['game_id'] = $gameId;
		}
		$orderBy = array('game_sort'=>'DESC',
				         'sort'=>'DESC',
				         'effect_start_time' => 'DESC');
		list($total, $giftsList) = Client_Service_Gift::getList($page,
																self::PAGE_LIMIT,
																$parmas,
																$orderBy);
		return array($total, $giftsList);
	}
	
	
	/**
	 * 获取游戏列表
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getGameList($page = 1, $limit = 10, $params = array(), $orderBy= array('game_sort'=>'DESC', 'game_id'=>'DESC', 'sort'=>'DESC', 'effect_start_time' => 'DESC','id' => 'DESC')) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getGameList($start, $limit, $params, $orderBy);
		$total = self::getDao()->getGameListCount($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = intval($data['id']);
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['game_sort'])) $tmp['game_sort'] = intval($data['game_sort']);
		if(isset($data['game_id'])) $tmp['game_id'] = $data['game_id'];
		if(isset($data['name'])) $tmp['name'] = $data['name'];
		if(isset($data['content'])) $tmp['content'] = $data['content'];
		if(isset($data['method'])) $tmp['method'] = $data['method'];
		if(isset($data['use_start_time'])) $tmp['use_start_time'] = $data['use_start_time'];
		if(isset($data['use_end_time'])) $tmp['use_end_time'] = $data['use_end_time'];
		if(isset($data['effect_start_time'])) $tmp['effect_start_time'] = $data['effect_start_time'];
		if(isset($data['effect_end_time'])) $tmp['effect_end_time'] = $data['effect_end_time'];
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['game_status'])) $tmp['game_status'] = $data['game_status'];
		return $tmp;
	}
	
	public static function getGrabGiftLogSwitch() {
		return Game_Service_Config::getValue('grab_log_status');
	}
	
	/**
	 * 
	 * @return Client_Dao_Gift
	 */
	private static function getDao() {
		return Common::getDao("Client_Dao_Gift");
	}
	
	/**
	 *
	 * @return Client_Dao_Giftlog
	 */
	private static function getGiftlogDao() {
		return Common::getDao("Client_Dao_Giftlog");
	}
}
