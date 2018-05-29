<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author ljp
 *
 */
class Festival_Service_BaseInfo{
	
	  const FIELD_ID = 'id';
	  const FIELD_TITLE = 'title';
	  const FIELD_START_TIME = 'start_time';
	  const FIELD_END_TIME = 'end_time';
	  const FIELD_BANNER_IMG = 'banner_img';
	  const FIELD_DESCRIPTION = 'description';
	  const FIELD_CLIENT_VSERSIONS = 'client_versions';
	  const FIELD_PROP_NAME = 'prop_name';
	  const FIELD_PRIZE_CLOMN_NAME = 'prize_column_name';
	  const FIELD_PRIZE_CONFIG = 'config';
	  const FIELD_STATUS = 'status';
	  const FIELD_CREATE_TIME = 'create_time';
	  
	  
	  const PRIZE_TYPE_ENTITY = 1;
	  const PRIZE_TYPE_ACOUPON = 2;
	  const PRIZE_TYPE_POINT = 3;
	  
	  const ACTIVITY_STATUS_OPEN = 1;
	  const ACTIVITY_STATUS_CLOSE = 0;
	  
	  public  static $mCleintVersionConfig = array(1=>'1.5.5',
			  		                              2=>'1.5.6',
			  		                              3=>'1.5.7',
			  		                              4=>'1.5.8',
			  		                              5=>'1.5.9');



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
	
	public static function getEffectFestivalInfo() {
		$time = Common::getTime();
		$params[Festival_Service_BaseInfo::FIELD_START_TIME] = array('<=', $time);
		$params[Festival_Service_BaseInfo::FIELD_END_TIME] = array('>=', $time);
		$params[Festival_Service_BaseInfo::FIELD_STATUS] = self::ACTIVITY_STATUS_OPEN;
		$result = Festival_Service_BaseInfo::getBy($params);
		return $result;
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
	

	public static function getConfigClientVersion(){
		return self::$mCleintVersionConfig;
	}
	
	public static function checkClientVersion($currentClientVersion, $festivalInfo) {
		$configClientVersion = self::getConfigClientVersion();
		$clientVersionKey = $festivalInfo[self::FIELD_CLIENT_VSERSIONS];
		$clientVersion = $configClientVersion[$clientVersionKey];
		$result = Common::compareClientVersion($currentClientVersion, $clientVersion);
		return $result;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function cookData($data) {
		$tmp = array();  
		if(isset($data[self::FIELD_ID])) $tmp[self::FIELD_ID] = $data[self::FIELD_ID];
		if(isset($data[self::FIELD_TITLE])) $tmp[self::FIELD_TITLE] = $data[self::FIELD_TITLE];
		if(isset($data[self::FIELD_START_TIME])) $tmp[self::FIELD_START_TIME] = $data[self::FIELD_START_TIME];
		if(isset($data[self::FIELD_END_TIME])) $tmp[self::FIELD_END_TIME] = $data[self::FIELD_END_TIME];
		if(isset($data[self::FIELD_BANNER_IMG])) $tmp[self::FIELD_BANNER_IMG] = $data[self::FIELD_BANNER_IMG];
		if(isset($data[self::FIELD_DESCRIPTION])) $tmp[self::FIELD_DESCRIPTION] = $data[self::FIELD_DESCRIPTION];
		if(isset($data[self::FIELD_CLIENT_VSERSIONS])) $tmp[self::FIELD_CLIENT_VSERSIONS] = $data[self::FIELD_CLIENT_VSERSIONS];
		if(isset($data[self::FIELD_PROP_NAME])) $tmp[self::FIELD_PROP_NAME] = $data[self::FIELD_PROP_NAME];
		if(isset($data[self::FIELD_PRIZE_CLOMN_NAME])) $tmp[self::FIELD_PRIZE_CLOMN_NAME] = $data[self::FIELD_PRIZE_CLOMN_NAME];
		if(isset($data[self::FIELD_PRIZE_CONFIG])) $tmp[self::FIELD_PRIZE_CONFIG] = $data[self::FIELD_PRIZE_CONFIG];
		if(isset($data[self::FIELD_STATUS])) $tmp[self::FIELD_STATUS] = $data[self::FIELD_STATUS];
		if(isset($data[self::FIELD_CREATE_TIME])) $tmp[self::FIELD_CREATE_TIME] = $data[self::FIELD_CREATE_TIME];
		return $tmp;
	}

	/**
	 * 
	 * @return Festival_Dao_BaseInfo
	 */
	private static function getDao() {
		return Common::getDao("Festival_Dao_BaseInfo");
	}
}
