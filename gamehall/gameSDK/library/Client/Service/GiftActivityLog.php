<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Client_Service_GiftActivityLog extends Common_Service_Base{
    const GIFT_ALREADY_SEND = 1; //礼包已送
    const GIFT_NOT_SEND = 0;     //礼包未送
    
	/**
	 * 
	 * @param int $page
	 * @param int $limit
	 * @param params $params
	 * @return multitype:unknown
	 */
	public static function getList($page, $limit, $params = array(), $orderBy = array('send_order'=>'DESC', 'create_time' => 'DESC')) {
		if(intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList(intval($start), intval($limit), $params, $orderBy);
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
	public static function getBy($params = array(), $orderBy = array('send_order'=>'DESC', 'create_time' => 'DESC')) {
		return self::getDao()->getBy($params, $orderBy);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getsBy($params = array(), $orderBy = array('send_order'=>'DESC', 'create_time' => 'DESC')) {
		return self::getDao()->getsBy($params, $orderBy);
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
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function mutiGiftLog($data) {
	    if (!is_array($data)) return false;
	    return self::getDao()->mutiInsert($data);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getGiftLogCount($param) {
	    return self::getDao()->count($param);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function delete($id) {
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
		if(isset($data['gift_id'])) $tmp['gift_id'] = $data['gift_id'];
		if(isset($data['game_id'])) $tmp['game_id'] = $data['game_id'];
		if(isset($data['uname'])) $tmp['uname'] = $data['uname'];
		if(isset($data['uuid'])) $tmp['uuid'] = $data['uuid'];
		if(isset($data['imei'])) $tmp['imei'] = $data['imei'];
		if(isset($data['imeicrc'])) $tmp['imeicrc'] = $data['imeicrc'];
		if(isset($data['activation_code'])) $tmp['activation_code'] = $data['activation_code'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['send_order'])) $tmp['send_order'] = intval($data['send_order']);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_GiftActivityLog
	 */
	private static function getDao() {
		return Common::getDao("Client_Dao_GiftActivityLog");
	}
	
	
}
