<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Client_Service_Giftlog{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllGiftlog() {
		return array(self::getDao()->count(), self::getDao()->getAll());
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array('gift_id'=>'DESC')) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * @param unknown_type $params
	 * @return boolean
	 */
	public static function getBy($params, $orderBy = array()) {
		return self::getDao()->getBy($params, $orderBy);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getGiftlog($id) {
		if (!intval($id)) return false;
		return self::getDao()->get(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getByGiftId($gift_id) {
		if (!intval($gift_id)) return false;
		return self::getDao()->getBy(array('gift_id'=>$gift_id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getByActivationCode($activation_code) {
		if (!$activation_code) return false;
		return self::getDao()->getBy(array('activation_code'=>$activation_code));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getsByGiftId($gift_id) {
		if (!intval($gift_id)) return false;
		return self::getDao()->getsBy(array('gift_id'=>$gift_id));
	}
	
	/**
	 * 
	 * @param unknown_type $imei
	 * @return boolean|Ambigous <boolean, mixed>
	 */
	public static function getByImei($imei) {
		if (!intval($imei)) return false;
		return self::getDao()->getsBy(array('imeicrc'=>$imei), array('create_time'=>'DESC'));
	}
	
	/**
	 * 通过帐号获取礼包信息
	 * @param unknown_type $uname
	 * @return boolean|Ambigous <boolean, mixed>
	 */
	public static function getByUname($uname) {
		if (!$uname) return false;
		return self::getDao()->getsBy(array('uname'=>$uname), array('create_time'=>'DESC'));
	}
	
	/**
	 *
	 * @param unknown_type $imei
	 * @return boolean|Ambigous <boolean, mixed>
	 */
	public static function getByImeiGiftId($imei,$gift_id) {
		if (!intval($imei) && !$gift_id) return false;
		return self::getDao()->getBy(array('imeicrc'=>$imei,'gift_id'=>$gift_id), array('create_time'=>'DESC'));
	}
	
	/**
	 *
	 * @param unknown_type $imei
	 * @return boolean|Ambigous <boolean, mixed>
	 */
	public static function getByUnameGiftId($uname,$gift_id) {
		if (!$uname && !$gift_id) return false;
		return self::getDao()->getBy(array('uname'=>$uname,'gift_id'=>$gift_id), array('create_time'=>'DESC'));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getGiftlogByImei($params,$orderBy) {
		if (!in_array($params)) return false;
		return self::getDao()->getsBy($params,$orderBy);
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getGiftlogByStatus($status,$gift_id) {
		return self::getDao()->count(array('status'=>$status,'gift_id'=>$gift_id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getGiftlogCount($gift_id) {
		return self::getDao()->count(array('gift_id'=>$gift_id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getGiftlogByGameID($game_id) {
		return self::getDao()->getsBy(array('game_id'=>$game_id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateImeiGiftId($imei,$id) {
		if (!$imei && !$id) return false;
		$time = Common::getTime();
		return self::getDao()->update(array('imei'=>$imei, 'imeicrc'=>crc32($imei),'create_time'=>$time,'status'=>1),$id);
	}
	
	
	/**
	 *
	 * @param unknown_type $old_game_id
	 * @param unknown_type $new_game_id
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updateGiftLogGameId($old_game_id,$new_game_id,$gift_id) {
		if (!$old_game_id && !$new_game_id) return false;
		return self::getDao()->updateBy(array('game_id'=>intval($new_game_id)), array('game_id'=>intval($old_game_id),'gift_id'=>$gift_id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateGiftlog($data, $id) {
		if (!is_array($data)) return false;
		$data = self::cookData($data);
		return self::getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * @param unknown $data
	 * @param unknown $params
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updateByGiftLog($data, $params) {
		if (!is_array($data)) return false;
		$data = self::cookData($data);
		return self::getDao()->updateBy($data, $params);
	}
	
	/**
	 *
	 * @param unknown_type $code
	 * @param unknown_type $id
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updateActivationCode($code, $id) {
		if (!$code && !$id) return false;
		return self::getDao()->update(array('activation_code'=>$code),$id);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function replaceGiftlog($data) {
		if (!is_array($data)) return false;
		return self::getDao()->replace($data);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteGiftlog($id) {
		return self::getDao()->delete(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteGiftlogByActivationCcode($activation_code) {
		return self::getDao()->deleteBy(array('activation_code'=>$activation_code));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addGiftlog($data) {
		if (!is_array($data)) return false;
		$data = self::cookData($data);
		return self::getDao()->insert($data);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function mutiGiftlog($data) {
		if (!is_array($data)) return false;
		return self::getDao()->mutiInsert($data);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function cookData($data) {
		$tmp = array();
		if(isset($data['gift_id'])) $tmp['gift_id'] = intval($data['gift_id']);
		if(isset($data['game_id'])) $tmp['game_id'] = intval($data['game_id']);
		if(isset($data['uname'])) $tmp['uname'] = $data['uname'];
		if(isset($data['imei'])) $tmp['imei'] = $data['imei'];
		if(isset($data['imeicrc'])) $tmp['imeicrc'] = intval($data['imeicrc']);
		if(isset($data['activation_code'])) $tmp['activation_code'] = $data['activation_code'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['start_time'])) $tmp['start_time'] = intval($data['start_time']);
		if(isset($data['end_time'])) $tmp['end_time'] = intval($data['end_time']);
		if(isset($data['send_order'])) $tmp['send_order'] = intval($data['send_order']);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_Giftlog
	 */
	private static function getDao() {
		return Common::getDao("Client_Dao_Giftlog");
	}
}
