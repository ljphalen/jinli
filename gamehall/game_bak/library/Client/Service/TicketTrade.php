<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author luojiapeng
 *
 */
class Client_Service_TicketTrade{

    const GRANT_CAUSE_A_COUPON_ACTIVITY = 3; //A券活动
    
    
	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAll() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 *
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @return multitype:unknown multitype:
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy= array('start_time'=>'DESC', 'id'=>'DESC')) {
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
	public static function getNum($params) {
		if (!is_array($params)) return false;
		return self::_getDao()->getNum($params);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getCount($params) {
		if (!is_array($params)) return false;
		return self::_getDao()->getCount($params);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getTotalTicket($params) {
		if (!is_array($params)) return false;
		return self::_getDao()->getTotalTicket($params);
	}
	

	/**
	 *
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @return multitype:unknown multitype:
	 */
	public static function getBy($params = array() , $orderBy = array('id'=>'ASC')){
		$ret = self::_getDao()->getBy($params ,$orderBy);
		if(!$ret) return false;
		return $ret;

	}
	
	/**
	 *
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @return multitype:unknown multitype:
	 */
	public static function getsBy($params = array(), $orderBy = array('id'=>'ASC') ){
		$ret = self::_getDao()->getsBy($params, $orderBy);
		if(!$ret) return false;
		return $ret;
	
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getByID($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	

	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function update($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * @param unknown_type $data
	 * @param unknown_type $params
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updateBy($data, $params) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->updateBy($data, $params);
	}

	
	
	/**
	 *
	 * @param unknown_type $data
	 * @param unknown_type $sorts
	 * @return boolean
	 */
	public static function sort($sorts) {
		foreach($sorts as $key=>$value) {
			self::_getDao()->update(array('sort'=>$value), $key);
		}
		return true;
	}
	

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * @param unknown_type $params
	 * @return Ambigous <boolean, number>
	 */
	public static function deleteBy($params) {
		return self::_getDao()->deleteBy($params);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function insert($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$ret = self::_getDao()->insert($data);
		if (!$ret) return $ret;
		return self::_getDao()->getLastInsertId();
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @param unknown_type $btype
	 * @return Ambigous <boolean, mixed>
	 */
	public static function mutinsert($data) {
		if (!is_array($data)) return false;
		return self::_getDao()->mutiInsert($data);
	}
	
	/**
	 * 
	 * @param unknown_type $data
	 * @return boolean
	 */
	public static function mutiFieldInsert($data) {
		if (!is_array($data)) return false;
		return self::_getDao()->mutiFieldInsert($data);
	}
	
	/**
	 *
	 * @param unknown_type $data
	 * @return boolean
	 */
	public static function getConsumeRank($page = 1, $limit = 10, $params = array()) {
		if (!is_array($params)) return false;
		return self::_getDao()->getConsumeRank($page = 1, $limit = 10, $params);
	}
	
	/**
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['uuid'])) $tmp['uuid'] = $data['uuid'];
		if(isset($data['aid'])) $tmp['aid'] = $data['aid'];
		if(isset($data['denomination'])) $tmp['denomination'] = $data['denomination'];
		if(isset($data['use_denomination'])) $tmp['use_denomination'] = $data['use_denomination'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['send_type'])) $tmp['send_type'] = $data['send_type'];
		if(isset($data['sub_send_type'])) $tmp['sub_send_type'] = $data['sub_send_type'];
		if(isset($data['consume_time'])) $tmp['consume_time'] = $data['consume_time'];
		if(isset($data['out_order_id'])) $tmp['out_order_id'] = $data['out_order_id'];
		if(isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
		if(isset($data['end_time'])) $tmp['end_time'] = $data['end_time'];
		if(isset($data['update_time'])) $tmp['update_time'] = $data['update_time'];
		if(isset($data['densection'])) $tmp['densection'] = $data['densection'];
		if(isset($data['description'])) $tmp['description'] = $data['description'];
		if(isset($data['third_type'])) $tmp['third_type'] = $data['third_type'];
		return $tmp;
	}
	
	public static function getListByEndTime($uuid, $sDate, $eDate) {
	    return self::_getDao()->getListByEndTime($uuid, $sDate, $eDate);
	}
	
	/**
	 * 
	 * @return Client_Dao_TicketTrade
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_TicketTrade");
	}
}