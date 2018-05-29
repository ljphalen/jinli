<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author ljp
 *
 */
class Festival_Service_Prizes{
	const FIELD_ID = 'id';
	const FIELD_NAME = 'name';
	const FIELD_PRIZE_TYPE = 'prize_type';
	const FIELD_DENOMINATION ='denomination';
	const FIELD_START_TIME ='start_time';
	const FIELD_END_TIME ='end_time';	 	
	const FIELD_FESTIVAL_ID = 'festival_id';
	const FIELD_TOTAL ='total';
	const FIELD_IMG ='img';
	const FIELD_ICON ='icon';
	const FIELD_RULE ='rule';
	const FIELD_CONDITION='condition';
	const FIELD_CREATE_TIME='create_time';
	
	const SEND_ENTITY = 1;
	const SEND_TICKET = 2;
	const SEND_POINT  = 3;
	
	
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
		if(isset($data[self::FIELD_NAME])) $tmp[self::FIELD_NAME] = $data[self::FIELD_NAME];
		if(isset($data[self::FIELD_PRIZE_TYPE])) $tmp[self::FIELD_PRIZE_TYPE] = $data[self::FIELD_PRIZE_TYPE];
		if(isset($data[self::FIELD_DENOMINATION])) $tmp[self::FIELD_DENOMINATION] = $data[self::FIELD_DENOMINATION];
		if(isset($data[self::FIELD_START_TIME])) $tmp[self::FIELD_START_TIME] = $data[self::FIELD_START_TIME];
		if(isset($data[self::FIELD_END_TIME])) $tmp[self::FIELD_END_TIME] = $data[self::FIELD_END_TIME];	
		if(isset($data[self::FIELD_FESTIVAL_ID])) $tmp[self::FIELD_FESTIVAL_ID] = $data[self::FIELD_FESTIVAL_ID];
		if(isset($data[self::FIELD_TOTAL])) $tmp[self::FIELD_TOTAL] = $data[self::FIELD_TOTAL];
		if(isset($data[self::FIELD_IMG])) $tmp[self::FIELD_IMG] = $data[self::FIELD_IMG];
		if(isset($data[self::FIELD_ICON])) $tmp[self::FIELD_ICON] = $data[self::FIELD_ICON];
		if(isset($data[self::FIELD_RULE])) $tmp[self::FIELD_RULE] = $data[self::FIELD_RULE];
		if(isset($data[self::FIELD_CONDITION])) $tmp[self::FIELD_CONDITION] = $data[self::FIELD_CONDITION];
		if(isset($data[self::FIELD_CREATE_TIME])) $tmp[self::FIELD_CREATE_TIME] = $data[self::FIELD_CREATE_TIME];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Festival_Dao_Prizes
	 */
	private static function getDao() {
		return Common::getDao("Festival_Dao_Prizes");
	}
}
