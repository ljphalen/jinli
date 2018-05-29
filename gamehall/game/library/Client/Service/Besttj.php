<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Client_Service_Besttj{

	const CACHE_EXPIRE = 7200;
	
	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllBesttj() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array('start_time'=>'DESC','id'=>'DESC')) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getBesttj($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateBesttj($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * @param unknown_type $status
	 * @param unknown_type $game_id
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function deleteByBesttj($params) {
		return self::_getDao()->deleteBy($params);
	}
	
	/**
	 * 
	 * @param unknown_type $id
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updateBesttjDate($id) {
		return self::_getDao()->update(array('update_time'=>Common::getTime()), intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function batchAddByBesttj($data,$id,$message) {
		$newBestGames = array();
		$info = Client_Service_Besttj::getBesttj(intval($id));
		foreach($data as $key=>$value) {
			$newBestGames[] = array(
					'id'=>'',
					'sort'=>0,
					'status'=>1,
					'besttj_id'=>$id,
					'game_id'=>$value,
					'game_status'=>1,
					'game_message' => $message[$value]
			);
		}
		
		$time = Common::getTime();
		self::_getDao()->update(array('update_time'=>$time),$id);
		return self::_getBesttjDao()->mutiInsert($newBestGames);
	}
	
	public static function mutiBesttjGames($data) {
	    if(!$data)  return false;
	    return self::_getBesttjDao()->mutiInsert($data);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function batchUpdateMessageByBesttj($id, $message) {
	    foreach($message as $key=>$value) {
	        self::_getBesttjDao()->updateBy(array('game_message' => $value), array('besttj_id' => $id,'game_id' => $key));
	    }
	    $time = Common::getTime();
	    self::_getDao()->update(array('update_time'=>$time),$id);
	    return true;
	}
	
	/**
	 *
	 * @param unknown_type $data
	 * @param unknown_type $sorts
	 * @return boolean
	 */
	public static function sortBesttjs($sorts,$id) {
		foreach($sorts as $key=>$value) {
			self::updateByBesttjs(array('sort'=>$value), array('game_id'=>$key,'besttj_id'=>$id));
		}
		return true;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateByBesttjs($data, $params) {
		return self::_getBesttjDao()->updateBy($data, $params);
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function getBesttjByBesttjId($besttj_id) {
		if (!$besttj_id) return false;
		return self::_getBesttjDao()->getsBy(array('besttj_id'=>intval($besttj_id)));
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function updateBesttjByBesttjId($besttj_id,$status) {
		if (!$besttj_id) return false;
		return self::_getBesttjDao()->updateBy(array('status'=>$status), array('besttj_id'=>intval($besttj_id)));
	}
	
	/**
	 *
	 * @param unknown_type $status
	 * @param unknown_type $game_id
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function deleteByIdxBesttj($params) {
		return self::_getBesttjDao()->deleteBy($params);
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function getBesttjByGtypes($params) {
		if (!isset($params)) return false;
		return self::_getDao()->getBy($params, array('start_time'=>'DESC', 'id'=>'DESC'));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function updateBesttjTJ($id) {
		if (!$id) return false;
		return self::_getDao()->increment('hits', array('id'=>intval($id)));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteBesttj($id) {
		if (!$id) return false;
		return self::_getDao()->delete(intval($id));
	}
	
	
	/**
	 *
	 * @param unknown_type $data
	 * @return boolean
	 */
	public static function deleteBatchBesttj($data) {
		if (!is_array($data)) return false;
		foreach($data as $key=>$value) {
			self::_getDao()->deleteBy(array('id'=>$value));
		}
		return true;
	}
	
	/**
	 *
	 * @param unknown_type $id
	 * @return Ambigous <boolean, number>
	 */
	public static function updateBatchBesttj($ids,$status) {
		if (!is_array($ids)) return false;
		return self::_getDao()->updateBatchBesttj($ids, $status);
	}
	
	
	public static function updateBesttjStatus($id,$status) {
		return self::_getDao()->update(array('status'=>$status), $id);
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function updateBesttjByGameId($gameId,$status) {
		if (!$gameId) return false;
		return self::_getBesttjDao()->updateBy(array('game_status'=>$status), array('game_id'=>intval($gameId)));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addBesttj($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$ret = self::_getDao()->insert($data);
		if (!$ret) return $ret;
		return self::_getDao()->getLastInsertId();
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
		if(isset($data['guide'])) $tmp['guide'] = $data['guide'];
		if(isset($data['gtype'])) $tmp['gtype'] = $data['gtype'];
		if(isset($data['ntype'])) $tmp['ntype'] = $data['ntype'];
		if(isset($data['btype'])) $tmp['btype'] = $data['btype'];
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
		if(isset($data['update_time'])) $tmp['update_time'] = intval($data['update_time']);
		if(isset($data['end_time'])) $tmp['end_time'] = intval($data['end_time']);
		return $tmp;
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function getIdxBesttjByBesttjId($id) {
		if (!$id) return false;
		return self::_getBesttjDao()->getsBy(array('besttj_id'=>$id,'game_status'=>1),array('sort'=>'DESC','game_id'=>'DESC'));
	}
	
	public static function deleteIdxBesttjByGameId($gameId){
		if (!$gameId) return false;
		return self::_getBesttjDao()->deleteBy(array('game_id'=>intval($gameId)));
	}
	
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function getIdxBesttjByOnlineBesttjId($besttj_id) {
		if (!$besttj_id) return false;
		return self::_getBesttjDao()->getsBy(array('besttj_id'=>intval($besttj_id),'status'=>1,'game_status'=>1), array('sort'=>'DESC','game_id'=>'DESC'));
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function getIdxBesttjByGameId($game_id) {
		if (!$game_id) return false;
		return self::_getBesttjDao()->getsBy(array('game_id'=>$game_id));
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function getIdxBesttjByGameBesttjId($game_id, $besttj_id) {
		if (!$game_id) return false;
		return self::_getBesttjDao()->getBy(array('game_id'=>$game_id,'besttj_id'=>$besttj_id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateByBesttjTmps($data, $params) {
		return self::_getBesttjTmpDao()->updateBy($data, $params);
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function getBesttjByBesttjTmpId($besttj_id) {
		return self::_getBesttjTmpDao()->getsBy(array('besttj_id'=>intval($besttj_id)));
	}
	
	/**
	 *
	 * @param unknown_type $data
	 * @return boolean
	 */
	public static function deleteBatchBesttjTmp($besttj_id) {
		if (!$besttj_id) return false;
		return self::_getBesttjTmpDao()->deleteBy(array('besttj_id'=>$besttj_id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function mutiBesttjTmp($data) {
		if(!$data)  return false;
		return self::_getBesttjTmpDao()->mutiInsert($data);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function rollBackBesttj($data) {
		if(!$data)  return false;
		return self::_getBesttjDao()->mutiInsert($data);
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function updateIdxBesttjStatus($game_id,$status) {
		if (!$game_id) return false;
		if($status == 0){
		  self::_getBesttjDao()->deleteBy(array('game_id'=>$game_id));
		}
		return true;
	}
	
	/**
	 *
	 * @param unknown_type $data
	 * @return boolean
	 */
	public static function deleteByBesttjGames($data,$id) {
		if (!is_array($data)) return false;
		foreach($data as $key=>$value) {
			self::_getBesttjDao()->deleteBy(array('game_id'=>$value,'besttj_id'=>$id));
		}
		$time = Common::getTime();
		self::_getDao()->update(array('update_time'=>$time),$id);
		return true;
	}
	
	/**
	 * 
	 * @param unknown_type $game_id
	 * @param unknown_type $besttj_id
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function deleteByBesttjId($game_id,$besttj_id) {
		if (!$game_id) return false;
		return self::_getBesttjDao()->deleteBy(array('game_id'=>$game_id,'besttj_id'=>$besttj_id));
	}
	
	
	
	public static function getDataVersion() {
		$redisCache = self::getCacheObject ();
		$versionKey = self::getversionKeyName();
		$version = $redisCache->get($versionKey);
		if($version === false){
			$version = Common::getTime();
			$redisCache->set($versionKey, $version, self::CACHE_EXPIRE);
		}
		return $version;
	}
	
	public static function getversionKeyName(){
		$versinoKey = Util_CacheKey::BEST_TJ_VERSION;
		return $versinoKey;
	}
	
	public static function updateVersionToCache(){
		$redisCache = self::getCacheObject ();
		$versionKey = self::getversionKeyName();
		$version = Common::getTime();
		$redisCache->set($versionKey, $version, self::CACHE_EXPIRE);
	}
	
	public static function getDataKeyName($groupId, $networkType, $clientVersionType, $version){
		$dataKeyName = Util_CacheKey::BEST_TJ_INFO . '::' . $groupId.'::'.$networkType."::".$clientVersionType.'::'.$version;
		return $dataKeyName;
	}
	
	
	private static function getCacheObject() {
		$redisCache = Cache_Factory::getCache();
		return $redisCache;
	}
	
	/**
	 *
	 * @return Ambigous <boolean, mixed, multitype:>
	 */
	public static function getIdxGameBesttjs() {
		return self::_getBesttjDao()->getsBy(array('game_status' => 1));
	}
	
	/**
	 * 
	 * @return Client_Dao_Besttj
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_Besttj");
	}
	
	/**
	 *
	 * @return Client_Dao_IdxGameClientBesttj
	 */
	private static function _getBesttjDao() {
		return Common::getDao("Client_Dao_IdxGameClientBesttj");
	}
	
	/**
	 *
	 * @return Client_Dao_IdxGameClientBesttjTmp
	 */
	private static function _getBesttjTmpDao() {
		return Common::getDao("Client_Dao_IdxGameClientBesttjTmp");
	}
}
