<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author ljp
 *
 */
class Festival_Service_PropsGrant{
	  
	  const FIELD_ID = 'id';
	  const FIELD_UUID = 'uuid';
	  const FIELD_FESTIVAL_ID = 'festival_id';
	  const FIELD_PROP_ID = 'prop_id';
	  const FIELD_GAME_ID = 'game_id';
	  const FIELD_CREATE_TIME = 'create_time';
	  



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
		if(isset($data[self::FIELD_PROP_ID])) $tmp[self::FIELD_PROP_ID] = $data[self::FIELD_PROP_ID];
		if(isset($data[self::FIELD_GAME_ID])) $tmp[self::FIELD_GAME_ID] = $data[self::FIELD_GAME_ID];
		if(isset($data[self::FIELD_CREATE_TIME])) $tmp[self::FIELD_CREATE_TIME] = $data[self::FIELD_CREATE_TIME];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Festival_Dao_PopsGrant
	 */
	private static function getDao() {
		return Common::getDao("Festival_Dao_PropsGrant");
	}
}
