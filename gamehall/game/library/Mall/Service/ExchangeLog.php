<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Mall_Service_ExchangeLog{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAll() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array('exchange_time'=>'DESC', 'id' => 'DESC')) {
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
	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public static function getsBy($params) {
		return self::_getDao()->getsBy($params, array('send_time'=>'DESC','id'=>'DESC'));
	}
	
	/**
	 * 
	 * @param unknown_type $params
	 * @return string
	 */
	public static function getCount($params) {
		return self::_getDao()->count($params);
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
	
	public static function updateLogStatus($data, $status) {
		if (!is_array($data)) return false;
		foreach($data as $key=>$value){
			if($status == 1) {
				$send_time = Common::getTime();
			    $ret = self::_getDao()->update(array('status'=>$status,'send_time'=>$send_time), $value);
			}
		}
		return $ret;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function add($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = intval($data['id']);
		if(isset($data['mall_id'])) $tmp['mall_id'] = intval($data['mall_id']);
		if(isset($data['uuid'])) $tmp['uuid'] =  $data['uuid'];
		if(isset($data['uname'])) $tmp['uname'] = $data['uname'];
		if(isset($data['exchange_num'])) $tmp['exchange_num'] = $data['exchange_num'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['exchange_time'])) $tmp['exchange_time'] = $data['exchange_time'];
		if(isset($data['send_time'])) $tmp['send_time'] = $data['send_time'];
		if(isset($data['address'])) $tmp['address'] = $data['address'];
		if(isset($data['receiver'])) $tmp['receiver'] = $data['receiver'];
		if(isset($data['receiverphone'])) $tmp['receiverphone'] = $data['receiverphone'];
		if(isset($data['qq'])) $tmp['qq'] = $data['qq'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Mall_Dao_ExchangeLog
	 */
	private static function _getDao() {
		return Common::getDao("Mall_Dao_ExchangeLog");
	}
}
