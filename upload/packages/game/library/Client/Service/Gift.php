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
	public static function updateGift($data, $id) {
		if (!is_array($data)) return false;
		$data = self::cookData($data);
		self::updataGiftBaseInfoCache($data, $id);
		self::updataGiftNumCacheByGiftId($id);
		return self::getDao()->update($data, intval($id));
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
	public static function updateGiftStatus($sorts, $status) {
		if (!is_array($sorts)) return false;
	    foreach($sorts as $key=>$value) {
			self::getDao()->update(array('status'=>$status), $value);		
		    //更新热门礼包游戏id
			Client_Service_GiftHot::updateBy(array('status'=>$status), array('gift_id'=>$value));
			Client_Service_Gift::updataGiftNumCacheByGiftId($value);
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
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addGift($data) {
		if (!is_array($data)) return false;
		$data = self::cookData($data);
		$ret  =  self::getDao()->insert($data);
		if (!$ret) return $ret;

		$gift_id = self::getDao()->getLastInsertId();
		self::updataGiftBaseInfoCache($data, $gift_id);
		self::updataGiftNumCacheByGiftId($gift_id);
		return $gift_id;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addGiftGame($data) {
		if (!is_array($data)) return false;
		try {
			//添加礼包
			$gift_id = self::addGift($data);
			if (!$gift_id) throw new Exception("Add Gift fail.", -202);
		
		} catch (Exception $e) {	
			return false;
		}
		return true;
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function updateGiftGame($data, $id, $game_id, $codes, $logs) {
		if (!is_array($data)) return false;
		//开始事务
		$trans = parent::beginTransaction();
		try {
			//更新礼包
			$gift = Client_Service_Giftlog::getByGiftId(intval($id));
			if(!$gift)  $data['status'] = 0;
			$ret = self::updateGift($data,$id);
			if (!$ret) throw new Exception("Update Gift fail.", -201);
			
			if($logs){
				foreach($logs as $key=>$value){
					if(!in_array($value,$codes)){                       //判断是否为新的激活码
						$log = Client_Service_Giftlog::getByActivationCode($value);
						if($log){
							$ret = Client_Service_Giftlog::deleteGiftlogByActivationCcode($value); //不存在的激活码删除
							if (!$ret) throw new Exception("Delete Giftlog fail.", -201);
						} 
						
					}
				}
			}
			
			//更新领取记录游戏id
			$ret_game = Client_Service_Giftlog::updateGiftLogGameId($game_id,$data['game_id'],$id);
			if (!$ret_game) throw new Exception("Update Giftlog fail.", -201);
			
			//更新热门礼包游戏id
			$ret_game = Client_Service_GiftHot::updateBy(array('game_id'=>$data['game_id'],'status'=>$data['status']), array('gift_id'=>$id));
			if (!$ret_game) throw new Exception("Update GiftHot fail.", -202);
			
			$tmp = array();
			foreach($codes as $k=>$v){
				$game_info = array();
				if(!in_array($v,$logs) && $logs){                              //判断是否为新的激活码
					if($v){
						$tmp[] = array(
									'id'=>'',
									'gift_id'=>$id,
								    'game_id'=>$data['game_id'],
									'uname' =>'',
									'imei'=>'',
									'imeicrc'=>'',
									'activation_code'=>$v,
									'create_time'=>'',
									'status'=>0,
						);
					}
				}
			}
			if($tmp){
				$ret = self::getGiftlogDao()->mutiInsert($tmp);       //新的激活码就添加
				if (!$ret) throw new Exception('Add Giftlog fail.', -205);
			}
			
			//事务提交
			if($trans) {
				self::updataGiftBaseInfoCache($data, $id);
				self::updataGiftNumCacheByGiftId($id);
				parent::commit();
				return true;
			}
		} catch (Exception $e) {
			parent::rollBack();
			print_r($e->getMessage());
			return false;
		}
		return true;
	}
	
	
	/**
	 *更新游戏ID关联的礼包数
	 */
	public static function updataGiftNumCacheByGameId($gameId){
		if(!$gameId){
			return false;
		}
		$cache = Common::getCache();
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
		$cache = Common::getCache();
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
		
		$cache = Common::getCache();
		$cacheHash = $gameId.'gameRelevanceGiftInfo' ;
		$cacheRelevanceGiftInfoKey      = 'giftNum';
		$giftNum = $cache->hGet($cacheHash, $cacheRelevanceGiftInfoKey);
        if($giftNum === false){
        	$params['game_id'] = $gameId ;
        	$params['status'] = 1 ;
        	$params['game_status']=1;
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
    	$cache = Common::getCache();
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
		$cache = Common::getCache();
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
		$cache = Common::getCache();
		$cacheHash = $giftId.'_gift_info' ;
		$cacheGiftTotalKey   = 'giftActivityCodeTotal';
		$giftTotal = $cache->hGet($cacheHash, $cacheGiftTotalKey);
		//礼包初始化
		if($giftTotal === false){
			$giftTotal = intval(Client_Service_Giftlog::getGiftlogCount($giftId));
			$cache->hSet($cacheHash, $cacheGiftTotalKey, $giftTotal, Client_Service_Gift::GIFT_EXPIRE);
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
		$cache = Common::getCache();
		$cacheHash = $giftId.'_gift_info' ;
		$cacheRemainNumKey      = 'giftActivityCodeRemainNum';
		$giftRemainNum = $cache->hGet($cacheHash, $cacheRemainNumKey);	
		if($giftRemainNum === false){
			$giftRemainNum = intval(Client_Service_Giftlog::getGiftlogByStatus(0,$giftId));
			$cache->hSet($cacheHash, $cacheRemainNumKey, $giftRemainNum, Client_Service_Gift::GIFT_EXPIRE);
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
		$cache = Common::getCache();
		$cacheHash = $giftId.'_gift_info' ;
		return  $cache->delete($cacheHash);
	}
		
	/**
	 * 礼包信息存入缓存
	 */
	public static function updataGiftBaseInfoCache($data , $giftId){
		if(!$giftId){
			return false;
		}
		if(!$data['name']){
			return false;
		}
		$cache = Common::getCache();
		$cacheHash = $giftId.'_gift_info' ;
		foreach ($data as $key=>$value){
			$cache->hSet($cacheHash, $key, $value, Client_Service_Gift::GIFT_EXPIRE);
		}
	}
	
	/**
	 * 获取礼包的基本的缓存信息
	 */
	public static  function getGiftBaseInfo($giftId){		
		if(!$giftId){
			return false;
		}
		$cache = Common::getCache();
		$cacheHash = $giftId.'_gift_info' ;
		$cacheKey      = 'name';
		if($cache->hExists($cacheHash, $cacheKey) ){
			$giftInfo = $cache->hGetAll($cacheHash);
			return $giftInfo;
		}else{
			$data = self::getGift($giftId);
			self::updataGiftBaseInfoCache($data, $giftId);
			$giftInfo = $cache->hGetAll($cacheHash);
			return $giftInfo;
		}
	}
	
	
	/**
	 * @param 缓存的激活码减少
	 */
	 public static function reduceRemainActivitionCodeCache($giftId, $currentRemains) {
	 	 if( $giftId < 1 || $currentRemains < 1 ){
	 	 	return false;
	 	 } 
		 $cache = Common::getCache();
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
		$cache = Common::getCache();
		$cacheHash = $giftId.'_gift_info' ;
		$cacheGiftTotalKey      = 'giftActivityCodeTotal';
		$currentTotal = $cache->hIncrBy($cacheHash, $cacheGiftTotalKey, -1);
		return $currentTotal;
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
