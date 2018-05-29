<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author ljp
 *
 */
class Client_Service_GiftHot extends Common_Service_Base{

	public static function addGiftHot($data) {
		$data = self::cookData($data);
		return self::getDao()->insert($data);
	}

	public static function updateGiftStatus($sorts, $status) {
		if (!is_array($sorts)) return false;
	    foreach($sorts as $key=>$value) {
	    	$info = Client_Service_GiftHot::getById($value);
	    	$remainGifts = Client_Service_Gift::getGiftRemainNum($info['gift_id']);
	    	if(($status && $remainGifts >0) || $status == 0){
	    		self::getDao()->update(array('status'=>$status), $value);
	    	}
			Client_Service_Gift::updataGiftNumCacheByGiftId($value);
		}
		return true;
	}

	public static function batchSortByGift($sorts) {
		foreach($sorts as $key=>$value) {
			self::getDao()->update(array('sort'=>$value), $key);
		}
		return true;
	}

	/**
	 * 
	 * @param int $page
	 * @param int $limit
	 * @param params $params
	 * @return multitype:unknown
	 */
	public static function getList($page, $limit, $params = array()) {
		if(intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList(intval($start), intval($limit), $params, array('sort'=>'DESC', 'id' => 'DESC'));
		$total = self::getDao()->count($params);
		return array($total, $ret);
	}
	
	public static function count($params = array()) {
	    return self::getDao()->count($params);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getBy($params = array()) {
		return self::getDao()->getBy($params);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getsBy($params = array()) {
		return self::getDao()->getsBy($params);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getById($id) {
		if (!intval($id)) return false;
		return self::getDao()->get(intval($id));
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
	public static function updateById($data, $id) {
		if (!is_array($data)) return false;
		$data = self::cookData($data);
		return self::getDao()->update($data, intval($id));
	}
	
	
	/**
	 *
	 * @param unknown_type $status
	 * @param unknown_type $game_id
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updateBy($data, $param) {
		if (!is_array($param) || !is_array($data) ){
			return false;
		} 
		return self::getDao()->updateBy($data, $param);
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteById($id) {
		return self::getDao()->delete(intval($id));
	}
	
	
	public static function deleteBy($params) {
	    return self::getDao()->deleteBy($params);
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
		if(isset($data['game_status'])) $tmp['game_status'] = $data['game_status'];
		if(isset($data['gift_id'])) $tmp['gift_id'] = $data['gift_id'];
		if(isset($data['gift_name'])) $tmp['gift_name'] = $data['gift_name'];
		if(isset($data['effect_start_time'])) $tmp['effect_start_time'] = $data['effect_start_time'];
		if(isset($data['effect_end_time'])) $tmp['effect_end_time'] = $data['effect_end_time'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['game_status'])) $tmp['game_status'] = $data['game_status'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		if(isset($data['update_time'])) $tmp['update_time'] = $data['update_time'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_GiftHot
	 */
	private static function getDao() {
		return Common::getDao("Client_Dao_GiftHot");
	}
	

	public static function getListBy($params = array()) {
	    $ret = self::getDao()->getsBy($params, array('sort'=>'DESC', 'id' => 'DESC'));
	    return $ret;
	}
	
}
