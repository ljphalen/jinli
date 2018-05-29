<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author ljp
 *
 */
class Festival_Service_PrizeExchanges{
	const FIELD_ID ='id';
	const FIELD_UUID = 'uuid';
	const FIELD_FESTIVAL_ID = 'festival_id';
	const FIELD_PRIZE_ID = 'prize_id';
	const FIELD_CONTACT = 'contact';
	const FIELD_PHONE ='phone';
	const FIELD_ADDRESS ='address';
	const FIELD_STATUS ='status';
	const FIELD_CREATE_TIME ='create_time';
	

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array('create_time'=>'DESC', 'id' => 'DESC')) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 *
	 * @param unknown $params
	 * @return boolean
	 */
	public static function getBy($params,  $orderBy = array()){
		if (!is_array($params)) return false;
		return self::getDao()->getBy($params, $orderBy);
	}
	
	
	/**
	 *
	 * @param unknown $params
	 * @return boolean
	 */
	public static function getsBy($params, $orderBy = array()){
		if (!is_array($params)) return false;
		return self::getDao()->getsBy($params, $orderBy);
	}
	

	/**
	 * 
	 * @param array $data
	 * @return boolean|Ambigous <boolean, unknown, number>|string
	 */
	public static function insert($data){
		if (!is_array($data)) return false;
		$data = self::cookData($data);
		$ret =  self::getDao()->insert($data);
		if (!$ret) return $ret;
		return self::getDao()->getLastInsertId();
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateBy($data, $params) {
		if (!is_array($data)) return false;
		$data = self::cookData($data);
		return self::getDao()->updateBy($data, $params);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function cookData($data) {
		$tmp = array();
		if(isset($data[self::FIELD_ID])) $tmp[self::FIELD_ID] = $data[self::FIELD_ID];
		if(isset($data[self::FIELD_UUID])) $tmp[self::FIELD_UUID] = $data[self::FIELD_UUID];
		if(isset($data[self::FIELD_FESTIVAL_ID])) $tmp[self::FIELD_FESTIVAL_ID] = $data[self::FIELD_FESTIVAL_ID];
		if(isset($data[self::FIELD_PRIZE_ID])) $tmp[self::FIELD_PRIZE_ID] = $data[self::FIELD_PRIZE_ID];
		if(isset($data[self::FIELD_CONTACT])) $tmp[self::FIELD_CONTACT] = $data[self::FIELD_CONTACT];
		if(isset($data[self::FIELD_PHONE])) $tmp[self::FIELD_PHONE] = $data[self::FIELD_PHONE];
		if(isset($data[self::FIELD_ADDRESS])) $tmp[self::FIELD_ADDRESS] = $data[self::FIELD_ADDRESS];
		if(isset($data[self::FIELD_STATUS])) $tmp[self::FIELD_STATUS] = $data[self::FIELD_STATUS];
		if(isset($data[self::FIELD_CREATE_TIME])) $tmp[self::FIELD_CREATE_TIME] = $data[self::FIELD_CREATE_TIME];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Festival_Dao_PrizeExchanges
	 */
	private static function getDao() {
		return Common::getDao("Festival_Dao_PrizeExchanges");
	}
}
